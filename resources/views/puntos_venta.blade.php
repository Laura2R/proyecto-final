<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Puntos de Venta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Puntos de Venta</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Municipio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Núcleo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Latitud</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Longitud</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($puntosVenta as $punto)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $punto->id_punto }}</td>
                    <td class="px-6 py-4">{{ $punto->tipo }}</td>
                    <td class="px-6 py-4">{{ $punto->municipio->nombre ?? '' }}</td>
                    <td class="px-6 py-4">{{ $punto->nucleo }}</td>
                    <td class="px-6 py-4">{{ $punto->direccion }}</td>
                    <td class="px-6 py-4">{{ $punto->latitud }}</td>
                    <td class="px-6 py-4">{{ $punto->longitud }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $puntosVenta->links() }}
    </div>
</div>
</body>
</html>
