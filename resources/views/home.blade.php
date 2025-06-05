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
                Tu sistema de transporte público en Huelva. Conectamos toda la provincia con servicios eficientes y accesibles para todos.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('tarifas.calculadora') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    <i class="fa-solid fa-calculator"></i> Calcular Tarifa
                </a>
                <a href="/horarios" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                    <i class="fa-solid fa-clock"></i> Consultar Horarios
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
                    <div class="text-gray-600">Municipios Conectados</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios Principales -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Nuestros Servicios</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Calculadora de Tarifas -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-calculator"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Calcular Tarifa</h3>
                    <p class="text-gray-600 mb-4">Calcula el precio de tu viaje y compra billetes digitales.</p>
                    <a href="{{ route('tarifas.calculadora') }}" class="text-blue-600 hover:text-blue-800 font-medium">Calcular ahora →</a>
                </div>

                <!-- Horarios -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fas fa-clock"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Horarios</h3>
                    <p class="text-gray-600 mb-4">Consulta los horarios de todas nuestras líneas actualizados.</p>
                    <a href="/horarios" class="text-blue-600 hover:text-blue-800 font-medium">Ver horarios →</a>
                </div>

                <!-- Paradas -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-street-view"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Paradas</h3>
                    <p class="text-gray-600 mb-4">Encuentra la parada más cercana y planifica tu viaje.</p>
                    <a href="/paradas/filtro" class="text-blue-600 hover:text-blue-800 font-medium">Buscar paradas →</a>
                </div>

                <!-- Líneas -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-bus"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Líneas</h3>
                    <p class="text-gray-600 mb-4">Explora todas las líneas disponibles y sus recorridos.</p>
                    <a href="/lineas" class="text-blue-600 hover:text-blue-800 font-medium">Ver líneas →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Ventajas de Registrarse -->
    <section class="py-16 bg-blue-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">¿Por qué registrarte en OnubaBus?</h2>
                <p class="text-xl text-gray-600">Descubre todas las ventajas de tener una cuenta personal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <!-- Tarjetas Digitales -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-regular fa-credit-card"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Tarjetas OnubaBus</h3>
                    <p class="text-gray-600">Crea y gestiona tus tarjetas de transporte. Recarga saldo de forma segura y rápida.</p>
                </div>

                <!-- Billetes Digitales -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-ticket"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Billetes Digitales</h3>
                    <p class="text-gray-600">Compra billetes online. Consulta todos tus billetes comprados y descargalos en PDF con códigos QR para validar tu viaje.</p>
                </div>

                <!-- Favoritos -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-regular fa-star"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Paradas y Líneas Favoritas</h3>
                    <p class="text-gray-600">Guarda tus paradas y líneas más utilizadas para acceder rápidamente a su información.</p>
                </div>

                <!-- Gestión Personal -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="text-blue-600 text-4xl mb-4"><i class="fa-solid fa-user-gear"></i></div>
                    <h3 class="text-xl font-semibold mb-3">Gestión Personal</h3>
                    <p class="text-gray-600">Administra toda tu información de transporte desde un panel personal y seguro.</p>
                </div>
            </div>

            <div class="text-center mt-12">
                @guest
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition inline-block mr-4">
                        <i class="fa-solid fa-user-plus"></i> Registrarse Gratis
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-blue-600 text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-600 hover:text-white transition inline-block">
                        <i class="fa-solid fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                @else
                    <a href="{{ route('cards.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition inline-block">
                        <i class="fa-regular fa-credit-card"></i> Ir a Mis Tarjetas
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Información del Sistema -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6">Sistema de Transporte Moderno</h2>
                    <p class="text-gray-600 mb-6">
                        OnubaBus conecta toda la provincia de Huelva con un sistema de transporte moderno,
                        eficiente y adaptado a las necesidades de todos los ciudadanos.
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2"><i class="fa-solid fa-check"></i></span>
                            Flota moderna y confortable
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2"><i class="fa-solid fa-check"></i></span>
                            Accesibilidad para personas con movilidad reducida
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2"><i class="fa-solid fa-check"></i></span>
                            Tarifas competitivas y descuentos
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2"><i class="fa-solid fa-check"></i></span>
                            Cobertura en toda la provincia
                        </li>
                        <li class="flex items-center">
                            <span class="text-green-500 mr-2"><i class="fa-solid fa-check"></i></span>
                            Sistema de pago digital integrado
                        </li>
                    </ul>
                </div>
                <div class="bg-blue-50 p-8 rounded-lg">
                    <h3 class="text-xl font-semibold mb-4"><i class="fa-solid fa-headset"></i> ¿Necesitas ayuda?</h3>
                    <p class="text-gray-600 mb-6">
                        Nuestro equipo está aquí para ayudarte con cualquier consulta sobre rutas, horarios,
                        tarifas o el uso de la plataforma digital.
                    </p>
                    <div class="space-y-3">
                        <a href="{{ route('contact') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition inline-block w-full text-center">
                            <i class="fa-solid fa-envelope"></i> Contactar
                        </a>
                        <div class="text-sm text-gray-600 text-center">
                            <p><i class="fa-solid fa-phone"></i> 959 123 456</p>
                            <p><i class="fa-regular fa-envelope"></i> info@onubabus.es</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
