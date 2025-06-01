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

    // Crear geocoder
    const geocoder = new google.maps.Geocoder();

    // Coordenadas específicas que requieren geocodificación
    const coordenadasProblematicas = [
        { lat: "36.59694163865113", lng: "-6.225042343139648" },
        { lat: "37.46474628609939", lng: "-5.344676971435547" },
        { lat: "36.82320976872225", lng: "-4.705488458275795" }
    ];

    // Contador para saber cuándo ajustar el mapa
    let marcadoresPendientes = 0;
    let marcadoresCreados = 0;

    // Función para verificar si las coordenadas son problemáticas
    function esCoordenadasProblematicas(lat, lng) {
        return coordenadasProblematicas.some(coord =>
            Math.abs(parseFloat(lat) - parseFloat(coord.lat)) < 0.0001 &&
            Math.abs(parseFloat(lng) - parseFloat(coord.lng)) < 0.0001
        );
    }

    // Función para crear marcador con geocodificación (para coordenadas problemáticas)
    function crearMarcadorConGeocoding(punto) {
        const direccionCompleta = `${punto.direccion}, ${punto.nucleo.nombre}, Huelva, España`;

        geocoder.geocode({ address: direccionCompleta }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const posicion = results[0].geometry.location;
                console.log(`Geocodificado: ${direccionCompleta} -> ${posicion.lat()}, ${posicion.lng()}`);
                crearMarcador(punto, posicion, mapa, infoWindow, bounds, true);
            } else {
                console.warn(`Geocodificación falló para: ${direccionCompleta}`);
                // NO usar coordenadas originales como fallback para coordenadas problemáticas
                // Simplemente no crear el marcador si falla la geocodificación
            }

            // Decrementar contador y ajustar mapa si es necesario
            marcadoresPendientes--;
            if (marcadoresPendientes === 0) {
                ajustarMapaFinal();
            }
        });
    }

    // Función para crear marcador con coordenadas directas (para coordenadas normales)
    function crearMarcadorDirecto(punto) {
        const posicion = {
            lat: parseFloat(punto.latitud),
            lng: parseFloat(punto.longitud)
        };
        crearMarcador(punto, posicion, mapa, infoWindow, bounds, false);
    }

    // Función unificada para crear marcador
    function crearMarcador(punto, posicion, mapa, infoWindow, bounds, esGeocoficado) {
        // Verificar coordenadas válidas
        const lat = typeof posicion.lat === 'function' ? posicion.lat() : posicion.lat;
        const lng = typeof posicion.lng === 'function' ? posicion.lng() : posicion.lng;

        if (isNaN(lat) || isNaN(lng)) {
            console.error(`Coordenadas inválidas para punto: ${punto.direccion}`);
            return;
        }

        const posicionFinal = { lat: lat, lng: lng };

        // Extender bounds
        bounds.extend(posicionFinal);

        // Crear marcador con icono diferente si fue geocodificado
        const marker = new google.maps.Marker({
            position: posicionFinal,
            map: mapa,
            title: punto.nombre || punto.tipo,
            icon: {
                url: esGeocoficado ? getIconByType(punto.tipo + '_geocodificado') : getIconByType(punto.tipo),
                scaledSize: new google.maps.Size(32, 32)
            }
        });

        // Contenido del popup
        const contenido = `
            <div class="p-2">
                <h4 class="text-sm mt-1">${punto.tipo}</h4>
                <p class="text-sm mt-1">${punto.direccion}, ${punto.nucleo.nombre}</p>
                <a href="https://www.google.com/maps?q=${lat},${lng}"
                   target="_blank"
                   class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                    Cómo llegar
                </a>
            </div>
        `;

        // Evento click para mostrar info
        marker.addListener('click', () => {
            infoWindow.setContent(contenido);
            infoWindow.open(mapa, marker);
        });

        marcadoresCreados++;
        console.log(`Marcador creado para: ${punto.direccion} en ${lat}, ${lng} ${esGeocoficado ? '(geocodificado)' : '(coordenadas originales)'}`);
    }

    // Función para ajustar el mapa al final
    function ajustarMapaFinal() {
        if (!bounds.isEmpty()) {
            mapa.fitBounds(bounds);

            // Si solo hay un punto, zoom más cercano
            if (marcadoresCreados === 1) {
                mapa.setZoom(15);
            } else if (marcadoresCreados > 1) {
                // Asegurar un zoom mínimo para ver bien los marcadores
                google.maps.event.addListenerOnce(mapa, 'bounds_changed', function() {
                    if (mapa.getZoom() > 12) {
                        mapa.setZoom(12);
                    }
                });
            }
        }
        console.log(`Mapa ajustado con ${marcadoresCreados} marcadores`);
    }

    // Procesar cada punto de venta
    window.puntosVenta.forEach(punto => {
        if (!punto.latitud || !punto.longitud) {
            console.warn(`Punto sin coordenadas: ${punto.direccion}`);
            return;
        }

        // Verificar si necesita geocodificación
        if (esCoordenadasProblematicas(punto.latitud, punto.longitud)) {
            console.log(`Coordenadas problemáticas detectadas para: ${punto.direccion}, usando geocodificación de dirección`);
            marcadoresPendientes++;
            crearMarcadorConGeocoding(punto);
        } else {
            // Usar coordenadas normales
            crearMarcadorDirecto(punto);
        }
    });

    // Si no hay marcadores pendientes de geocodificación, ajustar mapa inmediatamente
    if (marcadoresPendientes === 0) {
        ajustarMapaFinal();
    }
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
