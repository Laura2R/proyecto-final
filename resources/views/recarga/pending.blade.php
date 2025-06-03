@extends('layouts.app')

@section('title', 'Pago Pendiente - OnubaBus')

@section('content')
    <section class="py-8 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
                <div class="text-yellow-500 text-6xl mb-4">⏳</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Pago Pendiente</h2>
                <p class="text-gray-600 mb-6">
                    Tu pago está siendo procesado. Recibirás una confirmación en breve.
                </p>
                <a href="{{ route('cards.index') }}"
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Volver a Mis Tarjetas
                </a>
            </div>
        </div>
    </section>
@endsection
