<?php
require_once '../includes/secure_api.php';

$sql = "
  SELECT c.id, c.fecha, c.fecha_fin, c.estado, c.notas, c.total, c.pagado, cl.nombre AS cliente_nombre
  FROM citas c
  INNER JOIN clientes cl ON c.cliente_id = cl.id
  WHERE c.pagado = 1
  ORDER BY c.fecha DESC
";

$stmt = $pdo->query($sql);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($citas as &$cita) {
    $stmtServ = $pdo->prepare("
    SELECT s.id, s.nombre, s.precio
    FROM cita_servicios cs 
    INNER JOIN servicios s ON s.id = cs.servicio_id 
    WHERE cs.cita_id = ?
  ");
    $stmtServ->execute([$cita['id']]);
    $cita['servicios'] = $stmtServ->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($citas);
