function initMap() {
    // Obtener coordenadas desde Blade
    const lat = parseFloat(document.currentScript.getAttribute('data-lat')) || {{ $parada->latitud }};
    const lng = parseFloat(document.currentScript.getAttribute('data-lng')) || {{ $parada->longitud }};

    // Configuración del mapa
    const ubicacion = { lat: lat, lng: lng };
    const mapa = new google.maps.Map(document.getElementById("mapa"), {
        zoom: 15,
        center: ubicacion,
        mapTypeControl: true,
        streetViewControl: false
    });

    // Marcador con InfoWindow
    const marcador = new google.maps.Marker({
        position: ubicacion,
        map: mapa,
        title: "{{ $parada->nombre }}"
    });

    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div class="text-sm">
                <h3 class="font-bold">{{ $parada->nombre }}</h3>
                <p>ID: {{ $parada->id_parada }}</p>
                <p>Coordenadas: ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
            </div>
        `
    });

    marcador.addListener("click", () => {
        infoWindow.open(mapa, marcador);
    });

    // Abrir infoWindow automáticamente
    infoWindow.open(mapa, marcador);
}
