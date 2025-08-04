<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);


$nombre = $data['nombre'] ?? '';
$precio = $data['precio'] ?? '';
$duracion = $data['duracion'] ?? '';
$detalles = $data['detalles'] ?? '';
$status = $data['status'] ?? 1;

try {
    $sql = "INSERT INTO servicios (nombre, precio, duracion , detalles , status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $precio, $duracion, $detalles, $status]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al guardar servicio", "detalle" => $e->getMessage()]);
}
