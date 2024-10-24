<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role'])) {
    header('Location: login.php');
    exit;
}

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_producto > 0) {
    $sql = "SELECT * FROM Productos WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
}

if (!$producto) {
    echo '<p>Producto no encontrado.</p>';
    $conn->close();
    include 'footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Producto</title>
    <link rel="stylesheet" href="../css/productodetalle.css">
</head>
<body>
    <div class="container">
        <h1>Detalle del Producto</h1>

        <?php if (isset($producto['imagen'])) { ?>
            <img src="<?php echo 'uploaded_images/' . htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="imagen-producto">
        <?php } else { ?>
            <p>No disponible</p>
        <?php } ?>
        <div class="product-detail">
            <div>
                <label>ID:</label>
                <p><?php echo htmlspecialchars($producto['id_producto']); ?></ p>
            </div>
            <div>
                <label>Nombre:</label>
                <p><?php echo htmlspecialchars($producto['nombre']); ?></p>
            </div>
            <div>
                <label>Precio:</label>
                <p><?php echo htmlspecialchars($producto['precio']); ?></p>
            </div>
            <div>
                <label>Stock:</label>
                <p><?php echo htmlspecialchars($producto['stock_cantidad']); ?></p>
            </div>
            <div>
                <label>Descripción:</label>
                <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
            </div>
            <div>
                <label>Marca:</label>
                <p><?php echo htmlspecialchars($producto['marca']); ?></p>
            </div>
            <div>
                <label>Paleta de Color:</label>
                <?php if (isset($producto['paleta_color'])) { ?>
                    <p><?php echo htmlspecialchars($producto['paleta_color']); ?></p>
                <?php } else { ?>
                    <p>No disponible</p>
                <?php } ?>
            </div>
            <div>
                <label>Garantía:</label>
                <?php if (isset($producto['garantia'])) { ?>
                    <p><?php echo htmlspecialchars($producto['garantia']); ?></p>
                <?php } else { ?>
                    <p>No disponible</p>
                <?php } ?>
            </div>
            <div>
                <label>Pintura:</label>
                <?php if (isset($producto['pintura'])) { ?>
                    <p><?php echo htmlspecialchars($producto['pintura']); ?></p>
                <?php } else { ?>
                    <p>No disponible</p>
                <?php } ?>
            </div>
            <div>
                <label>Litros:</label>
                <?php if (isset($producto['litros'])) { ?>
                    <p><?php echo htmlspecialchars($producto['litros']); ?></p>
                <?php } else { ?>
                    <p>No disponible</p>
                <?php } ?>
            </div>
            <div>
                <label>Tipo de Producto:</label>
                <p><?php echo htmlspecialchars($producto['tipo_producto']); ?></p>
            </div>
        </div>

        <form action="carrito.php" method="post">
            <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
            <input type="number" name="cantidad" min="1" max="<?php echo $producto['stock_cantidad']; ?>" required>
            <input type="hidden" name="action" value="add">
            <button type="submit">Agregar al Carrito</button>
        </form>

        <div class="back-button">
            <a href="index.php">Volver al inicio</a>
        </div>
    </div>

    <?php
    $conn->close();
    include 'footer.php';
    ?>
</body>
</html>