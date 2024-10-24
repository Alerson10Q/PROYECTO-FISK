<?php
session_start();
include 'header.php';
include 'db_connection.php';
$_SESSION['carrito'][] = $producto;

$error = '';

$sql_proveedores = "SELECT id_proveedor, nombre FROM Proveedores";
$result_proveedores = $conn->query($sql_proveedores);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = htmlspecialchars($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock_cantidad = intval($_POST['stock_cantidad']);
    $marca = htmlspecialchars($_POST['marca']);
    $descripcion = htmlspecialchars($_POST['descripcion']);
    $tipo_producto = $_POST['tipo_producto'];
    $id_proveedor = intval($_POST['id_proveedor']);

    $litros = $funcion_aplicacion = $codigo_de_color = $fecha_vencimiento = $terminacion = '';
    $nombre_color = $tintes_utilizados = '';

    if ($tipo_producto === 'Accesorios') {
        $medidas = htmlspecialchars($_POST['medidas']);
        $tipo = htmlspecialchars($_POST['tipo']);
    } elseif ($tipo_producto === 'Mini-ferretería') {
        $garantia = htmlspecialchars($_POST['garantia']);
        $tipo = htmlspecialchars($_POST['tipo']);
    } elseif ($tipo_producto === 'Pinturas') {
        $litros = floatval($_POST['litros']);
        $funcion_aplicacion = htmlspecialchars($_POST['funcion_aplicacion']);
        $codigo_de_color = htmlspecialchars($_POST['codigo_de_color']);
        $fecha_vencimiento = htmlspecialchars($_POST['fecha_vencimiento']);
        $terminacion = htmlspecialchars($_POST['terminacion']);
        $nombre_color = htmlspecialchars($_POST['nombre_color']);
        $tintes_utilizados = htmlspecialchars($_POST['tintes_utilizados']);
    }

    $imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = uniqid() . '_' . basename($_FILES['imagen']['name']);
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
                $sql_pinturas = "INSERT INTO Pinturas (id_producto, litros, funcion_aplicacion, codigo_de_color, fecha_vencimiento, terminacion) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_pinturas = $conn->prepare($sql_pinturas);
                $stmt_pinturas->bind_param('idssss', $id_producto, $litros, $funcion_aplicacion, $codigo_de_color, $fecha_vencimiento, $terminacion);
                $stmt_pinturas->execute();
            } elseif ($tipo_producto === 'Accesorios') {
                $sql_accesorios = "INSERT INTO Accesorios (id_producto, medidas, tipo) VALUES (?, ?, ?)";
                $stmt_accesorios = $conn->prepare($sql_accesorios);
                $stmt_accesorios->bind_param('iss', $id_producto, $medidas, $tipo);
                $stmt_accesorios->execute();
            } elseif ($tipo_producto === 'Mini-ferretería') {
                $sql_mini_ferreteria = "INSERT INTO Mini_ferreteria (id_producto, garantia, tipo) VALUES (?, ?, ?)";
                $stmt_mini_ferreteria = $conn->prepare($sql_mini_ferreteria);
                $stmt_mini_ferreteria->bind_param('iss', $id_producto, $garantia, $tipo);
                $stmt_mini_ferreteria->execute();
            }
            echo "Producto agregado exitosamente.";
        } else {
            $error = "Error al agregar el producto: " . $stmt->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../css/agregar_producto.css">
</head>

