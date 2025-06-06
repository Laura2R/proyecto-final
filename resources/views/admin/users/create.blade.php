@extends('layouts.app')

@section('title', 'Crear Usuario - Admin')

@section('content')
    <section class="bg-gradient-to-r from-green-600 to-green-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fas fa-user-plus"></i> Crear Usuario</h1>
            <p class="text-xl">Añadir nuevo usuario al sistema</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <!-- Nombre -->
                    <div class="mb-6">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-user"></i> Nombre Completo
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock"></i> Contraseña
                        </label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('password') border-red-500 @enderror">
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock"></i> Confirmar Contraseña
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Es Admin -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-600">
                                <i class="fas fa-user-shield"></i> Hacer administrador
                            </span>
                        </label>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-between">
                        <a href="{{ route('admin.users') }}"
                           class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-save"></i> Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
