<?php
require_once '../includes/secure_api.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($_SESSION['usuario']['id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }

    $user_id = $_SESSION['usuario']['id'];

    $first_name = trim($data['first_name'] ?? '');
    $last_name = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $telefono = trim($data['telefono'] ?? '');

    if (!$first_name || !$last_name || !$email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
        exit;
    }

    $sql = "UPDATE usuarios 
            SET first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                telefono = :telefono
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':telefono' => $telefono,
        ':id' => $user_id,
    ]);

    $_SESSION['usuario']['first_name'] = $first_name;
    $_SESSION['usuario']['last_name'] = $last_name;
    $_SESSION['usuario']['email'] = $email;
    $_SESSION['usuario']['telefono'] = $telefono;

    echo json_encode(['success' => true, 'message' => 'Perfil actualizado']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el perfil',
        'detalle' => $e->getMessage()
    ]);
}