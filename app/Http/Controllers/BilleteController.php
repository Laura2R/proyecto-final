<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaccion;
use App\Models\Nucleo;
use App\Models\Card;
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

    // NUEVO: Metodo para mostrar billete desde QR (sin autenticación)
    public function mostrarBilleteQR($token)
    {
        try {
            $transaccionId = $this->decodificarToken($token);

            if (!$transaccionId) {
                abort(404, 'Billete no encontrado');
            }

            $transaccion = Transaccion::with('user')->where('id', $transaccionId)
                ->where('estado', 'completado')
                ->first();

            if (!$transaccion) {
                abort(404, 'Billete no encontrado o no válido');
            }

            $detalles = json_decode($transaccion->detalles, true);

            return view('billete.qr-view', compact('transaccion', 'detalles'));

        } catch (\Exception $e) {
            Log::error('Error mostrando billete QR: ' . $e->getMessage());
            abort(404, 'Error al procesar el billete');
        }
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
        $metodo = ($transaccion->metodo === 'tarjeta') ? 'Tarjeta de saldo' : $transaccion->metodo;
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
            . "Método: $metodo\n"
            . "Total: $precio €";

        // Generar QR con el texto
        $qrCode = base64_encode(
            QrCode::format('svg')
                ->size(120)
                ->encoding('UTF-8') // Sin esto intenta usar ISO-8859-1, lo cual no funciona
                ->errorCorrection('H') // Opcional pero mejora la legibilidad
                ->generate($qrText)
        );

        // Cargar la relación user para mostrar en el PDF
        $transaccion->load('user');

        $pdf = PDF::loadView('billete.pdf', [
            'transaccion' => $transaccion,
            'detalles' => $detalles,
            'qrCode' => $qrCode
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("billete-{$transaccion->id}.pdf");
    }


    // MÉTODOS PRIVADOS QUE FALTABAN:

    /**
     * Generar token seguro para el QR
     */
    private function generarToken($transaccionId)
    {
        return base64_encode(encrypt($transaccionId));
    }

    /**
     * Decodificar token del QR
     */
    private function decodificarToken($token)
    {
        try {
            return decrypt(base64_decode($token));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function obtenerTarifa($saltos)
    {
        return \App\Models\TarifaInterurbana::where('saltos', $saltos)->firstOrFail();
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
}
