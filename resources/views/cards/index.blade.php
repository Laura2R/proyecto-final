@extends('layouts.app')

@section('title', 'Mis Tarjetas - OnubaBus')

@section('content')

    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">💳 Mis Tarjetas</h1>
            <p class="text-xl">Crea y gestiona tus tarjetas de transporte</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-4xl font-bold text-gray-800">Mis Tarjetas</h2>
                <a href="{{ route('cards.create') }}"
                   class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-3 rounded-lg
                      font-semibold hover:from-blue-700 hover:to-blue-900 transition shadow-lg">
                    + Nueva Tarjeta
                </a>
            </div>

            @if(session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 shadow">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @forelse($cards as $card)
                    <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-gray-100">
                        <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-4 mb-4 text-white">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-sm opacity-80">OnubaBus Card</p>
                                    <p class="text-lg font-bold">#{{ $card->numero_tarjeta }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm opacity-80">Saldo</p>
                                    <p class="text-2xl font-bold">€{{ $card->saldo_formateado }}</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-end">
                                <p class="text-xs opacity-70">{{ auth()->user()->name }}</p>
                                <p class="text-xs opacity-70">{{ $card->created_at->format('m/y') }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('recarga.form', $card) }}"
                               class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-center rounded-lg
                                  transition font-medium">
                                <i class="fa-regular fa-credit-card"></i> Recargar Tarjeta
                            </a>

                            <form action="{{ route('cards.destroy', $card) }}" method="POST"
                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta tarjeta? Esta acción no se puede deshacer.');"
                                  class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white
                                               px-4 py-2 rounded-lg transition font-medium border border-red-200">
                                    <i class="fa-solid fa-trash-can"></i> Eliminar Tarjeta
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl shadow-lg p-12 text-center">
                        <div class="mb-6">
                            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No tienes tarjetas registradas</h3>
                            <p class="text-gray-600 mb-6">Crea tu primera tarjeta OnubaBus para comenzar a viajar</p>
                        </div>

                        <a href="{{ route('cards.create') }}"
                           class="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg
                              font-semibold hover:from-blue-700 hover:to-blue-900 transition shadow-lg">
                            Crear Mi Primera Tarjeta
                        </a>
                    </div>
                @endforelse
            </div>

            @if($cards->count() > 0)
                <div class="text-center">
                    <a href="{{ route('recarga.select') }}"
                       class="inline-block bg-gradient-to-r from-green-600 to-green-800 text-white px-8 py-3 rounded-lg
                          font-semibold hover:from-green-700 hover:to-green-900 transition shadow-lg mr-4">
                        <i class="fa-regular fa-credit-card"></i> Recargar Tarjetas
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
