<?php
require_once '../includes/secure_api.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de paquete requerido']);
    exit;
}

$id = intval($_GET['id']);

try {
   
    $stmt = $pdo->prepare("
        SELECT p.*,
               GROUP_CONCAT(s.id) AS servicios_ids,
               GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios_nombres
        FROM paquetes p
        LEFT JOIN paquete_servicio ps ON ps.id_paquete = p.id
        LEFT JOIN servicios s ON s.id = ps.id_servicio
        WHERE p.id = ? AND p.status = 1
        GROUP BY p.id
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $paquete = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paquete) {
        echo json_encode(['success' => false, 'error' => 'Paquete no encontrado']);
        exit;
    }

   
    $paquete['servicios_ids'] = $paquete['servicios_ids'] 
        ? explode(',', $paquete['servicios_ids']) 
        : [];

    echo json_encode(['success' => true, 'data' => $paquete]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
