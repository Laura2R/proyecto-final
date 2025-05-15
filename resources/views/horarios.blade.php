<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Horarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Horarios</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Línea</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sentido</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo de Día</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horas</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($horarios as $horario)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">{{ $horario->linea->nombre ?? $horario->id_linea }}</td>
                    <td class="px-6 py-4 capitalize">{{ $horario->sentido }}</td>
                    <td class="px-6 py-4">{{ $horario->tipo_dia }}</td>
                    <td class="px-6 py-4">
                        @foreach(is_array(json_decode($horario->horas, true)) ? json_decode($horario->horas, true) : [] as $hora)
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-1 mb-1">{{ $hora }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $horarios->links() }}
    </div>
</div>
</body>
</html>
