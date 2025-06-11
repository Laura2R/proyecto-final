@extends('layouts.app')

@section('title', 'Horarios de Líneas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Horarios de Líneas</h1>
            <p class="text-xl">Consulta los horarios de todas nuestras líneas de autobús</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-full mx-auto px-4">

            <!-- Selector de línea y ordenamiento -->
            <form method="GET" class="mb-8 bg-white p-4 md:p-6 rounded-lg shadow" id="filtroForm">
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

                    @if($lineaSeleccionada)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por:</label>
                            <select name="sort" onchange="this.form.submit()"
                                    class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="horas" {{ request('sort', 'horas') == 'horas' ? 'selected' : '' }}>Hora
                                </option>
                                <option value="frecuencia" {{ request('sort') == 'frecuencia' ? 'selected' : '' }}>
                                    Frecuencia
                                </option>
                            </select>
                        </div>
                    @endif

                    <div class="flex items-end gap-2">
                        @if($lineaSeleccionada)
                            <button type="button" onclick="toggleSortDirection()"
                                    class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                                {{ request('direction', 'asc') == 'asc' ? '↑ Asc' : '↓ Desc' }}
                            </button>
                        @endif
                        <div class="flex justify-end">
                            <a href="/horarios" class="text-gray-600 hover:text-gray-800 underline">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}" id="directionInput">
            </form>

            @if($lineaSeleccionada)
                <div class="bg-white p-4 md:p-6 rounded-lg shadow">
                    <h2 class="text-xl md:text-2xl font-bold mb-4 md:mb-6">
                        Horarios de la línea {{ $lineaSeleccionada->codigo }} - {{ $lineaSeleccionada->nombre }}
                    </h2>

                    @if($horarios->isNotEmpty())
                        <!-- Información de ordenamiento -->
                        <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-2">
                            <h3 class="text-lg font-semibold text-black">
                                <i class="fas fa-calendar-alt"></i> Horarios disponibles
                            </h3>
                            <div class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded">
                                Ordenado por:
                                <span class="font-medium text-blue-600">
                                {{ request('sort', 'horas') == 'horas' ? 'Hora' : 'Frecuencia' }}
                            </span>
                                <span class="ml-1">
{!! request('direction', 'asc') == 'asc'
    ? '<i class="fas fa-arrow-up"></i> Temprano → Tarde'
    : '<i class="fas fa-arrow-down"></i> Tarde → Temprano' !!}
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
                                        <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                                            <p class="text-sm text-blue-800">
                                                <i class="fa-regular fa-lightbulb"></i> <strong>Tip:</strong> Las horas
                                                marcadas como "--" indican que el bus no para en esa parada.
                                            </p>
                                        </div>

                                        <div class="table-scroll-container">
                                            <table class="fixed-table">
                                                <thead>
                                                <tr class="bg-gray-200">
                                                    @foreach($nucleosIda as $nucleo)
                                                        <th class="header-cell hora-cell text-black"
                                                            style="background-color: {{ $nucleo['color'] ?? '#F2F2F2' }}">
                                                            {{ $nucleo['nombre'] }}
                                                        </th>
                                                    @endforeach
                                                    <th class="header-cell frecuencia-cell bg-gray-200 text-black cursor-pointer hover:bg-gray-300"
                                                        onclick="sortBy('frecuencia')">
                                                        Frecuencia
                                                        @if(request('sort') == 'frecuencia')
                                                            <span
                                                                class="ml-1">{{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }}</span>
                                                        @endif
                                                    </th>
                                                    <th class="header-cell observaciones-cell bg-gray-200 text-black">
                                                        Observaciones
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($horariosIda as $horario)
                                                    <tr class="hover:bg-blue-50 transition border-b border-gray-200">
                                                        @if($horario->horas && is_array($horario->horas))
                                                            @foreach($horario->horas as $hora)
                                                                <td class="hora-cell">
                                                                    @if($hora && $hora !== '--' && $hora !== null)
                                                                        {{ $hora }}
                                                                    @else
                                                                        <span class="hora-vacia">--</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endif
                                                        <td class="frecuencia-cell">
                                                        <span
                                                            class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                            {{ $horario->frecuencia_acronimo }}
                                                        </span>
                                                        </td>
                                                        <td class="observaciones-cell text-gray-600">
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
                                        <div class="table-scroll-container">
                                            <table class="fixed-table">
                                                <thead>
                                                <tr class="bg-gray-200">
                                                    @foreach($nucleosVuelta as $nucleo)
                                                        <th class="header-cell hora-cell text-black"
                                                            style="background-color: {{ $nucleo['color'] ?? '#F2F2F2' }}">
                                                            {{ $nucleo['nombre'] }}
                                                        </th>
                                                    @endforeach
                                                    <th class="header-cell frecuencia-cell bg-gray-200 text-black cursor-pointer hover:bg-gray-300"
                                                        onclick="sortBy('frecuencia')">
                                                        Frecuencia
                                                        @if(request('sort') == 'frecuencia')
                                                            <span
                                                                class="ml-1">{{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }}</span>
                                                        @endif
                                                    </th>
                                                    <th class="header-cell observaciones-cell bg-gray-200 text-black">
                                                        Observaciones
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($horariosVuelta as $horario)
                                                    <tr class="hover:bg-blue-50 transition border-b border-gray-200">
                                                        @if($horario->horas && is_array($horario->horas))
                                                            @foreach($horario->horas as $hora)
                                                                <td class="hora-cell">
                                                                    @if($hora && $hora !== '--' && $hora !== null)
                                                                        {{ $hora }}
                                                                    @else
                                                                        <span class="hora-vacia">--</span>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        @endif
                                                        <td class="frecuencia-cell">
                                                        <span
                                                            class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                                            {{ $horario->frecuencia_acronimo }}
                                                        </span>
                                                        </td>
                                                        <td class="observaciones-cell text-gray-600">
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

                            <!-- Leyendas -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                                <!-- Leyenda de frecuencias -->
                                <div class="bg-white p-6 rounded-lg shadow">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Leyenda de Frecuencias</h4>
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach($frecuencias as $frecuencia)
                                            <div class="flex items-center">
                                            <span
                                                class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-3 min-w-[50px] text-center">
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
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                                            style="background-color: #fcff6d">A</span>
                                            <span class="text-sm text-gray-700">ZONA A</span>
                                        </div>
                                        <div class="flex items-center">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-white"
                                            style="background-color: #ff3c5b">B</span>
                                            <span class="text-sm text-gray-700">ZONA B</span>
                                        </div>
                                        <div class="flex items-center">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                                            style="background-color: #d89dff">C</span>
                                            <span class="text-sm text-gray-700">ZONA C</span>
                                        </div>
                                        <div class="flex items-center">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                                            style="background-color: #ffcef9">D</span>
                                            <span class="text-sm text-gray-700">ZONA D</span>
                                        </div>
                                        <div class="flex items-center">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                                            style="background-color: #9dff9d">E</span>
                                            <span class="text-sm text-gray-700">ZONA E</span>
                                        </div>
                                        <div class="flex items-center">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-black"
                                            style="background-color: #55c5ff">F</span>
                                            <span class="text-sm text-gray-700">ZONA F</span>
                                        </div>
                                        <div class="flex items-center">
                                        <span
                                            class="inline-block px-2 py-1 rounded text-xs mr-3 min-w-[30px] text-center font-bold text-white"
                                            style="background-color: #3ca5ff">R</span>
                                            <span class="text-sm text-gray-700">ZONA ESPECIAL EL ROCIO</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500 py-6">
                                No se encontraron horarios
                            </div>
                        @endif
                    @else
                        <div class="text-center text-gray-500 py-6">
                            No hay horarios disponibles para esta línea
                        </div>
                    @endif
                </div>
            @else
                <!-- Estado inicial sin línea seleccionada -->
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <div class="text-gray-400 text-6xl mb-4"><i class="fas fa-clock"></i></div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Selecciona una línea</h3>
                    <p class="text-gray-500">Elige una línea de autobús para consultar sus horarios detallados</p>
                </div>
            @endif
        </div>
    </section>

    <script src="{{ asset('js/horarios.js') }}"></script>
@endsection
