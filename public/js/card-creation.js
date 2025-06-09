const stripe = Stripe(window.stripeConfig.key);
const elements = stripe.elements();
let cardElement = null;
let cardMounted = false;

const saldoInput = document.getElementById('saldo');
const saldoDisplay = document.getElementById('saldo-display');
const paymentAmount = document.getElementById('payment-amount');
const paymentRow = document.getElementById('payment-row');
const paymentSection = document.getElementById('payment-section');
const buttonText = document.getElementById('button-text');

function setSaldoInicial(amount) {
    saldoInput.value = amount.toFixed(2);
    updateDisplays();

    // Actualizar estilos de botones
    document.querySelectorAll('.saldo-btn').forEach(btn => {
        btn.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
        btn.classList.add('bg-gray-100', 'border-gray-300');
    });
    event.target.classList.remove('bg-gray-100', 'border-gray-300');
    event.target.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
}

function updateDisplays() {
    const saldo = parseFloat(saldoInput.value) || 0;
    saldoDisplay.textContent = saldo.toFixed(2);
    paymentAmount.textContent = saldo.toFixed(2);

    if (saldo > 0) {
        paymentRow.style.display = 'flex';
        paymentSection.style.display = 'block';
        buttonText.textContent = `Crear Tarjeta y Pagar €${saldo.toFixed(2)}`;

        // Montar Stripe Elements si no está montado
        if (!cardMounted) {
            mountStripeElements();
        }
    } else {
        paymentRow.style.display = 'none';
        paymentSection.style.display = 'none';
        buttonText.textContent = 'Crear Tarjeta';

        // Desmontar Stripe Elements si está montado
        if (cardMounted) {
            cardElement.unmount();
            cardMounted = false;
        }
    }
}

function mountStripeElements() {
    if (!cardMounted) {
        cardElement = elements.create('card', {
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
        cardMounted = true;

        // Manejo de errores de la tarjeta
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    }
}

// Event listeners
saldoInput.addEventListener('input', updateDisplays);

// Manejo del formulario
document.getElementById('card-creation-form').addEventListener('submit', async (e) => {
    const saldo = parseFloat(saldoInput.value) || 0;
    const submitButton = document.getElementById('submit-button');
    const loadingText = document.getElementById('loading-text');

    // Si no hay saldo inicial, enviar formulario normalmente
    if (saldo <= 0) {
        return;
    }

    e.preventDefault();

    // Deshabilitar botón y mostrar loading
    submitButton.disabled = true;
    buttonText.classList.add('hidden');
    loadingText.classList.remove('hidden');

    try {
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
            e.target.submit();
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('card-errors').textContent = 'Error al procesar el pago';

        // Rehabilitar botón
        submitButton.disabled = false;
        buttonText.classList.remove('hidden');
        loadingText.classList.add('hidden');
    }
});

// Inicializar displays al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateDisplays();
});
