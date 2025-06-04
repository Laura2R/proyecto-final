@extends('layouts.app')

@section('title', 'Mis Paradas Favoritas - OnubaBus')

@section('content')
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">⭐ Mis Paradas Favoritas</h1>
            <p class="text-xl">Accede rápidamente a tus paradas de autobús favoritas</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            @if($paradasFavoritas->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Tus Paradas Favoritas</h2>
                        <p class="text-sm text-gray-600 mt-1">Total: {{ $paradasFavoritas->total() }} paradas</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                        @foreach($paradasFavoritas as $parada)
                            <div class="border-l-4 border-green-500 pl-4 bg-green-50 rounded p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <a href="{{ route('paradas.show', $parada->id_parada) }}"
                                           class="text-green-600 hover:underline font-medium text-lg">
                                            {{ $parada->nombre }}
                                        </a>
                                        <div class="mt-1 text-sm text-gray-600">
                                            📍 {{ $parada->nucleo->nombre ?? '-' }}
                                            ({{ $parada->municipio->nombre ?? '-' }})
                                        </div>
                                        @if($parada->zona)
                                            <div class="mt-1 text-xs text-gray-500">
                                                🗺️ Zona: {{ $parada->zona->nombre }}
                                            </div>
                                        @endif
                                    </div>
                                    <button onclick="toggleFavoritoParada({{ $parada->id_parada }})"
                                            class="text-red-600 hover:text-red-800 ml-2">
                                        ❌
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($paradasFavoritas->hasPages())
                    <div class="mt-6">
                        {{ $paradasFavoritas->links('pagination.simple') }}
                    </div>
                @endif
            @else
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <div class="text-gray-400 text-6xl mb-4">🚏</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes paradas favoritas</h3>
                    <p class="text-gray-500 mb-4">Explora las paradas y marca tus favoritas para acceder rápidamente</p>
                    <a href="/paradas/filtro" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Buscar Paradas
                    </a>
                </div>
            @endif
        </div>
    </section>

    <script>
        async function toggleFavoritoParada(paradaId) {
            try {
                const response = await fetch('{{ route("favoritos.toggle.parada") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ parada_id: paradaId })
                });

                const data = await response.json();

                if (data.success) {
                    location.reload(); // Recargar para actualizar la lista
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    </script>
@endsection
