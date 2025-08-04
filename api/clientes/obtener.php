<?php 
require_once '../includes/secure_api.php';

try {
    $sql = "SELECT * FROM clientes WHERE status = 1 ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $clientes = $stmt->fetchAll(); 

    echo json_encode($clientes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener los clientes',
        'detalle' => $e->getMessage()
    ]);
}
