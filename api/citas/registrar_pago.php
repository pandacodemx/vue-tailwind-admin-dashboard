<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents('php://input'), true);
$cita_id = $data['id'] ?? null;

if (!$cita_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de cita inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE citas SET pagado = 1, fecha_pago = NOW() WHERE id = ?");
    $stmt->execute([$cita_id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
