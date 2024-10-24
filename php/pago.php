<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_producto = isset($_GET['id_producto']) ? intval($_GET['id_producto']) : 0;
$cantidad = isset($_GET['cantidad']) ? intval($_GET['cantidad']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <h2>Selecciona el Método de Pago</h2>
            <form action="procesar_pago.php" method="post">
                <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                <input type="hidden" name="cantidad" value="<?php echo $cantidad; ?>">

                <label for="forma_de_pago">Forma de Pago:</label>
                <select id="forma_de_pago" name="forma_de_pago" required>
                    <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                    <option value="efectivo">Efectivo en Redes de Cobranza</option>
                </select>

                <div id="metodo_pago_detalle">
                </div>

                <button type="submit">Pagar</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php';?>
    <script src="../js/pago.js"></script>


</body>
</html>
