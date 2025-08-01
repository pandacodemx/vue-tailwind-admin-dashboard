<?php
header('Content-Type: application/json');
require_once '../conexion.php';
require_once '../cabeceras.php';

$archivo = __DIR__ . '/horarios.json';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !is_array($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos inválidos.',
    ]);
    exit;
}

file_put_contents($archivo, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
]);
