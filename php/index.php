<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinturería Arcoíris</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/producto.css">
    
</head>
<?php include 'header.php'; ?>
<body>

   <?php include 'Slider/index.php'; ?>

    <main>
        <h2>Productos Destacados</h2>
        <div class="productos">
            <?php
            include 'db_connection.php';

            $imagen_path = 'uploaded_images/';

            $sql = "SELECT * FROM Productos LIMIT 9";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="producto">';
                    echo '<img src="' . $imagen_path . htmlspecialchars($row['imagen']) . '" alt="' . htmlspecialchars($row['nombre']) . '">';
                    echo '<h4>' . htmlspecialchars($row['nombre']) . '</h4>';
                    echo '<p>' . htmlspecialchars($row['descripcion']) . '</p>';
                    echo '<p>Precio: $' . number_format($row['precio'], 2) . '</p>';
                    echo '<p>Stock: ' . htmlspecialchars($row['stock_cantidad']) . '</p>';
                    
                    if (!empty($row['marca'])) {
                        echo '<p>Marca: ' . htmlspecialchars($row['marca']) . '</p>';
                    } else {
                        echo '<p>Marca: N/D</p>';
                    }
                    
                    echo '<a class="ver-mas" href="producto.php?id=' . $row['id_producto'] . '">Ver más</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay productos destacados disponibles.</p>';
            }

            $conn->close();
            ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
