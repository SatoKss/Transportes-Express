<?php
include 'db.php';

// Consulta SQL
$sql = "SELECT * FROM viajes ORDER BY fecha_viaje DESC";
$result = $conn->query($sql);

$viajes = [];

while ($row = $result->fetch_assoc()) {
    $viajes[] = $row;
}

echo json_encode($viajes);
?>
