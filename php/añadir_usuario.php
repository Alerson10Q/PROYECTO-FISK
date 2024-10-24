<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssss", $nombre, $email, $password_hash, $rol);

            if ($stmt->execute()) {
                $message = '<p style="color: green;">Usuario añadido con éxito.</p>';
            } else {
                $message = '<p style="color: red;">Error al añadir el usuario: ' . $stmt->error . '</p>';
            }
        } else {
            $message = '<p style="color: red;">Error al preparar la consulta.</p>';
        }

        $stmt->close();
    } else {
        $message = '<p style="color: red;">Por favor, completa todos los campos correctamente.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Usuario</title>
    <link rel="stylesheet" href="../css/añadir_usuario.css">
</head>
<body>
    <div class="admin-dashboard">
        <h1>Añadir Usuario</h1>
        <?= $message; ?>
        <form action="añadir_usuario.php" method="post">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <label for="rol">Rol:</label>
            <select id="rol" name="rol" required>
                <option value="Administrador">Administrador</option>
                <option value="Usuario">Usuario</option>
            </select>

            <button type="submit">Añadir Usuario</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
