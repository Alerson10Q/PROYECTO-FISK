<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_producto = isset($_GET['id_producto']) ? intval($_GET['id_producto']) : 0;
$id_usuario = $_SESSION['id_usuario'];

$sql = "DELETE FROM Carrito WHERE id_producto = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_producto, $id_usuario);
$stmt->execute();

header('Location: carrito.php');
exit;
?>
