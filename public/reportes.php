<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../src/db.php';

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$reporte = isset($_GET['reporte']) ? $_GET['reporte'] : '';

switch ($reporte) {
    case 'enero':
        $sql = "SELECT * FROM viajes WHERE MONTH(fecha_viaje) = 1 AND usuario_ejecutivo = 'Alejandro Diaz'";
        break;
    case 'ejecutivos':
        $sql = "SELECT usuario_ejecutivo, COUNT(*) AS total_viajes, SUM(valor) AS total_valor, AVG(valor) AS promedio 
                FROM viajes 
                GROUP BY usuario_ejecutivo";
        break;
    case 'bajos':
        $sql = "SELECT * FROM viajes WHERE valor < 20000";
        break;
    case 'rango_fechas':
        $sql = "SELECT * FROM viajes WHERE origen_comuna = 'Santiago' AND destino_comuna = 'Las Condes' 
                AND fecha_viaje BETWEEN '2025-01-15' AND '2025-02-15'";
        break;
    default:
        die(json_encode(["error" => "Consulta no válida."]));
}

$result = $conn->query($sql);

if (!$result) {
    die(json_encode(["error" => "Error en la consulta SQL: " . $conn->error]));
}

$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($datos, JSON_PRETTY_PRINT);
