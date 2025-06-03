<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Billete de Viaje #{{ $transaccion->id }}</title>
    <style>
        .header {
            border-bottom: 2px solid #333;
            padding: 20px;
            position: relative;
        }

        .qr-code {
            position: absolute;
            right: 20px;
            top: 20px;
            width: 100px;
        }

        .detalles-viaje {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        td, th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="header">
    <h2>Billete de Viaje #{{ $transaccion->id }}</h2>
    <p>Fecha: {{ $transaccion->created_at->format('d/m/Y H:i') }}</p>
</div>

<div class="detalles-viaje">
    <h3>Detalles del Viaje:</h3>
    <table>
        <tr>
            <th>Origen</th>
            <td>{{ $detalles['origen'] }}</td>
        </tr>
        <tr>
            <th>Destino</th>
            <td>{{ $detalles['destino'] }}</td>
        </tr>
        <tr>
            <th>Zonas</th>
            <td>{{ $detalles['saltos'] }} saltos</td>
        </tr>
        <tr>
            <th>Precio</th>
            <td>€{{ number_format($transaccion->monto / 100, 2) }}</td>
        </tr>
        <tr>
            <th>Método de Pago</th>
            <td>{{ $transaccion->metodo === 'normal' ? 'Pago Tradicional' : 'Tarjeta Bus' }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    <small>Válido por 90 minutos desde la compra - Código QR: {{ $qrData }}</small>
</div>
</body>
</html>
