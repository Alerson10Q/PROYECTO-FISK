<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $sql = "INSERT INTO Proveedores (nombre, telefono, correo, direccion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $telefono, $correo, $direccion);

    if ($stmt->execute()) {
        header("Location: agregar_proveedor.php?success=1");
        exit;
    } else {
        echo '<p>Error al agregar el proveedor: ' . $conn->error . '</p>';
    }

    $stmt->close();
    $conn->close();
}
?>
