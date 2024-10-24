<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$id_usuario = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_usuario > 0) {
    $sql = "SELECT * FROM Usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
}

if (!$usuario) {
    echo '<p>Usuario no encontrado. Verifica el ID del usuario.</p>';
    $conn->close();
    include 'footer.php';
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $correo = trim($_POST['correo']);
    $clasificacion = trim($_POST['clasificacion']);

    if (empty($nombre_usuario)) {
        $errores[] = "El nombre de usuario es requerido.";
    }
    if (empty($correo)) {
        $errores[] = "El correo electrónico es requerido.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }
    if (empty($clasificacion)) {
        $errores[] = "La clasificación es requerida.";
    }

    if (empty($errores)) {
        $sql_verificar = "SELECT id_usuario FROM Usuarios WHERE correo = ? AND id_usuario != ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("si", $correo, $id_usuario);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {
            $errores[] = "El correo electrónico ya está en uso por otro usuario.";
        } else {
            $sql = "UPDATE Usuarios SET nombre_usuario = ?, correo = ?, clasificacion = ? WHERE id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nombre_usuario, $correo, $clasificacion, $id_usuario);

            if ($stmt->execute()) {
                header('Location: gestionar_usuarios.php');
                exit();
            } else {
                $errores[] = "Error al actualizar el usuario: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/editar_usuario.css">
</head>
<body>
    <div class="admin-dashboard">
        <h1>Editar Usuario</h1>
        <?php
        if (!empty($errores)) {
            echo '<div class="error">';
            foreach ($errores as $error) {
                echo '<p>' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
        ?>
        <form action="editar_usuario.php?id=<?php echo $id_usuario; ?>" method="post">
            <label for="nombre_usuario">Nombre:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($usuario['nombre_usuario'] ?? ''); ?>" required>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo'] ?? ''); ?>" required>

            <label for="clasificacion">Clasificación:</label>
            <select id="clasificacion" name="clasificacion" required>
                <option value="Administrador" <?php echo $usuario['clasificacion'] === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                <option value="Cliente" <?php echo $usuario['clasificacion'] === 'Cliente' ? 'selected' : ''; ?>>Cliente</option>
            </select>

            <input type="submit" value="Guardar Cambios">
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
include 'footer.php';
?>
