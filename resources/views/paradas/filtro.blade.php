@extends('layouts.app')

@section('title', 'Filtrar Paradas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Filtrar Paradas</h1>
            <p class="text-xl">Busca paradas por su municipio y núcleo</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
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
                            <strong>Información:</strong> Utiliza los filtros para encontrar paradas específicas en municipios y núcleos de población.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de filtros -->
            <form method="GET" action="{{ route('paradas.filtro') }}" class="mb-8 bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filtros de Búsqueda</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="municipio_id" class="block text-sm font-medium text-gray-700 mb-2">Municipio</label>
                        <select id="municipio_id" name="municipio_id" class="w-full rounded-md border-gray-300 shadow-sm"
                                onchange="this.form.submit()">
                            <option value="">-- Todos los municipios --</option>
                            @foreach($municipios as $municipio)
                                <option value="{{ $municipio->id_municipio }}"
                                    {{ request('municipio_id') == $municipio->id_municipio ? 'selected' : '' }}>
                                    {{ $municipio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="nucleo_id" class="block text-sm font-medium text-gray-700 mb-2">Núcleo</label>
                        <select id="nucleo_id" name="nucleo_id" class="w-full rounded-md border-gray-300 shadow-sm"
                                {{ count($nucleos) ? '' : 'disabled' }} onchange="this.form.submit()">
                            <option value="">-- Todos los núcleos --</option>
                            @foreach($nucleos as $nucleo)
                                <option value="{{ $nucleo->id_nucleo }}"
                                    {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                    {{ $nucleo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('paradas.filtro') }}" class="text-gray-600 hover:text-gray-800 underline">
                            Limpiar filtros
                        </a>
                    </div>
                </div>
            </form>

            <!-- Resultados -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Resultados de la Búsqueda</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($paradas->total() > 0)
                            Mostrando {{ $paradas->count() }} de {{ $paradas->total() }} paradas
                            @if(request('municipio_id') || request('nucleo_id'))
                                con los filtros aplicados
                            @endif
                        @else
                            No se encontraron paradas
                        @endif
                    </p>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Parada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Núcleo</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paradas as $parada)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-800">{{ $parada->id_parada }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('paradas.show', $parada->id_parada) }}"
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    {{ $parada->nombre }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $parada->municipio->nombre ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $parada->nucleo->nombre ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">No hay paradas para estos filtros</p>
                                    <p class="text-sm text-gray-400 mt-1">Prueba con otros criterios de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($paradas->hasPages())
                <div class="mt-6">
                    {{ $paradas->appends(request()->query())->links() }}
                </div>
            @endif


            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/paradas/filtro-linea" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🚌</div>
                        <div>
                            <div class="font-medium">Paradas por Línea</div>
                            <div class="text-sm text-gray-500">Buscar por línea de autobús</div>
                        </div>
                    </a>
                    <a href="/lineas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🗺️</div>
                        <div>
                            <div class="font-medium">Líneas</div>
                            <div class="text-sm text-gray-500">Ver todas las líneas</div>
                        </div>
                    </a>
                    <a href="/horarios" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🕒</div>
                        <div>
                            <div class="font-medium">Horarios</div>
                            <div class="text-sm text-gray-500">Consultar horarios</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
