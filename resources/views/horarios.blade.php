<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios de Línea</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Horarios de Líneas</h1>

    <!-- Selector de línea y planificador -->
    <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow" id="filtroForm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Línea:</label>
                <select name="linea_id" onchange="this.form.submit()"
                        class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Selecciona línea --</option>
                    @foreach($lineas as $linea)
                        <option
                            value="{{ $linea->id_linea }}" {{ request('linea_id') == $linea->id_linea ? 'selected' : '' }}>
                            {{ $linea->codigo }} - {{ $linea->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($lineaSeleccionada && isset($planificadores) && $planificadores->count() > 1)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Período:</label>
                    <select name="planificador_id" onchange="this.form.submit()"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($planificadores as $planificador)
                            <option value="{{ $planificador->id_planificador }}"
                                {{ request('planificador_id', $planificadores->first()->id_planificador) == $planificador->id_planificador ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($planificador->fecha_inicio)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($planificador->fecha_fin)->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($lineaSeleccionada)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por:</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="horas" {{ request('sort', 'horas') == 'horas' ? 'selected' : '' }}>Hora</option>
                        <option value="frecuencia" {{ request('sort') == 'frecuencia' ? 'selected' : '' }}>Frecuencia
                        </option>
                    </select>
                </div>
            @endif

            <div class="flex items-end gap-2">
                @if($lineaSeleccionada)
                    <button type="button" onclick="toggleSortDirection()"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                        {{ request('direction', 'asc') == 'asc' ? '↑ Ascendente' : '↓ Descendente' }}
                    </button>
                @endif
                <a href="{{ url()->current() }}" class="text-gray-600 hover:text-gray-800 underline">
                    Limpiar filtros
                </a>
            </div>
        </div>

        <!-- Campos ocultos para mantener el estado -->
        <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}" id="directionInput">
    </form>

    @if($lineaSeleccionada)
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-6">
                Horarios de la línea {{ $lineaSeleccionada->codigo }} - {{ $lineaSeleccionada->nombre }}
            </h2>

            @if($horarios->isNotEmpty())
                <!-- Información del período y ordenamiento -->
                @php
                    $primerHorario = $horarios->first();
                @endphp
                <div class="mb-6 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Desde {{ \Carbon\Carbon::parse($primerHorario->fecha_inicio)->format('d/m/Y') }}
                        hasta {{ \Carbon\Carbon::parse($primerHorario->fecha_fin)->format('d/m/Y') }}
                    </h3>
                    <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded">
                        Ordenado por:
                        <span class="font-medium text-blue-600">
            {{ request('sort', 'horas') == 'horas' ? 'Hora' : 'Frecuencia' }}
        </span>
                        <span class="ml-1">
            {{ request('direction', 'asc') == 'asc' ? '(⬆ Temprano → Tarde)' : '(⬇ Tarde → Temprano)' }}
        </span>
                    </div>
                </div>

                @if($horariosIda->isNotEmpty() || $horariosVuelta->isNotEmpty())
                    <!-- Horarios de Ida -->
                    @if($horariosIda->isNotEmpty())
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold mb-4 text-blue-800">Ida</h3>

                            @php
                                $primerHorarioIda = $horariosIda->first();
                                $nucleosIda = $primerHorarioIda && $primerHorarioIda->nucleos ? $primerHorarioIda->nucleos : [];
                            @endphp

                            @if(count($nucleosIda) > 0)
                                <div class="bg-white rounded-lg shadow overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            @foreach($nucleosIda as $nucleo)
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase border-r border-gray-200"
                                                    style="background-color: {{ $nucleo['color'] ?? '#F2F2F2' }}">
                                                    {{ $nucleo['nombre'] }}
                                                </th>
                                            @endforeach
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                                onclick="sortBy('frecuencia')">
                                                Frecuencia
                                                @if(request('sort') == 'frecuencia')
                                                    <span
                                                        class="ml-1">{{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Observaciones
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($horariosIda as $horario)
                                            <tr class="hover:bg-gray-50 transition">
                                                @if($horario->horas && is_array($horario->horas))
                                                    @foreach($horario->horas as $hora)
                                                        <td class="px-6 py-4 text-center text-sm border-r border-gray-200 font-mono">{{ $hora }}</td>
                                                    @endforeach
                                                @endif
                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                        {{ $horario->frecuencia_acronimo }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    {{ $horario->observaciones ?: '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-6">
                                    No hay información de núcleos disponible para ida
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Horarios de Vuelta -->
                    @if($horariosVuelta->isNotEmpty())
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold mb-4 text-blue-800">Vuelta</h3>

                            @php
                                $primerHorarioVuelta = $horariosVuelta->first();
                                $nucleosVuelta = $primerHorarioVuelta && $primerHorarioVuelta->nucleos ? $primerHorarioVuelta->nucleos : [];
                            @endphp

                            @if(count($nucleosVuelta) > 0)
                                <div class="bg-white rounded-lg shadow overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            @foreach($nucleosVuelta as $nucleo)
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase border-r border-gray-200"
                                                    style="background-color: {{ $nucleo['color'] ?? '#F2F2F2' }}">
                                                    {{ $nucleo['nombre'] }}
                                                </th>
                                            @endforeach
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase cursor-pointer hover:bg-gray-100"
                                                onclick="sortBy('frecuencia')">
                                                Frecuencia
                                                @if(request('sort') == 'frecuencia')
                                                    <span
                                                        class="ml-1">{{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                Observaciones
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($horariosVuelta as $horario)
                                            <tr class="hover:bg-gray-50 transition">
                                                @if($horario->horas && is_array($horario->horas))
                                                    @foreach($horario->horas as $hora)
                                                        <td class="px-6 py-4 text-center text-sm border-r border-gray-200 font-mono">{{ $hora }}</td>
                                                    @endforeach
                                                @endif
                                                <td class="px-6 py-4 text-center">
                                                    <span
                                                        class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                        {{ $horario->frecuencia_acronimo }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    {{ $horario->observaciones ?: '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-6">
                                    No hay información de núcleos disponible para vuelta
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Leyenda de frecuencias y zonas tarifarias -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <!-- Leyenda de frecuencias -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Leyenda de Frecuencias</h4>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($frecuencias as $frecuencia)
                                    <div class="flex items-center">
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-3 min-w-[50px] text-center">
                        {{ $frecuencia->acronimo }}
                    </span>
                                        <span class="text-sm text-gray-700">{{ $frecuencia->nombre }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Zonas tarifarias -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">Zonas Tarifarias</h4>
                            <div class="grid grid-cols-1 gap-3">
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #fcff6d">A</span>
                                    <span class="text-sm text-gray-700">ZONA A</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-white"
                      style="background-color: #ff3c5b">B</span>
                                    <span class="text-sm text-gray-700">ZONA B</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #d89dff">C</span>
                                    <span class="text-sm text-gray-700">ZONA C</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #ffcef9">D</span>
                                    <span class="text-sm text-gray-700">ZONA D</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #9dff9d">E</span>
                                    <span class="text-sm text-gray-700">ZONA E</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #55c5ff">F</span>
                                    <span class="text-sm text-gray-700">ZONA F</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #ff8a3c">G</span>
                                    <span class="text-sm text-gray-700">ZONA ESPECIAL EL ROCIO 2</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                      style="background-color: #99ff55">L</span>
                                    <span class="text-sm text-gray-700">ZONA ESPECIAL EL ROCIO 3</span>
                                </div>
                                <div class="flex items-center">
                <span class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-white"
                      style="background-color: #3ca5ff">R</span>
                                    <span class="text-sm text-gray-700">ZONA ESPECIAL EL ROCIO</span>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="text-center text-gray-500 py-6">
                        No se encontraron horarios con los filtros seleccionados
                    </div>
                @endif
            @else
                <div class="text-center text-gray-500 py-6">
                    No hay horarios disponibles para esta línea
                </div>
            @endif
        </div>
    @else
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <p class="text-gray-500">Selecciona una línea para ver sus horarios</p>
        </div>
    @endif
</div>

<script>
    function toggleSortDirection() {
        const directionInput = document.getElementById('directionInput');
        const currentDirection = directionInput.value;
        directionInput.value = currentDirection === 'asc' ? 'desc' : 'asc';
        document.getElementById('filtroForm').submit();
    }

    function sortBy(column) {
        const form = document.getElementById('filtroForm');
        const sortInput = form.querySelector('select[name="sort"]');
        const directionInput = document.getElementById('directionInput');

        // Si ya estamos ordenando por esta columna, cambiar dirección
        if (sortInput.value === column) {
            directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
        } else {
            // Si es una nueva columna, empezar con ascendente
            sortInput.value = column;
            directionInput.value = 'asc';
        }

        form.submit();
    }
</script>
</body>
</html>
