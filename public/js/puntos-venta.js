// Mejorar la experiencia de filtrado
document.addEventListener('DOMContentLoaded', function() {
    const municipioSelect = document.getElementById('municipio-select');
    const nucleoSelect = document.getElementById('nucleo-select');
    const limpiarBtn = document.getElementById('limpiar-filtros');
    const form = document.getElementById('filtros-form');

    // Actualizar contador de marcadores cuando cargue la página - Solo si hay mapa
    if (window.puntosVenta) {
        setTimeout(() => {
            const totalPuntos = window.puntosVenta ? window.puntosVenta.length : 0;
            const contador = document.getElementById('contador-marcadores');
            if (contador) {
                contador.textContent = `Mostrando ${totalPuntos} punto${totalPuntos !== 1 ? 's' : ''} en el mapa`;
            }
        }, 1000);
    }

    // Envío automático del formulario
    function enviarFormulario() {
        // Mostrar loading solo si existe el contador
        const contador = document.getElementById('contador-marcadores');
        if (contador) {
            contador.textContent = 'Actualizando mapa...';
        }

        // Enviar formulario
        form.submit();
    }

    // Manejar cambio de municipio
    if (municipioSelect) {
        municipioSelect.addEventListener('change', function() {
            // Limpiar y deshabilitar el select de núcleo cuando cambie el municipio
            if (nucleoSelect) {
                nucleoSelect.value = '';
                nucleoSelect.disabled = true;

                // Limpiar las opciones del núcleo excepto la primera
                while (nucleoSelect.children.length > 1) {
                    nucleoSelect.removeChild(nucleoSelect.lastChild);
                }
            }

            // Enviar formulario
            enviarFormulario();
        });
    }

    // Manejar cambio de núcleo
    if (nucleoSelect) {
        nucleoSelect.addEventListener('change', enviarFormulario);
    }

    // Confirmación para limpiar filtros
    if (limpiarBtn) {
        limpiarBtn.addEventListener('click', function(e) {
            if (municipioSelect.value || nucleoSelect.value) {
                const contador = document.getElementById('contador-marcadores');
                if (contador) {
                    contador.textContent = 'Limpiando filtros...';
                }
            }
        });
    }
});
