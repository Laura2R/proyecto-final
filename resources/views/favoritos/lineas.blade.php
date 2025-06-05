@extends('layouts.app')

@section('title', 'Mis Líneas Favoritas - OnubaBus')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">⭐ Mis Líneas Favoritas</h1>
            <p class="text-xl">Accede rápidamente a tus líneas de autobús favoritas</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            @if($lineasFavoritas->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Tus Líneas Favoritas</h2>
                        <p class="text-sm text-gray-600 mt-1">Total: {{ $lineasFavoritas->total() }} líneas</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($lineasFavoritas as $linea)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ $linea->codigo }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $linea->nombre }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $linea->operadores }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col sm:flex-row gap-2 justify-center">
                                            <a href="/paradas/filtro-linea?linea_id={{ $linea->id_linea }}"
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                                                🚏 Ver Paradas
                                            </a>
                                            <a href="/horarios?linea_id={{ $linea->id_linea }}"
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                                                <i class="fas fa-clock"></i> Ver Horarios
                                            </a>
                                            <button onclick="quitarFavorito('linea', {{ $linea->id_linea }})"
                                                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition">
                                                ❌ Quitar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($lineasFavoritas->hasPages())
                    <div class="mt-6">
                        {{ $lineasFavoritas->links('pagination.simple') }}
                    </div>
                @endif
            @else
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <div class="text-gray-400 text-6xl mb-4">⭐</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes líneas favoritas</h3>
                    <p class="text-gray-500 mb-4">Explora las líneas y marca tus favoritas para acceder rápidamente</p>
                    <a href="/lineas" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Ver Todas las Líneas
                    </a>
                </div>
            @endif
        </div>
    </section>

    <script>
        async function quitarFavorito(tipo, id) {
            const url = tipo === 'linea' ? '/favoritos/linea' : '/favoritos/parada';
            const data = tipo === 'linea' ? { linea_id: id } : { parada_id: id };

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
@endsection
