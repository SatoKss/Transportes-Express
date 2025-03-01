<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = isset($_POST['fecha_viaje']) ? $_POST['fecha_viaje'] : '';
    $origen = isset($_POST['origen_comuna']) ? $_POST['origen_comuna'] : '';
    $destino = isset($_POST['destino_comuna']) ? $_POST['destino_comuna'] : '';
    $ejecutivo = isset($_POST['usuario_ejecutivo']) ? $_POST['usuario_ejecutivo'] : '';
    $solicitante = isset($_POST['usuario_solicitante']) ? $_POST['usuario_solicitante'] : '';
    $valor = isset($_POST['valor']) ? $_POST['valor'] : 0;

    if ($fecha == '' || $origen == '' || $destino == '' || $ejecutivo == '' || $solicitante == '') {
        die("Error: Todos los campos son obligatorios.");
    }

    $sql = "INSERT INTO viajes (fecha_viaje, origen_comuna, destino_comuna, usuario_ejecutivo, usuario_solicitante, valor) 
            VALUES ('$fecha', '$origen', '$destino', '$ejecutivo', '$solicitante', '$valor')";

    if ($conn->query($sql)) {
        echo "Viaje agregado con éxito";
    } else {
        echo "Error en la consulta SQL: " . $conn->error;
    }
}
?>

