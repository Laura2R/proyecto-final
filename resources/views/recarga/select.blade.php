@extends('layouts.app')

@section('title', 'Seleccionar Tarjeta - OnubaBus')

@section('content')
    <section class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-6">
                <a href="{{ route('cards.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← Volver a Mis Tarjetas
                </a>
            </div>

            <h2 class="text-4xl font-bold text-gray-800 mb-8">Selecciona la tarjeta a recargar</h2>

            @if(session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow">
                    {{ session('status') }}
                </div>
            @endif

            @if($cards->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($cards as $card)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                            <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-sm opacity-80">OnubaBus Card</p>
                                        <p class="text-xl font-bold">#{{ str_pad($card->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm opacity-80">Saldo</p>
                                        <p class="text-2xl font-bold">€{{ $card->saldo_formateado }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-end">
                                    <p class="text-sm opacity-70">{{ auth()->user()->name }}</p>
                                    <p class="text-sm opacity-70">{{ $card->created_at->format('m/y') }}</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <a href="{{ route('recarga.form', $card->id) }}"
                                   class="block w-full bg-gradient-to-r from-green-600 to-green-800 text-white px-6 py-3 text-center rounded-lg
                                      hover:from-green-700 hover:to-green-900 transition font-semibold">
                                    <i class="fa-regular fa-credit-card"></i> Recargar esta tarjeta
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">No tienes tarjetas para recargar</h3>
                    <p class="text-gray-600 mb-8">Necesitas crear al menos una tarjeta antes de poder recargarla</p>

                    <a href="{{ route('cards.create') }}"
                       class="inline-block bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg
                          font-semibold hover:from-blue-700 hover:to-blue-900 transition shadow-lg">
                        Crear Nueva Tarjeta
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
