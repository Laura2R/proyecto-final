<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billete OnubaBus #{{ $transaccion->id }}</title>
    <style>
        @page {
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            font-size: 16px;
            line-height: 1.4;
        }
        .billete {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 10px;
        }
        .billete-numero {
            font-size: 20px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .fecha {
            font-size: 16px;
            color: #6b7280;
        }
        .usuario-info {
            background: #f3f4f6;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #374151;
        }
        .ruta {
            background: #eff6ff;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .origen-destino {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            flex: 1;
        }
        .flecha {
            font-size: 24px;
            color: #3b82f6;
            margin: 0 20px;
        }
        .detalles {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .detalle-row {
            display: table-row;
        }
        .detalle-label {
            display: table-cell;
            padding: 12px 20px 12px 0;
            font-weight: bold;
            color: #374151;
            width: 40%;
            font-size: 16px;
        }
        .detalle-value {
            display: table-cell;
            padding: 12px 0;
            color: #1f2937;
            font-size: 16px;
        }
        .precio {
            background: #dcfce7;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .precio-label {
            font-size: 16px;
            color: #166534;
            margin-bottom: 8px;
        }
        .precio-valor {
            font-size: 28px;
            font-weight: bold;
            color: #166534;
        }
        .valido {
            background: #fef3c7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            font-size: 16px;
            color: #92400e;
            font-weight: bold;
        }
        .info-importante {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            font-size: 14px;
            color: #6b7280;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="billete">
    <div class="header">
        <div class="logo">OnubaBus</div>
        <div class="billete-numero">Billete #{{ $transaccion->id }}</div>
        <div class="fecha">{{ $transaccion->created_at->format('d/m/Y H:i') }}</div>
    </div>

    <div class="usuario-info">
        <strong>Propietario:</strong> {{ $transaccion->user->name }}<br>
        <strong>Email:</strong> {{ $transaccion->user->email }}
    </div>

    <div class="ruta">
        <div class="origen-destino">{{ $detalles['origen'] }}</div>
        <div class="flecha">→</div>
        <div class="origen-destino">{{ $detalles['destino'] }}</div>
    </div>

    <div class="detalles">
        <div class="detalle-row">
            <div class="detalle-label">Zonas:</div>
            <div class="detalle-value">{{ $detalles['saltos'] }} salto(s)</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Metodo de Pago:</div>
            <div class="detalle-value">{{ $transaccion->metodo === 'tarjeta' ? 'Tarjeta Bus' : 'Tradicional' }}</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Precio Original:</div>
            <div class="detalle-value">{{ number_format($detalles['precio_original'], 2) }} EUR</div>
        </div>
        <div class="detalle-row">
            <div class="detalle-label">Ahorro:</div>
            <div class="detalle-value" style="color: #059669; font-weight: bold;">{{ number_format($detalles['ahorro'], 2) }} EUR</div>
        </div>
    </div>

    <div class="precio">
        <div class="precio-label">Total Pagado</div>
        <div class="precio-valor">{{ number_format($transaccion->monto / 100, 2) }} EUR</div>
    </div>

    <div class="valido">
        VALIDO: 90 minutos desde la compra para transbordos
    </div>

    <div class="info-importante">
        <p style="color: #ef4444; font-weight: bold; margin: 0;">
            BILLETE PERSONAL E INTRANSFERIBLE
        </p>
    </div>

    <div class="footer">
        <p><strong>OnubaBus</strong> - Sistema de Transporte Publico de Huelva</p>
        <p>Billete valido segun condiciones generales de transporte</p>
    </div>
</div>
</body>
</html>
