@extends('layouts.app')

@section('title', 'Crear Tarjeta - OnubaBus')

@section('content')
    <section class="py-16 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">
            <div class="mb-6">
                <a href="{{ route('cards.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Volver a Mis Tarjetas
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-white">
                    <h2 class="text-3xl font-bold mb-2">Crear Nueva Tarjeta OnubaBus</h2>
                    <p class="text-lg opacity-90">Crea tu tarjeta virtual para viajar por Huelva</p>
                </div>

                <div class="p-6">
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="card-creation-form" action="{{ route('cards.store') }}" method="POST">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Columna izquierda: Información de la tarjeta -->
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">Información de la tarjeta</h3>

                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-semibold mb-3">
                                        Saldo inicial (opcional)
                                    </label>

                                    <!-- Botones de saldo rápido -->
                                    <div class="grid grid-cols-4 gap-2 mb-4">
                                        <button type="button" onclick="setSaldoInicial(0)"
                                                class="saldo-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-3 py-2 rounded-lg text-center font-medium transition text-sm">
                                            €0
                                        </button>
                                        <button type="button" onclick="setSaldoInicial(10)"
                                                class="saldo-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-3 py-2 rounded-lg text-center font-medium transition text-sm">
                                            €10
                                        </button>
                                        <button type="button" onclick="setSaldoInicial(20)"
                                                class="saldo-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-3 py-2 rounded-lg text-center font-medium transition text-sm">
                                            €20
                                        </button>
                                        <button type="button" onclick="setSaldoInicial(50)"
                                                class="saldo-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-3 py-2 rounded-lg text-center font-medium transition text-sm">
                                            €50
                                        </button>
                                    </div>

                                    <input type="number" step="0.01" name="saldo" id="saldo" min="0" max="1000"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           value="{{ old('saldo', 0) }}">
                                    <p class="text-sm text-gray-500 mt-2">Mínimo: €0 - Máximo: €1000</p>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-800 mb-2">Resumen</h4>
                                    <div class="flex justify-between mb-1">
                                        <span>Saldo inicial:</span>
                                        <span>€<span id="saldo-display">{{ old('saldo', '0.00') }}</span></span>
                                    </div>
                                    <div class="flex justify-between mb-1" id="payment-row" style="display: none;">
                                        <span>Pago requerido:</span>
                                        <span class="font-semibold text-green-600">€<span id="payment-amount">0.00</span></span>
                                    </div>
                                </div>

                                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-800 mb-2"><i class="fa-regular fa-lightbulb"></i> Información</h4>
                                    <ul class="text-sm text-gray-600 space-y-2">
                                        <li>• Puedes crear la tarjeta sin saldo inicial</li>
                                        <li>• Si eliges saldo inicial, se procesará el pago inmediatamente</li>
                                        <li>• Podrás recargar tu tarjeta en cualquier momento</li>
                                        <li>• El saldo no caduca</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Columna derecha: Datos de pago -->
                            <div id="payment-section" style="display: none;">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">Datos de pago</h3>

                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-semibold mb-3">
                                        Información de la tarjeta bancaria
                                    </label>
                                    <div id="card-element" class="p-4 border border-gray-300 rounded-lg bg-white"></div>
                                    <div id="card-errors" class="text-red-600 mt-2 text-sm"></div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                    <div class="flex items-center mb-2">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Pago seguro con Stripe</span>
                                    </div>
                                    <p class="text-xs text-gray-600">Tus datos están protegidos con encriptación SSL</p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="payment_method_id" id="payment_method_id">

                        <div class="pt-6 border-t">
                            <button type="submit" id="submit-button"
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-4 rounded-lg
                                           font-semibold hover:from-blue-700 hover:to-blue-900 transition disabled:opacity-50
                                           disabled:cursor-not-allowed text-lg">
                                <span id="button-text">Crear Tarjeta</span>
                                <span id="loading-text" class="hidden">Procesando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ config('cashier.key') }}');
        const elements = stripe.elements();
        let cardElement = null;
        let cardMounted = false;

        const saldoInput = document.getElementById('saldo');
        const saldoDisplay = document.getElementById('saldo-display');
        const paymentAmount = document.getElementById('payment-amount');
        const paymentRow = document.getElementById('payment-row');
        const paymentSection = document.getElementById('payment-section');
        const buttonText = document.getElementById('button-text');

        function setSaldoInicial(amount) {
            saldoInput.value = amount.toFixed(2);
            updateDisplays();

            // Actualizar estilos de botones
            document.querySelectorAll('.saldo-btn').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                btn.classList.add('bg-gray-100', 'border-gray-300');
            });
            event.target.classList.remove('bg-gray-100', 'border-gray-300');
            event.target.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
        }

        function updateDisplays() {
            const saldo = parseFloat(saldoInput.value) || 0;
            saldoDisplay.textContent = saldo.toFixed(2);
            paymentAmount.textContent = saldo.toFixed(2);

            if (saldo > 0) {
                paymentRow.style.display = 'flex';
                paymentSection.style.display = 'block';
                buttonText.textContent = `Crear Tarjeta y Pagar €${saldo.toFixed(2)}`;

                // Montar Stripe Elements si no está montado
                if (!cardMounted) {
                    mountStripeElements();
                }
            } else {
                paymentRow.style.display = 'none';
                paymentSection.style.display = 'none';
                buttonText.textContent = 'Crear Tarjeta';

                // Desmontar Stripe Elements si está montado
                if (cardMounted) {
                    cardElement.unmount();
                    cardMounted = false;
                }
            }
        }

        function mountStripeElements() {
            if (!cardMounted) {
                cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '16px',
                            color: '#374151',
                            fontFamily: '"Inter", system-ui, sans-serif',
                            '::placeholder': {
                                color: '#9CA3AF',
                            },
                        },
                        invalid: {
                            color: '#EF4444',
                        },
                    }
                });

                cardElement.mount('#card-element');
                cardMounted = true;

                // Manejo de errores de la tarjeta
                cardElement.on('change', function(event) {
                    const displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
            }
        }

        saldoInput.addEventListener('input', updateDisplays);

        // Manejo del formulario
        document.getElementById('card-creation-form').addEventListener('submit', async (e) => {
            const saldo = parseFloat(saldoInput.value) || 0;
            const submitButton = document.getElementById('submit-button');
            const loadingText = document.getElementById('loading-text');

            // Si no hay saldo inicial, enviar formulario normalmente
            if (saldo <= 0) {
                return;
            }

            e.preventDefault();

            // Deshabilitar botón y mostrar loading
            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');

            try {
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: '{{ auth()->user()->name }}',
                        email: '{{ auth()->user()->email }}'
                    }
                });

                if (error) {
                    document.getElementById('card-errors').textContent = error.message;

                    // Rehabilitar botón
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                } else {
                    document.getElementById('payment_method_id').value = paymentMethod.id;
                    e.target.submit();
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('card-errors').textContent = 'Error al procesar el pago';

                // Rehabilitar botón
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                loadingText.classList.add('hidden');
            }
        });

        // Inicializar displays
        updateDisplays();
    </script>
@endsection
