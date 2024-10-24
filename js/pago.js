document.getElementById('forma_de_pago').addEventListener('change', function () {
    var metodoPagoDetalle = document.getElementById('metodo_pago_detalle');
    if (this.value === 'tarjeta') {
        metodoPagoDetalle.innerHTML = `
            <label for="numero_tarjeta">Número de Tarjeta:</label>
            <input type="text" id="numero_tarjeta" name="numero_tarjeta" required>
            <label for="fecha_expiracion">Fecha de Expiración:</label>
            <input type="text" id="fecha_expiracion" name="fecha_expiracion" required>
            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" required>
        `;
    } else if (this.value === 'efectivo') {
        metodoPagoDetalle.innerHTML = `
            <label for="red_cobranza">Red de Cobranza:</label>
            <select id="red_cobranza" name="red_cobranza" required>
                <option value="abitab">Abitab</option>
                <option value="redpagos">RedPagos</option>
            </select>
            <label for="numero_cedula">Número de Cédula:</label>
            <input type="text" id="numero_cedula" name="numero_cedula" required>
        `;
    }
});