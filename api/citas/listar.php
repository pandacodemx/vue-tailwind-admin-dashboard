<?php
require_once '../conexion.php';
require_once '../cabeceras.php';

$stmt = $pdo->query("
  SELECT c.id, c.fecha, c.estado, c.notas, cl.nombre AS cliente_nombre
  FROM citas c
  INNER JOIN clientes cl ON c.cliente_id = cl.id
  ORDER BY c.fecha DESC
");

$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener servicios por cita
foreach ($citas as &$cita) {
  $stmtServ = $pdo->prepare("
    SELECT s.id, s.nombre 
    FROM cita_servicios cs 
    INNER JOIN servicios s ON s.id = cs.servicio_id 
    WHERE cs.cita_id = ?
  ");
  $stmtServ->execute([$cita['id']]);
  $cita['servicios'] = $stmtServ->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($citas);
