<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data['nombre'] ?? '');
$categoria = trim($data['categoria'] ?? '');
$precio = floatval($data['precio'] ?? 0);
$stock = intval($data['stock'] ?? 0);
$stock_minimo = intval($data['stock_minimo'] ?? 0);
$descripcion = trim($data['descripcion'] ?? '');

if (!$nombre || $precio <= 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Nombre y precio son requeridos.']);
  exit;
}

$sql = "INSERT INTO productos (nombre, categoria, precio, stock, stock_minimo, descripcion)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre, $categoria, $precio, $stock, $stock_minimo, $descripcion]);

echo json_encode(['success' => true, 'message' => 'Producto creado correctamente']);
