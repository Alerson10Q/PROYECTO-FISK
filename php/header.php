<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!session_id()) {
    echo "Error: La sesión no se ha iniciado correctamente.";
    exit;
}

$paginas_publicas = ['index.php', 'about_us.php', 'todos_los_productos.php', 'carrito.php', 'login.php', 'register.php'];

if (!in_array(basename($_SERVER['PHP_SELF']), $paginas_publicas)) {
    if (!isset($_SESSION['nombre_usuario'])) {
        echo "Redirigiendo a login.php desde " . basename($_SERVER['PHP_SELF']); // Depuración
        header('Location: login.php');
        exit;
    }
}

if (isset($_SESSION['nombre_usuario']) && isset($_SESSION['user_role'])) {
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $user_role = $_SESSION['user_role'];
} else {
    if (!in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php'])) {
        header("Location: login.php");
        exit;
    }
}

$conexion = new mysqli('localhost', 'admin', 'admin', 'PintureriaArcoiris_db');
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$busqueda = isset($_GET['q']) ? $_GET['q'] : '';

$query = "SELECT * FROM Productos WHERE 1=1";
if ($categoria) {
    $query .= " AND tipo_producto = '$categoria'";
}
if ($busqueda) {
    $query .= " AND nombre LIKE '%$busqueda%'";
}
$resultado = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pinturería Arcoíris</title>
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
<header>
    <div class="header-container">
        <h1><a href="index.php">Pinturería Arcoíris</a></h1>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Inicio</a></li>
                <?php if (isset($user_role) && $user_role == 'Administrador'): ?>
                    <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
                <?php endif; ?>
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="todos_los_productos.php">Productos</a></li>
                <li><a href="carrito.php">Carrito</a></li>
                <?php if (isset($nombre_usuario)): ?>
                    <li>Hola, <?php echo htmlspecialchars($nombre_usuario); ?></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar sesión</a></li>
                <?php endif; ?>

                <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
                    <li>
                        <form action="index.php" method="get">
                            <input type="text" name="q" placeholder="Buscar productos" value="<?php echo htmlspecialchars($busqueda); ?>">
                            <button type="submit">Buscar</button>
                        </form>
                    </li>
                    <li>
                        <form action="index.php" method="get">
                            <select name="categoria" id="categoria" onchange="this.form.submit()">
                                <option value="">Filtrar por categoría</option>
                                <option value="mini-ferreteria" <?php echo $categoria == 'mini-ferreteria' ? 'selected' : ''; ?>>Mini Ferretería</option>
                                <option value="pinturas" <?php echo $categoria == 'pinturas' ? 'selected' : ''; ?>>Pinturas</option>
                                <option value="accesorios" <?php echo $categoria == 'accesorios' ? 'selected' : ''; ?>>Accesorios</option>
                            </select>
                        </form>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main>
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
        <h2>Productos</h2>
        <div class="productos-container">
            <?php 
            if ($resultado && $resultado->num_rows > 0): 
                while ($producto = $resultado->fetch_assoc()): ?>
                    <div class="producto">
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p>Precio: $<?php echo htmlspecialchars($producto['precio']); ?></p>
                        <img src="uploaded_images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="max-width: 200px; height: auto;">
                        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <a class="ver-mas" href="producto.php?id=<?php echo $producto['id_producto']; ?>">Ver más</a>
                    </div>
                <?php endwhile; 
            else: ?>
                <p>No hay productos disponibles.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>

<?php
$conexion->close();
?>
