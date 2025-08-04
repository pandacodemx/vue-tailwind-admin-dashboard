<?php
// includes/secure_api.php

// Inicia sesión 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cabeceras de seguridad y CORS
$allowed_origins = [
    'http://localhost:5174',
    'https://tusistema.com', 
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/auth.php';


requireLogin(); 
