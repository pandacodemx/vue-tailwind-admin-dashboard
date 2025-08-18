<?php
require_once '../includes/secure_api.php';

header('Content-Type: application/json');

try {

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id']) || empty($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID de paquete requerido']);
        exit;
    }

    if (!isset($data['nombre']) || empty(trim($data['nombre']))) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nombre del paquete requerido']);
        exit;
    }

    if (!isset($data['servicios']) || !is_array($data['servicios']) || count($data['servicios']) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Debe agregar al menos un servicio']);
        exit;
    }

    $id = intval($data['id']);
    $nombre = trim($data['nombre']);
    $precio = floatval($data['precio'] ?? 0);
    $duracion = intval($data['duracion'] ?? 0);
    $status = intval($data['status'] ?? 1);
    $servicios = $data['servicios'];

 
    $stmt = $pdo->prepare("UPDATE paquetes SET nombre = ?, precio = ?, duracion = ?, status = ? WHERE id = ?");
    $stmt->execute([$nombre, $precio, $duracion, $status, $id]);


    $stmt = $pdo->prepare("DELETE FROM paquete_servicio WHERE id_paquete = ?");
    $stmt->execute([$id]);


    $stmt = $pdo->prepare("INSERT INTO paquete_servicio (id_paquete, id_servicio) VALUES (?, ?)");
    foreach ($servicios as $id_servicio) {
        $stmt->execute([$id, $id_servicio]);
    }

    echo json_encode(['success' => true, 'message' => 'Paquete actualizado correctamente']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
