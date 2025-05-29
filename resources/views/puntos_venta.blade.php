@extends('layouts.app')

@section('title', 'Puntos de Venta - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
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
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Información:</strong> En estos puntos puedes adquirir tu tarjeta de transporte, recargarla y obtener información sobre el servicio.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filtros con autosubmit -->
            <form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Municipio:</label>
                        <select name="municipio_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
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
                        <select name="nucleo_id" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm" {{ $nucleos->isEmpty() ? 'disabled' : '' }}>
                            <option value="">-- Todos los núcleos --</option>
                            @foreach($nucleos as $nucleo)
                                <option value="{{ $nucleo->id_nucleo }}"
                                    {{ request('nucleo_id') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                    {{ $nucleo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <a href="{{ url('/puntos-venta') }}" class="text-gray-600 hover:text-gray-800 underline">
                            Limpiar filtros
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

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Núcleo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($puntosVenta as $punto)
                        <tr class="hover:bg-gray-50 transition" data-lat="{{ $punto->latitud }}" data-lng="{{ $punto->longitud }}" data-nombre="{{ $punto->id_punto }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $punto->municipio->nombre ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $punto->nucleo->nombre ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $punto->tipo }}
                            </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $punto->direccion }}</div>
                                @if($punto->telefono)
                                    <div class="text-xs text-gray-500 mt-1">📞 {{ $punto->telefono }}</div>
                                @endif
                                @if($punto->horario)
                                    <div class="text-xs text-gray-500 mt-1">🕒 {{ $punto->horario }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0H3m0 0h4M9 7h6m-6 4h6m-6 4h6" />
                                    </svg>
                                    <p class="text-lg font-medium">No hay puntos de venta disponibles</p>
                                    <p class="text-sm text-gray-400 mt-1">Prueba con otros filtros o contacta con nosotros</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($puntosVenta->hasPages())
                <div class="mt-4 mb-8">
                    {{ $puntosVenta->appends(request()->query())->links() }}
                </div>
            @endif

            <!-- Mapa con todos los puntos de venta -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Mapa de Puntos de Venta</h2>
                <div id="mapa-puntos-venta"></div>
            </div>

            <!-- Información adicional -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">🏪 Servicios Disponibles</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Venta de tarjetas de transporte nuevas
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Recarga de saldo en tarjetas existentes
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Información sobre tarifas y servicios
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            Atención personalizada
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">📊 Estadísticas</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total de puntos:</span>
                            <span class="font-medium">{{ $puntosVenta->total() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Municipios con servicio:</span>
                            <span class="font-medium">{{ $municipios->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Mostrando en esta página:</span>
                            <span class="font-medium">{{ $puntosVenta->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('tarifas.index') }}" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">💰</div>
                        <div>
                            <div class="font-medium">Tarifas</div>
                            <div class="text-sm text-gray-500">Ver precios y descuentos</div>
                        </div>
                    </a>
                    <a href="{{ route('contact') }}" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">📞</div>
                        <div>
                            <div class="font-medium">Contacto</div>
                            <div class="text-sm text-gray-500">Atención al cliente</div>
                        </div>
                    </a>
                    <a href="/paradas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🚏</div>
                        <div>
                            <div class="font-medium">Paradas</div>
                            <div class="text-sm text-gray-500">Buscar paradas cercanas</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Estilos específicos para esta vista -->
    <style>
        #mapa-puntos-venta {
            height: 500px;
            width: 100%;
            border-radius: 8px;
            margin-top: 2rem;
        }
    </style>

    <!-- Scripts -->
    <script>
        // Datos para el mapa
        window.puntosVenta = @json($todosPuntosVenta);
    </script>
    <script src="{{ asset('js/mapaPuntosVenta.js') }}"></script>
    <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapaPuntosVenta">
    </script>
@endsection
