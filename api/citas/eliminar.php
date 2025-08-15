<?php
require_once '../includes/secure_api.php';
date_default_timezone_set('America/Mexico_City');

$data = json_decode(file_get_contents("php://input"));
$id = $data->id ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID de cita no proporcionado']);
    exit;
}

try {
    $pdo->beginTransaction();

  
    $stmtServ = $pdo->prepare("DELETE FROM cita_servicio WHERE cita_id = ?");
    $stmtServ->execute([$id]);

    $stmt = $pdo->prepare("DELETE FROM citas WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
