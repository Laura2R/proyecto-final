<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OnubaBus - Transporte Público de Huelva')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="../../../../../images/LogoOnubaBusFondo2.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../css/css.css">
    <script src="https://kit.fontawesome.com/182628c18b.js" crossorigin="anonymous"></script>
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType("navigation")[0].type === "back_forward")) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
<!-- Navegación -->
<nav class="bg-blue-600 shadow-lg sticky top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo responsivo - único para todas las pantallas -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center text-white font-bold no-word-break">
                    <img src="../../../../../images/LogoOnubaBus2.png" alt="OnubaBus Logo" class="h-16 md:h-20 w-auto mr-7" />
                    <span class="text-xl md:text-3xl">OnubaBus</span>
                </a>
            </div>

            <!-- Menú Desktop -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="{{ route('home') }}"
                   class="nav-item text-white hover:scale-110 transition-transform text-lg {{ request()->routeIs('home') ? 'border-b-2 border-white' : '' }}">
                    Inicio
                </a>

                <!-- Dropdown Servicios -->
                <div class="relative group">
                    <button class="nav-item text-white hover:scale-110 transition-transform text-lg">
                        Servicios <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="/lineas" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-bus"></i> Líneas
                        </a>
                        <a href="/paradas/filtro" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-street-view"></i> Paradas
                        </a>
                        <a href="/paradas/filtro-linea" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-signs-post"></i> Paradas de una Línea
                        </a>
                        <a href="/horarios" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-clock"></i> Horarios
                        </a>
                        <a href="{{ route('tarifas.calculadora') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-route"></i> Calcular Tarifa
                        </a>
                    </div>
                </div>

                <!-- Dropdown Información -->
                <div class="relative group">
                    <button class="nav-item text-white hover:scale-110 transition-transform text-lg">
                        Información <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="absolute top-full left-0 bg-white shadow-lg rounded-lg py-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="/puntos-venta" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-shop"></i> Puntos de Venta
                        </a>
                        <a href="{{ route('tarifas.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fas fa-question-circle"></i> Tarifas/Zonas
                        </a>
                        <a href="/municipios" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                            <i class="fa-solid fa-map"></i> Municipio/Núcleo
                        </a>
                    </div>
                </div>

                <a href="{{ route('contact') }}"
                   class="nav-item text-white hover:scale-110 transition-transform text-lg {{ request()->routeIs('contact') ? 'border-b-2 border-white' : '' }}">
                    Contacto
                </a>

                <!-- Menu usuario -->
                @auth
                    <div class="relative group">
                        <button class="nav-item text-white hover:scale-110 transition-transform flex items-center gap-1 text-lg">
                            <i class="fa-regular fa-user"></i> {{ auth()->user()->name }} ▼
                        </button>
                        <!-- Submenú usuario -->
                        <div class="absolute top-full right-0 py-2 w-56 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 no-word-break">
                                    <i class="fas fa-shield-alt"></i> Panel de Administración
                                </a>
                                <hr class="my-1 border-gray-300">
                            @endif
                            <a href="{{ route('paradas.favoritas') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                                <i class="fa-regular fa-star"></i> Mis Paradas Favoritas
                            </a>
                            <a href="{{ route('lineas.favoritas') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                                <i class="fa-regular fa-star"></i> Mis Líneas Favoritas
                            </a>
                            <a href="{{ route('cards.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                                <i class="fa-regular fa-credit-card"></i> Mis Tarjetas
                            </a>
                            <a href="{{ route('billetes.mis-billetes') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 no-word-break">
                                <i class="fa-solid fa-ticket"></i> Mis Billetes
                            </a>
                            <hr class="my-1 border-gray-300">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-blue-50 no-word-break">
                                    <i class="fa-solid fa-user-slash"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-item text-white hover:scale-110 transition-transform text-lg">
                        <i class="fa-solid fa-sign-in-alt"></i> Iniciar Sesión
                    </a>
                @endauth
            </div>

            <!-- Botón hamburguesa para móvil -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú Móvil Desplegable -->
        <div id="mobile-menu" class="md:hidden hidden bg-blue-600 max-h-96 overflow-y-auto border-t border-blue-500">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('home') }}" class="block py-2 text-white hover:bg-blue-700 rounded no-word-break">
                    <i class="fas fa-home mr-2"></i> Inicio
                </a>

                <!-- Sección Servicios -->
                <div class="py-1">
                    <div class="text-white font-semibold text-xs uppercase tracking-wide mb-1 opacity-75">Servicios</div>
                    <a href="/lineas" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-bus mr-2"></i> Líneas
                    </a>
                    <a href="/paradas/filtro" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-street-view mr-2"></i> Paradas
                    </a>
                    <a href="/paradas/filtro-linea" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-signs-post mr-2"></i> Paradas de una Línea
                    </a>
                    <a href="/horarios" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-clock mr-2"></i> Horarios
                    </a>
                    <a href="{{ route('tarifas.calculadora') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-route mr-2"></i> Calcular Tarifa
                    </a>
                </div>

                <!-- Sección Información -->
                <div class="py-1">
                    <div class="text-white font-semibold text-xs uppercase tracking-wide mb-1 opacity-75">Información</div>
                    <a href="/puntos-venta" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-shop mr-2"></i> Puntos de Venta
                    </a>
                    <a href="{{ route('tarifas.index') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fas fa-question-circle mr-2"></i> Tarifas/Zonas
                    </a>
                    <a href="/municipios" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fas fa-map-marker-alt mr-2"></i> Municipio/Núcleo
                    </a>
                </div>

                <a href="{{ route('contact') }}" class="block py-2 text-white hover:bg-blue-700 rounded no-word-break">
                    <i class="fas fa-envelope mr-2"></i> Contacto
                </a>

                <!-- Menú usuario móvil -->
                @auth
                    <hr class="my-2 border-blue-500">
                    <div class="text-white font-semibold text-xs uppercase tracking-wide mb-1 opacity-75">Mi Cuenta</div>
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                            <i class="fas fa-shield-alt mr-2"></i> Panel de Administración
                        </a>
                    @endif
                    <a href="{{ route('paradas.favoritas') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-regular fa-star mr-2"></i> Mis Paradas Favoritas
                    </a>
                    <a href="{{ route('lineas.favoritas') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-regular fa-star mr-2"></i> Mis Líneas Favoritas
                    </a>
                    <a href="{{ route('cards.index') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-regular fa-credit-card mr-2"></i> Mis Tarjetas
                    </a>
                    <a href="{{ route('billetes.mis-billetes') }}" class="block py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                        <i class="fa-solid fa-ticket mr-2"></i> Mis Billetes
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left py-1.5 pl-3 text-white hover:bg-blue-700 rounded no-word-break text-sm">
                            <i class="fa-solid fa-user-slash mr-2"></i> Cerrar Sesión
                        </button>
                    </form>
                @else
                    <hr class="my-2 border-blue-500">
                    <a href="{{ route('login') }}" class="block py-2 text-white hover:bg-blue-700 rounded no-word-break">
                        <i class="fa-solid fa-sign-in-alt mr-2"></i> Iniciar Sesión
                    </a>
                @endauth
            </div>
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
