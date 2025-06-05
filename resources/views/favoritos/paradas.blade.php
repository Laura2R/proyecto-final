@extends('layouts.app')

@section('title', 'Mis Paradas Favoritas - OnubaBus')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fa-solid fa-star" style="color: #FFD43B;"></i> Mis Paradas Favoritas</h1>
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
                                            <i class="fas fa-map-marker-alt"></i> {{ $parada->nucleo->nombre ?? '-' }}
                                            ({{ $parada->municipio->nombre ?? '-' }})
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('paradas.show', $parada->id_parada) }}"
                                               class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                                                <i class="fa-solid fa-eye"></i> &nbsp;Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                    <button onclick="quitarFavorito('parada', {{ $parada->id_parada }})"
                                            class="text-red-600 hover:text-red-800 ml-2 text-lg">
                                        <i class="fa-solid fa-xmark"></i>
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
                    <div class="text-gray-400 text-6xl mb-4"><i class="fa-solid fa-street-view"></i></div>
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
