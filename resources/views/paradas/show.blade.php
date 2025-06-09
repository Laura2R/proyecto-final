@extends('layouts.app')

@section('title', $parada->nombre . ' - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
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


                        <!-- Información completa para paradas que existen en la API -->
                        <li><strong>Núcleo:</strong> {{ $parada->nucleo->nombre ?? '-' }}</li>
                        <li><strong>Municipio:</strong> {{ $parada->nucleo->municipio->nombre ?? '-' }}</li>
                        <li><strong>Zona:</strong> {{ $parada->zona->nombre ?? '-' }}</li>
                    @if(!$existeEnAPI)
                        @elseif($parada->observaciones)
                            <li><strong>Observaciones:</strong> {{ $parada->observaciones }}</li>
                        @endif
                </ul>
            </div>

            <!-- Sección de servicios -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4"><i class="fas fa-bus"></i> Próximos Autobuses</h2>
                <p class="text-sm text-blue-800 mb-4">
                   <i class="fas fa-info-circle"></i> Las horas de paso de los buses son aproximadas
                </p>


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
                    <div class="text-red-500 text-6xl mb-4"><i class="fa-regular fa-circle-xmark"></i></div>
                    <p class="text-gray-600">No hay servicios disponibles en el horario consultado.</p>
                    <p class="text-sm text-gray-500 mt-2">
                            Prueba con otra fecha u hora.
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

    <!-- Scripts -->
    <script>
        window.mapaParadaData = {
            nombre: @json($parada->nombre),
            id: @json($parada->id_parada),
            lat: {{ $parada->latitud }},
            lng: {{ $parada->longitud }}
        };

        window.paradaId = @json($parada->id_parada);
    </script>

    <script src="{{ asset('js/mapaParada.js') }}"></script>
    <script src="{{ asset('js/serviciosParada.js') }}"></script>
    <script src="{{ asset('js/scrollButton.js') }}"></script>
    <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapaParada">
    </script>
@endsection
