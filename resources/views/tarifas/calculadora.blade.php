@extends('layouts.app')

@section('title', 'Calculadora de Tarifas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Calculadora de Tarifas</h1>
            <p class="text-xl">Calcula el precio de tu viaje entre núcleos</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Mensaje de éxito -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold">¡Compra exitosa!</h4>
                            <p class="text-sm mt-1">{{ session('success') }}</p>
                            <a href="{{ route('billetes.mis-billetes') }}" class="text-green-600 hover:text-green-800 font-medium text-sm">
                                Ver mis billetes →
                            </a>
                        </div>
                    </div>
                </div>
            @endif



        @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <strong>¡Error!</strong> {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <strong>¡Errores de validación!</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Información general -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0 text-blue-700">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Información:</strong> Selecciona una línea y luego elige los núcleos de origen y destino para calcular la tarifa.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de selección de ruta -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4"><i class="fa-solid fa-calculator"></i> Selecciona tu ruta</h2>

                <form method="GET" action="{{ route('tarifas.calculadora') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Selección de línea -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Línea de Autobús</label>
                            <select name="linea_id" id="linea_id" onchange="this.form.submit()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Selecciona una línea</option>
                                @foreach($lineas as $linea)
                                    <option value="{{ $linea->id_linea }}"
                                        {{ request('linea_id') == $linea->id_linea ? 'selected' : '' }}>
                                        {{ $linea->codigo }} - {{ $linea->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($nucleos->count() > 0)
                            <!-- Núcleo origen -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo Origen</label>
                                <select name="nucleo_origen" id="nucleo_origen" onchange="this.form.submit()"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecciona origen</option>
                                    @foreach($nucleos as $nucleo)
                                        <option value="{{ $nucleo->id_nucleo }}"
                                            {{ request('nucleo_origen') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                            {{ $nucleo->nombre }} ({{ $nucleo->zona_nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Núcleo destino -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo Destino</label>
                                <select name="nucleo_destino" id="nucleo_destino" onchange="this.form.submit()"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Selecciona destino</option>
                                    @foreach($nucleos as $nucleo)
                                        <option value="{{ $nucleo->id_nucleo }}"
                                            {{ request('nucleo_destino') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                            {{ $nucleo->nombre }} ({{ $nucleo->zona_nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('tarifas.calculadora') }}"
                           class="text-gray-600 hover:text-gray-800 underline">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mostrar error si existe -->
            @if(isset($resultado['error']) && $resultado['error'])
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded mb-6">
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
            @endif

            <!-- Mostrar resultados y formulario de compra solo si hay datos válidos -->
            @if(isset($resultado) && !$resultado['error'] && isset($resultado['tarifa']))
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Resultado del Cálculo</h2>
                    </div>

                    <div class="p-6">
                        <!-- Información del viaje -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-800 mb-2"><i class="fas fa-map-marker-alt"></i> Origen</h3>
                                <p class="text-blue-700">{{ $resultado['nucleoOrigen']->nombre }}</p>
                                <p class="text-sm text-blue-600">Zona: {{ $resultado['nucleoOrigen']->zona_nombre }}</p>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-green-800 mb-2">🎯 Destino</h3>
                                <p class="text-green-700">{{ $resultado['nucleoDestino']->nombre }}</p>
                                <p class="text-sm text-green-600">Zona: {{ $resultado['nucleoDestino']->zona_nombre }}</p>
                            </div>
                        </div>

                        <!-- Información de saltos -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-6 text-center">
                            <h3 class="font-semibold text-gray-800 mb-2">🔄 Saltos</h3>
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $resultado['saltos'] }}</div>
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

                        <!-- Mostrar precio -->
                        <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg text-center mb-6">
                            <h4 class="text-2xl font-bold text-blue-800 mb-2">Precio con Tarjeta Bus: €{{ number_format($resultado['tarifa']->tarjeta, 2) }}</h4>
                            <p class="text-blue-700">
                                Precio normal: €{{ number_format($resultado['tarifa']->bs, 2) }}
                                <span class="text-green-600 font-medium">(Ahorras €{{ number_format($resultado['ahorro'], 2) }})</span>
                            </p>
                        </div>

                        <!-- Formulario de compra solo para usuarios autenticados -->
                        @auth
                            <form id="pagoForm" method="POST" action="{{ route('procesar.pago') }}">
                                @csrf
                                <input type="hidden" name="nucleo_origen_id" value="{{ $resultado['nucleoOrigen']->id_nucleo }}">
                                <input type="hidden" name="nucleo_destino_id" value="{{ $resultado['nucleoDestino']->id_nucleo }}">
                                <input type="hidden" name="saltos" value="{{ $resultado['saltos'] }}">

                                <!-- Selección de tarjetas -->
                                <div class="mb-6">
                                    <h5 class="text-lg font-semibold text-gray-800 mb-4">Selecciona tu tarjeta:</h5>
                                    @forelse(auth()->user()->cards as $card)
                                        <div class="bg-white border rounded-lg p-4 mb-3 hover:shadow-md transition
                                            @if($card->saldo < $resultado['tarifa']->tarjeta * 100) border-red-300 bg-red-50 @else border-gray-200 @endif">
                                            <label class="flex items-center cursor-pointer">
                                                <input class="form-radio h-4 w-4 text-blue-600" type="radio"
                                                       name="tarjeta_id" value="{{ $card->id }}"
                                                    @disabled($card->saldo < $resultado['tarifa']->tarjeta * 100)>
                                                <div class="ml-3 flex-1">
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <span class="font-medium text-gray-900">Tarjeta #{{ $card->numero_tarjeta }}</span>
                                                            <span class="text-gray-600"> - Saldo: €{{ number_format($card->saldo / 100, 2) }}</span>
                                                        </div>
                                                        @if($card->saldo < $resultado['tarifa']->tarjeta * 100)
                                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                                Saldo insuficiente
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @empty
                                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                                            <p class="text-yellow-800 mb-3">No tienes tarjetas activas.</p>
                                            <a href="{{ route('cards.create') }}"
                                               class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-900 transition">
                                                Crear tarjeta
                                            </a>
                                        </div>
                                    @endforelse
                                </div>

                                @if(auth()->user()->cards->count() > 0)
                                    <div class="text-center">
                                        <button type="submit"
                                                class="bg-gradient-to-r from-green-600 to-green-800 text-white px-8 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-900 transition text-lg">
                                            Comprar Billete - €{{ number_format($resultado['tarifa']->tarjeta, 2) }}
                                        </button>
                                    </div>
                                @endif
                            </form>
                        @else
                            <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg text-center">
                                <h5 class="text-lg font-semibold text-blue-800 mb-3">¿Quieres comprar este billete?</h5>
                                <p class="text-blue-700 mb-4">Debes iniciar sesión para poder comprar billetes con tu tarjeta de transporte.</p>
                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                    <a href="{{ route('login') }}"
                                       class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-900 transition">
                                        Iniciar Sesión
                                    </a>
                                    <a href="{{ route('register') }}"
                                       class="border-2 border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                        Registrarse
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif

            <!-- Información adicional -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-blue-800 font-semibold">💡 Información</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Con la tarjeta de transporte puedes realizar transbordos por solo 0,50€ en un máximo de 90 minutos.
                        </p>
                    </div>
                </div>
            </div>

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
                        <div class="text-blue-600 text-2xl mr-3"><i class="fas fa-bus"></i></div>
                        <div>
                            <div class="font-medium">Líneas</div>
                            <div class="text-sm text-gray-500">Ver todas las líneas</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript simplificado solo para validación -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('pagoForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const tarjetaSeleccionada = document.querySelector('input[name="tarjeta_id"]:checked');
                    if (!tarjetaSeleccionada) {
                        e.preventDefault();
                        alert('Por favor selecciona una tarjeta para continuar');
                        return false;
                    }
                });
            }
        });
    </script>
@endsection
