<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OnubaBus - Transporte Público de Huelva')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
<!-- Navegación -->
<nav class="bg-blue-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-white text-2xl font-bold">
                    🚌 OnubaBus
                </a>
            </div>

            <!-- Menú Desktop -->
            <div class="hidden md:flex space-x-6">
                <a href="{{ route('home') }}" class="text-white hover:text-blue-200 transition {{ request()->routeIs('home') ? 'border-b-2 border-white' : '' }}">
                    Inicio
                </a>
                <a href="{{ route('tarifas.index') }}" class="text-white hover:text-blue-200 transition {{ request()->routeIs('tarifas.*') ? 'border-b-2 border-white' : '' }}">
                    Tarifas
                </a>
                <div class="relative group">
                    <button class="text-white hover:text-blue-200 transition">
                        Servicios ▼
                    </button>
                    <div class="absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="/lineas" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Líneas</a>
                        <a href="/paradas/filtro" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Paradas</a>
                        <a href="/horarios" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Horarios</a>
                        <a href="/puntos-venta" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Puntos de Venta</a>
                    </div>
                </div>
                <div class="relative group">
                    <button class="text-white hover:text-blue-200 transition">
                        Información ▼
                    </button>
                    <div class="absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="{{ route('tarifas.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Zonas</a>
                        <a href="/municipios" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Municipios</a>
                        <a href="/municipios" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">Núcleos</a>
                    </div>
                </div>
                <a href="{{ route('contact') }}" class="text-white hover:text-blue-200 transition {{ request()->routeIs('contact') ? 'border-b-2 border-white' : '' }}">
                    Contacto
                </a>
            </div>

            <!-- Menú Móvil -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div id="mobile-menu" class="md:hidden hidden pb-4">
            <a href="{{ route('home') }}" class="block py-2 text-white hover:text-blue-200">Inicio</a>
            <a href="{{ route('tarifas.index') }}" class="block py-2 text-white hover:text-blue-200">Tarifas</a>
            <a href="/lineas" class="block py-2 text-white hover:text-blue-200">Líneas</a>
            <a href="/paradas/filtro" class="block py-2 text-white hover:text-blue-200">Paradas</a>
            <a href="/horarios" class="block py-2 text-white hover:text-blue-200">Horarios</a>
            <a href="{{ route('contact') }}" class="block py-2 text-white hover:text-blue-200">Contacto</a>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<main class="flex-grow">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Información de la empresa -->
            <div>
                <h3 class="text-lg font-semibold mb-4">OnubaBus</h3>
                <p class="text-gray-300 text-sm">
                    Tu sistema de transporte público en Huelva.
                    Conectando la provincia con servicios eficientes y sostenibles.
                </p>
            </div>

            <!-- Enlaces rápidos -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('tarifas.index') }}" class="text-gray-300 hover:text-white">Tarifas</a></li>
                    <li><a href="/horarios" class="text-gray-300 hover:text-white">Horarios</a></li>
                    <li><a href="/lineas" class="text-gray-300 hover:text-white">Líneas</a></li>
                    <li><a href="/paradas" class="text-gray-300 hover:text-white">Paradas</a></li>
                </ul>
            </div>

            <!-- Servicios -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Servicios</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/puntos-venta" class="text-gray-300 hover:text-white">Puntos de Venta</a></li>
                    <li><a href="/zonas" class="text-gray-300 hover:text-white">Zonas Tarifarias</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-white">Atención al Cliente</a></li>
                </ul>
            </div>

            <!-- Contacto -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                <div class="text-sm text-gray-300 space-y-2">
                    <p>📞 959 123 456</p>
                    <p>📧 info@onubabus.es</p>
                    <p>📍 Huelva, Andalucía</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-300">
            <p>&copy; {{ date('Y') }} OnubaBus. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script>
    // Menú móvil
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
</body>
</html>
