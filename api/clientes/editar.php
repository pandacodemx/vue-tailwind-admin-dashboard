<?php
require_once '../cabeceras.php';
require_once '../conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? '';
$telefono = $data['telefono'] ?? '';
$correo = $data['correo'] ?? '';
$status = $data['status'] ?? 1;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID del cliente es requerido."]);
    exit;
}

try {
    $sql = "UPDATE clientes SET nombre = ?, telefono = ?, correo = ?, status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $telefono, $correo, $status, $id]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al editar cliente", "detalle" => $e->getMessage()]);
}
