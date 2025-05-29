@extends('layouts.app')

@section('title', $parada->nombre . ' - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">{{ $parada->nombre }}</h1>
            <p class="text-xl">Información de la parada y próximos autobuses</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Mapa de la parada -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h2 class="text-xl font-semibold mb-4">Ubicación</h2>
                <div id="mapa-parada"></div>
            </div>

            <!-- Información de la parada -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h2 class="text-xl font-semibold mb-4">Información de la Parada</h2>
                <ul class="space-y-2">
                    <li><strong>ID Parada:</strong> {{ $parada->id_parada }}</li>

                    @if($existeEnAPI)
                        <!-- Información completa para paradas que existen en la API -->
                        <li><strong>Núcleo:</strong> {{ $parada->nucleo->nombre ?? '-' }}</li>
                        <li><strong>Municipio:</strong> {{ $parada->nucleo->municipio->nombre ?? '-' }}</li>
                        <li><strong>Zona:</strong> {{ $parada->zona->nombre ?? '-' }}</li>
                        @if($parada->observaciones)
                            <li><strong>Observaciones:</strong> {{ $parada->observaciones }}</li>
                        @endif
                    @else
                        <!-- Aviso para paradas "fantasma" -->
                        <div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Parada específica de línea:</strong> Esta parada solo está disponible en ciertas líneas y puede tener información limitada.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </ul>
            </div>

            <!-- Sección de servicios -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">🚌 Próximos Autobuses</h2>
                <p class="text-sm text-blue-800 mb-4">
                    ℹ️ Las horas de paso de los buses son aproximadas
                </p>

                @if(!$existeEnAPI)
                    <!-- Aviso adicional para paradas fantasma -->
                    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
                        <p class="text-sm text-blue-800">
                            ℹ️ <strong>Información limitada:</strong> Los servicios mostrados pueden ser limitados ya que esta parada tiene disponibilidad restringida.
                        </p>
                    </div>
                @endif

                <!-- Selector de fecha y hora -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Consultar horarios desde:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha:</label>
                            <input type="date" id="fecha-consulta" class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hora:</label>
                            <input type="time" id="hora-consulta" class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex items-end">
                            <button onclick="consultarServicios()"
                                    class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                Consultar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Información del período consultado -->
                <div id="periodo-info" class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded hidden">
                    <p class="text-sm text-blue-800">
                        <strong>Período consultado:</strong>
                        <span id="hora-inicio"></span> - <span id="hora-fin"></span>
                    </p>
                </div>

                <!-- Loading -->
                <div id="loading-servicios" class="text-center py-8 hidden">
                    <div class="loading-spinner"></div>
                    <p class="mt-2 text-gray-600">Consultando servicios...</p>
                </div>

                <!-- Error -->
                <div id="error-servicios" class="hidden bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <p class="text-red-700">
                        <strong>Error:</strong> No se pudieron cargar los servicios.
                        <button onclick="consultarServicios()" class="underline hover:no-underline">
                            Intentar de nuevo
                        </button>
                    </p>
                </div>

                <!-- Sin servicios -->
                <div id="sin-servicios" class="hidden text-center py-8">
                    <div class="text-gray-400 text-6xl mb-4">🚫</div>
                    <p class="text-gray-600">No hay servicios disponibles en el horario consultado.</p>
                    <p class="text-sm text-gray-500 mt-2">
                        @if(!$existeEnAPI)
                            Esta parada puede tener horarios limitados. Consulta los horarios de las líneas específicas.
                        @else
                            Prueba con otra fecha u hora.
                        @endif
                    </p>
                </div>

                <!-- Lista de servicios -->
                <div id="lista-servicios" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Los servicios se cargarán aquí dinámicamente -->
                </div>
            </div>

            <!-- Botón volver -->
            <div class="mt-6 text-center">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </section>

    <!-- Botón de scroll -->
    <button id="scroll-button" onclick="toggleScroll()">
        <span id="scroll-arrow">↑</span>
    </button>

    <!-- Estilos específicos para esta vista -->
    <style>
        #mapa-parada {
            height: 350px;
            width: 100%;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .servicio-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .servicio-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Estilos para el botón de scroll */
        #scroll-button {
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 50px;
            height: 50px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #scroll-button.show {
            opacity: 1;
            visibility: visible;
        }

        #scroll-button:hover {
            background-color: #2563eb;
            transform: scale(1.1);
        }

        #scroll-arrow {
            font-size: 20px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
    </style>

    <!-- Scripts -->
    <script>
        window.mapaParadaData = {
            nombre: @json($parada->nombre),
            id: @json($parada->id_parada),
            lat: {{ $parada->latitud }},
            lng: {{ $parada->longitud }}
        };

        window.paradaId = @json($parada->id_parada);
        window.existeEnAPI = @json($existeEnAPI);
    </script>

    <script src="{{ asset('js/mapaParada.js') }}"></script>
    <script src="{{ asset('js/serviciosParada.js') }}"></script>
    <script src="{{ asset('js/scrollButton.js') }}"></script>
    <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapaParada">
    </script>
@endsection
