@extends('layouts.app')

@section('title', 'Recargar Tarjeta - OnubaBus')

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
                    <h2 class="text-3xl font-bold mb-2">Recargar Tarjeta</h2>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg opacity-90">Tarjeta #{{ str_pad($card->id, 4, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-sm opacity-80">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm opacity-80">Saldo actual</p>
                            <p class="text-3xl font-bold">€{{ $card->saldo_formateado }}</p>
                        </div>
                    </div>
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

                    <form id="payment-form" action="{{ route('recarga.procesar', $card->id) }}" method="POST">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-8">
                            <!-- Columna izquierda: Cantidad -->
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">Seleccionar cantidad</h3>

                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-semibold mb-3">
                                        Cantidad a recargar (€)
                                    </label>

                                    <!-- Botones de cantidad rápida -->
                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                        <button type="button" onclick="setCantidad(10)"
                                                class="cantidad-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-3 rounded-lg text-center font-medium transition">
                                            €10
                                        </button>
                                        <button type="button" onclick="setCantidad(20)"
                                                class="cantidad-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-3 rounded-lg text-center font-medium transition">
                                            €20
                                        </button>
                                        <button type="button" onclick="setCantidad(50)"
                                                class="cantidad-btn bg-gray-100 hover:bg-blue-100 border border-gray-300 px-4 py-3 rounded-lg text-center font-medium transition">
                                            €50
                                        </button>
                                    </div>

                                    <input type="number" name="cantidad" id="cantidad"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           min="1" max="100" value="{{ old('cantidad', 10) }}" required>
                                    <p class="text-sm text-gray-500 mt-2">Mínimo: €1 - Máximo: €100</p>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold text-gray-800 mb-2">Resumen</h4>
                                    <div class="flex justify-between mb-1">
                                        <span>Saldo actual:</span>
                                        <span>€{{ $card->saldo_formateado }}</span>
                                    </div>
                                    <div class="flex justify-between mb-1">
                                        <span>Recarga:</span>
                                        <span>€<span id="amount-display">{{ old('cantidad', 10) }}</span></span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between font-semibold">
                                        <span>Nuevo saldo:</span>
                                        <span>€<span id="new-balance">{{ number_format(($card->saldo / 100) + old('cantidad', 10), 2) }}</span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna derecha: Datos de pago -->
                            <div>
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
                                    class="w-full bg-gradient-to-r from-green-600 to-green-800 text-white px-6 py-4 rounded-lg
                                           font-semibold hover:from-green-700 hover:to-green-900 transition disabled:opacity-50
                                           disabled:cursor-not-allowed text-lg">
                                <span id="button-text">Recargar €<span id="button-amount">{{ old('cantidad', 10) }}</span></span>
                                <span id="loading-text" class="hidden">Procesando pago...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3"><i class="fa-regular fa-lightbulb"></i> Información sobre las recargas</h3>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li>• Las recargas se procesan de forma inmediata</li>
                    <li>• El saldo se añadirá automáticamente a tu tarjeta</li>
                    <li>• Puedes recargar entre €1 y €100 por transacción</li>
                    <li>• El pago es seguro y está protegido por Stripe</li>
                </ul>
            </div>
        </div>
    </section>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements();

        const cardElement = elements.create('card', {
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

        const form = document.getElementById('payment-form');
        const cantidadInput = document.getElementById('cantidad');
        const amountDisplay = document.getElementById('amount-display');
        const buttonAmount = document.getElementById('button-amount');
        const newBalance = document.getElementById('new-balance');
        const currentBalance = {{ $card->saldo / 100 }};

        function setCantidad(amount) {
            cantidadInput.value = amount;
            updateDisplays();

            // Actualizar estilos de botones
            document.querySelectorAll('.cantidad-btn').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                btn.classList.add('bg-gray-100', 'border-gray-300');
            });
            event.target.classList.remove('bg-gray-100', 'border-gray-300');
            event.target.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
        }

        function updateDisplays() {
            const amount = parseFloat(cantidadInput.value) || 0;
            amountDisplay.textContent = amount.toFixed(2);
            buttonAmount.textContent = amount.toFixed(2);
            newBalance.textContent = (currentBalance + amount).toFixed(2);
        }

        cantidadInput.addEventListener('input', updateDisplays);

        // Manejo de errores de la tarjeta
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const loadingText = document.getElementById('loading-text');

            // Deshabilitar botón y mostrar loading
            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');

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
                form.submit();
            }
        });

        // Inicializar displays
        updateDisplays();
    </script>
@endsection
