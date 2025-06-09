@extends('layouts.app')

@section('title', 'Editar Usuario - Admin')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fas fa-user-edit"></i> Editar Usuario</h1>
            <p class="text-xl">Modificar información de {{ $user->name }}</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nombre -->
                    <div class="mb-6">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-user"></i> Nombre Completo
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock"></i> Nueva Contraseña (opcional)
                        </label>
                        <input id="password" type="password" name="password"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">Deja en blanco para mantener la contraseña actual</p>
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock"></i> Confirmar Nueva Contraseña
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Es Admin -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_admin" value="1"
                                   {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">
                                <i class="fas fa-user-shield"></i> Es administrador
                            </span>
                        </label>
                    </div>

                    <!-- Información adicional -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-700 mb-2">
                            <i class="fas fa-info-circle"></i> Información del Usuario
                        </h3>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-calendar-alt"></i> Registrado: {{ $user->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-credit-card"></i> Tarjetas: {{ $user->cards->count() }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-user-tag"></i> Estado:
                            <span
                                class="px-2 py-1 text-xs rounded-full {{ $user->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $user->is_admin ? 'Administrador' : 'Usuario' }}
                            </span>
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-between">
                        <a href="{{ route('admin.users') }}"
                           class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save"></i> Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
