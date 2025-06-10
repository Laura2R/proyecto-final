@extends('layouts.app')

@section('title', 'Recuperar Contraseña - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Recuperar Contraseña</h2>

            <div class="mb-6 text-sm text-gray-600 text-center">
                ¿Olvidaste tu contraseña? No hay problema. Solo déjanos saber tu dirección de email y te enviaremos un enlace para restablecer tu contraseña que te permitirá elegir una nueva.
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition">
                    Enviar Enlace de Recuperación
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">¿Recordaste tu contraseña?</p>
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Volver al inicio de sesión</a>
            </div>
        </div>
    </section>
@endsection
