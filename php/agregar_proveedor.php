<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Proveedor</title>
    <link rel="stylesheet" href="../css/proveedor.css">
    <style>
        .notification {
            display: none;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            margin: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <h1>Agregar Proveedor</h1>

        <div id="notification" class="notification">Proveedor agregado exitosamente.</div>

        <form action="guardar_proveedor.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono">
            
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo">
            
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion">
            
            <button type="submit">Agregar Proveedor</button>
        </form>
    </div>

        <script src="../js/agregar_proveedor.js"></script>
</body>
<footer><?php include 'footer.php'; ?> </footer>
</html>
