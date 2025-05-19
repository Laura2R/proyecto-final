<!DOCTYPE html>
<html>
<head>
    <title>Paradas de Línea</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto">
    <!-- Selector de Línea -->
    <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Línea:</label>
                <select name="linea_id" onchange="this.form.submit()"
                        class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Seleccionar línea --</option>
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

            @foreach($paradasAgrupadas as $sentido => $paradas)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4 text-blue-800">
                        Sentido: {{ $sentido == "ida" ? 'Ida' : 'Vuelta' }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($paradas as $parada)
                            <div class="border-l-4 border-blue-500 pl-4 bg-blue-50 rounded p-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="font-medium block">{{ $parada->nombre }}</span>
                                        <span class="text-sm text-gray-600">
                                            {{ $parada->nucleo->nombre }} ({{ $parada->nucleo->municipio->nombre }})
                                        </span>
                                        <div class="mt-1 text-xs text-gray-500">
                                            Zona {{ $parada->id_zona }} | Orden: {{ $parada->pivot_orden }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full">
                                            {{ $parada->pivot_sentido == "ida" ? 'Ida' : 'Vuelta' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs">
                                    <span class="text-gray-500">Coords:</span>
                                    {{ number_format($parada->latitud, 6) }}, {{ number_format($parada->longitud, 6) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
