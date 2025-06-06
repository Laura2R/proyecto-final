@extends('layouts.app')

@section('title', 'No Autorizado - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">401</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-4">No Autorizado</h3>
            </div>

            <p class="text-gray-600 mb-6">
                Necesitas iniciar sesión para acceder a esta página. Por favor, identifícate con tus credenciales.
            </p>

            <div class="space-y-3">
                <a href="{{ route('login') }}"
                   class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-block">
                    Iniciar Sesión
                </a>

                <a href="{{ route('register') }}"
                   class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-block">
                    Crear Cuenta
                </a>

                <a href="{{ url('/') }}"
                   class="w-full bg-gray-100 text-gray-600 py-2 px-4 rounded-lg font-semibold hover:bg-gray-200 transition inline-block">
                    Volver al Inicio
                </a>
            </div>
        </div>
    </section>
@endsection
