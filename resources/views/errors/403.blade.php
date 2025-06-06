@extends('layouts.app')

@section('title', 'Acceso Prohibido - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">403</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Acceso Prohibido</h3>
            </div>

            <p class="text-gray-600 mb-6">
                No tienes permisos para acceder a esta página. Si crees que esto es un error, contacta con el administrador.
            </p>

            <div class="space-y-3">
                <a href="{{ url('/') }}"
                   class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition inline-block">
                    Volver al Inicio
                </a>

                @auth
                    <a href="{{ url()->previous() }}"
                       class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-300 transition inline-block">
                        Página Anterior
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-semibold hover:bg-gray-300 transition inline-block">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection
