<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = trim($_POST['id']);
    $fecha = trim($_POST['fecha_viaje']);
    $origen = trim($_POST['origen_comuna']);
    $destino = trim($_POST['destino_comuna']);
    $ejecutivo = trim($_POST['usuario_ejecutivo']);
    $solicitante = trim($_POST['usuario_solicitante']);
    $valor = trim($_POST['valor']);

    if (empty($id) || empty($fecha) || empty($origen) || empty($destino) || empty($ejecutivo) || empty($solicitante) || empty($valor)) {
        echo json_encode(["status" => "error", "message" => "❌ Todos los campos son obligatorios."]);
        exit();
    }

    if (!ctype_digit($id)) {
        echo json_encode(["status" => "error", "message" => "❌ ID inválido."]);
        exit();
    }

    if (!is_numeric($valor) || $valor <= 0) {
        echo json_encode(["status" => "error", "message" => "❌ El valor debe ser un número positivo."]);
        exit();
    }

    $sql = "UPDATE viajes SET fecha_viaje = ?, origen_comuna = ?, destino_comuna = ?, usuario_ejecutivo = ?, usuario_solicitante = ?, valor = ? WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "❌ Error en la preparación de la consulta."]);
        exit();
    }

    $stmt->bind_param("ssssssi", $fecha, $origen, $destino, $ejecutivo, $solicitante, $valor, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "✅ Viaje actualizado con éxito."]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Error al actualizar el viaje."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "❌ Solicitud inválida."]);
}

$conn->close();
?>
