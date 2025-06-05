@extends('layouts.app')

@section('title', 'Filtrar Paradas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
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
                    <div class="flex-shrink-0 text-blue-700">
                        <i class="fa-solid fa-circle-exclamation"></i>
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
                @csrf
                <h2 class="text-lg font-semibold text-gray-800 mb-4"><i class="fa-solid fa-magnifying-glass"></i> Filtros de Búsqueda</h2>
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
                        @if($paradas->total() > 0)
                            Mostrando {{ $paradas->count() }} de {{ $paradas->total() }} paradas
                        @else
                            No se encontraron paradas
                        @endif
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            @auth
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Favorito</th>
                            @endauth

                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Municipio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Núcleo</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($paradas as $parada)
                            <tr class="hover:bg-gray-50 transition">
                                @auth
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <button onclick="toggleFavoritoParada({{ $parada->id_parada }})"
                                                class="favorite-btn text-2xl hover:scale-110 transition-transform"
                                                data-parada-id="{{ $parada->id_parada }}"
                                                data-is-favorite="{{ auth()->user()->paradasFavoritas->contains('id_parada', $parada->id_parada) ? 'true' : 'false' }}">
                                            @if(auth()->user()->paradasFavoritas->contains('id_parada', $parada->id_parada))
                                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                            @else
                                                <i class="fa-regular fa-star"></i>
                                            @endif
                                        </button>
                                    </td>
                                @endauth


                                <td class="px-4 py-4">
                                    <a href="{{ route('paradas.show', $parada->id_parada) }}"
                                       class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        {{ $parada->nombre }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $parada->municipio->nombre ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $parada->nucleo->nombre ?? '-' }}</div>
                                </td>
                            </tr>
                        @empty
                            <!-- Estado vacío -->
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Paginación -->
            @if($paradas->hasPages())
                <div class="mt-6">
                    {{ $paradas->appends(request()->query())->links('pagination.simple') }}
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
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-regular fa-map"></i></div>
                        <div>
                            <div class="font-medium">Líneas</div>
                            <div class="text-sm text-gray-500">Ver todas las líneas</div>
                        </div>
                    </a>
                    <a href="/horarios" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-regular fa-clock"></i></div>
                        <div>
                            <div class="font-medium">Horarios</div>
                            <div class="text-sm text-gray-500">Consultar horarios</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <script src="{{ asset('js/favoritos.js') }}"></script>


@endsection
