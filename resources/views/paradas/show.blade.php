<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $parada->nombre }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        #mapa-parada {
            height: 350px;
            width: 100%;
            border-radius: 8px;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 py-8">
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">{{ $parada->nombre }}</h1>
    <div id="mapa-parada"></div>
    <ul class="mt-6 space-y-2">
        <li><strong>ID Parada:</strong> {{ $parada->id_parada }}</li>
        <li><strong>Núcleo:</strong> {{ $parada->nucleo->nombre ?? '-' }}</li>
        <li><strong>Municipio:</strong> {{ $parada->nucleo->municipio->nombre ?? '-' }}</li>
        <li><strong>Zona:</strong> {{ $parada->zona->nombre ?? '-' }}</li>
        @if($parada->observaciones)
            <p><span class="font-semibold">Observaciones:</span> {{ $parada->observaciones }}</p>
        @endif
    </ul>
    <div class="mt-6">
        <a href="{{ url()->previous() }}" class="text-blue-700 hover:underline">&larr; Volver</a>
    </div>
</div>

<script>
    window.mapaParadaData = {
        nombre: @json($parada->nombre),
        id: @json($parada->id_parada),
        lat: {{ $parada->latitud }},
        lng: {{ $parada->longitud }}
    };
</script>
<script src="{{ asset('js/mapaParada.js') }}"></script>
<script async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMapaParada">
</script>
</body>
</html>
