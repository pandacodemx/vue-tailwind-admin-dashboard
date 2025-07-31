<?php
require_once '../conexion.php';
require_once '../cabeceras.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;
$estado = $data['estado'] ?? null;

if (!$id || !$estado) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
  exit;
}

try {
  $stmt = $pdo->prepare("UPDATE citas SET estado = ? WHERE id = ?");
  $stmt->execute([$estado, $id]);

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
