<?php

namespace App\Http\Controllers;

use App\Models\TarifaInterurbana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaccion;
use App\Models\Nucleo;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BilleteController extends Controller
{
    public function procesar(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para comprar billetes');
        }

        Log::info('=== PROCESANDO COMPRA DE BILLETE ===', $request->all());

        $request->validate([
            'nucleo_origen_id' => 'required|exists:nucleos,id_nucleo',
            'nucleo_destino_id' => 'required|exists:nucleos,id_nucleo',
            'saltos' => 'required|integer|min:0',
            'tarjeta_id' => 'required|exists:cards,id'
        ]);

        $user = Auth::user();
        $tarifa = $this->obtenerTarifa($request->saltos);

        // Obtener núcleos correctamente
        $nucleoOrigen = Nucleo::where('id_nucleo', $request->nucleo_origen_id)->first();
        $nucleoDestino = Nucleo::where('id_nucleo', $request->nucleo_destino_id)->first();

        if (!$nucleoOrigen || !$nucleoDestino) {
            return redirect()->back()->withErrors(['error' => 'Núcleos no encontrados']);
        }

        DB::beginTransaction();

        try {
            // Procesar pago con tarjeta de saldo
            $this->procesarPagoTarjeta($user, $tarifa->tarjeta, $request->tarjeta_id);

            // Crear registro de transacción
            $transaccion = Transaccion::create([
                'user_id' => $user->id,
                'monto' => $tarifa->tarjeta * 100, // En centimos
                'metodo' => 'tarjeta',
                'estado' => 'completado',
                'detalles' => json_encode([
                    'origen' => $nucleoOrigen->nombre,
                    'destino' => $nucleoDestino->nombre,
                    'saltos' => $request->saltos,
                    'tarjeta_id' => $request->tarjeta_id,
                    'precio_original' => $tarifa->bs,
                    'precio_pagado' => $tarifa->tarjeta,
                    'ahorro' => $tarifa->bs - $tarifa->tarjeta
                ])
            ]);

            DB::commit();

            Log::info('Billete comprado exitosamente', ['transaccion_id' => $transaccion->id]);

            return redirect()->route('tarifas.calculadora')
                ->with('success', 'Billete comprado correctamente. Puedes verlo en "Mis Billetes".');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error comprando billete: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }

    public function misBilletes()
    {
        $billetes = Auth::user()->transacciones()
            ->where('estado', 'completado')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('billete.mis-billetes', compact('billetes'));
    }

    public function mostrarBillete(Transaccion $transaccion)
    {
        // Verificar que el billete pertenece al usuario autenticado
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este billete');
        }

        if ($transaccion->estado !== 'completado') {
            abort(404, 'Billete no válido');
        }

        $detalles = json_decode($transaccion->detalles, true);

        return view('billete.mostrar', compact('transaccion', 'detalles'));
    }

    public function descargarPDF(Transaccion $transaccion)
    {
        // Verificar que el usuario puede descargar este billete
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para descargar este billete');
        }

        $detalles = json_decode($transaccion->detalles, true);

        // Obtener datos del usuario y detalles
        $propietario = $transaccion->user->name;
        $email = $transaccion->user->email;
        $origen = $detalles['origen'];
        $destino = $detalles['destino'];
        $zonas = $detalles['saltos'];
        $metodo = ($transaccion->metodo === 'tarjeta') ? 'Tarjeta Bus' : $transaccion->metodo;
        $precio = number_format($transaccion->monto / 100, 2, ',', '.'); // Formato europeo: 0,00
        $fechaCompra = $transaccion->created_at->format('d/m/Y H:i');

        // Construir texto para el QR
        $qrText = "Billete de Transporte\n"
            . "----------------------\n"
            . "Fecha compra: $fechaCompra\n"
            . "Propietario: $propietario\n"
            . "Email: $email\n"
            . "Origen: $origen\n"
            . "Destino: $destino\n"
            . "Zonas: $zonas saltos\n"
            . "Metodo de Pago: $metodo\n"
            . "Total: $precio EUR";

        // Generar QR con el texto
        $qrCode = base64_encode(
            QrCode::format('svg')
                ->size(120)
                ->encoding('UTF-8')
                ->errorCorrection('H')
                ->generate($qrText)
        );

        $pdf = PDF::loadView('billete.pdf', [
            'transaccion' => $transaccion,
            'detalles' => $detalles,
            'qrCode' => $qrCode
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("billete-{$transaccion->id}.pdf");
    }

    private function obtenerTarifa($saltos)
    {
        return TarifaInterurbana::where('saltos', $saltos)->firstOrFail();
    }

    private function procesarPagoTarjeta($user, $monto, $tarjetaId)
    {
        $tarjeta = $user->cards()->findOrFail($tarjetaId);
        $montoEnCentimos = $monto * 100;

        if ($tarjeta->saldo < $montoEnCentimos) {
            throw new \Exception('Saldo insuficiente en la tarjeta seleccionada. Saldo actual: €' .
                number_format($tarjeta->saldo / 100, 2) .
                ', necesario: €' . number_format($monto, 2));
        }

        // Descontar el monto de la tarjeta
        $tarjeta->decrement('saldo', $montoEnCentimos);

        Log::info('Pago procesado con tarjeta', [
            'tarjeta_id' => $tarjetaId,
            'monto_descontado' => $montoEnCentimos,
            'saldo_restante' => $tarjeta->fresh()->saldo
        ]);

        return true;
    }

    public function destroy(Transaccion $transaccion)
    {
        // Verificar que el billete pertenece al usuario autenticado
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este billete');
        }

        try {
            // Eliminar el billete
            $transaccion->delete();

            Log::info('Billete eliminado', [
                'billete_id' => $transaccion->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', 'Billete eliminado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error eliminando billete: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al eliminar el billete.');
        }
    }
}
