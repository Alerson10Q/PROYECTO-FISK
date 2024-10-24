<?php
include 'db_connection.php';
include 'header.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['clasificacion'] !== 'Administrador') {
    echo '<p>No tienes permiso para acceder a esta página.</p>';
    include 'footer.php';
    exit;
}
?>

<main>
    <div class="container">
        <h2>Agregar Nuevo Producto</h2>
        <form action="guardar_producto.php" method="post" enctype="multipart/form-data">
            <label for="precio">Precio:</label>
            <input type="number" name="precio" id="precio" step="0.01" min="0" required>
            
            <label for="stock_cantidad">Stock Cantidad:</label>
            <input type="number" name="stock_cantidad" id="stock_cantidad" min="0" required>
            
            <label for="marca">Marca:</label>
            <input type="text" name="marca" id="marca" required>
            
            <label for="imagen">Imagen (URL):</label>
            <input type="text" name="imagen" id="imagen" required>
            
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" id="descripcion" rows="4" required></textarea>
            
            <label for="tipo_producto">Tipo de Producto:</label>
            <select name="tipo_producto" id="tipo_producto" required>
                <option value="Pinturas">Pinturas</option>
                <option value="Accesorios">Accesorios</option>
                <option value="Mini-ferretería">Mini-ferretería</option>
            </select>
            
            <label for="id_proveedor">Proveedor ID:</label>
            <input type="number" name="id_proveedor" id="id_proveedor" min="1" required>
            
            <button type="submit">Agregar Producto</button>
        </form>
    </div>
</main>

<?php
include 'footer.php';
?>
