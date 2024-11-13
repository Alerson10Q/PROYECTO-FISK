<?php
session_start();
include 'db_connection.php';

function verificarCedula($cedula) {
    $factores = [2, 9, 8, 7, 6, 3, 4];
    
    if (strlen($cedula) != 7 || !ctype_digit($cedula)) {
        return false;
    }
    
    $suma = 0;
    for ($i = 0; $i < 7; $i++) {
        $suma += $cedula[$i] * $factores[$i];
    }
    
    $proximoMultiplo10 = ceil($suma / 10) * 10;
    $digitoVerificador = $proximoMultiplo10 - $suma;

    return $digitoVerificador;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'checkout') {
    $user_id = $_SESSION['id_usuario'];
    $cedula = isset($_POST['cedula']) ? $_POST['cedula'] : '';

    if (strlen($cedula) != 8 || substr($cedula, -1) != verificarCedula(substr($cedula, 0, 7))) {
        echo 'Cédula de identidad incorrecta.';
        exit;
    }

    $direccion_de_envio = isset($_POST['direccion_de_envio']) ? $_POST['direccion_de_envio'] : '';
    $forma_de_pago = isset($_POST['forma_de_pago']) ? $_POST['forma_de_pago'] : '';
    $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
    $datos_extra_notas = isset($_POST['datos_extra_notas']) ? $_POST['datos_extra_notas'] : '';

    $sql = "SELECT c.id_producto, p.precio, c.cantidad, p.stock_cantidad
            FROM Carrito c
            JOIN Productos p ON c.id_producto = p.id_producto
            WHERE c.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $productos = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($productos)) {
        echo 'No hay productos en el carrito.';
        exit;
    }

    $total = 0;
    foreach ($productos as $producto) {
        $total += $producto['precio'] * $producto['cantidad'];
    }

    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO Ventas (forma_de_pago, fecha_de_venta, productos_vendidos, valor_de_venta, estado, direccion_de_envio, datos_extra_notas, id_usuario) 
                VALUES (?, NOW(), ?, ?, 'en proceso', ?, ?, ?)";
        $productos_vendidos = json_encode(array_map(function($producto) {
            return [
                'id_producto' => $producto['id_producto'],
                'cantidad' => $producto['cantidad']
            ];
        }, $productos));
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $forma_de_pago, $productos_vendidos, $total, $direccion_de_envio, $datos_extra_notas, $user_id);
        $stmt->execute();
        $id_venta = $stmt->insert_id;

        $sql = "INSERT INTO venta_de_productos (id_venta, id_producto, precio_del_momento, cantidad) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        foreach ($productos as $producto) {
            $stmt->bind_param("iidi", $id_venta, $producto['id_producto'], $producto['precio'], $producto['cantidad']);
            $stmt->execute();

            $nuevo_stock = $producto['stock_cantidad'] - $producto['cantidad'];
            $sql_update_stock = "UPDATE Productos SET stock_cantidad = ? WHERE id_producto = ?";
            $stmt_update = $conn->prepare($sql_update_stock);
            $stmt_update->bind_param("ii", $nuevo_stock, $producto['id_producto']);
            $stmt_update->execute();
        }

        $sql = "DELETE FROM Carrito WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $conn->commit();

        echo 'Compra realizada con éxito.';

    } catch (Exception $e) {
        $conn->rollback();
        echo 'Error al realizar la compra. Por favor, intente nuevamente.<br>';
        echo 'Detalles del error: ' . $e->getMessage();
    }

    $stmt->close();
}

$conn->close();
?>
