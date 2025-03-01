<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../src/db.php'; // Conectar a la base de datos

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar si se recibió el parámetro 'reporte' en la URL
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
        die(json_encode(["error" => "Consulta no válida."])); // Respuesta en JSON
}

// Ejecutar la consulta
$result = $conn->query($sql);

if (!$result) {
    die(json_encode(["error" => "Error en la consulta SQL: " . $conn->error])); // Respuesta en JSON
}

// Convertir los datos en un array
$datos = [];
while ($row = $result->fetch_assoc()) {
    $datos[] = $row;
}

// Mostrar el resultado en JSON
header('Content-Type: application/json');
echo json_encode($datos, JSON_PRETTY_PRINT);
?>
