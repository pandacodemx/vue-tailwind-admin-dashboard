<?php
require_once '../cabeceras.php'; 
session_start();
require_once '../conexion.php';


try {
    $data = json_decode(file_get_contents("php://input"), true);

    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email y contraseña son requeridos.']);
        exit;
    }

    // Buscar usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($password, $usuario['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
        exit;
    }

    // Guardar datos en sesión (sin password)
    unset($usuario['password']);
    $_SESSION['usuario'] = $usuario;

    echo json_encode([
        'success' => true,
        'message' => 'Inicio de sesión exitoso.',
        'usuario' => $usuario
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor.',
        'detalle' => $e->getMessage()
    ]);
}
