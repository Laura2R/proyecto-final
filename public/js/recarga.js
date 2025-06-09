const stripe = Stripe(window.stripeConfig.key);
const elements = stripe.elements();

const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#374151',
            fontFamily: '"Inter", system-ui, sans-serif',
            '::placeholder': {
                color: '#9CA3AF',
            },
        },
        invalid: {
            color: '#EF4444',
        },
    }
});

cardElement.mount('#card-element');

const form = document.getElementById('payment-form');
const cantidadInput = document.getElementById('cantidad');
const amountDisplay = document.getElementById('amount-display');
const buttonAmount = document.getElementById('button-amount');
const newBalance = document.getElementById('new-balance');

function setCantidad(amount) {
    cantidadInput.value = amount;
    updateDisplays();

    // Actualizar estilos de botones
    document.querySelectorAll('.cantidad-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
        btn.classList.add('bg-gray-100', 'border-gray-300');
    });
    event.target.classList.remove('bg-gray-100', 'border-gray-300');
    event.target.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
}

function updateDisplays() {
    const amount = parseFloat(cantidadInput.value) || 0;
    amountDisplay.textContent = amount.toFixed(2);
    buttonAmount.textContent = amount.toFixed(2);
    newBalance.textContent = (window.cardData.currentBalance + amount).toFixed(2);
}

cantidadInput.addEventListener('input', updateDisplays);

// Manejo de errores de la tarjeta
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const loadingText = document.getElementById('loading-text');

    // Deshabilitar botón y mostrar loading
    submitButton.disabled = true;
    buttonText.classList.add('hidden');
    loadingText.classList.remove('hidden');

    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: window.stripeConfig.userName,
            email: window.stripeConfig.userEmail
        }
    });

    if (error) {
        document.getElementById('card-errors').textContent = error.message;

        // Rehabilitar botón
        submitButton.disabled = false;
        buttonText.classList.remove('hidden');
        loadingText.classList.add('hidden');
    } else {
        document.getElementById('payment_method_id').value = paymentMethod.id;
        form.submit();
    }
});

// Inicializar displays al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateDisplays();
});
