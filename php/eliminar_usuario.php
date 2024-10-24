<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_usuario > 0) {
    $sql = "SELECT nombre_usuario FROM Usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->bind_result($nombre_usuario);
    if (!$stmt->fetch()) {
        echo '<p>Usuario no encontrado.</p>';
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    try {
        $sql = "DELETE vp FROM venta_de_productos vp
                INNER JOIN ventas v ON vp.id_venta = v.id_venta
                WHERE v.id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();

        $sql = "DELETE FROM ventas WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();

        $sql = "DELETE FROM carrito WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();

        $sql = "DELETE FROM Clientes WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $stmt->close();

        $sql = "DELETE FROM Usuarios WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);

        if ($stmt->execute()) {
            header('Location: gestionar_usuarios.php?message=' . urlencode($nombre_usuario . ' ha sido eliminado.'));
            exit;
        } else {
            echo '<p>Error al eliminar el usuario: ' . $conn->error . '</p>';
        }

        $stmt->close();
    } catch (Exception $e) {
        echo '<p>Error: ' . $e->getMessage() . '</p>';
    }
} else {
    echo '<p>Usuario no válido.</p>';
}

$conn->close();
?>
