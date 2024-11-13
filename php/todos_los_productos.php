<?php
session_start();
include 'header.php';
include 'db_connection.php';

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
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QX4F97GZ55"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QX4F97GZ55');
</script>
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
