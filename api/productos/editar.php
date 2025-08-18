<?php
require_once '../includes/secure_api.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? '';
$precio = $data['precio'] ?? '';
$descripcion = $data['descripcion'] ?? '';
$id_categoria = $data['id_categoria'];
$stock = $data['stock'] ?? '';
$stock_minimo = $data['stock_minimo'] ?? '';
$activo = $data['activo'] ?? 1;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID del producto es requerido."]);
    exit;
}

try {
    $sql = "UPDATE productos SET nombre = ?, precio = ?, descripcion = ?, id_categoria = ?, stock = ?, stock_minimo = ?, activo = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$nombre, $precio, $descripcion, $id_categoria, $stock, $stock_minimo, $activo, $id]);

    echo json_encode(["success" => $success]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al editar producto", "detalle" => $e->getMessage()]);
}
