window.initMapaPuntosVenta = function () {
    if (!window.puntosVenta || !window.puntosVenta.length) {
        console.error('No hay datos de puntos de venta para el mapa');
        return;
    }

    // Crear mapa
    const mapa = new google.maps.Map(document.getElementById('mapa-puntos-venta'), {
        zoom: 10,
        mapTypeId: 'roadmap'
    });

    // Crear bounds para ajustar zoom
    const bounds = new google.maps.LatLngBounds();

    // Ventana de información compartida
    const infoWindow = new google.maps.InfoWindow();

    // Añadir marcadores para cada punto de venta
    window.puntosVenta.forEach(punto => {
        if (!punto.latitud || !punto.longitud) return;

        const posicion = {
            lat: parseFloat(punto.latitud),
            lng: parseFloat(punto.longitud)
        };

        // Verificar coordenadas válidas
        if (isNaN(posicion.lat) || isNaN(posicion.lng)) return;

        // Extender bounds
        bounds.extend(posicion);

        // Crear marcador
        const marker = new google.maps.Marker({
            position: posicion,
            map: mapa,
            title: punto.nombre,
            icon: {
                url: getIconByType(punto.tipo),
                scaledSize: new google.maps.Size(32, 32)
            }
        });

        // Contenido del popup
        const contenido = `
            <div class="p-2">
                <p class="text-sm mt-1">${punto.tipo}</p>
                <p class="text-sm mt-1">${punto.direccion}, ${punto.municipio}</p>
                <a href="https://www.google.com/maps?q=${punto.latitud},${punto.longitud}"
                   target="_blank"
                   class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                    Ver en Google Maps
                </a>
            </div>
        `;

        // Evento click para mostrar info
        marker.addListener('click', () => {
            infoWindow.setContent(contenido);
            infoWindow.open(mapa, marker);
        });
    });

    // Ajustar zoom para mostrar todos los puntos
    mapa.fitBounds(bounds);

    // Si solo hay un punto, zoom más cercano
    if (window.puntosVenta.length === 1) {
        mapa.setZoom(15);
    }
};

// Función para obtener icono según tipo de punto de venta
function getIconByType(tipo) {
    const tipoLower = (tipo || '').toLowerCase();

    if (tipoLower.includes('estanco')) {
        return 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
    } else if (tipoLower.includes('quiosco')) {
        return 'https://maps.google.com/mapfiles/ms/icons/green-dot.png';
    } else if (tipoLower.includes('oficina')) {
        return 'https://maps.google.com/mapfiles/ms/icons/red-dot.png';
    }

    return 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
}
