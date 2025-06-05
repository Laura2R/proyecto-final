@extends('layouts.app')

@section('title', 'Mi Billete #' . $transaccion->id . ' - OnubaBus')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">🎫 Mi Billete Digital</h1>
            <p class="text-xl">Billete personal #{{ $transaccion->id }}</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-2xl mx-auto px-4">
            <!-- Información de seguridad -->
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-red-800 font-semibold">🔒 Billete Personal</h4>
                        <p class="text-sm text-red-700 mt-1">
                            Este billete es personal e intransferible. Solo {{ auth()->user()->name }} puede utilizarlo.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header del billete -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 text-center">
                    <h2 class="text-2xl font-bold mb-2"><i class="fas fa-bus"></i> OnubaBus</h2>
                    <p class="text-lg">Billete #{{ $transaccion->id }}</p>
                    <p class="text-sm opacity-80">{{ $transaccion->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <!-- Información del propietario -->
                <div class="bg-gray-50 p-4 border-b">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Propietario del billete</p>
                            <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold text-gray-900">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ruta -->
                <div class="p-6 bg-blue-50 text-center">
                    <div class="text-xl font-bold text-blue-800 mb-2">{{ $detalles['origen'] }}</div>
                    <div class="text-2xl text-blue-600 mb-2">↓</div>
                    <div class="text-xl font-bold text-blue-800">{{ $detalles['destino'] }}</div>
                </div>

                <!-- Detalles -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">Zonas</div>
                            <div class="text-xl font-bold text-gray-800">{{ $detalles['saltos'] }} salto(s)</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-sm text-gray-600">Precio Pagado</div>
                            <div class="text-xl font-bold text-green-600">€{{ number_format($transaccion->monto / 100, 2) }}</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">Método de Pago:</span>
                            <span class="text-gray-900">{{ $transaccion->metodo === 'tarjeta' ? 'Tarjeta Bus' : 'Pago Tradicional' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">Precio Original:</span>
                            <span class="text-gray-500 line-through">€{{ number_format($detalles['precio_original'], 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">Ahorro:</span>
                            <span class="text-green-600 font-medium">€{{ number_format($detalles['ahorro'], 2) }}</span>
                        </div>
                    </div>

                    <!-- Información importante -->
                    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-yellow-800 font-semibold">⏰ Información Importante</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Este billete es válido por 90 minutos desde la compra y permite transbordos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 text-center text-sm text-gray-600">
                    <p><strong>OnubaBus</strong> - Sistema de Transporte Público de Huelva</p>
                    <p>Billete válido según condiciones generales de transporte</p>
                    <p class="text-red-600 font-bold mt-2">BILLETE PERSONAL E INTRANSFERIBLE</p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="text-center mt-6 space-x-4">
                <a href="{{ route('billetes.mis-billetes') }}"
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    ← Volver a Mis Billetes
                </a>
                <a href="{{ route('billete.descargar', $transaccion->id) }}"
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    📥 Descargar Billete PDF
                </a>
            </div>
        </div>
    </section>
@endsection
