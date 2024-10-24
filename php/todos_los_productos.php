<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role'])) {
    header('Location: login.php');
    exit;
}

$sql = "SELECT * FROM Productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Productos</title>
    <link rel="stylesheet" href="../css/todoslosproductos.css">
</head>
<body>
    <div class="container">
        <h1>Todos los Productos</h1>

        <div class="product-grid">
            <?php while ($producto = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="uploaded_images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                    <p>Precio: <?php echo htmlspecialchars($producto['precio']); ?></p>
                    <p>Stock: <?php echo htmlspecialchars($producto['stock_cantidad']); ?></p>
                    <a href="producto_detalle.php?id=<?php echo htmlspecialchars($producto['id_producto']); ?>">Ver Detalle</a>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="back-button">
            <a href="index.php">Volver a la página principal</a>
        </div>
    </div>

    <?php $conn->close(); ?>
    <?php include 'footer.php'; ?>
</body>
</html>