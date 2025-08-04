<?php 
require_once '../includes/secure_api.php';
try {
    $sql = "SELECT * FROM servicios WHERE status = 1 ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $servicios = $stmt->fetchAll(); 

    echo json_encode($servicios);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener los servicios',
        'detalle' => $e->getMessage()
    ]);
}
