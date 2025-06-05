@extends('layouts.app')

@section('title', 'OnubaBus - Inicio')

@section('content')
    <!-- Hero Section -->
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Bienvenido a OnubaBus
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Tu sistema de transporte público en Huelva. Conectamos toda la provincia con servicios eficientes, sostenibles y accesibles.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('tarifas.index') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Ver Tarifas
                </a>
                <a href="/horarios" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                    Consultar Horarios
                </a>
            </div>
        </div>
    </section>

    <!-- Estadísticas -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ $stats['lineas'] }}</div>
                    <div class="text-gray-600">Líneas de Autobús</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ $stats['paradas'] }}</div>
                    <div class="text-gray-600">Paradas Disponibles</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-blue-600 mb-2">{{ $stats['municipios'] }}</div>
                    <div class="text-gray-600">Municipios Distintos</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios Principales -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Nuestros Servicios</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Horarios -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fas fa-clock"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Horarios</h3>
                    <p class="text-gray-600 mb-4">Consulta los horarios de todas nuestras líneas en tiempo real.</p>
                    <a href="/horarios" class="text-blue-600 hover:text-blue-800 font-medium">Ver horarios →</a>
                </div>

                <!-- Tarifas -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-sack-dollar"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Tarifas</h3>
                    <p class="text-gray-600 mb-4">Conoce todas las tarifas y descuentos disponibles.</p>
                    <a href="{{ route('tarifas.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">Ver tarifas →</a>
                </div>

                <!-- Paradas -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fas fa-map-marker-alt"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Paradas</h3>
                    <p class="text-gray-600 mb-4">Encuentra la parada más cercana y sus servicios.</p>
                    <a href="/paradas/filtro" class="text-blue-600 hover:text-blue-800 font-medium">Buscar paradas →</a>
                </div>

                <!-- Puntos de Venta -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Puntos de Venta</h3>
                    <p class="text-gray-600 mb-4">Localiza dónde puedes adquirir tu tarjeta de transporte.</p>
                    <a href="/puntos-venta" class="text-blue-600 hover:text-blue-800 font-medium">Ver puntos →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Información Adicional -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6">Transporte Sostenible para Huelva</h2>
                    <p class="text-gray-600 mb-6">
                        OnubaBus es más que un sistema de transporte. Somos el motor que conecta Huelva,
                        facilita el acceso al trabajo, la educación y los servicios, mientras cuidamos nuestro medio ambiente.
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Flota moderna y ecológica
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Accesibilidad para personas con movilidad reducida
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Tarifas sociales y descuentos
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            Cobertura en toda la provincia
                        </li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-8 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4">¿Necesitas ayuda?</h3>
                    <p class="text-gray-600 mb-6">
                        Nuestro equipo está aquí para ayudarte con cualquier consulta sobre rutas, horarios o servicios.
                    </p>
                    <a href="{{ route('contact') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition inline-block">
                        Contactar
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
