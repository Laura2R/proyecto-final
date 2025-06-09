function toggleSortDirection() {
    const directionInput = document.getElementById('directionInput');
    const currentDirection = directionInput.value;
    directionInput.value = currentDirection === 'asc' ? 'desc' : 'asc';
    document.getElementById('filtroForm').submit();
}

function sortBy(column) {
    const form = document.getElementById('filtroForm');
    const sortInput = form.querySelector('select[name="sort"]');
    const directionInput = document.getElementById('directionInput');

    if (sortInput.value === column) {
        directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortInput.value = column;
        directionInput.value = 'asc';
    }

    form.submit();
}
