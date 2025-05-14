<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Línea-Parada</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Relación Líneas - Paradas</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Línea</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Línea</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Parada</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Parada</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sentido</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($lineaParada as $relacion)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $relacion->id_linea }}</td>
                    <td class="px-6 py-4">
                        {{ $relacion->linea->nombre ?? '-' }}
                    </td>
                    <td class="px-6 py-4">{{ $relacion->id_parada }}</td>
                    <td class="px-6 py-4">
                        {{ $relacion->parada->nombre ?? '-' }}
                    </td>
                    <td class="px-6 py-4">{{ $relacion->orden ?? '-' }}</td>
                    <td class="px-6 py-4 capitalize">{{ $relacion->sentido }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $lineaParada->links() }}
    </div>
</div>
</body>
</html>
