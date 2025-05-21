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

    // Obtener todas las paradas de la página
    const obtenerParadas = (sentido) => {
        const paradas = [];
        const contenedoresParada = document.querySelectorAll(`[data-sentido="${sentido}"] .parada-item`);

        contenedoresParada.forEach(parada => {
            const lat = parseFloat(parada.dataset.lat);
            const lng = parseFloat(parada.dataset.lng);
            const nombre = parada.dataset.nombre;
            const orden = parada.dataset.orden;
            const id = parada.dataset.id;

            if (!isNaN(lat) && !isNaN(lng)) {
                paradas.push({
                    position: { lat, lng },
                    nombre: nombre,
                    orden: orden,
                    id: id
                });
            }
        });

        return paradas;
    };

    // Dibujar ambas polilíneas si existen
    if (window.polilineaIda && window.polilineaIda.length > 0) {
        const rutaIda = procesarCoordenadas(window.polilineaIda);
        const paradasIda = obtenerParadas('ida');
        dibujarPolilinea('mapa-ida', rutaIda, paradasIda, '#669900'); // Color del JSON
    }

    if (window.polilineaVuelta && window.polilineaVuelta.length > 0) {
        const rutaVuelta = procesarCoordenadas(window.polilineaVuelta);
        const paradasVuelta = obtenerParadas('vuelta');
        dibujarPolilinea('mapa-vuelta', rutaVuelta, paradasVuelta, '#FF0000'); // Rojo para diferenciar
    }
};

function dibujarPolilinea(mapId, coordenadas, paradas, color) {
    const elemento = document.getElementById(mapId);
    if (!elemento) {
        console.error(`Elemento con ID ${mapId} no encontrado`);
        return;
    }

    const mapa = new google.maps.Map(elemento, {
        zoom: 12,
        center: coordenadas[0],
        mapTypeId: 'roadmap'
    });

    // Dibujar la polilínea (ruta)
    new google.maps.Polyline({
        path: coordenadas,
        map: mapa,
        strokeColor: color,
        strokeOpacity: 1.0,
        strokeWeight: 4
    });

    // Crear un bounds para ajustar el zoom
    const bounds = new google.maps.LatLngBounds();

    // Añadir todos los puntos de la ruta al bounds
    coordenadas.forEach(coord => bounds.extend(coord));

    // Añadir marcadores de paradas
    if (paradas && paradas.length > 0) {
        paradas.forEach(parada => {
            // Añadir la posición de la parada al bounds
            bounds.extend(parada.position);

            // Crear el marcador
            const marker = new google.maps.Marker({
                position: parada.position,
                map: mapa,
                title: `${parada.nombre} (Orden: ${parada.orden})`,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 7,
                    fillColor: "#3498db",
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: "#ffffff"
                }
            });

            // Añadir InfoWindow para mostrar detalles al hacer clic con el mismo formato que solicitaste
            const contentString = `
                <div style="min-width:180px;">
                    <div class="font-bold text-base mb-1" style="font-weight:bold;">${parada.nombre}</div>
                    <div>PARADA Nº <span style="font-weight:bold;">${parada.id || '—'}</span></div>
                    <div class="mt-1">Orden: ${parada.orden}</div>
                    <div class="mt-2">
                        <a href="https://www.google.com/maps/search/?api=1&query=${parada.position.lat},${parada.position.lng}"
                           target="_blank"
                           rel="noopener noreferrer"
                           style="color: #2563eb; text-decoration: underline; font-weight: 500;">
                            Cómo llegar
                        </a>
                    </div>
                </div>
            `;

            const infoWindow = new google.maps.InfoWindow({
                content: contentString
            });

            marker.addListener('click', function() {
                infoWindow.open(mapa, marker);
            });
        });
    }

    // Marcadores de inicio y fin de la ruta
    new google.maps.Marker({
        position: coordenadas[0],
        map: mapa,
        label: "Inicio",
        zIndex: 100
    });

    new google.maps.Marker({
        position: coordenadas[coordenadas.length - 1],
        map: mapa,
        label: "Fin",
        zIndex: 100
    });

    // Ajustar zoom para mostrar toda la ruta y paradas
    mapa.fitBounds(bounds);
}
