let isAtTop = true;
let scrollButton;
let scrollArrow;

document.addEventListener('DOMContentLoaded', function() {
    scrollButton = document.getElementById('scroll-button');
    scrollArrow = document.getElementById('scroll-arrow');

    // Detectar scroll
    window.addEventListener('scroll', handleScroll);

    // Verificar posición inicial
    handleScroll();
});

function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    // Determinar si estamos arriba o abajo
    if (scrollTop < 100) {
        // Estamos arriba
        if (!isAtTop) {
            isAtTop = true;
            updateButton();
        }
        showButton();
    } else if (scrollTop + windowHeight >= documentHeight - 100) {
        // Estamos abajo
        if (isAtTop) {
            isAtTop = false;
            updateButton();
        }
        showButton();
    } else {
        // Estamos en el medio
        hideButton();
    }
}

function updateButton() {
    if (isAtTop) {
        // Mostrar flecha hacia abajo
        scrollArrow.innerHTML = '↓';
    } else {
        // Mostrar flecha hacia arriba
        scrollArrow.innerHTML = '↑';
    }
}

function showButton() {
    scrollButton.classList.add('show');
}

function hideButton() {
    scrollButton.classList.remove('show');
}

function toggleScroll() {
    if (isAtTop) {
        // Ir al final
        window.scrollTo({
            top: document.documentElement.scrollHeight,
            behavior: 'smooth'
        });
    } else {
        // Ir al principio
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}
