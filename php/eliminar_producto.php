<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_producto) {
    $conn->begin_transaction();
    
    try {
        $sql = "DELETE FROM Mini_ferreteria WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $sql = "DELETE FROM Pinturas WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $sql = "DELETE FROM Accesorios WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $sql = "DELETE FROM Venta_de_productos WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $sql = "DELETE FROM Productos WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();

        $conn->commit();
        
        header('Location: gestion_productos.php');
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error al eliminar el producto: " . htmlspecialchars($e->getMessage());
    }
} else {
    $error = "ID de producto no válido.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Producto</title>
</head>
<body>
    <div class="admin-dashboard">
        <h1>Eliminar Producto</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php else: ?>
            <p>Producto eliminado correctamente.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>