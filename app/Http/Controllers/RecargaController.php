<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class RecargaController extends Controller
{
    public function selectCard()
    {
        $user = auth()->user();
        $cards = $user->cards;

        return view('recarga.select', compact('cards'));
    }

    public function showForm(Card $card)
    {
        $this->authorizeCard($card);

        return view('recarga.form', [
            'card' => $card,
            'stripeKey' => config('cashier.key')
        ]);
    }

    public function procesar(Request $request, Card $card)
    {
        $this->authorizeCard($card);

        $request->validate([
            'cantidad' => 'required|integer|min:1|max:100',
            'payment_method_id' => 'required'
        ]);


        $user = $request->user();
        $amount = $request->cantidad * 100; // En céntimos

        try {
            // Crear o actualizar cliente en Stripe
            $user->createOrGetStripeCustomer();

            $paymentIntent = $user->charge(
                $amount,
                $request->payment_method_id,
                [
                    'currency' => 'eur',
                    'description' => 'Recarga tarjeta #'.$card->id,
                    'confirm' => true,
                    'return_url' => url('/cards'),
                    'metadata' => [
                        'card_id' => $card->id,
                        'user_id' => $user->id
                    ]
                ]
            );

            // Verificar el estado del pago
            if ($paymentIntent->status === 'succeeded') {
                $card->saldo += $amount;
                $card->save();
                return redirect()->route('cards.index')
                    ->with('status', 'Recarga realizada con éxito. Nuevo saldo: €'.number_format($card->saldo/100, 2));
            }

            if ($paymentIntent->status === 'requires_action' &&
                isset($paymentIntent->next_action->redirect_to_url)) {
                return redirect($paymentIntent->next_action->redirect_to_url->url);
            }

            if ($paymentIntent->status === 'requires_confirmation') {
                return redirect()->route('recarga.pending', ['card' => $card->id])
                    ->with('info', 'Pago pendiente de confirmación');
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al procesar el pago: ' . $e->getMessage()]);
        }

        return back()->withErrors(['error' => 'Error desconocido al procesar el pago']);
    }

    public function success(Request $request, Card $card)
    {
        $this->authorizeCard($card);

        $paymentIntentId = $request->get('payment_intent');

        if (!$paymentIntentId) {
            return redirect()->route('cards.index')
                ->withErrors(['error' => 'No se encontró información del pago']);
        }

        try {
            Stripe::setApiKey(config('cashier.secret'));
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                $cardId = $paymentIntent->metadata->card_id ?? null;

                if ($cardId && $cardId == $card->id) {
                    $amount = $paymentIntent->amount;
                    $card->saldo += $amount;
                    $card->save();

                    return redirect()->route('cards.index')
                        ->with('status', 'Recarga completada con éxito. Nuevo saldo: €'.number_format($card->saldo/100, 2));
                }
            }

            return redirect()->route('cards.index')
                ->withErrors(['error' => 'El pago no se completó correctamente']);

        } catch (\Exception $e) {
            return redirect()->route('cards.index')
                ->withErrors(['error' => 'Error al verificar el pago: ' . $e->getMessage()]);
        }
    }

    public function pending(Card $card)
    {
        $this->authorizeCard($card);
        return view('recarga.pending', compact('card'));
    }

    public function cancel(Card $card)
    {
        $this->authorizeCard($card);
        return redirect()->route('cards.index')
            ->with('info', 'Pago cancelado por el usuario');
    }




    private function authorizeCard(Card $card)
    {
        if ($card->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para acceder a esta tarjeta');
        }
    }

}
