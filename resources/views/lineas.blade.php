@extends('layouts.app')

@section('title', 'Líneas de Autobús - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Líneas de Autobús</h1>
            <p class="text-xl">Todas las líneas de transporte público de la provincia de Huelva</p>
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
                            <strong>Información:</strong> Estas son todas las líneas de autobús que operan en la red de transporte público OnubaBus.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabla de líneas -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Listado de Líneas</h2>
                    <p class="text-sm text-gray-600 mt-1">Total: {{ $lineas->total() }} líneas disponibles</p>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Línea</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operadores</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lineas as $linea)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-800">{{ $linea->id_linea }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $linea->codigo }}
                            </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $linea->nombre }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $linea->modo }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $linea->operadores }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="/paradas/filtro-linea?linea_id={{ $linea->id_linea }}"
                                       class="text-blue-600 hover:text-blue-900">Ver paradas</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="/horarios?linea_id={{ $linea->id_linea }}"
                                       class="text-green-600 hover:text-green-900">Ver horarios</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">No hay líneas disponibles</p>
                                    <p class="text-sm text-gray-400 mt-1">Las líneas se sincronizarán automáticamente</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($lineas->hasPages())
                <div class="mt-6">
                    {{ $lineas->links() }}
                </div>
            @endif

            <!-- Información adicional -->
            @if($lineas->total() > 0)
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">🚌 Información de Líneas</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <span class="text-blue-500 mr-2">•</span>
                                Cada línea conecta diferentes municipios y núcleos
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-500 mr-2">•</span>
                                Consulta paradas y horarios específicos de cada línea
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-500 mr-2">•</span>
                                Todas las líneas están adaptadas para PMR
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-500 mr-2">•</span>
                                Servicios regulares con diferentes frecuencias
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">📊 Estadísticas</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total de líneas:</span>
                                <span class="font-medium">{{ $lineas->total() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Mostrando en esta página:</span>
                                <span class="font-medium">{{ $lineas->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Líneas por página:</span>
                                <span class="font-medium">{{ $lineas->perPage() }}</span>
                            </div>
                            @if($lineas->hasPages())
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Página actual:</span>
                                    <span class="font-medium">{{ $lineas->currentPage() }} de {{ $lineas->lastPage() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Enlaces relacionados -->
            <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enlaces Relacionados</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/paradas" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🚏</div>
                        <div>
                            <div class="font-medium">Paradas</div>
                            <div class="text-sm text-gray-500">Ver todas las paradas</div>
                        </div>
                    </a>
                    <a href="/horarios" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">🕒</div>
                        <div>
                            <div class="font-medium">Horarios</div>
                            <div class="text-sm text-gray-500">Consultar horarios</div>
                        </div>
                    </a>
                    <a href="{{ route('tarifas.index') }}" class="flex items-center p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="text-blue-600 text-2xl mr-3">💰</div>
                        <div>
                            <div class="font-medium">Tarifas</div>
                            <div class="text-sm text-gray-500">Ver precios</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
