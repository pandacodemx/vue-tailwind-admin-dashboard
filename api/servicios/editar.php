<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? '';
$precio = $data['precio'] ?? '';
$duracion = $data['duracion'] ?? '';
$detalles = $data['detalles'] ?? '';
$categoria = $data['categoria'] ?? '';
$status = $data['status'] ?? 1;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID del servicio es requerido."]);
    exit;
}

try {
    $sql = "UPDATE servicios SET nombre = ?, precio = ?, duracion = ?, detalles= ? , categoria=?,  status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $precio, $duracion, $detalles, $categoria, $status, $id]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al editar servicio", "detalle" => $e->getMessage()]);
}
