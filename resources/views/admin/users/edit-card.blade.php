@extends('layouts.app')

@section('title', 'Editar Tarjeta - Admin')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fas fa-edit"></i> Editar Tarjeta</h1>
            <p class="text-xl">Modificar saldo de la tarjeta #{{ $card->numero_tarjeta }}</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <!-- Información actual -->
                <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-2">
                        <i class="fas fa-info-circle"></i> Información Actual
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Usuario:</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email:</p>
                            <p class="font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Número de Tarjeta:</p>
                            <p class="font-medium">#{{ $card->numero_tarjeta }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Saldo Actual:</p>
                            <p class="font-medium text-green-600">€{{ number_format($card->saldo / 100, 2) }}</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.users.cards.update', [$user, $card]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Nuevo Saldo -->
                    <div class="mb-6">
                        <label for="saldo" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-euro-sign"></i> Nuevo Saldo (€)
                        </label>
                        <input id="saldo" type="number" name="saldo" step="0.01" min="0" max="999.99"
                               value="{{ old('saldo', number_format($card->saldo / 100, 2)) }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('saldo') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">Máximo: €999.99</p>
                        @error('saldo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Motivo del cambio -->
                    <div class="mb-6">
                        <label for="motivo" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-comment"></i> Motivo del Cambio
                        </label>
                        <input id="motivo" type="text" name="motivo" value="{{ old('motivo') }}" required
                               placeholder="Ej: Ajuste por error, Reembolso, Corrección administrativa..."
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 @error('motivo') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">Este motivo quedará registrado en los logs del sistema</p>
                        @error('motivo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Advertencia -->
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-yellow-800 font-semibold">Advertencia</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Esta acción modificará directamente el saldo de la tarjeta.
                                    El cambio quedará registrado en los logs del sistema con tu usuario administrador.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-between">
                        <a href="{{ route('admin.users.cards', $user) }}"
                           class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save"></i> Actualizar Saldo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
