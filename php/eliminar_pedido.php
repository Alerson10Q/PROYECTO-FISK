<?php
require 'db_connection.php';

if (!isset($_POST['id_venta'])) {
    die('Error: ID no definido.');
}

$id_venta = $_POST['id_venta'];

$sql_detalles = "DELETE FROM venta_de_productos WHERE id_venta = ?";
$stmt_detalles = $conn->prepare($sql_detalles);
$stmt_detalles->bind_param("i", $id_venta);
$stmt_detalles->execute();

$sql = "DELETE FROM Ventas WHERE id_venta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_venta);

if ($stmt->execute()) {
    echo 'Pedido eliminado correctamente.';
} else {
    echo 'Error al eliminar el pedido: ' . $conn->error;
}

$stmt_detalles->close();
$stmt->close();
$conn->close();
?>
