<?php
require_once '../includes/secure_api.php';

try {
    $stmt = $pdo->query("
        SELECT p.*, 
               GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios
        FROM paquetes p
        LEFT JOIN paquete_servicio ps ON ps.id_paquete = p.id
        LEFT JOIN servicios s ON s.id = ps.id_servicio WHERE p.status=1
        GROUP BY p.id
        ORDER BY p.id DESC 
    ");
    $paquetes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($paquetes);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}