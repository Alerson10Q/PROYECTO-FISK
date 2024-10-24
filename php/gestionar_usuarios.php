<?php
session_start();
include 'header.php';
include 'db_connection.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$sql = "SELECT id_usuario, nombre_usuario, correo, clasificacion FROM Usuarios";
$result = $conn->query($sql);

if ($result === false) {
    echo "Error en la consulta: " . $conn->error;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $clasificacion = $_POST['clasificacion'];
    $direccion = $_POST['direccion'] ?? '';
    $datos_contacto = $_POST['datos_contacto'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';

    if (empty($nombre_usuario) || empty($correo) || empty($contraseña) || empty($clasificacion)) {
        echo "<p style='color:red;'>Por favor, completa todos los campos.</p>";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Formato de correo no válido.</p>";
    } elseif (strlen($contraseña) < 6) {
        echo "<p style='color:red;'>La contraseña debe tener al menos 6 caracteres.</p>";
    } else {
        $check_email = $conn->prepare("SELECT id_usuario FROM Usuarios WHERE correo = ?");
        $check_email->bind_param("s", $correo);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            echo "<p style='color:red;'>El correo ya está registrado. Por favor, utiliza otro.</p>";
        } else {
            $hashed_password = password_hash($contraseña, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO Usuarios (nombre_usuario, correo, contraseña, clasificacion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre_usuario, $correo, $hashed_password, $clasificacion);

            if ($stmt->execute()) {
                echo "<p style='color:green;'>Usuario agregado exitosamente.</p>";

                if ($clasificacion === 'Cliente') {
                    $last_id = $stmt->insert_id;
                    $insert_cliente = $conn->prepare("INSERT INTO Clientes (id_usuario, nombre_cliente, direccion, datos_contacto, fecha_nacimiento) VALUES (?, ?, ?, ?, ?)");
                    $insert_cliente->bind_param("issss", $last_id, $nombre_usuario, $direccion, $datos_contacto, $fecha_nacimiento);

                    if ($insert_cliente->execute()) {
                        echo "<p style='color:green;'>Cliente agregado exitosamente.</p>";
                    } else {
                        echo "<p style='color:red;'>Error al agregar el cliente: " . htmlspecialchars($insert_cliente->error) . "</p>";
                    }

                    $insert_cliente->close();
                }
            } else {
                echo "<p style='color:red;'>Error al agregar el usuario: " . htmlspecialchars($stmt->error) . "</p>";
            }

            $stmt->close();
        }

        $check_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Usuarios</title>
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
        .form-container {
            margin-bottom: 20px;
            display: none;
        }
        .form-container input, .form-container select {
            margin: 5px 0;
            padding: 10px;
            width: 100%;
            max-width: 400px;
        }
        .cliente-fields {
            display: none;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <h1>Gestionar Usuarios</h1>

        <button onclick="toggleForm()">Agregar Usuario</button>
        <div class="form-container" id="formContainer">
            <h2>Agregar Usuario</h2>
            <form method="POST" action="">
                <input type="text" name="nombre_usuario" placeholder="Nombre de Usuario" required>
                <input type="email" name="correo" placeholder="Correo" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <select name="clasificacion" id="clasificacion" required onchange="toggleClientFields()">
                    <option value="">Selecciona Clasificación</option>
                    <option value="Cliente">Cliente</option>
                    <option value="Administrador">Administrador</option>
                </select>

                <div class="cliente-fields" id="clienteFields">
                    <input type="text" name="direccion" placeholder="Dirección">
                    <input type="text" name="datos_contacto" placeholder="Datos de Contacto">
                    <input type="date" name="fecha_nacimiento" placeholder="Fecha de Nacimiento">
                </div>

                <input type="submit" value="Agregar Usuario">
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Clasificación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id_usuario']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre_usuario']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['clasificacion']) . "</td>";
                        echo "<td class='actions'>
                                <a href='editar_usuario.php?id=" . htmlspecialchars($row['id_usuario']) . "'>Editar</a>
                                <a href='#' onclick=\"confirmDelete(" . htmlspecialchars($row['id_usuario']) . ")\">Eliminar</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        if (isset($_GET['message'])) {
            echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>";
        }
        ?>
    </div>

    <script>
        function toggleForm() {
            const formContainer = document.getElementById('formContainer');
            formContainer.style.display = formContainer.style.display === 'none' || formContainer.style.display === '' ? 'block' : 'none';
        }

        function toggleClientFields() {
            const clasificacion = document.getElementById('clasificacion').value;
            const clienteFields = document.getElementById('clienteFields');
            clienteFields.style.display = clasificacion === 'Cliente' ? 'block' : 'none';
        }

        function confirmDelete(userId) {
            if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
                window.location.href = 'eliminar_usuario.php?id=' + userId;
            }
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>
