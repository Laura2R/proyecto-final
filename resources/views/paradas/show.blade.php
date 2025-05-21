<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $parada->nombre }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
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
                <p><span class="font-semibold">Observaciones:</span> {{ $parada->observaciones }}</p>
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

<!-- Datos para el mapa -->
<script>
    // Definir las variables del mapa antes de cargar Google Maps
    const coordenadasParada = {
        lat: {{ $parada->latitud }},
        lng: {{ $parada->longitud }},
        titulo: "{{ $parada->nombre }}",
        id: "{{ $parada->id_parada }}"
    };

    // Función que será llamada por la API de Google Maps
    function initMap() {
        // Crear el mapa
        const mapa = new google.maps.Map(document.getElementById("mapa"), {
            zoom: 15,
            center: coordenadasParada,
            mapTypeControl: true,
            streetViewControl: false
        });

        // Crear el marcador
        const marcador = new google.maps.Marker({
            position: coordenadasParada,
            map: mapa,
            title: coordenadasParada.titulo
        });

        // Crear la ventana de información
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="text-sm">
                    <h3 class="font-bold">${coordenadasParada.titulo}</h3>
                    <p>ID: ${coordenadasParada.id}</p>
                    <p>Coordenadas: ${coordenadasParada.lat.toFixed(6)}, ${coordenadasParada.lng.toFixed(6)}</p>
                </div>
            `
        });

        // Añadir evento al marcador
        marcador.addListener("click", () => {
            infoWindow.open(mapa, marcador);
        });

        // Abrir infoWindow automáticamente
        infoWindow.open(mapa, marcador);
    }
</script>

<!-- Script de Google Maps -->
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap">
</script>
</body>
</html>
