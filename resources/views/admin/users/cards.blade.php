@extends('layouts.app')

@section('title', 'Tarjetas de ' . $user->name . ' - Admin')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fas fa-credit-card"></i> Tarjetas de {{ $user->name }}</h1>
            <p class="text-xl">Gestión de tarjetas del usuario</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Mensajes -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-credit-card"></i> Tarjetas de {{ $user->name }}
                    </h2>
                    <p class="text-gray-600">
                        <i class="fas fa-envelope"></i> {{ $user->email }}
                    </p>
                </div>
                <a href="{{ route('admin.users') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="fas fa-arrow-left"></i> Volver a Usuarios
                </a>
            </div>

            @if($cards->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Tarjetas del Usuario</h2>
                        <p class="text-sm text-gray-600 mt-1">Total: {{ $cards->total() }} tarjetas</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarjeta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creada</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cards as $card)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-2xl mr-3"><i class="fas fa-credit-card text-blue-600"></i></div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">#{{ $card->numero_tarjeta }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $card->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-600">
                                            <i class="fas fa-euro-sign"></i>{{ number_format($card->saldo / 100, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <i class="fas fa-calendar-alt"></i> {{ $card->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('admin.users.cards.edit', [$user, $card]) }}"
                                               class="text-blue-600 hover:text-blue-900" title="Editar saldo">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.users.cards.destroy', [$user, $card]) }}" method="POST"
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta tarjeta? Se perderá el saldo de €{{ number_format($card->saldo / 100, 2) }}')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar tarjeta">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación personalizada -->
                @if($cards->hasPages())
                    <div class="mt-6">
                        {{ $cards->links('pagination.simple') }}
                    </div>
                @endif
            @else
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <div class="text-gray-400 text-6xl mb-4"><i class="fas fa-credit-card"></i></div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Sin tarjetas</h3>
                    <p class="text-gray-500">Este usuario no tiene tarjetas registradas</p>
                </div>
            @endif
        </div>
    </section>
@endsection
