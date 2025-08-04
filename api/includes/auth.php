<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function requireLogin() {
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado. Inicia sesión.'
        ]);
        exit;
    }
}

/**
 * Verifica que el usuario tenga rol admin.
 */
function requireAdmin() {
    requireLogin();
    
    if ($_SESSION['usuario']['rol'] !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Acceso denegado. Solo administradores.'
        ]);
        exit;
    }
}
