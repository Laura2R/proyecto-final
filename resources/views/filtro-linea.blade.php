@extends('layouts.app')

@section('title', 'Filtrar Paradas por Línea - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Paradas por Línea</h1>
            <p class="text-xl">Encuentra las paradas de cualquier línea de autobús</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">

            <!-- Formulario de filtros -->
            <form method="GET" class="mb-8 bg-white p-6 rounded-lg shadow">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Línea:</label>
                        <select name="linea_id" onchange="this.form.submit()"
                                class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Selecciona línea --</option>
                            @foreach($lineas as $linea)
                                <option value="{{ $linea->id_linea }}"
                                    {{ $lineaSeleccionada && $lineaSeleccionada->id_linea == $linea->id_linea ? 'selected' : '' }}>
                                    {{ $linea->codigo }} - {{ $linea->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($lineaSeleccionada)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Municipio:</label>
                            <select name="municipio_id" onchange="this.form.submit()"
                                    class="w-full rounded-md border-gray-300 shadow-sm">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Núcleo:</label>
                            <select name="nucleo_id" onchange="this.form.submit()"
                                    class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Todos los núcleos --</option>
                                @foreach($nucleos as $nucleo)
                                    <option value="{{ $nucleo->id_nucleo }}"
                                        {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                        {{ $nucleo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </form>

            @if($lineaSeleccionada)
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-2xl font-bold mb-6">
                        Paradas de la línea {{ $lineaSeleccionada->codigo }} - {{ $lineaSeleccionada->nombre }}
                    </h2>

                    @if($paradasAgrupadas->isNotEmpty())
                        @foreach($paradasAgrupadas as $sentido => $paradas)
                            <!-- Contenedor de sentido con atributo de identificación -->
                            <div class="mb-8" data-sentido="{{ $sentido == 'ida' || $sentido == 1 ? 'ida' : 'vuelta' }}">
                                <h3 class="text-xl font-semibold mb-4 text-blue-800">
                                    {{ $sentido == 'ida' || $sentido == 1 ? 'Ida' : 'Vuelta' }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($paradas as $parada)
                                        <!-- Añadir atributos data al elemento de parada -->

                                        <div class="border-l-4 border-blue-500 pl-4 bg-blue-50 rounded p-3 parada-item"
                                             data-lat="{{ $parada->latitud }}"
                                             data-lng="{{ $parada->longitud }}"
                                             data-nombre="{{ $parada->nombre }}"
                                             data-orden="{{ $parada->pivot_orden }}"
                                             data-id="{{ $parada->id_parada }}">
                                            <div class="flex justify-between items-start gap-2">
                                                @auth
                                                    <button onclick="toggleFavoritoParada({{ $parada->id_parada }})"
                                                            class="favorite-btn text-lg hover:scale-110 transition-transform ml-2"
                                                            data-parada-id="{{ $parada->id_parada }}"
                                                            data-is-favorite="{{ auth()->user()->paradasFavoritas->contains('id_parada', $parada->id_parada) ? 'true' : 'false' }}">
                                                        @if(auth()->user()->paradasFavoritas->contains('id_parada', $parada->id_parada))
                                                            <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                                                        @else
                                                            <i class="fa-regular fa-star"></i>
                                                        @endif
                                                    </button>
                                                @endauth

                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ route('paradas.show', $parada->id_parada) }}"
                                                       class="text-blue-600 hover:underline font-medium block truncate">
                                                        {{ $parada->nombre }}
                                                    </a>
                                                    <div class="mt-1 text-sm text-gray-600">
                                                    <span class="truncate block">
                                                        {{ $parada->nucleo->nombre ?? '-' }}
                                                        ({{ $parada->nucleo->municipio->nombre ?? '-' }})
                                                    </span>
                                                    </div>
                                                    @if($parada->descripcion)
                                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                                            {{ $parada->descripcion }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="flex-shrink-0 ml-2">
                                                <span class="text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded-full whitespace-nowrap">
                                                    Orden: {{ $parada->pivot_orden }}
                                                </span>
                                                </div>
                                            </div>
                                            @if($parada->zona)
                                                <div class="mt-2 text-xs">
                                                    <span class="text-gray-500">Zona:</span>
                                                    <span class="truncate">{{ $parada->zona->nombre }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500 py-6">
                            No se encontraron paradas con los filtros seleccionados
                        </div>
                    @endif

                    <!-- Mapa para Ida -->
                    @if(isset($polilineaIda) && count($polilineaIda) > 0)
                        @php
                            $tieneCoordenadasIda = false;
                            foreach($polilineaIda as $punto) {
                                if(is_array($punto) && count($punto) > 0 && !empty($punto[0])) {
                                    $tieneCoordenadasIda = true;
                                    break;
                                }
                            }
                        @endphp
                        @if($tieneCoordenadasIda)
                            <div class="bg-white p-6 rounded-lg shadow mt-6">
                                <h3 class="text-xl font-semibold mb-4 text-blue-800">Recorrido de Ida</h3>
                                <div id="mapa-ida" class="map-container"></div>
                            </div>
                        @endif
                    @endif

                    <!-- Mapa para Vuelta -->
                    @if(isset($polilineaVuelta) && count($polilineaVuelta) > 0)
                        @php
                            $tieneCoordenadasVuelta = false;
                            foreach($polilineaVuelta as $punto) {
                                if(is_array($punto) && count($punto) > 0 && !empty($punto[0])) {
                                    $tieneCoordenadasVuelta = true;
                                    break;
                                }
                            }
                        @endphp
                        @if($tieneCoordenadasVuelta)
                            <div class="bg-white p-6 rounded-lg shadow mt-6">
                                <h3 class="text-xl font-semibold mb-4 text-blue-800">Recorrido de Vuelta</h3>
                                <div id="mapa-vuelta" class="map-container"></div>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <!-- Estado inicial sin línea seleccionada -->
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <div class="text-gray-400 text-6xl mb-4"><i class="fas fa-bus"></i></div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Selecciona una línea</h3>
                    <p class="text-gray-500">Elige una línea de autobús para ver todas sus paradas y recorridos</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Estilos específicos para esta vista -->
    <style>
        .map-container {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-top: 1rem;
        }
    </style>

    <!-- Scripts -->
    <script>
        window.polilineaIda = @json($polilineaIda ?? []);
        window.polilineaVuelta = @json($polilineaVuelta ?? []);
    </script>
    <script src="{{ asset('js/google-polylines.js') }}"></script>
    <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&loading=async&callback=initMaps">
    </script>
    <script src="{{ asset('js/favoritos.js') }}"></script>


@endsection
