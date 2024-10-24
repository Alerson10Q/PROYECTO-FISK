<?php
session_start();
include 'db_connection.php';
include 'header.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    if ($id_producto > 0 && $cantidad > 0) {
        $sql = "SELECT * FROM Carrito WHERE id_producto = ? AND id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_producto, $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $sql = "UPDATE Carrito SET cantidad = cantidad + ? WHERE id_producto = ? AND id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $cantidad, $id_producto, $id_usuario);
        } else {
            $sql = "INSERT INTO Carrito (id_producto, id_usuario, cantidad) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $id_producto, $id_usuario, $cantidad);
        }

        if ($stmt->execute()) {
            header('Location: carrito.php');
            exit;
        } else {
            $message = 'Error al agregar el producto al carrito: ' . htmlspecialchars($stmt->error);
        }
    } else {
        $message = 'ID de producto o cantidad inválida.';
    }
}

$sql = "SELECT c.id_producto, p.marca, p.descripcion, p.precio, c.cantidad
        FROM Carrito c
        JOIN Productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$productos = $result->fetch_all(MYSQLI_ASSOC);

if (!empty($message)) {
    echo '<p style="color: red;">' . htmlspecialchars($message) . '</p>';
}

if (empty($productos)) {
    echo '<p>Tu carrito está vacío.</p>';
} else {
    echo '<div class="cart-container">';
    echo '<h2>Tu Carrito</h2>';
    echo '<table>';
    echo '<thead><tr><th>Producto</th><th>Descripción</th><th>Precio</th><th>Cantidad</th><th>Total</th><th>Acciones</th></tr></thead>';
    echo '<tbody>';

    $total = 0;
    foreach ($productos as $producto) {
        $subtotal = $producto['precio'] * $producto['cantidad'];
        $total += $subtotal;
        echo '<tr>';
        echo '<td>' . htmlspecialchars($producto['marca']) . '</td>';
        echo '<td>' . htmlspecialchars($producto['descripcion']) . '</td>';
        echo '<td>$' . number_format($producto['precio'], 2) . '</td>';
        echo '<td>' . htmlspecialchars($producto['cantidad']) . '</td>';
        echo '<td>$' . number_format($subtotal, 2) . '</td>';
        echo '<td><a href="eliminar_del_carrito.php?id_producto=' . htmlspecialchars($producto['id_producto']) . '">Eliminar</a></td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '<tfoot><tr><td colspan="4">Total</td><td>$' . number_format($total, 2) . '</td><td></td></tr></tfoot>';
    echo '</table>';

    echo '<h3>Detalles de la Compra</h3>';
    echo '<form action="comprar.php" method="post">';
    echo '<label for="direccion_de_envio">Dirección de Envío:</label>';
    echo '<input type="text" name="direccion_de_envio" id="direccion_de_envio" required>';

    echo '<label for="forma_de_pago">Forma de Pago:</label>';
    echo '<select name="forma_de_pago" id="forma_de_pago" required>';
    echo '<option value="">Seleccione...</option>';
    echo '<option value="tarjeta">Tarjeta de Crédito/Débito</option>';
    echo '<option value="efectivo">Efectivo en Red de Cobranza</option>';
    echo '</select>';

    echo '<div id="datos_tarjeta" style="display:none;">';
    echo '<label for="tarjeta">Número de Tarjeta:</label>';
    echo '<input type="text" name="tarjeta" id="tarjeta">';
    echo '</div>';

    echo '<div id="datos_cedula" style="display:none;">';
    echo '<label for="cedula">Cédula:</label>';
    echo '<input type="text" name="cedula" id="cedula">';
    echo '</div>';

    echo '<div id="red_cobranza" style="display:none;">';
    echo '<label for="red_cobranza">Red de Cobranza:</label>';
    echo '<select name="red_cobranza" id="red_cobranza">';
    echo '<option value="">Seleccione...</option>';
    echo '<option value="Abitab">Abitab</option>';
    echo '<option value="RedPagos">RedPagos</option>';
    echo '</select>';
    echo '</div>';

    echo '<label for="datos_extra_notas">Notas adicionales:</label>';
    echo '<input type="text" name="datos_extra_notas" id="datos_extra_notas">';

    echo '<input type="hidden" name="action" value="checkout">';
    echo '<button type="submit">Proceder a la Compra</button>';
    echo '</form>';
    
    echo '</div>';

    echo '<script>
        document.getElementById("forma_de_pago").addEventListener("change", function() {
            var formaPago = this.value;
            document.getElementById("datos_tarjeta").style.display = formaPago === "tarjeta" ? "block" : "none";
            document.getElementById("datos_cedula").style.display = formaPago === "efectivo" ? "block" : "none";
            document.getElementById("red_cobranza").style.display = formaPago === "efectivo" ? "block" : "none";
        });
    </script>';
}

$conn->close();
include 'footer.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
</body>
</html>
