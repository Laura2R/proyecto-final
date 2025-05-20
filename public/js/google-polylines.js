// Función global para inicializar los mapas
window.initMaps = function() {
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Google Maps API no se cargó correctamente');
        return;
    }

    // Procesar coordenadas desde el formato del JSON
    const procesarCoordenadas = (apiData) => {
        return apiData.map(item => {
            const [lat, lng] = item[0].split(',').map(Number);
            return { lat, lng };
        });
    };

    // Dibujar ambas polilíneas si existen
    if (window.polilineaIda && window.polilineaIda.length > 0) {
        const rutaIda = procesarCoordenadas(window.polilineaIda);
        dibujarPolilinea('mapa-ida', rutaIda, '#669900'); // Color del JSON
    }

    if (window.polilineaVuelta && window.polilineaVuelta.length > 0) {
        const rutaVuelta = procesarCoordenadas(window.polilineaVuelta);
        dibujarPolilinea('mapa-vuelta', rutaVuelta, '#FF0000'); // Rojo para diferenciar
    }
};

function dibujarPolilinea(mapId, coordenadas, color) {
    const mapa = new google.maps.Map(document.getElementById(mapId), {
        zoom: 12,
        center: coordenadas[0],
        mapTypeId: 'roadmap'
    });

    new google.maps.Polyline({
        path: coordenadas,
        map: mapa,
        strokeColor: color,
        strokeOpacity: 1.0,
        strokeWeight: 4
    });

    // Ajustar zoom para mostrar toda la ruta
    const bounds = new google.maps.LatLngBounds();
    coordenadas.forEach(coord => bounds.extend(coord));
    mapa.fitBounds(bounds);

    // Marcadores de inicio y fin
    new google.maps.Marker({
        position: coordenadas[0],
        map: mapa,
        label: "Inicio"
    });

    new google.maps.Marker({
        position: coordenadas[coordenadas.length - 1],
        map: mapa,
        label: "Fin"
    });
}
