<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntos de Venta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #mapa-puntos-venta {
            height: 500px;
            width: 100%;
            border-radius: 8px;
            margin-top: 2rem;
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Puntos de Venta</h1>

    <!-- Filtros con autosubmit -->
    <form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Municipio:</label>
                <select name="municipio_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
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
                <select name="nucleo_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm" {{ $nucleos->isEmpty() ? 'disabled' : '' }}>
                    <option value="">-- Todos los núcleos --</option>
                    @foreach($nucleos as $nucleo)
                        <option value="{{ $nucleo->id_nucleo }}"
                            {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                            {{ $nucleo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex justify-end">
                <a href="{{ url('/puntos-venta') }}" class="text-gray-600 hover:text-gray-800 underline">
                    Limpiar filtros
                </a>
            </div>
        </div>
    </form>

    <!-- Tabla de puntos de venta -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Municipio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Núcleo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($puntosVenta as $punto)
                <tr class="hover:bg-gray-50 transition" data-lat="{{ $punto->latitud }}" data-lng="{{ $punto->longitud }}" data-nombre="{{ $punto->id_punto }}">
                    <td class="px-6 py-4">{{ $punto->municipio->nombre ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $punto->nucleo->nombre ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $punto->tipo }}</td>
                    <td class="px-6 py-4">{{ $punto->direccion }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4 mb-8">
        {{ $puntosVenta->appends(request()->query())->links() }}
    </div>

    <!-- Mapa con todos los puntos de venta -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Mapa de Puntos de Venta</h2>
        <div id="mapa-puntos-venta"></div>
    </div>
</div>
    <script>
    // Datos para el mapa
    window.puntosVenta = @json($todosPuntosVenta);
</script>
<script src="{{ asset('js/mapaPuntosVenta.js') }}"></script>
<script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapaPuntosVenta">
</script>
</body>
</html>
