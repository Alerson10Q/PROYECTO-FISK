<?php
session_start();
include 'db_connection.php';

// Verificación de sesión activa
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

// Obtener ID de usuario
$id_usuario = $_SESSION['id_usuario'];

// -----------------------
// Baja de Producto
// -----------------------
if (isset($_GET['accion']) && $_GET['accion'] == 'baja') {
    $id_producto = isset($_GET['id_producto']) ? intval($_GET['id_producto']) : 0;

    // Eliminar producto del carrito
    $sql_baja = "DELETE FROM Carrito WHERE id_producto = ? AND id_usuario = ?";
    $stmt_baja = $conn->prepare($sql_baja);
    $stmt_baja->bind_param("ii", $id_producto, $id_usuario);
    $stmt_baja->execute();
    
    header('Location: carrito.php');
    exit;
}

// -----------------------
// Alta de Producto
// -----------------------
if (isset($_POST['accion']) && $_POST['accion'] == 'alta') {
    $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

    // Verifica si el producto ya está en el carrito
    $sql_verificar = "SELECT id_producto FROM Carrito WHERE id_producto = ? AND id_usuario = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $id_producto, $id_usuario);
    $stmt_verificar->execute();
    $stmt_verificar->store_result();

    if ($stmt_verificar->num_rows > 0) {
        // Si el producto ya está en el carrito, actualiza la cantidad
        $sql_update = "UPDATE Carrito SET cantidad = cantidad + ? WHERE id_producto = ? AND id_usuario = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $cantidad, $id_producto, $id_usuario);
        $stmt_update->execute();
    } else {
        // Si el producto no está en el carrito, se inserta como nuevo
        $sql_alta = "INSERT INTO Carrito (id_producto, id_usuario, cantidad) VALUES (?, ?, ?)";
        $stmt_alta = $conn->prepare($sql_alta);
        $stmt_alta->bind_param("iii", $id_producto, $id_usuario, $cantidad);
        $stmt_alta->execute();
    }

    header('Location: carrito.php');
    exit;
}
?>
