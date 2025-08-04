<?php
require_once '../cabeceras.php'; 
session_start();
session_unset();
session_destroy();
require_once '../conexion.php';


header('Content-Type: application/json');
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

echo json_encode([
    'success' => true,
    'message' => 'Sesión cerrada correctamente'
]);
