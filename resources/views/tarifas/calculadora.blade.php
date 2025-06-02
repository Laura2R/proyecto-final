@extends('layouts.app')

@section('title', 'Calculadora de Tarifas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Calculadora de Tarifas</h1>
            <p class="text-xl">Calcula el precio de tu viaje</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Información general -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Información:</strong> Selecciona una línea y luego elige los núcleos de origen y destino para calcular la tarifa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de cálculo -->
            <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
                @csrf
                <h2 class="text-lg font-semibold text-gray-800 mb-4">🧮 Calculadora de Tarifas</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Selección de línea -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Línea de Autobús</label>
                        <select name="linea_id" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Selecciona una línea --</option>
                            @foreach($lineas as $linea)
                                <option value="{{ $linea->id_linea }}"
                                    {{ request('linea_id') == $linea->id_linea ? 'selected' : '' }}>
                                    {{ $linea->codigo }} - {{ $linea->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Núcleo origen -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo Origen</label>
                        <select name="nucleo_origen" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                            {{ $nucleos->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Selecciona origen --</option>
                            @foreach($nucleos as $nucleo)
                                <option value="{{ $nucleo->id_nucleo }}"
                                    {{ request('nucleo_origen') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                    {{ $nucleo->nombre }}
                                    @if($nucleo->zona_nombre)
                                        ({{ $nucleo->zona_nombre }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Núcleo destino -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo Destino</label>
                        <select name="nucleo_destino" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                            {{ $nucleos->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Selecciona destino --</option>
                            @foreach($nucleos as $nucleo)
                                <option value="{{ $nucleo->id_nucleo }}"
                                    {{ request('nucleo_destino') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                    {{ $nucleo->nombre }}
                                    @if($nucleo->zona_nombre)
                                        ({{ $nucleo->zona_nombre }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('tarifas.calculadora') }}" class="text-gray-600 hover:text-gray-800 underline">
                        Limpiar
                    </a>
                </div>
            </form>

            <!-- Resultado del cálculo -->
            @if($resultado)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @if($resultado['error'])
                        <!-- Error -->
                        <div class="p-6 bg-red-50 border-l-4 border-red-400">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ $resultado['error'] }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Resultado exitoso -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Resultado del Cálculo</h2>
                        </div>

                        <div class="p-6">
                            <!-- Información del viaje -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-blue-800 mb-2">📍 Origen</h3>
                                    <p class="text-blue-700">{{ $resultado['nucleoOrigen']->nombre }}</p>
                                    <p class="text-sm text-blue-600">
                                        {{ $resultado['nucleoOrigen']->zona_nombre }}
                                    </p>
                                </div>

                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-green-800 mb-2">🎯 Destino</h3>
                                    <p class="text-green-700">{{ $resultado['nucleoDestino']->nombre }}</p>
                                    <p class="text-sm text-green-600">
                                        {{ $resultado['nucleoDestino']->zona_nombre }}
                                    </p>
                                </div>
                            </div>

                            <!-- Información de saltos -->
                            <div class="bg-gray-50 p-4 rounded-lg mb-6 text-center">
                                <h3 class="font-semibold text-gray-800 mb-2">🔄 Saltos</h3>
                                <div class="text-3xl font-bold text-blue-600">{{ $resultado['saltos'] }}</div>
                                <p class="text-sm text-gray-600">
                                    @if($resultado['saltos'] == 0)
                                        Misma zona
                                    @elseif($resultado['saltos'] == 1)
                                        1 salto (cambio de zona)
                                    @else
                                        {{ $resultado['saltos'] }} saltos (cambios de zonas)
                                    @endif
                                </p>
                            </div>

                            <!-- Precios -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Precio en efectivo -->
                                <div class="bg-gray-50 p-6 rounded-lg text-center">
                                    <h3 class="font-semibold text-gray-800 mb-2">💵 Precio en Efectivo</h3>
                                    <div class="text-3xl font-bold text-gray-900 mb-2">
                                        {{ number_format($resultado['tarifa']->bs, 2) }}€
                                    </div>
                                    <p class="text-sm text-gray-600">Pago en efectivo</p>
                                </div>

                                <!-- Precio con tarjeta -->
                                <div class="bg-green-50 p-6 rounded-lg text-center border-2 border-green-200">
                                    <h3 class="font-semibold text-green-800 mb-2">💳 Precio con Tarjeta</h3>
                                    <div class="text-3xl font-bold text-green-700 mb-2">
                                        {{ number_format($resultado['tarifa']->tarjeta, 2) }}€
                                    </div>
                                    <div class="text-sm text-green-600">
                                        <div class="font-medium">Ahorro: {{ number_format($resultado['ahorro'], 2) }}€</div>
                                        <div>({{ number_format($resultado['porcentajeAhorro']) }}% menos)</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-blue-800 font-semibold">💡 Recuerda</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            Con la tarjeta de transporte puedes realizar transbordos por solo 0,50€
                                            en un máximo de 90 minutos.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/tarifas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">💰</div>
                        <div>
                            <div class="font-medium">Tabla de Tarifas</div>
                            <div class="text-sm text-gray-500">Ver todas las tarifas</div>
                        </div>
                    </a>
                    <a href="/zonas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🗺️</div>
                        <div>
                            <div class="font-medium">Zonas Tarifarias</div>
                            <div class="text-sm text-gray-500">Ver mapa de zonas</div>
                        </div>
                    </a>
                    <a href="/lineas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🚌</div>
                        <div>
                            <div class="font-medium">Líneas</div>
                            <div class="text-sm text-gray-500">Ver todas las líneas</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
