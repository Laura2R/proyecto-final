// Inicializar fecha y hora actual menos 5 minutos
function inicializarFechaHora() {
    const ahora = new Date();
    ahora.setMinutes(ahora.getMinutes() - 5);

    const fecha = ahora.toISOString().split('T')[0];
    const hora = ahora.toTimeString().split(' ')[0].substring(0, 5);

    document.getElementById('fecha-consulta').value = fecha;
    document.getElementById('hora-consulta').value = hora;
}

// Formatear fecha para la API (DD-MM-YYYY+HH:MM)
function formatearFechaParaAPI(fecha, hora) {
    const [year, month, day] = fecha.split('-');
    return `${day}-${month}-${year}+${hora}`;
}

// Formatear fecha para mostrar
function formatearFechaParaMostrar(fechaISO) {
    const fecha = new Date(fechaISO);
    return fecha.toLocaleString('es-ES', {
        day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
}

// Obtener color según el sentido
function getColorSentido(sentido) {
    return sentido === '1' ? 'bg-green-100 border-green-300' : 'bg-blue-100 border-blue-300';
}

// Crear card de servicio
function crearCardServicio(servicio) {
    const colorCard = getColorSentido(servicio.sentido);
    const iconoSentido = servicio.sentido === '1' ? '<i class="fas fa-arrow-right"></i>' : '<i class="fas fa-arrow-left"></i>';

    return `
        <div class="servicio-card ${colorCard} border-2 rounded-lg p-4"
             onclick="irALinea('${servicio.idLinea}')">
            <div class="flex justify-between items-start mb-2">
                <div class="text-2xl font-bold text-gray-800">${servicio.servicio}</div>
                <div class="text-lg">${iconoSentido}</div>
            </div>
            <div class="space-y-1">
                <div class="font-semibold text-gray-700">${servicio.linea}</div>
                <div class="text-sm text-gray-600">${servicio.nombre}</div>
                <div class="text-sm font-medium text-gray-800">
                    Destino: ${servicio.destino}
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                Ver detalles de la línea
            </div>
        </div>
    `;
}

// Ir a la página de la línea
function irALinea(idLinea) {
    window.location.href = `/paradas/filtro-linea?linea_id=${idLinea}`;
}

// Mostrar/ocultar elementos de la UI
function mostrarElemento(id) {
    document.getElementById(id).classList.remove('hidden');
}

function ocultarElemento(id) {
    document.getElementById(id).classList.add('hidden');
}

function limpiarResultados() {
    ocultarElemento('error-servicios');
    ocultarElemento('sin-servicios');
    ocultarElemento('periodo-info');
    document.getElementById('lista-servicios').innerHTML = '';
}

// Consultar servicios
async function consultarServicios() {
    const fecha = document.getElementById('fecha-consulta').value;
    const hora = document.getElementById('hora-consulta').value;

    if (!fecha || !hora) {
        alert('Por favor, selecciona fecha y hora');
        return;
    }

    // Mostrar loading y limpiar resultados anteriores
    mostrarElemento('loading-servicios');
    limpiarResultados();

    try {
        const fechaFormateada = formatearFechaParaAPI(fecha, hora);
        const url = `https://api.ctan.es/v1/Consorcios/9/paradas/${window.paradaId}/servicios?horaIni=${fechaFormateada}`;

        console.log('Consultando URL:', url);

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        // Ocultar loading
        ocultarElemento('loading-servicios');

        if (data.servicios && data.servicios.length > 0) {
            // Mostrar información del período
            document.getElementById('hora-inicio').textContent = formatearFechaParaMostrar(data.horaIni);
            document.getElementById('hora-fin').textContent = formatearFechaParaMostrar(data.horaFin);
            mostrarElemento('periodo-info');

            // Mostrar servicios
            const listaServicios = document.getElementById('lista-servicios');
            listaServicios.innerHTML = data.servicios.map(servicio => crearCardServicio(servicio)).join('');
        } else {
            // No hay servicios
            mostrarElemento('sin-servicios');
        }

    } catch (error) {
        console.error('Error al consultar servicios:', error);
        ocultarElemento('loading-servicios');
        mostrarElemento('error-servicios');
    }
}

// Inicializar cuando carga la página
document.addEventListener('DOMContentLoaded', function () {
    // Verificar que tenemos el ID de la parada
    if (!window.paradaId) {
        console.error('No se encontró el ID de la parada');
        return;
    }

    console.log('Inicializando servicios para parada:', window.paradaId);

    inicializarFechaHora();
    consultarServicios(); // Cargar servicios automáticamente
});

// Exportar funciones para uso global
window.consultarServicios = consultarServicios;
window.irALinea = irALinea;
