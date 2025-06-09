@extends('layouts.app')

@section('title', 'Puntos de Venta - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Puntos de Venta</h1>
            <p class="text-xl">Encuentra dónde adquirir y recargar tu tarjeta de transporte</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Información general -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0 text-blue-400">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Información:</strong> En estos puntos puedes adquirir tu tarjeta de transporte, recargarla y obtener información sobre el servicio. El mapa se actualiza automáticamente al cambiar los filtros.
                        </p>
                    </div>
                </div>
            </div>


            <!-- Filtros con JavaScript mejorado -->
            <form method="GET" id="filtros-form" class="mb-6 bg-white p-4 rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Municipio:</label>
                        <select name="municipio_id" id="municipio-select" class="w-full rounded-md border-gray-300 shadow-sm">
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
                        <select name="nucleo_id" id="nucleo-select" class="w-full rounded-md border-gray-300 shadow-sm" {{ $nucleos->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Todos los núcleos --</option>
                            @foreach($nucleos as $nucleo)
                                <option value="{{ $nucleo->id_nucleo }}"
                                    {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                    {{ $nucleo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span id="contador-mapa">Los filtros se aplican automáticamente al mapa</span>
                        </div>
                        <a href="/puntos-venta" id="limpiar-filtros" class="text-gray-600 hover:text-gray-800 underline">
                            Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabla de puntos de venta -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Listado de Puntos de Venta</h2>
                    <p class="text-sm text-gray-600 mt-1">Total: {{ $puntosVenta->total() }} puntos de venta</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Municipio</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Núcleo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($puntosVenta as $punto)
                            <tr class="hover:bg-gray-50 transition" data-lat="{{ $punto->latitud }}" data-lng="{{ $punto->longitud }}" data-nombre="{{ $punto->id_punto }}">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $punto->municipio->nombre ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $punto->nucleo->nombre ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $punto->tipo }}
                        </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ $punto->direccion }}</div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    <div class="text-4xl mb-2"><i class="fas fa-search"></i></div>
                                    <div class="text-lg font-medium">No se encontraron puntos de venta</div>
                                    <div class="text-sm">Prueba a cambiar los filtros de búsqueda</div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            @if($puntosVenta->hasPages())
                <div class="mt-4 mb-8">
                    {{ $puntosVenta->appends(request()->query())->links('pagination.simple') }}
                </div>
            @endif

            <!-- Mapa con todos los puntos de venta filtrados - Solo se muestra si hay puntos filtrados -->
            @if($puntosVentaMapa->count() > 0)
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Mapa de Puntos de Venta</h2>
                        <div class="text-sm text-gray-600">
                            <span id="contador-marcadores">Cargando...</span>
                        </div>
                    </div>
                    <div id="mapa-puntos-venta"></div>

                    <!-- Leyenda del mapa -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Leyenda:</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/blue-dot.png" width="16" height="16" class="mr-1">
                                <span>Estanco</span>
                            </div>
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/green-dot.png" width="16" height="16" class="mr-1">
                                <span>Taquilla</span>
                            </div>
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/red-dot.png" width="16" height="16" class="mr-1">
                                <span>Bar</span>
                            </div>
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/yellow-dot.png" width="16" height="16" class="mr-1">
                                <span>Consorcio</span>
                            </div>
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/orange-dot.png" width="16" height="16" class="mr-1">
                                <span>Prensa</span>
                            </div>
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/pink-dot.png" width="16" height="16" class="mr-1">
                                <span>Copistería</span>
                            </div>
                            <div class="flex items-center">
                                <img src="https://maps.google.com/mapfiles/ms/icons/purple-dot.png" width="16" height="16" class="mr-1">
                                <span>Sin datos</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Mensaje cuando no hay puntos que mostrar -->
                <div class="bg-white p-6 rounded-lg shadow mb-6 text-center">
                    <div class="text-4xl mb-4"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h2 class="text-xl font-semibold text-gray-600 mb-2">No hay puntos para mostrar en el mapa</h2>
                    <p class="text-gray-500">Ajusta los filtros para ver los puntos de venta en el mapa</p>
                </div>
            @endif

            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('tarifas.index') }}" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div>
                            <div class="font-medium">Tarifas</div>
                            <div class="text-sm text-gray-500">Ver precios y descuentos</div>
                        </div>
                    </a>
                    <a href="{{ route('contact') }}" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <div class="font-medium">Contacto</div>
                            <div class="text-sm text-gray-500">Atención al cliente</div>
                        </div>
                    </a>
                    <a href="/paradas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3"><i class="fa-solid fa-street-view"></i></div>
                        <div>
                            <div class="font-medium">Paradas</div>
                            <div class="text-sm text-gray-500">Buscar paradas cercanas</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- Scripts - Solo se cargan si hay puntos de venta para el mapa -->
    @if($puntosVentaMapa->count() > 0)
        <script>
            // Datos para el mapa - Usar TODOS los puntos filtrados (no paginados)
            window.puntosVenta = @json($puntosVentaMapa);

            // Debug: mostrar cuántos puntos tenemos
            console.log('Puntos de venta cargados para el mapa:', window.puntosVenta.length);
        </script>
        <script src="{{ asset('js/mapaPuntosVenta.js') }}"></script>
        <script async
                src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapaPuntosVenta">
        </script>
    @endif
    <script src="{{ asset('js/puntos-venta.js') }}"></script>
@endsection
