<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$sql = "SELECT * FROM Proveedores";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Proveedor</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/proveedor.css">
</head>
<body>
    <div class="admin-dashboard">
        <h1>Eliminar Proveedor</h1>
        <form action="eliminar_proveedor.php" method="post">
            <label for="id_proveedor">ID del Proveedor:</label>
            <select id="id_proveedor" name="id_proveedor">
                <?php while ($proveedor = $result->fetch_assoc()): ?>
                    <option value="<?php echo $proveedor['id_proveedor']; ?>"><?php echo $proveedor['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
            
            <button type="submit">Eliminar Proveedor</button>
        </form>
    </div>
    <footer>
        <?php include 'footer.php'; ?>
    </footer>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_proveedor'])) {
    $id_proveedor = $_POST['id_proveedor'];

    $sql = "DELETE FROM Venta_de_productos WHERE id_producto IN (SELECT id_producto FROM Productos WHERE id_proveedor = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_proveedor);
    $stmt->execute();
    $stmt->close();

    $sql = "DELETE FROM Accesorios WHERE id_producto IN (SELECT id_producto FROM Productos WHERE id_proveedor = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_proveedor);
    $stmt->execute();
    $stmt->close();

    $sql = "DELETE FROM Mini_ferreteria WHERE id_producto IN (SELECT id_producto FROM Productos WHERE id_proveedor = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_proveedor);
    $stmt->execute();
    $stmt->close();

    $sql = "DELETE FROM Productos WHERE id_proveedor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_proveedor);
    $stmt->execute();
    $stmt->close();

    $sql = "DELETE FROM Proveedores WHERE id_proveedor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_proveedor);

    if ($stmt->execute()) {
        echo '<p>Proveedor eliminado exitosamente.</p>';
    } else {
        echo '<p>Error al eliminar el proveedor: ' . $conn->error . '</p>';
    }

    $stmt->close();
    $conn->close();
}
?>
