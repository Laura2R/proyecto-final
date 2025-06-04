@extends('layouts.app')

@section('title', 'Compra Exitosa - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">¡Compra Realizada con Éxito!</h1>
            <p class="text-xl">Tu billete ha sido procesado correctamente</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Mensaje de éxito -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="font-semibold">¡Compra Realizada con Éxito!</h4>
                        <p class="text-sm mt-1">Tu billete ha sido procesado correctamente y está listo para descargar.</p>
                    </div>
                </div>
            </div>

            <!-- Detalles del billete -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-blue-600 text-white">
                    <h2 class="text-xl font-semibold">📄 Detalles de tu Billete</h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información del viaje -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Viaje</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Número de Billete:</span>
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                        #{{ $transaccion->id }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Origen:</span>
                                    <span class="text-gray-900">{{ $detalles['origen'] }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Destino:</span>
                                    <span class="text-gray-900">{{ $detalles['destino'] }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-medium text-gray-700">Saltos:</span>
                                    <span class="text-gray-900">{{ $detalles['saltos'] }} zona(s)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Información del pago -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Pago</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Precio Pagado:</span>
                                    <span class="text-2xl font-bold text-green-600">
                                        €{{ number_format($transaccion->monto / 100, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Precio Normal:</span>
                                    <span class="text-gray-500 line-through">
                                        €{{ number_format($detalles['precio_original'], 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="font-medium text-gray-700">Ahorro:</span>
                                    <span class="text-green-600 font-medium">
                                        €{{ number_format($detalles['ahorro'], 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="font-medium text-gray-700">Fecha:</span>
                                    <span class="text-gray-900">{{ $transaccion->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información importante -->
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-blue-800 font-semibold">📱 Importante</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    Este billete es válido por 90 minutos desde la compra y permite transbordos.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('billete.descargar', ['transaccion' => $transaccion, 'download' => 'true']) }}"
                           class="w-full bg-gradient-to-r from-green-600 to-green-800 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-900 transition text-center block">
                            📥 Descargar Billete PDF
                        </a>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <a href="{{ route('tarifas.calculadora') }}"
                               class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition text-center block">
                                🔙 Comprar Otro Billete
                            </a>
                            <a href="/"
                               class="border-2 border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-200 transition text-center block">
                                🏠 Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">¿Qué puedes hacer ahora?</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('cards.index') }}" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">💳</div>
                        <div>
                            <div class="font-medium">Mis Tarjetas</div>
                            <div class="text-sm text-gray-500">Ver saldo y recargar</div>
                        </div>
                    </a>
                    <a href="/horarios" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🕒</div>
                        <div>
                            <div class="font-medium">Horarios</div>
                            <div class="text-sm text-gray-500">Consultar horarios</div>
                        </div>
                    </a>
                    <a href="/paradas/filtro" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">📍</div>
                        <div>
                            <div class="font-medium">Paradas</div>
                            <div class="text-sm text-gray-500">Buscar paradas</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
