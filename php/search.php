<?php
include 'db_connection.php';

function buscar_productos($query) {
    $sql = "SELECT * FROM Productos WHERE MATCH (nombre) AGAINST ('$query' IN NATURAL LANGUAGE MODE)";
    $result = $conn->query($sql);
    return $result;
}

function mostrar_resultados($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<h2>" . $row["nombre"] . "</h2>";
        echo "<p>" . $row["descripcion"] . "</p>";
        echo "<p>Precio: " . $row["precio"] . "</p>";
        echo "</li>";
    }
}

if (isset($_GET["q"])) {
    $query = $_GET["q"];
    $result = buscar_productos($query);
    if ($result->num_rows > 0) {
        echo "<h1>Resultados de la búsqueda</h1>";
        echo "<ul>";
        mostrar_resultados($result);
        echo "</ul>";
    } else {
        echo "<p>No se encontraron resultados para la búsqueda.</p>";
    }
}

$conn->close();
?>