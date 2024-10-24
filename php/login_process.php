<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    $sql = "SELECT id_usuario, nombre_usuario, contraseña, clasificacion FROM Usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        if (password_verify($contraseña, $usuario['contraseña'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['user_role'] = $usuario['clasificacion'];

            if ($usuario['clasificacion'] == 'Administrador') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            header('Location: login.php?error=Contraseña incorrecta');
            exit();
        }
    } else {
        header('Location: login.php?error=Usuario no encontrado');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}

$conn->close();
?>
