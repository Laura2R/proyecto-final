<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Líneas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Líneas</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID Línea</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Modo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operadores</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($lineas as $linea)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $linea->id_linea }}</td>
                    <td class="px-6 py-4">{{ $linea->codigo }}</td>
                    <td class="px-6 py-4">{{ $linea->nombre }}</td>
                    <td class="px-6 py-4">{{ $linea->modo }}</td>
                    <td class="px-6 py-4">{{ $linea->operadores }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    {{-- Enlaces de paginación --}}
    <div class="mt-6">
        {{ $lineas->links() }}
    </div>
</div>
</body>
</html>
