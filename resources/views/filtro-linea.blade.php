<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrar Paradas por Línea</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .map-container {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto">
    <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Línea:</label>
                <select name="linea_id" onchange="this.form.submit()"
                        class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Selecciona línea --</option>
                    @foreach($lineas as $linea)
                        <option value="{{ $linea->id_linea }}"
                            {{ $lineaSeleccionada && $lineaSeleccionada->id_linea == $linea->id_linea ? 'selected' : '' }}>
                            {{ $linea->codigo }} - {{ $linea->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($lineaSeleccionada)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Municipio:</label>
                    <select name="municipio_id" onchange="this.form.submit()"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Todos los municipios --</option>
                        @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id_municipio }}"
                                {{ request('municipio_id') == $municipio->id_municipio ? 'selected' : '' }}>
                                {{ $municipio->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo:</label>
                    <select name="nucleo_id" onchange="this.form.submit()"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">-- Todos los núcleos --</option>
                        @foreach($nucleos as $nucleo)
                            <option value="{{ $nucleo->id_nucleo }}"
                                {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                {{ $nucleo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </form>

    @if($lineaSeleccionada)
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-6">
                Paradas de la línea {{ $lineaSeleccionada->codigo }} - {{ $lineaSeleccionada->nombre }}
            </h2>

            @if($paradasAgrupadas->isNotEmpty())
                @foreach($paradasAgrupadas as $sentido => $paradas)
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-4 text-blue-800">
                            {{ $sentido == 'ida' || $sentido == 1 ? 'Ida' : 'Vuelta' }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($paradas as $parada)
                                <div class="border-l-4 border-blue-500 pl-4 bg-blue-50 rounded p-3">
                                    <div class="flex justify-between items-start gap-2">
                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('paradas.show', $parada->id_parada) }}"
                                               class="text-blue-600 hover:underline font-medium block truncate">
                                                {{ $parada->nombre }}
                                            </a>
                                            <div class="mt-1 text-sm text-gray-600">
                                                <span class="truncate block">
                                                    {{ $parada->nucleo->nombre ?? '-' }}
                                                    ({{ $parada->nucleo->municipio->nombre ?? '-' }})
                                                </span>
                                            </div>
                                            @if($parada->descripcion)
                                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                                    {{ $parada->descripcion }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0 ml-2">
                                            <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full whitespace-nowrap">
                                                Orden: {{ $parada->pivot_orden }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($parada->zona)
                                        <div class="mt-2 text-xs">
                                            <span class="text-gray-500">Zona:</span>
                                            <span class="truncate">{{ $parada->zona->nombre }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Mapa de Ida o Vuelta --}}
                        @if($sentido == 1 && isset($polilineaIda) && count($polilineaIda))
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold mb-2">Recorrido de Ida</h4>
                                <div id="mapa-ida" class="map-container"></div>
                            </div>
                        @endif
                        @if($sentido == 2 && isset($polilineaVuelta) && count($polilineaVuelta))
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold mb-2">Recorrido de Vuelta</h4>
                                <div id="mapa-vuelta" class="map-container"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center text-gray-500 py-6">
                    No se encontraron paradas con los filtros seleccionados
                </div>
            @endif

            <!-- Mapa para Ida -->
            @if(isset($polilineaIda) && count($polilineaIda))
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 text-blue-800">Recorrido de Ida</h3>
                    <div id="mapa-ida" class="map-container"></div>
                </div>
            @endif

            <!-- Mapa para Vuelta -->
            @if(isset($polilineaVuelta) && count($polilineaVuelta))
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 text-blue-800">Recorrido de Vuelta</h3>
                    <div id="mapa-vuelta" class="map-container"></div>
                </div>
            @endif

        </div>
    @endif
</div>
<script>
    window.polilineaIda = @json($polilineaIda ?? []);
    window.polilineaVuelta = @json($polilineaVuelta ?? []);
</script>
<script src="{{ asset('js/google-polylines.js') }}"></script>
<script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&loading=async&callback=initMaps">
</script>
</body>
</html>
