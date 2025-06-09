async function quitarFavorito(tipo, id) {
    const url = tipo === 'linea' ? '/favoritos/linea' : '/favoritos/parada';
    const data = tipo === 'linea' ? { linea_id: id } : { parada_id: id };

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            location.reload();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
