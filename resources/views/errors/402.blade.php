@extends('layouts.app')

@section('title', 'Pago Requerido - OnubaBus')

@section('content')
    <section class="py-16 bg-white">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">402</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Pago Requerido</h3>
            </div>

            <p class="text-gray-600 mb-6">
                Se requiere un pago para acceder a este contenido o servicio. Verifica tu suscripción o realiza el pago correspondiente.
            </p>

            <div class="space-y-1">

                <a href="{{ url('/') }}"
                   class="w-full bg-gray-100 text-gray-600 py-2 px-4 rounded-lg font-semibold hover:bg-gray-200 transition inline-block">
                    Volver al Inicio
                </a>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    ¿Problemas con tu pago? Contacta con soporte
                </p>
            </div>
        </div>
    </section>
@endsection
