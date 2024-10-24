<?php
session_start();
include 'db_connection.php';
include 'header.php';

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';

$sql = "SELECT p.*, a.medidas, a.tipo AS tipo_accesorio, m.garantia, m.tipo AS tipo_mini_ferreteria 
        FROM Productos p 
        LEFT JOIN Accesorios a ON p.id_producto = a.id_producto 
        LEFT JOIN Mini_ferreteria m ON p.id_producto = m.id_producto 
        WHERE p.id_producto = ?";
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

$sql_proveedores = "SELECT id_proveedor, nombre FROM Proveedores";
$result_proveedores = $conn->query($sql_proveedores);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $stock_cantidad = $_POST['stock_cantidad'] ?? '';
    $marca = $_POST['marca'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $tipo_producto = $_POST['tipo_producto'] ?? '';
    $id_proveedor = $_POST['id_proveedor'] ?? '';

    $litros = $funcion_aplicacion = $codigo_de_color = $fecha_vencimiento = '';
    $terminacion = $fecha_creacion = '';
    $medidas = $tipo = $garantia = '';

    if ($tipo_producto === 'Accesorios') {
        $medidas = $_POST['medidas'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
    } elseif ($tipo_producto === 'Mini-ferretería') {
        $garantia = $_POST['garantia'] ?? '';
    } elseif ($tipo_producto === 'Pinturas') {
        $litros = $_POST['litros'] ?? '';
        $funcion_aplicacion = $_POST['funcion_aplicacion'] ?? '';
        $codigo_de_color = $_POST['codigo_de_color'] ?? '';
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';
        $terminacion = $_POST['terminacion'] ?? '';
        $fecha_creacion = $_POST['fecha_creacion'] ?? '';
    }

    $imagen = $producto['imagen'];
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = uniqid() . '_' . $_FILES['imagen']['name'];
        $imagen_path = 'uploaded_images/' . $imagen;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_path)) {
            $error = "Error al cargar la imagen.";
        }
    }

    if (!$error) {
        $sql = "UPDATE Productos SET nombre = ?, precio = ?, stock_cantidad = ?, marca = ?, imagen = ?, descripcion = ?, tipo_producto = ?, id_proveedor = ? WHERE id_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sdsdssssi', $nombre, $precio, $stock_cantidad, $marca, $imagen, $descripcion, $tipo_producto, $id_proveedor, $id_producto);

        if ($stmt->execute()) {

            if ($tipo_producto === 'Pinturas') {
                $sql_pinturas = "UPDATE Pinturas SET litros = ?, funcion_aplicacion = ?, codigo_de_color = ?, fecha_vencimiento = ?, terminacion = ?, fecha_creacion = ? WHERE id_producto = ?";
                $stmt_pinturas = $conn->prepare($sql_pinturas);
                $stmt_pinturas->bind_param('dsssssi', $litros, $funcion_aplicacion, $codigo_de_color, $fecha_vencimiento, $terminacion, $fecha_creacion, $id_producto);
                $stmt_pinturas->execute();
            } elseif ($tipo_producto === 'Accesorios') {
                $sql_accesorios = "UPDATE Accesorios SET medidas = ?, tipo = ? WHERE id_producto = ?";
                $stmt_accesorios = $conn->prepare($sql_accesorios);
                $stmt_accesorios->bind_param('ssi', $medidas, $tipo, $id_producto);
                $stmt_accesorios->execute();
            } elseif ($tipo_producto === 'Mini-ferretería') {
                $sql_mini_ferreteria = "UPDATE Mini_ferreteria SET garantia = ? WHERE id_producto = ?";
                $stmt_mini_ferreteria = $conn->prepare($sql_mini_ferreteria);
                $stmt_mini_ferreteria->bind_param('si', $garantia, $id_producto);
                $stmt_mini_ferreteria->execute();
            }

            echo "Producto actualizado exitosamente.";
        } else {
            $error = "Error al actualizar el producto: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="../css/editar_producto.css">
</head>
<body>
    <div class="container">
        <h2>Editar Producto</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="editar_producto.php?id=<?php echo $id_producto; ?>" method="post" enctype="multipart/form-data">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>

            <label for="stock_cantidad">Cantidad en Stock:</label>
            <input type="number" id="stock_cantidad" name="stock_cantidad" value="<?php echo htmlspecialchars($producto['stock_cantidad']); ?>" required>

            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($producto['marca']); ?>" required>

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

            <label for="tipo_producto">Tipo de Producto:</label>
            <select id="tipo_producto" name="tipo_producto" onchange="showFields(this.value)" required>
                <option value="">Seleccionar</option>
                <option value="Pinturas" <?php echo ($producto['tipo_producto'] === 'Pinturas') ? 'selected' : ''; ?>>Pinturas</option>
                <option value="Accesorios" <?php echo ($producto['tipo_producto'] === 'Accesorios') ? 'selected' : ''; ?>>Accesorios</option>
                <option value="Mini-ferretería" <?php echo ($producto['tipo_producto'] === 'Mini-ferretería') ? 'selected' : ''; ?>>Mini-ferretería</option>
            </select>

            <div id="pinturas_fields" style="display: <?php echo ($producto['tipo_producto'] === 'Pinturas') ? 'block' : 'none'; ?>;">
                <label for="litros">Litros:</label>
                <input type="number" id="litros" name="litros" step="0.01" value="<?php echo htmlspecialchars($producto['litros'] ?? ''); ?>">

                <label for="funcion_aplicacion">Función de Aplicación:</label>
                <select id="funcion_aplicacion" name="funcion_aplicacion">
                    <option value="">Seleccionar</option>
                    <option value="exterior" <?php echo ($producto['funcion_aplicacion'] === 'exterior') ? 'selected' : ''; ?>>Exterior</option>
                    <option value="interior" <?php echo ($producto['funcion_aplicacion'] === 'interior') ? 'selected' : ''; ?>>Interior</option>
                    <option value="metal" <?php echo ($producto['funcion_aplicacion'] === 'metal') ? 'selected' : ''; ?>>Metal</option>
                    <option value="madera" <?php echo ($producto['funcion_aplicacion'] === 'madera') ? 'selected' : ''; ?>>Madera</option>
                </select>

                <label for="codigo_de_color">Código de Color:</label>
                <input type="text" id="codigo_de_color" name="codigo_de_color" value="<?php echo htmlspecialchars($producto['codigo_de_color'] ?? ''); ?>">

                <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="<?php echo htmlspecialchars($producto['fecha_vencimiento'] ?? ''); ?>">

                <label for="terminacion">Terminación:</label>
                <select id="terminacion" name="terminacion">
                    <option value="">Seleccionar</option>
                    <option value="mate" <?php echo ($producto['terminacion'] === 'mate') ? 'selected' : ''; ?>>Mate</option>
                    <option value="brillante" <?php echo ($producto['terminacion'] === 'brillante') ? 'selected' : ''; ?>>Brillante</option>
                    <option value="semimate" <?php echo ($producto['terminacion'] === 'semimate') ? 'selected' : ''; ?>>Semimate</option>
                    <option value="satinada" <?php echo ($producto['terminacion'] === 'satinada') ? 'selected' : ''; ?>>Satinada</option>
                </select>

                <label for="fecha_creacion">Fecha de Creación:</label>
                <input type="date" id="fecha_creacion" name="fecha_creacion" value="<?php echo htmlspecialchars($producto['fecha_creacion'] ?? ''); ?>">
            </div>

            <div id="accesorios_fields" style="display: <?php echo ($producto['tipo_producto'] === 'Accesorios') ? 'block' : 'none'; ?>;">
                <label for="medidas">Medidas:</label>
                <input type="text" id="medidas" name="medidas" value="<?php echo htmlspecialchars($producto['medidas'] ?? ''); ?>">

                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($producto['tipo_accesorio'] ?? ''); ?>">
            </div>

            <div id="mini_ferreteria_fields" style="display: <?php echo ($producto['tipo_producto'] === 'Mini-ferretería') ? 'block' : 'none'; ?>;">
                <label for="garantia">Garantía:</label>
                <input type="text" id="garantia" name="garantia" value="<?php echo htmlspecialchars($producto['garantia'] ?? ''); ?>">
            </div>

            <label for="id_proveedor">Proveedor:</label>
            <select id="id_proveedor" name="id_proveedor" required>
                <option value="">Seleccionar Proveedor</option>
                <?php if ($result_proveedores->num_rows > 0): ?>
                    <?php while ($row = $result_proveedores->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_proveedor']; ?>" <?php echo ($row['id_proveedor'] == $producto['id_proveedor']) ? 'selected' : ''; ?>><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <button type="submit">Actualizar Producto</button>
        </form>
    </div>
    <script>
        function showFields(value) {
            document.getElementById('pinturas_fields').style.display = value === 'Pinturas' ? 'block' : 'none';
            document.getElementById('accesorios_fields').style.display = value === 'Accesorios' ? 'block' : 'none';
            document.getElementById('mini_ferreteria_fields').style.display = value === 'Mini-ferretería' ? 'block' : 'none';
        }
        window.onload = function() {
            showFields('<?php echo htmlspecialchars($producto['tipo_producto']); ?>');
        };
    </script>
</body>
</html>
