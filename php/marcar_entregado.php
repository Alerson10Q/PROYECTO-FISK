<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_venta'], $_POST['correo_cliente'])) {
    $id_venta = intval($_POST['id_venta']);
    $correo_cliente = $_POST['correo_cliente'];

    $sql = "UPDATE ventas SET estado = 'entregado' WHERE id_venta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_venta);

    if ($stmt->execute()) {
        header('Location: gestionar_pedidos.php?success=1');
        exit;
    } else {
        echo "Error al actualizar el estado del pedido: " . $conn->error;
    }
}

$conn->close();
?>
