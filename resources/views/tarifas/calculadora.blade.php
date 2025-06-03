@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Calculadora de Tarifas</h2>

        {{-- AGREGAR ESTA SECCIÓN DE MENSAJES --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>¡Éxito!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>¡Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>¡Errores de validación!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Formulario de selección de ruta --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5>Selecciona tu ruta</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tarifas.calculadora') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="linea_id">Línea:</label>
                            <select name="linea_id" id="linea_id" class="form-control" required>
                                <option value="">Selecciona una línea</option>
                                @foreach($lineas as $linea)
                                    <option value="{{ $linea->id_linea }}"
                                        {{ request('linea_id') == $linea->id_linea ? 'selected' : '' }}>
                                        {{ $linea->codigo }} - {{ $linea->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($nucleos->count() > 0)
                            <div class="col-md-4">
                                <label for="nucleo_origen">Origen:</label>
                                <select name="nucleo_origen" id="nucleo_origen" class="form-control" required>
                                    <option value="">Selecciona origen</option>
                                    @foreach($nucleos as $nucleo)
                                        <option value="{{ $nucleo->id_nucleo }}"
                                            {{ request('nucleo_origen') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                            {{ $nucleo->nombre }} ({{ $nucleo->zona_nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="nucleo_destino">Destino:</label>
                                <select name="nucleo_destino" id="nucleo_destino" class="form-control" required>
                                    <option value="">Selecciona destino</option>
                                    @foreach($nucleos as $nucleo)
                                        <option value="{{ $nucleo->id_nucleo }}"
                                            {{ request('nucleo_destino') == $nucleo->id_nucleo ? 'selected' : '' }}>
                                            {{ $nucleo->nombre }} ({{ $nucleo->zona_nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Calcular Tarifa</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Mostrar error si existe --}}
        @if(isset($resultado['error']) && $resultado['error'])
            <div class="alert alert-danger">{{ $resultado['error'] }}</div>
        @endif

        {{-- Mostrar resultados y formulario de compra solo si hay datos válidos --}}
        @if(isset($resultado) && !$resultado['error'] && isset($resultado['tarifa']))
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Resultado del Cálculo</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h4>Origen: {{ $resultado['nucleoOrigen']->nombre }}</h4>
                            <p>Zona: {{ $resultado['nucleoOrigen']->zona_nombre }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Destino: {{ $resultado['nucleoDestino']->nombre }}</h4>
                            <p>Zona: {{ $resultado['nucleoDestino']->zona_nombre }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="text-center">
                                @if($resultado['saltos'] == 0)
                                    Misma zona
                                @elseif($resultado['saltos'] == 1)
                                    1 salto (cambio de zona)
                                @else
                                    {{ $resultado['saltos'] }} saltos (cambios de zonas)
                                @endif
                            </h5>
                        </div>
                    </div>

                    {{-- Mostrar precio --}}
                    <div class="alert alert-info text-center">
                        <h4>Precio con Tarjeta Bus: €{{ number_format($resultado['tarifa']->tarjeta, 2) }}</h4>
                        <p class="mb-0">Precio normal: €{{ number_format($resultado['tarifa']->bs, 2) }}
                            <span class="text-success">(Ahorras €{{ number_format($resultado['ahorro'], 2) }})</span>
                        </p>
                    </div>

                    {{-- Formulario de compra solo para usuarios autenticados --}}
                    @auth
                        <form id="pagoForm" method="POST" action="{{ route('procesar.pago') }}">
                            @csrf
                            <input type="hidden" name="nucleo_origen_id"
                                   value="{{ $resultado['nucleoOrigen']->id_nucleo }}">
                            <input type="hidden" name="nucleo_destino_id"
                                   value="{{ $resultado['nucleoDestino']->id_nucleo }}">
                            <input type="hidden" name="saltos" value="{{ $resultado['saltos'] }}">

                            {{-- Selección de tarjetas --}}
                            <div class="mb-4">
                                <h5>Selecciona tu tarjeta:</h5>
                                @forelse(auth()->user()->cards as $card)
                                    <div
                                        class="card mb-2 tarjeta-item @if($card->saldo < $resultado['tarifa']->tarjeta * 100) bg-danger text-white @endif"
                                        data-saldo="{{ $card->saldo }}">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                       name="tarjeta_id" value="{{ $card->id }}"
                                                    @disabled($card->saldo < $resultado['tarifa']->tarjeta * 100)>
                                                <label class="form-check-label">
                                                    <strong>Tarjeta #{{ $card->id }}</strong> -
                                                    Saldo: €{{ number_format($card->saldo / 100, 2) }}
                                                    @if($card->saldo < $resultado['tarifa']->tarjeta * 100)
                                                        <span class="badge bg-danger ms-2">Saldo insuficiente</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-warning">
                                        No tienes tarjetas activas.
                                        <a href="{{ route('cards.create') }}" class="btn btn-primary btn-sm">Crear
                                            tarjeta</a>
                                    </div>
                                @endforelse
                            </div>

                            @if(auth()->user()->cards->count() > 0)
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        Comprar Billete - €{{ number_format($resultado['tarifa']->tarjeta, 2) }}
                                    </button>
                                </div>
                            @endif
                        </form>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>¿Quieres comprar este billete?</h5>
                            <p>Debes iniciar sesión para poder comprar billetes con tu tarjeta de transporte.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">Iniciar Sesión</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">Registrarse</a>
                        </div>
                    @endauth
                </div>
            </div>
        @endif

        <div class="alert alert-info">
            <strong>Información:</strong> Con la tarjeta de transporte puedes realizar transbordos
            por solo 0,50€ en un máximo de 90 minutos.
        </div>
    </div>

    {{-- JavaScript simplificado solo para validación --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('pagoForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const tarjetaSeleccionada = document.querySelector('input[name="tarjeta_id"]:checked');
                    if (!tarjetaSeleccionada) {
                        e.preventDefault();
                        alert('Por favor selecciona una tarjeta para continuar');
                        return false;
                    }
                });
            }
        });
    </script>

@endsection
