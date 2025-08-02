<?php
require_once '../conexion.php';
require_once '../cabeceras.php';
require_once '../includes/auth.php';

requireAdmin();
header('Content-Type: application/json');



try {
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = trim($data['nombre'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');
    $rol = $data['rol'] ?? 'admin';

    if (!$nombre || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios.']);
        exit;
    }

    //VERIFICANDO EMAIL (SI YA ESTA REGISTRADO)
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'El email ya está registrado.']);
        exit;
    }

    //ENCRYPTANDO PASS
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    //SET USER
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $passwordHash, $rol]);

    echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar el usuario.',
        'detalle' => $e->getMessage()
    ]);
}
