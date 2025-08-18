<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? '';
$descripcion = $data['descripcion'] ?? '';
$activo = $data['activo'] ?? 1;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID de la categoria es requerido."]);
    exit;
}

try {
    $sql = "UPDATE categorias SET nombre = ?, descripcion = ?, activo = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $descripcion, $activo, $id]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al editar categoria", "detalle" => $e->getMessage()]);
}
