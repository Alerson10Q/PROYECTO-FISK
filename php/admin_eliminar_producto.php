<?php
session_start();
include 'db_connection.php';
include 'header.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['clasificacion'] !== 'Administrador') {
    echo '<p>No tienes permiso para acceder a esta página.</p>';
    include 'footer.php';
    exit;
}
?>

<main>
    <div class="container">
        <h2>Eliminar Producto</h2>
        <form action="eliminar_producto.php" method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
            <label for="id_producto">ID del Producto a Eliminar:</label>
            <input type="number" name="id_producto" id="id_producto" min="1" required>
            <button type="submit">Eliminar Producto</button>
        </form>
    </div>
</main>

<?php
include 'footer.php';
?>
