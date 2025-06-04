@extends('layouts.app')

@section('title', 'Mis Billetes - OnubaBus')

@section('content')
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">🎫 Mis Billetes</h1>
            <p class="text-xl">Accede rápidamente a todos tus billetes de transporte</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
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
                                            <div class="text-2xl mr-3">🎫</div>
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
                                        <a href="{{ route('billete.descargar', ['transaccion' => $billete->id]) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                                            📥 Descargar PDF
                                        </a>
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
                    <div class="text-gray-400 text-6xl mb-4">🎫</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes billetes</h3>
                    <p class="text-gray-500 mb-4">Compra tu primer billete usando la calculadora de tarifas</p>
                    <a href="{{ route('tarifas.calculadora') }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        Comprar Billete
                    </a>
                </div>
            @endif
        </div>
    </section>
@endsection
