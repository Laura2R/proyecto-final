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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Código</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operadores</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($lineas as $linea)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $linea->codigo }}
                        </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $linea->nombre }}</div>
                                    <!-- Mostrar operadores debajo del nombre solo en móvil -->
                                    <div class="md:hidden text-xs text-gray-500 mt-1">{{ $linea->operadores }}</div>
                                </td>
                                <td class="hidden md:table-cell px-4 py-4">
                                    <div class="text-sm text-gray-900">{{ $linea->operadores }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col sm:flex-row gap-2 justify-center">
                                        <a href="/paradas/filtro-linea?linea_id={{ $linea->id_linea }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Paradas
                                        </a>
                                        <a href="/horarios?linea_id={{ $linea->id_linea }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Horarios
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3 md:colspan-4" class="px-6 py-4 text-center text-gray-500">
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
            </div>



            <!-- Paginación -->
            @if($lineas->hasPages())
                <div class="mt-6">
                    {{ $lineas->links('pagination.simple') }}
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
