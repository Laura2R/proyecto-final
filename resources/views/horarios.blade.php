@extends('layouts.app')

@section('title', 'Horarios de Líneas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
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
                @csrf
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

                    @if($lineaSeleccionada)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por:</label>
                            <select name="sort" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="horas" {{ request('sort', 'horas') == 'horas' ? 'selected' : '' }}>Hora</option>
                                <option value="frecuencia" {{ request('sort') == 'frecuencia' ? 'selected' : '' }}>Frecuencia</option>
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
                                📅 Horarios disponibles
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
                                        <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                                            <p class="text-sm text-blue-800">
                                                💡 <strong>Tip:</strong> Las horas marcadas como "--" indican que el bus no para en esa parada.
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
                                                            <span class="ml-1">{{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }}</span>
                                                        @endif
                                                    </th>
                                                    <th class="header-cell observaciones-cell bg-gray-200 text-black">Observaciones</th>
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
                                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
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
                                                            <span class="ml-1">{{ request('direction', 'asc') == 'asc' ? '↑' : '↓' }}</span>
                                                        @endif
                                                    </th>
                                                    <th class="header-cell observaciones-cell bg-gray-200 text-black">Observaciones</th>
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
                                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
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
                    <div class="text-gray-400 text-6xl mb-4">🕒</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Selecciona una línea</h3>
                    <p class="text-gray-500">Elige una línea de autobús para consultar sus horarios detallados</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Estilos específicos para esta vista -->
    <style>
        .table-scroll-container {
            position: relative;
            overflow-x: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            max-width: 100%;
        }

        .table-scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .table-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .fixed-table {
            table-layout: fixed;
            width: 100%;
            min-width: 800px;
        }

        .hora-cell {
            width: 80px;
            min-width: 80px;
            max-width: 80px;
            padding: 8px 4px;
            font-family: 'Courier New', monospace;
            font-weight: 500;
            text-align: center;
            border-right: 1px solid #d1d5db;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .frecuencia-cell {
            width: 100px;
            min-width: 100px;
            max-width: 100px;
            padding: 8px 4px;
            text-align: center;
            border-right: 1px solid #d1d5db;
        }

        .observaciones-cell {
            width: 200px;
            min-width: 200px;
            max-width: 200px;
            padding: 8px 12px;
            font-size: 13px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .header-cell {
            padding: 12px 8px;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            border-right: 1px solid #000;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .hora-vacia {
            color: #9ca3af;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .hora-cell {
                width: 70px;
                min-width: 70px;
                max-width: 70px;
                padding: 6px 2px;
                font-size: 13px;
            }

            .frecuencia-cell {
                width: 80px;
                min-width: 80px;
                max-width: 80px;
            }

            .observaciones-cell {
                width: 150px;
                min-width: 150px;
                max-width: 150px;
                font-size: 12px;
            }

            .header-cell {
                padding: 8px 4px;
                font-size: 11px;
            }
        }
    </style>

    <!-- Scripts -->
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

            if (sortInput.value === column) {
                directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
            } else {
                sortInput.value = column;
                directionInput.value = 'asc';
            }

            form.submit();
        }
    </script>
@endsection
