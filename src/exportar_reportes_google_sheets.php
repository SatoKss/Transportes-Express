<?php
header('Content-Type: application/json'); // Permitir respuesta en JSON

include '../src/db.php'; // Conexión a la BD
require __DIR__ . '/../vendor/autoload.php'; // Cargar Composer

// Importar clases de Google
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

// Obtener el tipo de reporte desde la URL
$reporte = isset($_GET['reporte']) ? $_GET['reporte'] : '';

if (!$reporte) {
    echo json_encode(["status" => "error", "message" => "❌ Falta el parámetro de reporte."]);
    exit();
}

// Configurar cliente de Google
$client = new Client();
$client->setApplicationName("Exportador de Reportes");
$client->setScopes([Sheets::SPREADSHEETS]);
$client->setAuthConfig(__DIR__ . '/../config/credenciales.json');
$client->setAccessType('offline');

$service = new Sheets($client);
$spreadsheetId = "1qiQGlXHG2cDgZH_oEZyogMRhlOMV9Ptl80HKa8xJCbE"; // ID de la hoja de cálculo
$sheetTitle = "Reporte_$reporte"; // Nombre de la hoja
$range = "$sheetTitle!A1"; // Rango de actualización

// Verificar conexión a la base de datos
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "❌ Error de conexión a la base de datos."]);
    exit();
}

// Definir consulta SQL según el reporte
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
        echo json_encode(["status" => "error", "message" => "❌ Reporte no válido."]);
        exit();
}

// Ejecutar la consulta
$result = $conn->query($sql);
if (!$result) {
    echo json_encode(["status" => "error", "message" => "❌ Error en la consulta: " . $conn->error]);
    exit();
}

// Obtener datos y preparar para Google Sheets
$values = [];
if ($result->num_rows > 0) {
    $headers = array_keys($result->fetch_assoc());
    $values[] = $headers; // Cabeceras
    $result->data_seek(0); // Reiniciar puntero
    while ($row = $result->fetch_assoc()) {
        // Solo convertir 'valor' si existe en la consulta
        if (isset($row['valor'])) {
            $row['valor'] = floatval($row['valor']);
        }
        $values[] = array_values($row);
    }
} else {
    echo json_encode(["status" => "error", "message" => "⚠️ No hay datos disponibles para este reporte."]);
    exit();
}

// Verificar si la hoja ya existe, si no, crearla
try {
    $sheets = $service->spreadsheets->get($spreadsheetId, ['fields' => 'sheets(properties(title))']);
    $sheetExists = false;

    foreach ($sheets->getSheets() as $sheet) {
        if ($sheet['properties']['title'] === $sheetTitle) {
            $sheetExists = true;
            break;
        }
    }

    if (!$sheetExists) {
        $batchUpdateRequest = new BatchUpdateSpreadsheetRequest([
            'requests' => [['addSheet' => ['properties' => ['title' => $sheetTitle]]]]
        ]);
        $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "❌ Error al verificar/crear hoja: " . $e->getMessage()]);
    exit();
}

// Enviar datos a Google Sheets
$body = new ValueRange(['values' => $values]);
$params = ['valueInputOption' => 'RAW'];

try {
    $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

    // Obtener el ID de la hoja
    $sheetId = getSheetId($spreadsheetId, $sheetTitle, $service);
    if ($sheetId === null) {
        throw new Exception("No se pudo obtener el ID de la hoja.");
    }

    // Aplicar formato condicional si la consulta contiene la columna "valor"
    if (in_array("valor", $headers)) {
        $rowCount = count($values); // Total de filas (incluyendo encabezados)
        $valueColumnIndex = array_search("valor", $headers); // Índice de la columna "valor"

        $formatRequest = new BatchUpdateSpreadsheetRequest([
            'requests' => [
                [
                    'addConditionalFormatRule' => [
                        'rule' => [
                            'ranges' => [
                                [
                                    'sheetId' => $sheetId,
                                    'startRowIndex' => 1, // Desde la fila 2 (sin encabezado)
                                    'endRowIndex' => $rowCount, // Última fila
                                    'startColumnIndex' => $valueColumnIndex,
                                    'endColumnIndex' => $valueColumnIndex + 1
                                ]
                            ],
                            'booleanRule' => [
                                'condition' => [
                                    'type' => 'NUMBER_LESS',
                                    'values' => [['userEnteredValue' => "20000"]]
                                ],
                                'format' => [
                                    'backgroundColor' => ['red' => 1.0, 'green' => 0.8, 'blue' => 0.8], // Rojo claro
                                    'textFormat' => ['bold' => true, 'foregroundColor' => ['red' => 1.0, 'green' => 0.0, 'blue' => 0.0]] // Texto rojo
                                ]
                            ]
                        ],
                        'index' => 0
                    ]
                ]
            ]
        ]);

        $service->spreadsheets->batchUpdate($spreadsheetId, $formatRequest);
    }

    echo json_encode(["status" => "success", "message" => "✅ Reporte '$reporte' exportado correctamente a Google Sheets con formato condicional."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "❌ Error al aplicar formato condicional: " . $e->getMessage()]);
}

/**
 * Función para obtener el ID de la hoja dentro del spreadsheet
 */
function getSheetId($spreadsheetId, $sheetTitle, $service) {
    try {
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet['properties']['title'] === $sheetTitle) {
                return $sheet['properties']['sheetId'];
            }
        }
        return null;
    } catch (Exception $e) {
        return null;
    }
}
?>
