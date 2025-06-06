@extends('layouts.app')

@section('title', 'Demasiadas Solicitudes - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">429</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Demasiadas Solicitudes</h3>
            </div>

            <p class="text-gray-600 mb-6">
                Has realizado demasiadas solicitudes en poco tiempo. Por favor, espera unos momentos antes de intentar nuevamente.
            </p>

            <div class="space-y-3">


                <button onclick="window.location.reload()"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition">
                    Reintentar
                </button>

                <a href="{{ url('/') }}"
                   class="w-full bg-gray-100 text-gray-600 py-2 px-4 rounded-lg font-semibold hover:bg-gray-200 transition inline-block">
                    Volver al Inicio
                </a>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Este límite ayuda a mantener el servicio estable para todos
                </p>
            </div>
        </div>
    </section>
@endsection
