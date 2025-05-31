@if ($paginator->hasPages())
    <nav
        class="flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-lg shadow-sm sm:px-6">
        <!-- VERSIÓN MÓVIL: Botones + indicador de páginas -->
        <div class="flex justify-between items-center flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-md">
                    ← Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:bg-gray-50 transition">
                    ← Anterior
                </a>
            @endif

            <!-- Indicador de páginas en móvil -->
            <div class="flex items-center">
                <span
                    class="text-sm font-medium text-gray-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                    {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}
                </span>
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:bg-gray-50 transition">
                    Siguiente →
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-md">
                    Siguiente →
                </span>
            @endif
        </div>

        <!-- VERSIÓN DESKTOP: Información en español + paginación -->
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:flex-1">
            <div>
                <p class="text-sm text-gray-700">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-medium text-blue-600">{{ $paginator->firstItem() }}</span>
                        a
                        <span class="font-medium text-blue-600">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-medium text-blue-600">{{ $paginator->count() }}</span>
                    @endif
                    de
                    <span class="font-medium text-blue-600">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm">
                    {{-- Botón Anterior --}}
                    @if ($paginator->onFirstPage())
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-default rounded-l-md leading-5">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                           class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>
                    @endif

                    {{-- Números de página --}}
                    @foreach ($elements as $element)
                        {{-- Separador "..." --}}
                        @if (is_string($element))
                            <span
                                class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">{{ $element }}</span>
                        @endif

                        {{-- Array de enlaces --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                       class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Botón Siguiente --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                           class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </a>
                    @else
                        <span
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 cursor-default rounded-r-md leading-5">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                      d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
