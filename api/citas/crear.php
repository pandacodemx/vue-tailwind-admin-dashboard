<?php
require_once '../includes/secure_api.php';
date_default_timezone_set('America/Mexico_City');

$data = json_decode(file_get_contents('php://input'), true);

$cliente_id = $data['cliente']['id'] ?? null;
$fecha = $data['fecha'] ?? null;
$servicios = $data['servicios'] ?? [];
$notas = $data['notas'] ?? '';
$estado = $data['estado'] ?? 'pendiente';

if (!$cliente_id || !$fecha || empty($servicios)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Insertar cita
    $stmt = $pdo->prepare('INSERT INTO citas (cliente_id, fecha, notas, estado) VALUES (?, ?, ?, ?)');
    $stmt->execute([$cliente_id, $fecha, $notas, $estado]);

    $cita_id = $pdo->lastInsertId();

    // 2. Insertar servicios relacionados
    $stmtServ = $pdo->prepare('INSERT INTO cita_servicios (cita_id, servicio_id) VALUES (?, ?)');
    foreach ($servicios as $servicio) {
        $stmtServ->execute([$cita_id, $servicio['id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
