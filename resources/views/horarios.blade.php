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
    <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Línea:</label>
                <select name="linea_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">-- Selecciona línea --</option>
                    @foreach($lineas as $linea)
                        <option value="{{ $linea->id_linea }}" {{ request('linea_id') == $linea->id_linea ? 'selected' : '' }}>
                            {{ $linea->codigo }} - {{ $linea->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($lineaSeleccionada && isset($planificadores) && $planificadores->count() > 1)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Período:</label>
                    <select name="planificador_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
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

            <div class="flex items-end">
                <a href="{{ url()->current() }}" class="text-black hover:text-gray-700">
                    Limpiar filtros
                </a>
            </div>
        </div>
    </form>

    @if($lineaSeleccionada)
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-6">
                Horarios de la línea {{ $lineaSeleccionada->codigo }} - {{ $lineaSeleccionada->nombre }}
            </h2>

            @if($horarios->isNotEmpty())
                <!-- Información del período -->
                @php
                    $primerHorario = $horarios->first();
                @endphp
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700">
                        Desde {{ \Carbon\Carbon::parse($primerHorario->fecha_inicio)->format('d/m/Y') }}
                        hasta {{ \Carbon\Carbon::parse($primerHorario->fecha_fin)->format('d/m/Y') }}
                    </h3>
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
                                        <thead class="bg-blue-500">
                                        <tr>
                                            @foreach($nucleosIda as $nucleo)
                                                <th class="px-6 py-3 text-center text-xs font-medium text-black uppercase border-r border-gray-200">
                                                    {{ $nucleo['nombre'] }}
                                                </th>
                                            @endforeach
                                            <th class="px-6 py-3 text-center text-xs font-medium text-black uppercase">Frecuencia</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-black uppercase">Observaciones</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($horariosIda as $horario)
                                            <tr class="hover:bg-blue-50 transition">
                                                @if($horario->horas && is_array($horario->horas))
                                                    @foreach($horario->horas as $hora)
                                                        <td class="px-6 py-4 text-center text-sm border-r border-gray-200">{{ $hora }}</td>
                                                    @endforeach
                                                @endif
                                                <td class="px-6 py-4 text-center">
                                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
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
                                        <thead class="bg-blue-500">
                                        <tr>
                                            @foreach($nucleosVuelta as $nucleo)
                                                <th class="px-6 py-3 text-center text-xs font-medium text-black uppercase border-r border-gray-200">
                                                    {{ $nucleo['nombre'] }}
                                                </th>
                                            @endforeach
                                            <th class="px-6 py-3 text-center text-xs font-medium text-black uppercase">Frecuencia</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-black uppercase">Observaciones</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($horariosVuelta as $horario)
                                            <tr class="hover:bg-blue-50 transition">
                                                @if($horario->horas && is_array($horario->horas))
                                                    @foreach($horario->horas as $hora)
                                                        <td class="px-6 py-4 text-center text-sm border-r border-gray-200">{{ $hora }}</td>
                                                    @endforeach
                                                @endif
                                                <td class="px-6 py-4 text-center">
                                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
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

                    <!-- Leyenda de frecuencias -->
                    <div class="bg-white p-6 rounded-lg shadow mt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Leyenda de Frecuencias</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($frecuencias as $frecuencia)
                                <div class="flex items-center">
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-2">
                                        {{ $frecuencia->acronimo }}
                                    </span>
                                    <span class="text-sm text-gray-700">{{ $frecuencia->nombre }}</span>
                                </div>
                            @endforeach
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
</body>
</html>
