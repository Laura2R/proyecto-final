@extends('layouts.app')

@section('title', 'Mis Billetes - OnubaBus')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-28 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fa-solid fa-ticket"></i> Mis Billetes</h1>
            <p class="text-xl">Accede a todos tus billetes de transporte</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Mensajes de estado -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0 text-green-400">
                            <i class="fa-solid fa-circle-check"></i>
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

            @if($billetes->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Tus Billetes</h2>
                        <p class="text-sm text-gray-600 mt-1">Total: {{ $billetes->total() }} billetes</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Billete</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($billetes as $billete)
                                @php
                                    $detalles = json_decode($billete->detalles, true);
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-2xl mr-3"><i class="fa-solid fa-ticket"></i></div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">#{{ $billete->id }}</div>
                                                <div class="text-xs text-gray-500">{{ $detalles['saltos'] }} salto(s)</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">{{ $detalles['origen'] }}</div>
                                            <div class="text-gray-500">↓</div>
                                            <div class="font-medium">{{ $detalles['destino'] }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $billete->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $billete->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-600">€{{ number_format($billete->monto / 100, 2) }}</div>
                                        <div class="text-xs text-gray-500">Ahorro: €{{ number_format($detalles['ahorro'], 2) }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <div class="flex flex-col sm:flex-row gap-2 justify-center">
                                            <a href="{{ route('billete.mostrar', $billete->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                                                <i class="fa-regular fa-eye"></i>&nbsp; Ver Billete
                                            </a>
                                            <a href="{{ route('billete.descargar', $billete->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition">
                                                <i class="fa-solid fa-file-arrow-down"></i>&nbsp; Descargar Billete
                                            </a>
                                            <form action="{{ route('billete.destroy', $billete->id) }}" method="POST"
                                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar este billete? Esta acción no se puede deshacer.');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition">
                                                    <i class="fa-solid fa-trash-can"></i>&nbsp; Eliminar
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

                @if($billetes->hasPages())
                    <div class="mt-6">
                        {{ $billetes->links('pagination.simple') }}
                    </div>
                @endif
            @else
                <div class="bg-white p-8 rounded-lg shadow text-center">
                    <div class="text-gray-400 text-6xl mb-4"><i class="fa-solid fa-ticket"></i></div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes billetes</h3>
                    <p class="text-gray-500 mb-4">Compra tu primer billete usando la calculadora de tarifas</p>
                    <a href="{{ route('tarifas.calculadora') }}" class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-8 py-3 rounded-lg
                              font-semibold hover:from-blue-700 hover:to-blue-900 transition shadow-lg">
                        Comprar Billete
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
