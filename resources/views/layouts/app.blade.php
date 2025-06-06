<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OnubaBus - Transporte Público de Huelva')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://kit.fontawesome.com/182628c18b.js" crossorigin="anonymous"></script>

</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
<!-- Navegación -->
<nav class="bg-blue-600 shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-white text-2xl font-bold">
                    OnubaBus
                </a>
            </div>

            <!-- Menú Desktop -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="{{ route('home') }}"
                   class="text-white hover:text-black transition {{ request()->routeIs('home') ? 'border-b-2 border-white' : '' }}">
                    Inicio
                </a>
                <a href="/puntos-venta"
                   class="text-white hover:text-black transition {{request()->routeIs('puntos-venta') ? 'border-b-2 border-white' : '' }}">
                    Puntos de Venta
                </a>
                <div class="relative group">
                    <button class="text-white hover:text-black transition">
                        Servicios <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div
                        class="absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="/lineas" class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i
                                class="fa-solid fa-bus"></i> Líneas</a>
                        <a href="/paradas/filtro" class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i
                                class="fa-solid fa-street-view"></i> Paradas</a>
                        <a href="/paradas/filtro-linea" class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i
                                class="fa-solid fa-signs-post"></i> Paradas de una Línea</a>
                        <a href="/horarios" class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i
                                class="fa-solid fa-clock"></i> Horarios</a>
                        <a href="{{ route('tarifas.calculadora') }}"
                           class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i class="fa-solid fa-route"></i>
                            Calcular Tarifa</a>
                    </div>
                </div>
                <div class="relative group">
                    <button class="text-white hover:text-black transition">
                        Información <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div
                        class="absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('tarifas.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i
                                class="fa-regular fa-circle-question"></i> Tarifas/Zonas</a>
                        <a href="/municipios"
                           class="block px-4 py-2 text-gray-700 hover:bg-blue-50"><i
                                class="fa-regular fa-circle-question"></i> Municipio/Núcleo</a>
                    </div>
                </div>
                <a href="{{ route('contact') }}"
                   class="text-white hover:text-black transition {{ request()->routeIs('contact') ? 'border-b-2 border-white' : '' }}">
                    Contacto
                </a>

                <!-- Menú de usuario autenticado -->
                @auth
                    <div class="relative group">
                        <button class="text-white hover:text-black transition flex items-center gap-1">
                            <i class="fa-regular fa-user"></i> {{ auth()->user()->name }} ▼
                        </button>
                        <div
                            class="absolute top-full right-0 py-2 w-56 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}"
                                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-shield-alt"></i> Panel de Administración
                                </a>
                                <hr class="my-1 border-gray-300">
                            @endif
                            <a href="{{ route('paradas.favoritas') }}"
                               class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                <i class="fa-regular fa-star"></i> Mis Paradas Favoritas
                            </a>
                            <a href="{{ route('lineas.favoritas') }}"
                               class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                <i class="fa-regular fa-star"></i> Mis Líneas Favoritas
                            </a>
                            <a href="{{ route('cards.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                <i class="fa-regular fa-credit-card"></i> Mis Tarjetas
                            </a>
                            <a href="{{ route('billetes.mis-billetes') }}"
                               class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fa-solid fa-ticket"></i> Mis Billetes
                            </a>
                            <hr class="my-1 border-gray-300">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-blue-50">
                                    <i class="fa-solid fa-user-slash"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-black transition">
                        <i class="fa-solid fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                @endauth
            </div>

            <!-- Menú Móvil -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div id="mobile-menu" class="md:hidden hidden pb-4">
            <a href="{{ route('home') }}" class="block py-2 text-white hover:text-black">Inicio</a>
            <a href="{{ route('tarifas.index') }}" class="block py-2 text-white hover:text-black">Tarifas</a>
            <a href="{{ route('tarifas.calculadora') }}" class="block py-2 text-white hover:text-black">Calcular
                Tarifa</a>
            <a href="/lineas" class="block py-2 text-white hover:text-black">Líneas</a>
            <a href="/paradas/filtro" class="block py-2 text-white hover:text-black">Paradas</a>
            <a href="/paradas/filtro-linea" class="block py-2 text-white hover:text-black">Paradas de una Línea</a>
            <a href="/horarios" class="block py-2 text-white hover:text-black">Horarios</a>
            <a href="{{ route('contact') }}" class="block py-2 text-white hover:text-black">Contacto</a>

            <!-- Menú móvil para usuarios autenticados -->
            @auth
                <hr class="my-2 border-gray-400">
                <a href="/" class="block py-2 text-white hover:text-black">Mis Paradas Favoritas</a>
                <a href="/" class="block py-2 text-white hover:text-black">Mis Líneas Favoritas</a>
                <a href="{{ route('cards.index') }}" class="block py-2 text-white hover:text-black">Mis Tarjetas</a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left py-2 text-white hover:text-black">Cerrar Sesión
                    </button>
                </form>
            @else
                <hr class="my-2 border-gray-400">
                <a href="{{ route('login') }}" class="block py-2 text-white hover:text-black">Iniciar Sesión</a>
            @endauth
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<main class="flex-grow pt-16">
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

            <!-- Servicios -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Servicios</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/lineas" class="text-gray-300 hover:text-white">Líneas</a></li>
                    <li><a href="/paradas/filtro" class="text-gray-300 hover:text-white">Paradas</a></li>
                    <li><a href="/paradas/filtro-linea" class="text-gray-300 hover:text-white"> Paradas de una Línea</a>
                    </li>
                    <li><a href="/horarios" class="text-gray-300 hover:text-white"> Horarios</a></li>
                    <li><a href="{{ route('tarifas.calculadora') }}" class="text-gray-300 hover:text-white"> Calcular
                            Tarifa</a></li>
                </ul>
            </div>

            <!-- Información -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Información</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('tarifas.index') }}" class="text-gray-300 hover:text-white">Tarifas/Zonas</a>
                    </li>
                    <li><a href="/municipios" class="text-gray-300 hover:text-white">Municipios/Núcleos</a></li>
                    <li><a href="/puntos-venta" class="text-gray-300 hover:text-white">Puntos de Venta</a></li>
                </ul>
            </div>

            <!-- Contacto -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                <div class="text-sm text-gray-300 space-y-2">
                    <p><i class="fa-solid fa-phone"></i> 959 123 456</p>
                    <p><i class="fa-regular fa-envelope"></i> info@onubabus.es</p>
                    <p><i class="fa-solid fa-location-dot"></i> Huelva, Andalucía</p>
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
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
@stack('scripts')

</body>
</html>
