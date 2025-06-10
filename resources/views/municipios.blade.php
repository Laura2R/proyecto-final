@extends('layouts.app')

@section('title', 'Municipios y Núcleos - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Municipios y Núcleos</h1>
            <p class="text-xl">Explora la organización territorial</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Información general -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0 text-blue-400">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Información:</strong> Selecciona un municipio para ver sus núcleos de población.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de búsqueda -->
            <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4"><i class="fa-solid fa-magnifying-glass"></i> Filtros de Búsqueda</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <!-- Filtro por municipio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
                        <select name="municipio_id" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Todos los municipios --</option>
                            @foreach($todosMunicipios as $municipio)
                                <option value="{{ $municipio->id_municipio }}"
                                    {{ request('municipio_id') == $municipio->id_municipio ? 'selected' : '' }}>
                                    {{ $municipio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por núcleo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo</label>
                        <select name="nucleo_id" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm"
                            {{ $todosNucleos->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Todos los núcleos --</option>
                            @foreach($todosNucleos as $nucleo)
                                <option value="{{ $nucleo->id_nucleo }}"
                                    {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                    {{ $nucleo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex gap-2">
                        <a href="/municipios"
                           class="text-gray-600 hover:text-gray-800 underline">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <!-- Resultados -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Resultados de la Búsqueda</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($nucleos->total() > 0)
                            Mostrando {{ $nucleos->count() }} de {{ $nucleos->total() }} núcleos
                            @if(request()->hasAny(['municipio_id', 'nucleo_id']))
                                con los filtros aplicados
                            @endif
                        @else
                            No se encontraron núcleos
                        @endif
                    </p>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Núcleo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Municipio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Zona Tarifaria</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($nucleos as $nucleo)
                            <tr class="hover:bg-gray-50 transition">
                                <!-- Nombre del Núcleo -->
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $nucleo->nombre }}</div>
                                </td>

                                <!-- Municipio -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $nucleo->municipio->nombre ?? '-' }}</div>
                                </td>

                                <!-- Zona Tarifaria -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($nucleo->zona)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              style="background-color: {{ $nucleo->zona->color ?? '#e5e7eb' }};
                                     color: {{ in_array($nucleo->zona->nombre, ['B']) ? 'white' : 'black' }}">
                            {{ $nucleo->zona->nombre }}
                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Sin zona
                        </span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col sm:flex-row gap-2 justify-center">
                                        <a href="/paradas/filtro?nucleo_id={{ $nucleo->id_nucleo }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                            <i class="fa-solid fa-street-view"></i>&nbsp;
                                            Paradas
                                        </a>
                                        <a href="/puntos-venta?nucleo_id={{ $nucleo->id_nucleo }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                                            <i class="fa-solid fa-shop"></i>&nbsp;
                                            Puntos venta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium">No hay núcleos para estos filtros</p>
                                        <p class="text-sm text-gray-400 mt-1">Prueba con otros criterios de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Paginación -->
            @if($nucleos->hasPages())
                <div class="mt-6">
                    {{ $nucleos->appends(request()->query())->links('pagination.simple') }}
                </div>
            @endif

            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/zonas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-solid fa-map-location-dot"></i></div>
                        <div>
                            <div class="font-medium">Zonas Tarifarias</div>
                            <div class="text-sm text-gray-500">Ver todas las zonas</div>
                        </div>
                    </a>
                    <a href="/nucleos" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-solid fa-house-chimney"></i></div>
                        <div>
                            <div class="font-medium">Núcleos</div>
                            <div class="text-sm text-gray-500">Vista detallada de núcleos</div>
                        </div>
                    </a>
                    <a href="/paradas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-solid fa-street-view"></i></div>
                        <div>
                            <div class="font-medium">Paradas</div>
                            <div class="text-sm text-gray-500">Ver todas las paradas</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
