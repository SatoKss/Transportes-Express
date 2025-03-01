<?php
require __DIR__ . '/../vendor/autoload.php'; // Cargar Composer
include '../src/db.php'; // Conexión a la BD

// Importar clases de Google
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

// Configurar cliente de Google
$client = new Client();
$client->setApplicationName("Exportador de Viajes");
$client->setScopes([Sheets::SPREADSHEETS]);
$client->setAuthConfig(__DIR__ . '/../config/credenciales.json'); // Asegúrate de que este archivo existe
$client->setAccessType('offline');

$service = new Sheets($client);
$spreadsheetId = "1qiQGlXHG2cDgZH_oEZyogMRhlOMV9Ptl80HKa8xJCbE"; // ID de tu hoja de cálculo

$range = "A1"; // Celda donde comenzará la exportación

// Obtener datos de la BD
$sql = "SELECT * FROM viajes";
$result = $conn->query($sql);

if (!$result) {
    die("❌ Error en la consulta: " . $conn->error);
}

// Construir los valores para Google Sheets
$values = [["ID", "Fecha", "Origen", "Destino", "Ejecutivo", "Solicitante", "Valor"]]; // Cabeceras
while ($row = $result->fetch_assoc()) {
    $values[] = [
        (int) $row['id'],
        (string) $row['fecha_viaje'],
        (string) $row['origen_comuna'],
        (string) $row['destino_comuna'],
        (string) $row['usuario_ejecutivo'],
        (string) $row['usuario_solicitante'],
        (float) $row['valor']
    ];
}

// Verificar los datos antes de enviarlos a Google Sheets
echo "<pre>";
print_r($values);
echo "</pre>";

// Enviar datos a Google Sheets
$body = new ValueRange([
    'values' => $values
]);

$params = ['valueInputOption' => 'RAW'];

try {
    $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    echo "✅ Datos exportados correctamente a Google Sheets.";
} catch (Exception $e) {
    echo "❌ Error al exportar: " . $e->getMessage();
}

?>
