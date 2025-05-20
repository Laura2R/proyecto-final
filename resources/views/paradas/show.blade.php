<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $parada->nombre }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        #mapa {
            height: 400px;
            width: 100%;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100 py-8">
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">{{ $parada->nombre }}</h1>

        <!-- Mapa Container -->
        <div id="mapa" class="mb-6"></div>

        <!-- Detalles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p><span class="font-semibold">ID:</span> {{ $parada->id_parada }}</p>
                <p><span class="font-semibold">Núcleo:</span> {{ $parada->nucleo->nombre }}</p>
                <p><span class="font-semibold">Municipio:</span> {{ $parada->nucleo->municipio->nombre }}</p>
            </div>
            <div>
                <p><span class="font-semibold">Zona:</span> {{ $parada->zona->nombre ?? '-' }}</p>
                <p><span class="font-semibold">Latitud:</span> {{ $parada->latitud }}</p>
                <p><span class="font-semibold">Longitud:</span> {{ $parada->longitud }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">&larr; Volver</a>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inicializar mapa
    const mapa = L.map('mapa').setView([{{ $parada->latitud }}, {{ $parada->longitud }}], 15);

    // Añadir capa de OpenStreetMap
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(mapa);

    // Añadir marcador
    L.marker([{{ $parada->latitud }}, {{ $parada->longitud }}])
        .addTo(mapa)
        .bindPopup(`
                <b>{{ $parada->nombre }}</b><br>
                Lat: {{ $parada->latitud }}<br>
                Lng: {{ $parada->longitud }}
        `);
</script>
</body>
</html>
