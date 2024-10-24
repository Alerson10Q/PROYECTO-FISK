<?php
$servername = "localhost";
$username = "admin";
$password = "admin";
$database = "PintureriaArcoiris_db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
