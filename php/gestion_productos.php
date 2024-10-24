<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$sql = "SELECT * FROM Productos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Productos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions a {
            text-decoration: none;
            color: #007bff;
        }
        .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <h1>Gestionar Productos</h1>
        <a href="agregar_producto.php" class="btn btn-primary">Agregar Nuevo Producto</a>
        <?php if (isset($_GET['msg'])): ?>
            <p class="message"><?php echo htmlspecialchars($_GET['msg']); ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($producto = $result->fetch_assoc()) {
                        $id_producto = htmlspecialchars($producto['id_producto']);
                        $nombre = htmlspecialchars($producto['nombre']);
                        $precio = number_format($producto['precio'], 2);
                        $stock = htmlspecialchars($producto['stock_cantidad']);
                ?>
                    <tr>
                        <td><?php echo $id_producto; ?></td>
                        <td><?php echo $nombre; ?></td>
                        <td>$<?php echo $precio; ?></td>
                        <td><?php echo $stock; ?></td>
                        <td class="actions">
                            <a href="editar_producto.php?id=<?php echo $id_producto; ?>">Editar</a>
                            <a href="eliminar_producto.php?id=<?php echo $id_producto; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</a>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="5">No hay productos disponibles.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
