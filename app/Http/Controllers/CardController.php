<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\CardException;

class CardController extends Controller
{
    public function index()
    {
        $cards = Auth::user()->cards()->latest()->get();
        return view('cards.index', compact('cards'));
    }

    public function create()
    {
        return view('cards.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'saldo' => 'nullable|numeric|min:0|max:1000',
            'payment_method_id' => 'nullable|string|required_if:saldo,>0',
        ]);

        $saldoInicial = floatval($validated['saldo'] ?? 0);
        $saldoCentimos = intval($saldoInicial * 100);

        DB::beginTransaction();

        try {
            // Crear la tarjeta primero
            $card = Auth::user()->cards()->create([
                'saldo' => 0, // Inicialmente 0, se actualizará después del pago
            ]);

            // Si hay saldo inicial, procesar el pago
            if ($saldoInicial > 0 && $request->payment_method_id) {
                $user = $request->user();

                // Crear o actualizar cliente en Stripe si es necesario
                if (!$user->hasStripeId()) {
                    $user->createAsStripeCustomer();
                }

                // Procesar el pago usando Laravel Cashier
                $payment = $user->charge($saldoCentimos, $request->payment_method_id, [
                    'currency' => 'eur',
                    'description' => 'Saldo inicial tarjeta #' . $card->id,
                    'return_url' => route('cards.index'),
                    'metadata' => [
                        'card_id' => $card->id,
                        'user_id' => $user->id,
                        'tipo' => 'saldo_inicial'
                    ]
                ]);

                // Si llegamos aquí, el pago fue exitoso
                // Actualizar el saldo de la tarjeta
                $card->update(['saldo' => $saldoCentimos]);
            }

            DB::commit();

            $mensaje = $saldoInicial > 0
                ? "Tarjeta creada correctamente con saldo inicial de €{$saldoInicial}."
                : 'Tarjeta creada correctamente.';

            return redirect()->route('cards.index')->with('status', $mensaje);

        } catch (CardException $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'Error en la tarjeta: ' . $e->getMessage()
            ])->withInput();

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors([
                'error' => 'Error al procesar el pago: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function show(Card $card)
    {
        // Verificar que la tarjeta pertenece al usuario
        if ($card->user_id !== Auth::id()) {
            abort(403);
        }

        return view('cards.show', compact('card'));
    }

    public function destroy(Card $card)
    {
        if ($card->user_id !== Auth::id()) {
            abort(403);
        }

        $card->delete();
        return redirect()->route('cards.index')->with('status', 'Tarjeta eliminada correctamente.');
    }
}
