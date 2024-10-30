<?php
session_start();
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QX4F97GZ55"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QX4F97GZ55');
</script>
<body>
    <h1>Registro de Usuario</h1>
    
    <?php if (isset($_GET['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form action="register_process.php" method="post">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>

        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" required>

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="contraseña" required>

        <input type="hidden" name="clasificacion" value="Cliente">

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
