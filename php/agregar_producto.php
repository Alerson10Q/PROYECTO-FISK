<?php
session_start();
include 'header.php';
include 'db_connection.php';

$error = '';

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

    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = uniqid() . '_' . $_FILES['imagen']['name'];
        $imagen_path = 'uploaded_images/' . $imagen;

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_path)) {
            $error = "Error al cargar la imagen.";
        }
    } else {
        $error = "No se ha seleccionado ninguna imagen.";
    }

    if (!$error) {
        $sql = "INSERT INTO Productos (nombre, precio, stock_cantidad, marca, imagen, descripcion, tipo_producto, id_proveedor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sdsdssss', $nombre, $precio, $stock_cantidad, $marca, $imagen, $descripcion, $tipo_producto, $id_proveedor);

        if ($stmt->execute()) {
            $id_producto = $conn->insert_id;

            if ($tipo_producto === 'Pinturas') {
                $sql_pinturas = "INSERT INTO Pinturas (id_producto, litros, funcion_aplicacion, codigo_de_color, fecha_vencimiento, terminacion, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_pinturas = $conn->prepare($sql_pinturas);
                $stmt_pinturas->bind_param('idsssss', $id_producto, $litros, $funcion_aplicacion, $codigo_de_color, $fecha_vencimiento, $terminacion, $fecha_creacion);
                $stmt_pinturas->execute();
            } elseif ($tipo_producto === 'Accesorios') {
                $sql_accesorios = "INSERT INTO Accesorios (id_producto, medidas, tipo) VALUES (?, ?, ?)";
                $stmt_accesorios = $conn->prepare($sql_accesorios);
                $stmt_accesorios->bind_param('iss', $id_producto, $medidas, $tipo);
                $stmt_accesorios->execute();
            } elseif ($tipo_producto === 'Mini-ferretería') {
                $sql_mini_ferreteria = "INSERT INTO Mini_ferreteria (id_producto, garantia) VALUES (?, ?)";
                $stmt_mini_ferreteria = $conn->prepare($sql_mini_ferreteria);
                $stmt_mini_ferreteria->bind_param('is', $id_producto, $garantia);
                $stmt_mini_ferreteria->execute();
            }

            echo "Producto agregado exitosamente.";
        } else {
            $error = "Error al agregar el producto: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../css/agregar_producto.css">
</head>
<body>
    <div class="container">
        <h2>Agregar Nuevo Producto</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="agregar_producto.php" method="post" enctype="multipart/form-data">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <label for="stock_cantidad">Cantidad en Stock:</label>
            <input type="number" id="stock_cantidad" name="stock_cantidad" required>

            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" required>

            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="tipo_producto">Tipo de Producto:</label>
            <select id="tipo_producto" name="tipo_producto" onchange="showFields(this.value)" required>
                <option value="">Seleccionar</option>
                <option value="Pinturas">Pinturas</option>
                <option value="Accesorios">Accesorios</option>
                <option value="Mini-ferretería">Mini-ferretería</option>
            </select>

            <div id="pinturas_fields" style="display:none;">
                <label for="litros">Litros:</label>
                <input type="number" id="litros" name="litros" step="0.01">

                <label for="funcion_aplicacion">Función de Aplicación:</label>
                <select id="funcion_aplicacion" name="funcion_aplicacion">
                    <option value="">Seleccionar</option>
                    <option value="exterior">Exterior</option>
                    <option value="interior">Interior</option>
                    <option value="metal">Metal</option>
                    <option value="madera">Madera</option>
                </select>

                <label for="codigo_de_color">Código de Color:</label>
                <input type="text" id="codigo_de_color" name="codigo_de_color">

                <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">

                <label for="terminacion">Terminación:</label>
                <select id="terminacion" name="terminacion">
                    <option value="">Seleccionar</option>
                    <option value="mate">Mate</option>
                    <option value="brillante">Brillante</option>
                    <option value="semimate">Semimate</option>
                    <option value="satinada">Satinada</option>
                </select>

                <label for="fecha_creacion">Fecha de Creación:</label>
                <input type="date" id="fecha_creacion" name="fecha_creacion">

            </div>

            <div id="accesorios_fields" style="display:none;">
                <label for="medidas">Medidas:</label>
                <input type="text" id="medidas" name="medidas">

                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo">
            </div>

            <div id="mini_ferreteria_fields" style="display:none;">
                <label for="garantia">Garantía:</label>
                <input type="text" id="garantia" name="garantia">
            </div>

            <label for="id_proveedor">Proveedor:</label>
            <select id="id_proveedor" name="id_proveedor" required>
                <option value="">Seleccionar Proveedor</option>
                <?php if ($result_proveedores->num_rows > 0): ?>
                    <?php while ($row = $result_proveedores->fetch_assoc()): ?>
                        <option value="<?php echo $row['id_proveedor']; ?>"><?php echo $row['nombre']; ?></option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <button type="submit">Agregar Producto</button>
        </form>
    </div>
    <script>
        function showFields(value) {
            document.getElementById('pinturas_fields').style.display = value === 'Pinturas' ? 'block' : 'none';
            document.getElementById('accesorios_fields').style.display = value === 'Accesorios' ? 'block' : 'none';
            document.getElementById('mini_ferreteria_fields').style.display = value === 'Mini-ferretería' ? 'block' : 'none';
        }
    </script>
</body>
</html>
