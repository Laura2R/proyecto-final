<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrar Paradas por Línea</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-medium block">{{ $parada->nombre }}</span>
                                            <span class="text-sm text-gray-600">
                                                {{ $parada->nucleo->nombre ?? '-' }} ({{ $parada->nucleo->municipio->nombre ?? '-' }})
                                            </span>
                                            @if($parada->descripcion)
                                                <p class="text-xs text-gray-500 mt-1">{{ $parada->descripcion }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full">
                                                Orden: {{ $parada->pivot_orden }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs">
                                        <span class="text-gray-500">Coords:</span>
                                        {{ $parada->latitud }}, {{ $parada->longitud }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-gray-500 py-6">
                    No se encontraron paradas con los filtros seleccionados
                </div>
            @endif
        </div>
    @endif
</div>
</body>
</html>
