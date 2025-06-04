<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaccion;
use App\Models\Nucleo;

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

        // CORRECCIÓN: Obtener núcleos correctamente
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

            // CAMBIO: Redirigir a calculadora con mensaje de éxito
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

    // NUEVA FUNCIÓN: Vista de mis billetes
    public function misBilletes()
    {
        $billetes = Auth::user()->transacciones()
            ->where('estado', 'completado')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('billete.mis-billetes', compact('billetes'));
    }

    public function descargarPDF(Transaccion $transaccion)
    {
        // Verificar que el usuario puede descargar este billete
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para descargar este billete');
        }

        $detalles = json_decode($transaccion->detalles, true);
        $pdf = PDF::loadView('billete.pdf', [
            'transaccion' => $transaccion,
            'detalles' => $detalles,
            'qrData' => "BILLETE:{$transaccion->id}|FECHA:{$transaccion->created_at->format('Y-m-d H:i:s')}|USER:{$transaccion->user_id}"
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("billete-{$transaccion->id}.pdf");
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
