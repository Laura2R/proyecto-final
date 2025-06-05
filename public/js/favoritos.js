/**
 * Gestión de favoritos para OnubaBus
 * Este archivo contiene las funciones JavaScript para manejar favoritos de líneas y paradas
 */

/**
 * Toggle favorito para líneas
 * @param {number} lineaId - ID de la línea
 * @param {string} csrfToken - Token CSRF de Laravel
 * @param {string} toggleUrl - URL del endpoint para toggle
 */
async function toggleFavoritoLinea(lineaId) {
    try {
        const response = await fetch('/favoritos/linea', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ linea_id: lineaId })
        });

        // Verificar si la respuesta es JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new TypeError("La respuesta no es JSON válido");
        }

        const data = await response.json();

        if (data.success) {
            const btn = document.querySelector(`[data-linea-id="${lineaId}"]`);
            if (btn) {
                btn.innerHTML = data.is_favorite ? '<i class="fa-solid fa-star" style="color: #FFD43B;"></i>' : '<i class="fa-regular fa-star"></i>';
                btn.dataset.isFavorite = data.is_favorite;
            }
        } else {
            console.error('Error del servidor:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
    }
}
/**
 * Toggle favorito para paradas
 * @param {number} paradaId - ID de la parada
 * @param {string} csrfToken - Token CSRF de Laravel
 * @param {string} toggleUrl - URL del endpoint para toggle
 */
async function toggleFavoritoParada(paradaId) {
    try {
        const response = await fetch('/favoritos/parada', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ parada_id: paradaId })
        });

        // Verificar si la respuesta es JSON
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new TypeError("La respuesta no es JSON válido");
        }

        const data = await response.json();

        if (data.success) {
            const btn = document.querySelector(`[data-parada-id="${paradaId}"]`);
            if (btn) {
                btn.innerHTML = data.is_favorite ? '<i class="fa-solid fa-star" style="color: #FFD43B;"></i>' : '<i class="fa-regular fa-star"></i>';
                btn.dataset.isFavorite = data.is_favorite;
            }
        } else {
            console.error('Error del servidor:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
    }
}
/**
 * Mostrar mensaje temporal
 * @param {string} message - Mensaje a mostrar
 * @param {string} type - Tipo de mensaje (success, error, info)
 */
function showTemporaryMessage(message, type = 'info') {
    // Crear el elemento del mensaje
    const messageDiv = document.createElement('div');
    messageDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

    // Estilos según el tipo
    switch (type) {
        case 'success':
            messageDiv.className += ' bg-green-500 text-white';
            break;
        case 'error':
            messageDiv.className += ' bg-red-500 text-white';
            break;
        default:
            messageDiv.className += ' bg-blue-500 text-white';
    }

    messageDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                ×
            </button>
        </div>
    `;

    // Añadir al DOM
    document.body.appendChild(messageDiv);

    // Animar entrada
    setTimeout(() => {
        messageDiv.classList.remove('translate-x-full');
    }, 100);

    // Auto-eliminar después de 3 segundos
    setTimeout(() => {
        messageDiv.classList.add('translate-x-full');
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, 3000);
}

/**
 * Inicializar event listeners cuando se carga el DOM
 */
document.addEventListener('DOMContentLoaded', function() {
    // Añadir tooltips a los botones de favoritos
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    favoriteBtns.forEach(btn => {
        if (!btn.title) {
            const isFavorite = btn.dataset.isFavorite === 'true';
            btn.title = isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos';
        }
    });

    // Añadir efectos de hover
    favoriteBtns.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
