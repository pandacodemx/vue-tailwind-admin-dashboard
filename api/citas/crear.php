<?php
require_once '../includes/secure_api.php';
date_default_timezone_set('America/Mexico_City');

$data = json_decode(file_get_contents('php://input'), true);

$cliente_id = $data['cliente']['id'] ?? null;
$fecha = $data['fecha'] ?? null;
$servicios = $data['servicios'] ?? [];
$notas = $data['notas'] ?? '';
$estado = $data['estado'] ?? 'pendiente';
$fecha_fin = $data['fecha_fin'] ?? null;

if (!$cliente_id || !$fecha || !$fecha_fin || empty($servicios)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

try {

    $sqlCheck = "SELECT COUNT(*) as total
    FROM citas
    WHERE 
       (fecha < :fecha_fin) 
       AND (fecha_fin > :fecha)
       AND estado != 'cancelada'";
    $stmtCheck = $pdo->prepare($sqlCheck);      
    $stmtCheck->execute([
        ':fecha' => $fecha,
        ':fecha_fin' => $fecha_fin
    ]);
    $resCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($resCheck['total'] > 0) {
        echo json_encode(['success' => false, 'message' => 'El horario seleccionado ya está ocupado.']);
        exit;
        }

        $total = 0;
        foreach ($servicios as $servicio) {
            $total += floatval($servicio['precio']);
        }    
    $pdo->beginTransaction();

    //Insertar cita
    $stmt = $pdo->prepare('INSERT INTO citas (cliente_id, fecha, fecha_fin, notas, estado, total) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$cliente_id, $fecha, $fecha_fin, $notas, $estado, $total]);

    $cita_id = $pdo->lastInsertId();

    //Insertar servicios relacionados
    $stmtServ = $pdo->prepare('INSERT INTO cita_servicios (cita_id, servicio_id, precio) VALUES (?, ?, ?)');
    foreach ($servicios as $servicio) {
        $stmtServ->execute([$cita_id, $servicio['id'], $servicio['precio']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
