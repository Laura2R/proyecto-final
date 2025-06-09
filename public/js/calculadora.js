document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('pagoForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const tarjetaSeleccionada = document.querySelector('input[name="tarjeta_id"]:checked');
            if (!tarjetaSeleccionada) {
                e.preventDefault();
                alert('Por favor selecciona una tarjeta para continuar');
                return false;
            }
        });
    }
});
