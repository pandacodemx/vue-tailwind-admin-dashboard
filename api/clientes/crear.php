<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$telefono = $data['telefono'];
$correo = $data['correo'];
$status = $data['status'];

try {
    $sql = "INSERT INTO clientes (nombre, telefono, correo, status) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $telefono, $correo, $status]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al guardar cliente", "detalle" => $e->getMessage()]);
}
