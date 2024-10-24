<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $clasificacion = 'Cliente';

    $sql = "SELECT id_usuario FROM Usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: register.php?error=Correo ya registrado");
        exit();
    }

    $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Usuarios (nombre_usuario, correo, contraseña, clasificacion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre_usuario, $correo, $hashed_password, $clasificacion);

    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;

        $_SESSION['id_usuario'] = $id_usuario;
        $_SESSION['nombre_usuario'] = $nombre_usuario;

        header("Location: login.php");
        exit();
    } else {
        header("Location: register.php?error=Error al registrar el usuario");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
