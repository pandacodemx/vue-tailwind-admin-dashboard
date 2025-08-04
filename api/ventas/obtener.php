<?php
require_once '../includes/secure_api.php';

$inicio = $_GET['fecha_inicio'] ?? null;
$fin = $_GET['fecha_fin'] ?? null;

$query = "
  SELECT v.id, v.fecha, v.total, v.metodo_pago, u.nombre AS vendedor 
  FROM ventas v 
  JOIN usuarios u ON u.id = v.usuario_id
  WHERE 1";

$params = [];

if ($inicio) {
  $query .= " AND DATE(v.fecha) >= ?";
  $params[] = $inicio;
}

if ($fin) {
  $query .= " AND DATE(v.fecha) <= ?";
  $params[] = $fin;
}

$query .= " ORDER BY v.fecha DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
