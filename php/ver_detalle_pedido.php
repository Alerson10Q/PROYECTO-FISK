<?php
require 'db_connection.php';

$id_pedido = $_GET['id'];

$sql = "SELECT * FROM pedidos WHERE id_pedido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

$sql_detalles = "SELECT * FROM detalles_pedido WHERE id_pedido = ?";
$stmt_detalles = $conn->prepare($sql_detalles);
$stmt_detalles->bind_param("i", $id_pedido);
$stmt_detalles->execute();
$productos = $stmt_detalles->get_result();

if ($pedido) {
    echo '<h1>Detalles del Pedido</h1>';
    echo '<p>ID Pedido: ' . $pedido['id_pedido'] . '</p>';
    echo '<p>Fecha: ' . $pedido['fecha'] . '</p>';
    echo '<p>Cliente: ' . $pedido['id_cliente'] . '</p>';
    echo '<p>Total: ' . $pedido['total'] . '</p>';
    echo '<p>Estado: ' . $pedido['estado'] . '</p>';

    echo '<h2>Productos en el Pedido</h2>';
    echo '<table border="1">
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
            </tr>';

    while($producto = $productos->fetch_assoc()) {
        echo '<tr>
                <td>' . $producto['producto'] . '</td>
                <td>' . $producto['cantidad'] . '</td>
                <td>' . $producto['precio'] . '</td>
              </tr>';
    }

    echo '</table>';

    echo '<h2>Actualizar Estado del Pedido</h2>';
    echo '<form action="actualizar_estado_pedido.php" method="post">
            <input type="hidden" name="id_pedido" value="' . $id_pedido . '">
            <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <option value="Pendiente" ' . ($pedido['estado'] == 'Pendiente' ? 'selected' : '') . '>Pendiente</option>
                <option value="Enviado" ' . ($pedido['estado'] == 'Enviado' ? 'selected' : '') . '>Enviado</option>
                <option value="Entregado" ' . ($pedido['estado'] == 'Entregado' ? 'selected' : '') . '>Entregado</option>
                <option value="Cancelado" ' . ($pedido['estado'] == 'Cancelado' ? 'selected' : '') . '>Cancelado</option>
            </select>
            <input type="submit" value="Actualizar Estado">
          </form>';
} else {
    echo 'Pedido no encontrado.';
}

$stmt->close();
$stmt_detalles->close();
$conn->close();
?>
