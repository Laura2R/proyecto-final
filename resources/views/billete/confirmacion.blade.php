@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{-- Mensaje de éxito --}}
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">🎉 ¡Compra Realizada con Éxito!</h4>
                    <p class="mb-0">Tu billete ha sido procesado correctamente y está listo para descargar.</p>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📄 Detalles de tu Billete</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Número de Billete:</th>
                                        <td><span class="badge bg-secondary">#{{ $transaccion->id }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Origen:</th>
                                        <td>{{ $detalles['origen'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Destino:</th>
                                        <td>{{ $detalles['destino'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Saltos:</th>
                                        <td>{{ $detalles['saltos'] }} zona(s)</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Precio Pagado:</th>
                                        <td><strong
                                                class="text-success">€{{ number_format($transaccion->monto / 100, 2) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Precio Normal:</th>
                                        <td><s>€{{ number_format($detalles['precio_original'], 2) }}</s></td>
                                    </tr>
                                    <tr>
                                        <th>Ahorro:</th>
                                        <td><span
                                                class="text-success">€{{ number_format($detalles['ahorro'], 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fecha:</th>
                                        <td>{{ $transaccion->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>📱 Importante:</strong> Este billete es válido por 90 minutos desde la compra y
                            permite transbordos.
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="{{ route('billete.descargar', ['transaccion' => $transaccion, 'download' => 'true']) }}"
                               class="btn btn-success btn-lg">
                                📥 Descargar Billete PDF
                            </a>
                            <a href="{{ route('tarifas.calculadora') }}"
                               class="btn btn-outline-primary">
                                🔙 Comprar Otro Billete
                            </a>
                            <a href="{{ route('dashboard') }}"
                               class="btn btn-outline-secondary">
                                🏠 Ir al Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
