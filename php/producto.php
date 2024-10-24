<?php
session_start();
include 'db_connection.php';
include 'header.php';

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM Productos WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();

if (!$producto) {
    echo '<p>Producto no encontrado.</p>';
    $conn->close();
    exit;
}

$tipo_producto = $producto['tipo_producto'];
$detalles_adicionales = [];

if ($tipo_producto === 'Accesorios') {
    $sql_accesorios = "SELECT medidas, tipo FROM Accesorios WHERE id_producto = ?";
    $stmt_accesorios = $conn->prepare($sql_accesorios);
    $stmt_accesorios->bind_param("i", $id_producto);
    $stmt_accesorios->execute();
    $result_accesorios = $stmt_accesorios->get_result();
    $detalles_adicionales = $result_accesorios->fetch_assoc();
} elseif ($tipo_producto === 'Pinturas') {
    $sql_pinturas = "SELECT litros, funcion_aplicacion, codigo_de_color, fecha_vencimiento, terminacion, fecha_creacion FROM Pinturas WHERE id_producto = ?";
    $stmt_pinturas = $conn->prepare($sql_pinturas);
    $stmt_pinturas->bind_param("i", $id_producto);
    $stmt_pinturas->execute();
    $result_pinturas = $stmt_pinturas->get_result();
    $detalles_adicionales = $result_pinturas->fetch_assoc();
} elseif ($tipo_producto === 'Mini-ferretería') {
    $sql_mini_ferreteria = "SELECT garantia FROM Mini_ferreteria WHERE id_producto = ?";
    $stmt_mini_ferreteria = $conn->prepare($sql_mini_ferreteria);
    $stmt_mini_ferreteria->bind_param("i", $id_producto);
    $stmt_mini_ferreteria->execute();
    $result_mini_ferreteria = $stmt_mini_ferreteria->get_result();
    $detalles_adicionales = $result_mini_ferreteria->fetch_assoc();
}

$max_cantidad = $producto['stock_cantidad'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Producto</title>
    <link rel="stylesheet" href="../css/producto.css">
</head>
<body>
<main>
    <div class="container">
        <section>
            <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
            <img class="producto-imagen" src="uploaded_images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
            <p>Precio: $<?php echo number_format($producto['precio'], 2); ?></p>
            <p>Stock: <?php echo htmlspecialchars($producto['stock_cantidad']); ?></p>
            <p>Marca: <?php echo htmlspecialchars($producto['marca']); ?></p>
            <p>Tipo de Producto: <?php echo htmlspecialchars($producto['tipo_producto']); ?></p>

            <?php if ($tipo_producto === 'Accesorios'): ?>
                <p>Medidas: <?php echo htmlspecialchars($detalles_adicionales['medidas']); ?></p>
                <p>Tipo: <?php echo htmlspecialchars($detalles_adicionales['tipo']); ?></p>
            <?php elseif ($tipo_producto === 'Pinturas'): ?>
                <p>Litros: <?php echo htmlspecialchars($detalles_adicionales['litros']); ?></p>
                <p>Función de Aplicación: <?php echo htmlspecialchars($detalles_adicionales['funcion_aplicacion']); ?></p>
                <p>Código de Color: <?php echo htmlspecialchars($detalles_adicionales['codigo_de_color']); ?></p>
                <p>Fecha de Vencimiento: <?php echo htmlspecialchars($detalles_adicionales['fecha_vencimiento']); ?></p>
                <p>Terminación: <?php echo htmlspecialchars($detalles_adicionales['terminacion']); ?></p>
                <p>Fecha de Creación: <?php echo htmlspecialchars($detalles_adicionales['fecha_creacion']); ?></p>
            <?php elseif ($tipo_producto === 'Mini-ferretería'): ?>
                <p>Garantía: <?php echo htmlspecialchars($detalles_adicionales['garantia']); ?></p>
            <?php endif; ?>

            <form action="carrito.php" method="post">
                <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                <label for="cantidad">Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" min="1" max="<?php echo $max_cantidad; ?>" required>
                <input type="hidden" name="action" value="add">
                <button type="submit">Agregar al Carrito</button>
            </form>
        </section>
    </div>
</main>

<?php
$conn->close();
include 'footer.php';
?>
</body>
</html>
