@extends('layouts.app')

@section('title', 'Error del Servidor - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">

                <h2 class="text-3xl font-bold text-gray-800 mb-2">500</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Error del Servidor</h3>
            </div>

            <p class="text-gray-600 mb-6">
                Ha ocurrido un error interno en el servidor. Nuestro equipo técnico ha sido notificado y está trabajando para solucionarlo.
            </p>

            <div class="space-y-3">
                <a href="{{ url('/') }}"
                   class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-block">
                    Volver al Inicio
                </a>

                <button onclick="window.location.reload()"
                        class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Intentar de Nuevo
                </button>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Si el problema persiste, contacta con soporte técnico
                </p>
            </div>
        </div>
    </section>
@endsection
