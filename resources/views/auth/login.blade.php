@extends('layouts.app')

@section('title', 'Iniciar Sesión - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h2>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>

                    @if (route('password.request', [], false))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif

                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white py-2 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-900 transition">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">¿No tienes cuenta?</p>
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Regístrate aquí</a>
            </div>
        </div>
    </section>
@endsection
