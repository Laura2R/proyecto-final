@extends('layouts.app')

@section('title', 'Servicio No Disponible - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">503</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Servicio No Disponible</h3>
            </div>

            <p class="text-gray-600 mb-6">
                El servicio está temporalmente fuera de línea por mantenimiento. Volveremos pronto. Gracias por tu paciencia.
            </p>

            <div class="space-y-3">
                <a href="{{ url('/') }}"
                   class="w-full bg-gray-100 text-gray-600 py-2 px-4 rounded-lg font-semibold hover:bg-gray-200 transition inline-block">
                    Volver al Inicio
                </a>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Síguenos en redes sociales para actualizaciones en tiempo real
                </p>
            </div>

            <!-- Contador de tiempo opcional -->
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-600">
                    Tiempo estimado de restablecimiento: <span class="font-semibold">En breve</span>
                </p>
            </div>
        </div>
    </section>
@endsection
