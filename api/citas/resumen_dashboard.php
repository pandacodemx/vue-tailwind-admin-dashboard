<?php
require_once '../conexion.php';
require_once '../cabeceras.php';
header('Content-Type: application/json');

$resumen = [
  'total' => 0,
  'pendientes' => 0,
  'atendidas' => 0,
  'canceladas' => 0,
  'proximas' => [],
  'top_clientes' => [],
];

// 1. Resumen por estado
$sqlResumen = "SELECT estado, COUNT(*) AS cantidad FROM citas GROUP BY estado";
$stmtResumen = $pdo->query($sqlResumen);
while ($row = $stmtResumen->fetch(PDO::FETCH_ASSOC)) {
  $estado = $row['estado'];
  $resumen[$estado] = (int)$row['cantidad'];
  $resumen['total'] += (int)$row['cantidad'];
}

// 2. Próximas citas
$sqlProximas = "
  SELECT c.id, c.fecha, c.estado, cli.nombre AS cliente, GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios
  FROM citas c
  JOIN clientes cli ON c.cliente_id = cli.id
  LEFT JOIN cita_servicios cs ON cs.cita_id = c.id
  LEFT JOIN servicios s ON s.id = cs.servicio_id
  WHERE c.fecha >= NOW()
  GROUP BY c.id
  ORDER BY c.fecha ASC
  LIMIT 5
";
$stmtProximas = $pdo->query($sqlProximas);
$resumen['proximas'] = $stmtProximas->fetchAll(PDO::FETCH_ASSOC);

// 3. Top clientes
$sqlTopClientes = "
  SELECT cli.nombre, COUNT(c.id) AS total_citas
  FROM citas c
  JOIN clientes cli ON c.cliente_id = cli.id
  GROUP BY cli.id
  ORDER BY total_citas DESC
  LIMIT 5
";
$stmtTop = $pdo->query($sqlTopClientes);
$resumen['top_clientes'] = $stmtTop->fetchAll(PDO::FETCH_ASSOC);

// Devolver resultado
echo json_encode($resumen);
