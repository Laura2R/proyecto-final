@extends('layouts.app')

@section('title', 'Municipios y Núcleos - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Municipios y Núcleos</h1>
            <p class="text-xl">Explora la organización territorial con búsqueda avanzada</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Información general -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Información:</strong> Utiliza los filtros para buscar núcleos específicos por municipio, nombre o zona tarifaria.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de búsqueda -->
            <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">🔍 Filtros de Búsqueda</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                    <!-- Búsqueda por nombre de núcleo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Núcleo</label>
                        <input type="text" name="nucleo_search" value="{{ request('nucleo_search') }}"
                               placeholder="Nombre del núcleo..."
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <!-- Filtro por zona -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Zona Tarifaria</label>
                        <select name="zona_id" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Todas las zonas --</option>
                            @foreach($todasZonas as $zona)
                                <option value="{{ $zona->id_zona }}"
                                    {{ request('zona_id') == $zona->id_zona ? 'selected' : '' }}>
                                    Zona {{ $zona->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                            Buscar
                        </button>
                        <a href="/municipios"
                           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
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
                            @if(request()->hasAny(['municipio_id', 'nucleo_search', 'zona_id']))
                                con los filtros aplicados
                            @endif
                        @else
                            No se encontraron núcleos
                        @endif
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID Núcleo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre del Núcleo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Municipio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Zona Tarifaria
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($nucleos as $nucleo)
                            <tr class="hover:bg-gray-50 transition">
                                <!-- ID Núcleo -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-xs font-medium text-blue-800">{{ $nucleo->id_nucleo }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Nombre del Núcleo -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $nucleo->nombre }}</div>
                                </td>

                                <!-- Municipio -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $nucleo->municipio->nombre ?? '-' }}
                                    </div>
                                </td>

                                <!-- Zona Tarifaria -->
                                <td class="px-6 py-4">
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/paradas/filtro?nucleo_id={{ $nucleo->id_nucleo }}"
                                           class="text-blue-600 hover:text-blue-900">Ver paradas</a>
                                        <span class="text-gray-300">|</span>
                                        <a href="/puntos-venta?nucleo_id={{ $nucleo->id_nucleo }}"
                                           class="text-green-600 hover:text-green-900">Puntos venta</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
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
                    {{ $nucleos->appends(request()->query())->links() }}
                </div>
            @endif



            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/zonas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🗺️</div>
                        <div>
                            <div class="font-medium">Zonas Tarifarias</div>
                            <div class="text-sm text-gray-500">Ver todas las zonas</div>
                        </div>
                    </a>
                    <a href="/nucleos" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🏘️</div>
                        <div>
                            <div class="font-medium">Núcleos</div>
                            <div class="text-sm text-gray-500">Vista detallada de núcleos</div>
                        </div>
                    </a>
                    <a href="/paradas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🚏</div>
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
