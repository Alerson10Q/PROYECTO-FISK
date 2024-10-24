<?php 
session_start();
include 'db_connection.php';
include 'header.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrativo</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <header>
        <h1>Panel de Administración</h1>
        <nav>
            <ul>
                <li><a href="gestion_productos.php">Gestionar Productos</a></li>
                <li><a href="gestionar_usuarios.php">Gestionar Usuarios</a></li>
                <li><a href="gestionar_pedidos.php">Gestionar Pedidos</a></li>
                <li><a href="formulario_informe_ventas.php">Generar Informe de Ventas</a></li>
                <li><a href="formulario_informe_pedidos.php">Generar Informe de Pedidos</a></li>
                <li><a href="agregar_proveedor.php">Agregar Proveedor</a></li>
                <li><a href="eliminar_proveedor.php">Eliminar Proveedor</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard-content">
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?></h2>
        <p>Este es el panel principal donde puedes gestionar todos los aspectos del sistema.</p>
    </main>

    <footer>
    <?php include 'footer.php'; ?>
    </footer>
</body>
</html>