<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents('php://input'), true);
$nombre = $data['nombre'] ?? '';
$precio = $data['precio'] ?? 0;
$duracion = $data['duracion'] ?? '';
$status = $data['status'] ?? 1;
$detalles = $data['detalles'] ?? '';
$servicios = $data['servicios'] ?? []; 

if (!$nombre || empty($servicios)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("INSERT INTO paquetes (nombre, precio, duracion, status, detalles) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $precio, $duracion, $status, $detalles]);
    $id_paquete = $pdo->lastInsertId();

    $stmtRel = $pdo->prepare("INSERT INTO paquete_servicio (id_paquete, id_servicio) VALUES (?, ?)");
    foreach ($servicios as $id_servicio) {
        $stmtRel->execute([$id_paquete, $id_servicio]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
