<?php
require 'db_connection.php';

$fecha_inicio = $_POST['fecha_inicio'] ?? '1970-01-01';
$fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');

$sql = "SELECT id_venta, fecha_de_venta, id_usuario, valor_de_venta, estado FROM ventas WHERE fecha_de_venta BETWEEN ? AND ? ORDER BY fecha_de_venta";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $fecha_inicio, $fecha_fin);
$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="informe_pedidos.csv"');
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');
fputcsv($output, array('ID Pedido', 'Fecha', 'ID Cliente', 'Total', 'Estado'));

while ($row = $result->fetch_assoc()) {
    $datos = array(
        $row['id_venta'],
        $row['fecha_de_venta'],
        $row['id_usuario'],
        $row['valor_de_venta'],
        $row['estado']
    );
    fputcsv($output, $datos);
}

fclose($output);
$conn->close();
?>