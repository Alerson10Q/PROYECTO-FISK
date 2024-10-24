<?php
include 'db_connection.php';

session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['clasificacion'] !== 'Administrador') {
    echo '<p>No tienes permiso para acceder a esta página.</p>';
    exit;
}

$precio = $_POST['precio'];
$stock_cantidad = $_POST['stock_cantidad'];
$marca = $_POST['marca'];
$imagen = $_POST['imagen'];
$descripcion = $_POST['descripcion'];
$tipo_producto = $_POST['tipo_producto'];
$id_proveedor = $_POST['id_proveedor'];

$sql = "INSERT INTO Productos (precio, stock_cantidad, marca, imagen, descripcion, tipo_producto, id_proveedor) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("dssssii", $precio, $stock_cantidad, $marca, $imagen, $descripcion, $tipo_producto, $id_proveedor);

if ($stmt->execute()) {
    echo '<p>Producto agregado exitosamente.</p>';
} else {
    echo '<p>Error al agregar el producto: ' . $conn->error . '</p>';
}

$stmt->close();
$conn->close();
?>
