<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Pedidos</title>
    <link rel="stylesheet" href="../css/gestionar_pedidos.css">
</head>
<body>

<?php
$sql = "
    SELECT ventas.*, usuarios.correo AS correo_cliente 
    FROM ventas 
    JOIN usuarios ON ventas.id_usuario = usuarios.id_usuario";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<h1>Pedidos</h1>';
    echo '<div class="grid-container">';
    
    echo '<div class="grid-header">ID de pedido</div>';
    echo '<div class="grid-header">Fecha de pedido</div>';
    echo '<div class="grid-header">Productos pedidos</div>';
    echo '<div class="grid-header">Valor del pedido</div>';
    echo '<div class="grid-header">Estado del pedido</div>';
    echo '<div class="grid-header">Acción</div>';

    while ($row = $result->fetch_assoc()) {
        $correo_cliente = $row['correo_cliente'];

        echo '<div class="grid-item">' . $row['id_venta'] . '</div>';
        echo '<div class="grid-item">' . $row['fecha_de_venta'] . '</div>';
        
        $productos = json_decode($row['productos_vendidos'], true);
        echo '<div class="grid-item">';
        foreach ($productos as $producto) {
            echo 'Producto ID: ' . $producto['id_producto'] . ', Cantidad: ' . $producto['cantidad'] . '<br>';
        }
        echo '</div>';
        
        echo '<div class="grid-item">' . number_format($row['valor_de_venta'], 2) . '</div>';
        echo '<div class="grid-item">' . ucfirst($row['estado']) . '</div>';
        
        echo '<div class="grid-item">';
        echo '<form method="POST" action="marcar_entregado.php">';
        echo '<input type="hidden" name="id_venta" value="' . $row['id_venta'] . '">';
        echo '<input type="hidden" name="correo_cliente" value="' . $correo_cliente . '">';
        echo '<input type="submit" value="Marcar como Entregado">';
        echo '</form>';
        echo '</div>';
        
        echo '<div class="grid-item">';
        echo '<form method="POST" action="eliminar_pedido.php">';
        echo '<input type="hidden" name="id_pedido" value="' . $row['id_venta'] . '">';
        echo '<input type="hidden" name="correo_cliente" value="' . $correo_cliente . '">';
        echo '<input type="submit" value="Eliminar Pedido">';
        echo '</form>';
        echo '</div>';
    }

    echo '</div>';
} else {
    echo '<p>No hay pedidos.</p>';
}

$conn->close();
?>

</body>
</html>
