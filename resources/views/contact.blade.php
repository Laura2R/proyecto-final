@extends('layouts.app')

@section('title', 'Contacto - OnubaBus')

@section('content')
    <!-- Header -->
    <section class="bg-blue-600 text-white px-6 py-20 hover:bg-blue-700 transition">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Contacto</h1>
            <p class="text-xl">Estamos aquí para ayudarte. Ponte en contacto con nosotros.</p>
        </div>
    </section>

    <!-- Contenido Principal -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Formulario de Contacto -->
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-bold mb-6">Envíanos un mensaje</h2>

                    @if(session('success'))
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                            <p class="text-green-700">{{ session('success') }}</p>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm @error('nombre') border-red-500 @enderror">
                                @error('nombre')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm @error('email') border-red-500 @enderror">
                                @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Asunto *</label>
                            <input type="text" name="asunto" value="{{ old('asunto') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm @error('asunto') border-red-500 @enderror">
                            @error('asunto')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje *</label>
                            <textarea name="mensaje" rows="5"
                                      class="w-full rounded-md border-gray-300 shadow-sm @error('mensaje') border-red-500 @enderror">{{ old('mensaje') }}</textarea>
                            @error('mensaje')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                            Enviar Mensaje
                        </button>
                    </form>
                </div>

                <!-- Información de Contacto -->
                <div>
                    <h2 class="text-2xl font-bold mb-6">Información de Contacto</h2>

                    <div class="space-y-6">
                        <!-- Teléfono -->
                        <div class="flex items-start">
                            <div class="text-blue-600 text-2xl mr-4">📞</div>
                            <div>
                                <h3 class="font-semibold mb-1">Teléfono</h3>
                                <p class="text-gray-600">959 123 456</p>
                                <p class="text-sm text-gray-500">Lunes a Viernes: 8:00 - 20:00</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start">
                            <div class="text-blue-600 text-2xl mr-4">📧</div>
                            <div>
                                <h3 class="font-semibold mb-1">Email</h3>
                                <p class="text-gray-600">info@onubabus.es</p>
                                <p class="text-sm text-gray-500">Respuesta en 24-48 horas</p>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="flex items-start">
                            <div class="text-blue-600 text-2xl mr-4"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h3 class="font-semibold mb-1">Oficinas</h3>
                                <p class="text-gray-600">Av. de Andalucía, 123<br>21001 Huelva</p>
                                <p class="text-sm text-gray-500">Lunes a Viernes: 9:00 - 14:00</p>
                            </div>
                        </div>

                        <!-- Emergencias -->
                        <div class="flex items-start">
                            <div class="text-red-600 text-2xl mr-4">🚨</div>
                            <div>
                                <h3 class="font-semibold mb-1">Emergencias</h3>
                                <p class="text-gray-600">112 (Emergencias generales)</p>
                                <p class="text-sm text-gray-500">Disponible 24/7</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ -->
                    <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                        <h3 class="font-semibold mb-4">Preguntas Frecuentes</h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="font-medium">¿Cómo puedo recargar mi tarjeta?</p>
                                <p class="text-gray-600">En cualquiera de nuestros puntos de venta autorizados.</p>
                            </div>
                            <div>
                                <p class="font-medium">¿Hay descuentos para estudiantes?</p>
                                <p class="text-gray-600">Sí, consulta nuestra sección de tarifas especiales.</p>
                            </div>
                            <div>
                                <p class="font-medium">¿Los autobuses son accesibles?</p>
                                <p class="text-gray-600">Toda nuestra flota está adaptada para PMR.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
