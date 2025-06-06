@extends('layouts.app')

@section('title', 'Panel de Administración - OnubaBus')

@section('content')
    <section class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4"><i class="fas fa-shield-alt"></i> Panel de Administración</h1>
            <p class="text-xl">Gestión del sistema OnubaBus</p>
        </div>
    </section>

    <section class="py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <div class="text-3xl text-blue-600 mr-4"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</div>
                            <div class="text-gray-600">Usuarios Totales</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <div class="text-3xl text-purple-600 mr-4"><i class="fas fa-user-shield"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ $totalAdmins }}</div>
                            <div class="text-gray-600">Administradores</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <div class="text-3xl text-green-600 mr-4"><i class="fas fa-credit-card"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">{{ $totalCards }}</div>
                            <div class="text-gray-600">Tarjetas Activas</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <div class="text-3xl text-yellow-600 mr-4"><i class="fas fa-coins"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800">€{{ number_format($totalBalance, 2) }}</div>
                            <div class="text-gray-600">Saldo Total</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Acciones Rápidas</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users') }}"
                       class="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition text-center">
                        <div class="text-2xl mb-2"><i class="fas fa-users"></i></div>
                        <div class="font-medium">Gestionar Usuarios</div>
                    </a>

                    <a href="{{ route('admin.users.create') }}"
                       class="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition text-center">
                        <div class="text-2xl mb-2"><i class="fas fa-user-plus"></i></div>
                        <div class="font-medium">Crear Usuario</div>
                    </a>

                    <a href="{{ route('home') }}"
                       class="bg-gray-600 text-white p-4 rounded-lg hover:bg-gray-700 transition text-center">
                        <div class="text-2xl mb-2"><i class="fas fa-home"></i></div>
                        <div class="font-medium">Volver al Inicio</div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
