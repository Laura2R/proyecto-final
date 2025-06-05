window.initMapaPuntosVenta = function () {
    if (!window.puntosVenta || !window.puntosVenta.length) {
        console.error('No hay datos de puntos de venta para el mapa');
        return;
    }

    // Variables globales del mapa
    let mapa;
    let marcadores = [];
    let infoWindow;
    let geocoder;
    let bounds;

    // Coordenadas específicas que requieren geocodificación
    const coordenadasProblematicas = [
        { lat: "36.59694163865113", lng: "-6.225042343139648" },
        { lat: "37.46474628609939", lng: "-5.344676971435547" },
        { lat: "36.82320976872225", lng: "-4.705488458275795" }
    ];

    // Función para inicializar el mapa
    function inicializarMapa() {
        // Crear mapa
        mapa = new google.maps.Map(document.getElementById('mapa-puntos-venta'), {
            zoom: 10,
            mapTypeId: 'roadmap'
        });

        // Ventana de información compartida
        infoWindow = new google.maps.InfoWindow();

        // Crear geocoder
        geocoder = new google.maps.Geocoder();
    }

    // Función para limpiar marcadores existentes
    function limpiarMarcadores() {
        marcadores.forEach(marcador => {
            marcador.setMap(null);
        });
        marcadores = [];
    }

    // Función para obtener todos los puntos (ya vienen filtrados del servidor)
    function obtenerPuntosFiltrados() {
        return window.puntosVenta || [];
    }

    // Función para verificar si las coordenadas son problemáticas
    function esCoordenadasProblematicas(lat, lng) {
        return coordenadasProblematicas.some(coord =>
            Math.abs(parseFloat(lat) - parseFloat(coord.lat)) < 0.0001 &&
            Math.abs(parseFloat(lng) - parseFloat(coord.lng)) < 0.0001
        );
    }

    // Función para crear marcador con geocodificación (para coordenadas problemáticas)
    function crearMarcadorConGeocoding(punto, callback) {
        const direccionCompleta = `${punto.direccion}, ${punto.nucleo.nombre}, Huelva, España`;

        geocoder.geocode({ address: direccionCompleta }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const posicion = results[0].geometry.location;
                console.log(`Geocodificado: ${direccionCompleta} -> ${posicion.lat()}, ${posicion.lng()}`);
                const marcador = crearMarcador(punto, posicion, mapa, infoWindow, true);
                if (marcador) {
                    marcadores.push(marcador);
                    bounds.extend(posicion);
                }
            } else {
                console.warn(`Geocodificación falló para: ${direccionCompleta}`);
            }

            if (callback) callback();
        });
    }

    // Función para crear marcador con coordenadas directas (para coordenadas normales)
    function crearMarcadorDirecto(punto) {
        const posicion = {
            lat: parseFloat(punto.latitud),
            lng: parseFloat(punto.longitud)
        };
        const marcador = crearMarcador(punto, posicion, mapa, infoWindow, false);
        if (marcador) {
            marcadores.push(marcador);
            bounds.extend(posicion);
        }
    }

    // Función unificada para crear marcador
    function crearMarcador(punto, posicion, mapa, infoWindow, esGeocoficado) {
        // Verificar coordenadas válidas
        const lat = typeof posicion.lat === 'function' ? posicion.lat() : posicion.lat;
        const lng = typeof posicion.lng === 'function' ? posicion.lng() : posicion.lng;

        if (isNaN(lat) || isNaN(lng)) {
            console.error(`Coordenadas inválidas para punto: ${punto.direccion}`);
            return null;
        }

        const posicionFinal = { lat: lat, lng: lng };

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
                <h4 class="text-sm font-semibold">${punto.tipo}</h4>
                <p class="text-sm mt-1">${punto.direccion}</p>
                <p class="text-xs text-gray-600 mt-1">${punto.nucleo.nombre}, ${punto.municipio.nombre}</p>
                ${punto.telefono ? `<p class="text-xs text-gray-600 mt-1"><i class="fa-solid fa-phone"></i> ${punto.telefono}</p>` : ''}
                ${punto.horario ? `<p class="text-xs text-gray-600 mt-1"><i class="fas fa-clock"></i> ${punto.horario}</p>` : ''}
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

        console.log(`Marcador creado para: ${punto.direccion} en ${lat}, ${lng} ${esGeocoficado ? '(geocodificado)' : '(coordenadas originales)'}`);
        return marker;
    }

    // Función para ajustar el mapa al final
    function ajustarMapaFinal() {
        if (!bounds.isEmpty()) {
            mapa.fitBounds(bounds);

            // Si solo hay un punto, zoom más cercano
            if (marcadores.length === 1) {
                mapa.setZoom(15);
            } else if (marcadores.length > 1) {
                // Asegurar un zoom mínimo para ver bien los marcadores
                google.maps.event.addListenerOnce(mapa, 'bounds_changed', function() {
                    if (mapa.getZoom() > 12) {
                        mapa.setZoom(12);
                    }
                });
            }
        } else {
            // Si no hay marcadores, centrar en Huelva
            mapa.setCenter({ lat: 37.2614, lng: -6.9447 });
            mapa.setZoom(10);
        }
        console.log(`Mapa ajustado con ${marcadores.length} marcadores`);
    }

    // Función principal para actualizar el mapa
    function actualizarMapa() {
        // Limpiar marcadores existentes
        limpiarMarcadores();

        // Crear nuevo bounds
        bounds = new google.maps.LatLngBounds();

        // Obtener puntos filtrados (ya vienen filtrados del servidor)
        const puntosFiltrados = obtenerPuntosFiltrados();

        if (puntosFiltrados.length === 0) {
            console.log('No hay puntos que mostrar con los filtros actuales');
            ajustarMapaFinal();
            return;
        }

        // Contadores para geocodificación
        let marcadoresPendientes = 0;

        // Procesar cada punto filtrado
        puntosFiltrados.forEach(punto => {
            if (!punto.latitud || !punto.longitud) {
                console.warn(`Punto sin coordenadas: ${punto.direccion}`);
                return;
            }

            // Verificar si necesita geocodificación
            if (esCoordenadasProblematicas(punto.latitud, punto.longitud)) {
                console.log(`Coordenadas problemáticas detectadas para: ${punto.direccion}, usando geocodificación de dirección`);
                marcadoresPendientes++;
                crearMarcadorConGeocoding(punto, () => {
                    marcadoresPendientes--;
                    if (marcadoresPendientes === 0) {
                        ajustarMapaFinal();
                    }
                });
            } else {
                // Usar coordenadas normales
                crearMarcadorDirecto(punto);
            }
        });

        // Si no hay marcadores pendientes de geocodificación, ajustar mapa inmediatamente
        if (marcadoresPendientes === 0) {
            ajustarMapaFinal();
        }
    }

    // Inicialización
    inicializarMapa();

    // Cargar mapa inicial con todos los puntos filtrados
    actualizarMapa();

    // Exponer función para uso externo si es necesario
    window.actualizarMapaPuntosVenta = actualizarMapa;
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
        return 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
    } else if (tipoLower.includes('prensa')) {
        return 'https://maps.google.com/mapfiles/ms/icons/orange-dot.png';
    } else if (tipoLower.includes('copistería')) {
        return 'https://maps.google.com/mapfiles/ms/icons/pink-dot.png';
    } else {
        return 'https://maps.google.com/mapfiles/ms/icons/purple-dot.png';
    }
}
