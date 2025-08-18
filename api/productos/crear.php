<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data['nombre'] ?? '');
$precio = floatval($data['precio'] ?? 0);
$id_categoria = $data['id_categoria'];
$stock = intval($data['stock'] ?? 0);
$stock_minimo = intval($data['stock_minimo'] ?? 0);
$descripcion = trim($data['descripcion'] ?? '');
$activo = $data['activo'] ?? 1;

if (!$nombre || $precio <= 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Nombre y precio son requeridos.']);
  exit;
}

$sql = "INSERT INTO productos (nombre, id_categoria, precio, stock, stock_minimo, descripcion)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre, $id_categoria, $precio, $stock, $stock_minimo, $descripcion]);

echo json_encode(['success' => true, 'message' => 'Producto creado correctamente']);
