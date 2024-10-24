<?php
require 'db_connection.php';

session_start();

if (!isset($_POST['id_venta']) || !isset($_POST['estado'])) {
    echo 'Faltan datos necesarios para actualizar el estado de la venta.';
    exit;
}

$id_venta = $_POST['id_venta'];
$estado = $_POST['estado'];

$sql = "UPDATE Ventas SET estado = ? WHERE id_venta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $estado, $id_venta);

if ($stmt->execute()) {
    echo 'Estado de la venta actualizado correctamente.';
} else {
    echo 'Error al actualizar el estado de la venta: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
