function toggleClientFields() {
    const clasificacion = document.getElementById('clasificacion').value;
    const clienteFields = document.getElementById('clienteFields');
    clienteFields.style.display = clasificacion === 'Cliente' ? 'block' : 'none';
}

toggleClientFields();