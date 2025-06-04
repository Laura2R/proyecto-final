<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billete OnubaBus #{{ $transaccion->id }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-size: 14px;
        }
        .billete {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            margin: 0 auto;
            page-break-inside: avoid;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 8px;
        }
        .billete-numero {
            font-size: 18px;
            color: #6b7280;
        }
        .detalles {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }
        .detalle-row {
            display: table-row;
        }
        .detalle-label {
            display: table-cell;
            padding: 10px 15px 10px 0;
            font-weight: bold;
            color: #374151;
            width: 40%;
            font-size: 14px;
        }
        .detalle-value {
            display: table-cell;
            padding: 10px 0;
            color: #1f2937;
            font-size: 14px;
        }
        .ruta {
            background: #eff6ff;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }
        .origen-destino {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            padding: 25px;
            margin: 6px 0;
            text-align: left;
        }
        .precio {
            background: #dcfce7;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 15px 0;
        }
        .precio-valor {
            font-size: 24px;
            font-weight: bold;
            color: #166534;
        }
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #d1d5db;
        }
        .qr-title {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 11px;
            color: #6b7280;
        }
        .valido {
            background: #fef3c7;
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            text-align: center;
            font-size: 12px;
            color: #92400e;
            font-weight: bold;
        }
        .usuario-info {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 12px;
            margin: 15px 0;
            font-size: 12px;
            color: #374151;
        }
        .two-column {
            display: table;
            width: 100%;
            margin: 15px 0;
        }
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
    </style>
</head>
<body>
<div class="billete">
    <div class="header">
        <div class="logo">OnubaBus</div>
        <div class="billete-numero">Billete #{{ $transaccion->id }}</div>
        <div style="font-size: 14px; color: #6b7280; margin-top: 8px;">
            {{ $transaccion->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="usuario-info">
        <strong>Propietario:</strong> {{ $transaccion->user->name }}<br>
        <strong>Email:</strong> {{ $transaccion->user->email }}
    </div>

    <div class="ruta">
        <div class="origen-destino">Origen: {{ $detalles['origen'] }}</div>
        <div class="origen-destino">Destino: {{ $detalles['destino'] }}</div>
    </div>

    <div class="two-column">
        <div class="column">
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
        </div>
        <div class="column">
            <div class="qr-section">
                <div class="qr-title">Escanea para ver billete completo</div>
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" style="width: 120px; height: 120px; border: 1px solid #e5e7eb; border-radius: 6px;" />
            </div>
        </div>
    </div>

    <div class="precio">
        <div style="font-size: 14px; color: #166534; margin-bottom: 6px;">Total Pagado</div>
        <div class="precio-valor">{{ number_format($transaccion->monto / 100, 2) }} EUR</div>
    </div>

    <div class="valido">
        VALIDO: 90 minutos desde la compra para transbordos
    </div>

    <div class="footer">
        <p style="font-size: 12px;"><strong>OnubaBus</strong> - Sistema de Transporte Publico de Huelva</p>
        <p>Billete valido segun condiciones generales de transporte</p>
        <p style="color: #ef4444; font-weight: bold;">BILLETE PERSONAL E INTRANSFERIBLE</p>
    </div>
</div>
</body>
</html>
