window.initMapaParada = function() {
    if (!window.mapaParadaData) return;

    const { nombre, id, lat, lng } = window.mapaParadaData;

    // Crear el mapa
    const map = new google.maps.Map(document.getElementById("mapa-parada"), {
        center: { lat: lat, lng: lng },
        zoom: 16,
        mapTypeId: "roadmap"
    });

    // Crear el contenido del InfoWindow
    const contentString = `
        <div style="min-width:180px;">
            <div class="font-bold text-base mb-1" style="font-weight:bold;">${nombre}</div>
            <div>PARADA Nº <span style="font-weight:bold;">${id}</span></div>
            <div class="mt-2">
                <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}"
                   target="_blank"
                   rel="noopener noreferrer"
                   style="color: #2563eb; text-decoration: underline; font-weight: 500;">
                    Como llegar
                </a>
            </div>
        </div>
    `;

    // Crear el marcador
    const marker = new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: map,
        title: nombre
    });

    // Crear el InfoWindow
    const infowindow = new google.maps.InfoWindow({
        content: contentString
    });

    // Abrir el popup al cargar
    infowindow.open(map, marker);

    // Permitir abrirlo al hacer click
    marker.addListener("click", function() {
        infowindow.open(map, marker);
    });
};
