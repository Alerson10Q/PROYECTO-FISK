<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Informe de Pedidos</title>
    <link rel="stylesheet" href="../css/pedidos.css">
    
</head>
<body>
    <h1>Generar Informe de Pedidos</h1>
    <form action="generar_informe_pedidos.php" method="post">
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>
        
        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required>
        
        <input type="submit" value="Generar Informe">
    </form>
</body>
</html>
