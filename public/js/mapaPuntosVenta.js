window.initMapaPuntosVenta = function () {
    if (!window.puntosVenta || !window.puntosVenta.length) {
        console.error('No hay datos de puntos de venta para el mapa');
        return;
    }

    // Crear mapa centrado en Huelva
    const mapa = new google.maps.Map(document.getElementById('mapa-puntos-venta'), {
        zoom: 10,
        center: { lat: 37.25, lng: -6.95 }, // Centro aproximado de Huelva
        mapTypeId: 'roadmap'
    });

    // Crear bounds para ajustar zoom
    const bounds = new google.maps.LatLngBounds();

    // Ventana de información compartida
    const infoWindow = new google.maps.InfoWindow();

    // Crear geocoder
    const geocoder = new google.maps.Geocoder();

    // Contador para geocodificaciones completadas
    let geocodedCount = 0;

    // Procesar cada punto de venta
    window.puntosVenta.forEach((punto, index) => {
        // Obtener el nombre del municipio correctamente
        const municipioNombre = punto.municipio?.nombre || 'Huelva';
        const nucleoNombre = punto.nucleo?.nombre || 'Huelva';

        // Construir dirección completa con municipio
        const direccionCompleta = `${punto.direccion},${nucleoNombre}, Huelva , España`;

        // Añadir retraso para evitar límites de la API (200ms entre solicitudes)
        setTimeout(() => {
            geocoder.geocode({ address: direccionCompleta }, (results, status) => {
                if (status === 'OK' && results && results[0]) {
                    const position = results[0].geometry.location;

                    // Crear marcador
                    const marker = new google.maps.Marker({
                        position: position,
                        map: mapa,
                        title: punto.tipo,
                        icon: {
                            url: getIconByType(punto.tipo),
                            scaledSize: new google.maps.Size(32, 32)
                        }
                    });

                    // Contenido del popup
                    const contenido = `
                        <div class="p-2">
                            <p class="text-sm mt-1">${punto.tipo}</p>
                            <p class="text-sm mt-1">${punto.direccion}, ${nucleoNombre}</p>
                            <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(direccionCompleta)}"
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

                    // Extender bounds
                    bounds.extend(position);
                } else {
                    console.warn(`No se pudo geocodificar: ${direccionCompleta}`, status);
                }

                geocodedCount++;

                // Ajustar el mapa cuando se hayan procesado todos los puntos
                if (geocodedCount === window.puntosVenta.length) {
                    if (!bounds.isEmpty()) {
                        mapa.fitBounds(bounds);
                        if (window.puntosVenta.length === 1) {
                            mapa.setZoom(15);
                        }
                    }
                }
            });
        }, index * 200); // 200ms entre solicitudes para evitar límites de API
    });
};

// Función para obtener icono según tipo de punto de venta
function getIconByType(tipo) {
    const tipoLower = (tipo || '').toLowerCase();

    if (tipoLower.includes('estanco')) {
        return 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
    } else if (tipoLower.includes('taquilla operador')) {
        return 'https://maps.google.com/mapfiles/ms/icons/green-dot.png';
    } else if (tipoLower.includes('bar')) {
        return 'https://maps.google.com/mapfiles/ms/icons/red-dot.png';
    } else if (tipoLower.includes('consorcio')) {
        return 'https://maps.google.com/mapfiles/ms/icons/brown-dot.png';
    } else if (tipoLower.includes('prensa')) {
        return 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png';
    } else if (tipoLower.includes('copistería')) {
        return 'https://maps.google.com/mapfiles/ms/icons/pink-dot.png';
    } else if (tipoLower.includes('sin datos')) {
        return 'https://maps.google.com/mapfiles/ms/icons/purple-dot.png';
    }
    return 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
}
