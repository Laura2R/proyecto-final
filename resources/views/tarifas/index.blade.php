@extends('layouts.app')

@section('title', 'Tarifas Interurbanas - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Tarifas Interurbanas</h1>
            <p class="text-xl">Conoce todos los precios y descuentos del transporte público</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Información de Tarifas -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-solid fa-chart-simple"></i> Información de Tarifas</h2>

                <!-- Fecha de entrada en vigor -->
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <p class="text-blue-800 font-semibold">
                        <i class="fa-solid fa-triangle-exclamation"></i> Precios con entrada en vigor a partir del 01/01/2025
                    </p>
                </div>

                <!-- Tabla de tarifas -->
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Tabla de Precios por Saltos</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Saltos</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Precio Billete</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Precio Tarjeta</th>
                                <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Ahorro</th>
                                <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">% Descuento</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tarifas as $tarifa)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-blue-800">{{ $tarifa->saltos }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-lg font-bold text-gray-900">{{ number_format($tarifa->bs, 2) }}€</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-lg font-bold text-green-600">{{ number_format($tarifa->tarjeta, 2) }}€</div>
                                        <!-- Mostrar ahorro en móvil debajo del precio -->
                                        @php
                                            $ahorro = $tarifa->bs - $tarifa->tarjeta;
                                            $porcentaje = $tarifa->bs > 0 ? (($tarifa->bs - $tarifa->tarjeta) / $tarifa->bs) * 100 : 0;
                                        @endphp
                                        <div class="sm:hidden text-xs text-green-600 mt-1">
                                            Ahorro: {{ number_format($ahorro, 2) }}€ ({{ number_format($porcentaje) }}%)
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-600">
                                            {{ number_format($ahorro, 2) }}€
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $porcentaje >= 50 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ number_format($porcentaje) }}%
                    </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3 sm:colspan-4 md:colspan-5" class="px-6 py-4 text-center text-gray-500">
                                        No hay tarifas disponibles
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Información de transbordos -->
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0 text-green-400">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-green-800 font-semibold"><i class="fa-regular fa-credit-card"></i> Ventaja de Transbordo</h4>
                            <p class="text-sm text-green-700 mt-1">
                                Con la tarjeta de transporte puedes realizar <strong>un transbordo</strong> a otra línea
                                en un máximo de <strong>90 minutos</strong> por solo <strong>0,50€</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Zonas -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-solid fa-map-location-dot"></i> Sistema de Zonas Tarifarias</h2>

                <!-- Diagrama de zonas -->
                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Diagrama Zonas</h3>
                        <img src="{{ asset('images/diagramaZonas.jpg') }}" alt="Diagrama de Zonas"
                             class="w-4/5 rounded-lg mx-auto">
                    </div>
                </div>

                <!-- Mapa de zonas -->
                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Mapa Detallado de Zonas</h3>
                        <img src="{{ asset('images/mapaZonas.jpg') }}" alt="Mapa Detallado de Zonas"
                             class="w-1/2 rounded-lg mx-auto">
                    </div>
                </div>

                <!-- Explicación del sistema -->
                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">¿Cómo funciona el sistema de zonas?</h3>
                    <div class="prose text-gray-600">
                        <p class="mb-4">
                            El sistema de tarificación se basa en <strong>6 zonas concéntricas (A, B, C, D, E y F)</strong>.
                            La tarifa se determina por el número de <strong>"saltos"</strong> entre zonas hasta el destino.
                        </p>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>Un <strong>salto</strong> = paso de una zona a otra</li>
                            <li>Viajes dentro de la misma zona = <strong>0 saltos</strong></li>
                            <li>La tarifa es independiente de las paradas específicas</li>
                            <li>Se paga según el mayor número de saltos del recorrido</li>
                        </ul>
                    </div>
                </div>

                <!-- Tabla de zonas y municipios -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Distribución de Municipios por Zonas</h3>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nº Municipios</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Municipios</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $zonasMunicipios = [
                                'A' => ['color' => '#fcff6d', 'municipios' => ['Huelva']],
                                'B' => ['color' => '#ff3c5b', 'municipios' => ['Aljaraque', 'Gibraleón', 'Lucena del Puerto', 'Moguer', 'Palos de la Frontera', 'Punta Umbría', 'Trigueros', 'San Juan del Puerto']],
                                'C' => ['color' => '#d89dff', 'municipios' => ['Beas', 'Bonares', 'Cartaya', 'San Bartolomé de la Torre']],
                                'D' => ['color' => '#ffcef9', 'municipios' => ['Lepe', 'Rociana del Condado']],
                                'E' => ['color' => '#9dff9d', 'municipios' => ['Almonte', 'Bollullos Par del Condado', 'Isla Cristina', 'Villablanca']],
                                'F' => ['color' => '#55c5ff', 'municipios' => ['Ayamonte', 'Hinojos']]
                            ];
                        @endphp

                        @foreach($zonasMunicipios as $zona => $info)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold"
                                      style="background-color: {{ $info['color'] }}; color: {{ in_array($zona, ['B']) ? 'white' : 'black' }}">
                                    {{ $zona }}
                                </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ count($info['municipios']) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ implode(', ', $info['municipios']) }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Integración EMTUSA -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-bus"></i> Integración con EMTUSA</h2>

                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Beneficios de la Integración</h3>
                            <ul class="space-y-3 text-gray-600">
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 mt-1">✓</span>
                                    <span><strong>Una sola tarjeta</strong> para toda la red metropolitana</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 mt-1">✓</span>
                                    <span><strong>Ahorro de 0,60€</strong> en cada transbordo a EMTUSA</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 mt-1">✓</span>
                                    <span><strong>90 minutos</strong> para realizar transbordos</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2 mt-1">✓</span>
                                    <span><strong>Hasta 4 etapas</strong> en un mismo viaje</span>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tarifas de Transbordo</h3>
                            <div class="space-y-3">
                                <div class="p-3 bg-blue-50 rounded">
                                    <div class="text-sm font-medium text-blue-800">Billete EMTUSA</div>
                                    <div class="text-lg font-bold text-blue-900">1,10€</div>
                                </div>
                                <div class="p-3 bg-green-50 rounded">
                                    <div class="text-sm font-medium text-green-800">Billete EMTUSA tras transbordo CTHU ↔ EMTUSA</div>
                                    <div class="text-lg font-bold text-green-900">0,50€</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de Transporte -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-regular fa-credit-card"></i> Tarjetas de Transporte</h2>

                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <p class="text-gray-600 mb-4">
                        Las tarjetas de transporte ofrecen importantes ventajas: <strong>ahorro económico</strong>,
                        <strong>comodidad</strong> en el pago, <strong>transbordos</strong> con descuento y
                        <strong>recarga fácil</strong>. Elige la tarjeta que mejor se adapte a tu perfil.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Tarjeta de Transporte -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
                                <i class="fa-regular fa-credit-card text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tarjeta de Transporte</h3>
                            <p class="text-sm text-gray-600 mb-4">Tarjeta estándar para todos los usuarios</p>
                            <a href="https://www.cthu.es/?op=din&id=21" target="_blank"
                               class="inline-block px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                Más información
                            </a>
                        </div>
                    </div>

                    <!-- Tarjeta Familia Numerosa -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600">
                                <i class="fa-solid fa-users text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Familia Numerosa</h3>
                            <p class="text-sm text-gray-600 mb-4">Descuentos para familias numerosas</p>
                            <a href="https://www.cthu.es/?op=din&id=22" target="_blank"
                               class="inline-block px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                Más información
                            </a>
                        </div>
                    </div>

                    <!-- Tarjeta Joven -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 text-purple-600">
                                <i class="fa-solid fa-user text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tarjeta Joven</h3>
                            <p class="text-sm text-gray-600 mb-4">Ventajas especiales para jóvenes</p>
                            <a href="https://www.cthu.es/?op=din&id=24" target="_blank"
                               class="inline-block px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition">
                                Más información
                            </a>
                        </div>
                    </div>

                    <!-- Tarjeta 65+ -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-600">
                                <i class="fa-solid fa-person-cane text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tarjeta 65+</h3>
                            <p class="text-sm text-gray-600 mb-4">Para mayores de 65 años</p>
                            <a href="https://www.juntadeandalucia.es/agenciadeserviciossocialesydependencia/index.php/m-tarjeta-andalucia-junta-sesentaycinco"
                               target="_blank"
                               class="inline-block px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600 transition">
                                Más información
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
