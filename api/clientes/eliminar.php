<?php
require_once '../conexion.php';
require_once '../cabeceras.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit;
}

try {
    $sql = "UPDATE clientes SET status = 0 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$id]);

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
