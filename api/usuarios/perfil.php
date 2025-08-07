<?php 
require_once '../includes/secure_api.php';

try {
    $user_id = $_SESSION['usuario']['id'];
    
    $sql = "SELECT *
            FROM usuarios WHERE id = :id LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode($usuario);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al obtener el perfil',
        'detalle' => $e->getMessage()
    ]);
}
