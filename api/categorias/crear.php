<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? '';
$descripcion = $data['descripcion'] ?? '';
$activo = $data['activo'] ?? 1;

try {
    $sql = "INSERT INTO categorias (nombre, descripcion, activo) VALUES ( ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $descripcion, $activo]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al guardar categoria", "detalle" => $e->getMessage()]);
}
