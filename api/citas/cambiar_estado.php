<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$estado = $data['estado'] ?? null;

if (!$id || !$estado) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT estado FROM citas WHERE id = ?");
    $stmt->execute([$id]);
    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cita) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Cita no encontrada']);
        exit;
    }

    if ($cita['estado'] === 'cancelada') {
      
        echo json_encode(['success' => false, 'message' => 'No se puede cambiar el estado de una cita cancelada']);
        exit;
    }

    $stmtUpdate = $pdo->prepare("UPDATE citas SET estado = ? WHERE id = ?");
    $stmtUpdate->execute([$estado, $id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
