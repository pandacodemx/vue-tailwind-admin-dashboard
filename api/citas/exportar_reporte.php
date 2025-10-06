<?php
require_once '../includes/secure_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}


$data = json_decode(file_get_contents('php://input'), true);
$fechaInicio = isset($data['fechaInicio']) ? $data['fechaInicio'] : null;
$fechaFin = isset($data['fechaFin']) ? $data['fechaFin'] : null;
$filtro = isset($data['filtro']) ? $data['filtro'] : '';


$sql = "
  SELECT 
    c.id,
    c.fecha,
    c.estado,
    c.total,
    c.pagado,
    c.notas,
    cl.nombre AS cliente_nombre,
    cl.telefono,
    cl.correo
  FROM citas c
  INNER JOIN clientes cl ON c.cliente_id = cl.id
  WHERE c.pagado = 1
";

$params = [];


if ($fechaInicio) {
    $sql .= " AND c.fecha >= ?";
    $params[] = $fechaInicio;
}

if ($fechaFin) {
    $sql .= " AND c.fecha <= ?";
    $params[] = $fechaFin;
}

if ($filtro) {
    $sql .= " AND (cl.nombre LIKE ? OR cl.telefono LIKE ? OR cl.correo LIKE ?)";
    $searchTerm = "%$filtro%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY c.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($citas as &$cita) {
    $stmtServ = $pdo->prepare("
        SELECT s.nombre, s.precio
        FROM cita_servicios cs 
        INNER JOIN servicios s ON s.id = cs.servicio_id 
        WHERE cs.cita_id = ?
    ");
    $stmtServ->execute([$cita['id']]);
    $cita['servicios'] = $stmtServ->fetchAll(PDO::FETCH_ASSOC);
}


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=reporte-citas-' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');


fputcsv($output, [
    'ID',
    'Fecha',
    'Cliente',
    'Teléfono',
    'Correo',
    'Servicios',
    'Total',
    'Estado',
    'Notas'
]);


foreach ($citas as $cita) {
    $servicios = array_map(function ($servicio) {
        return $servicio['nombre'] . ' ($' . $servicio['precio'] . ')';
    }, $cita['servicios']);

    $serviciosStr = implode(', ', $servicios);

    fputcsv($output, [
        $cita['id'],
        $cita['fecha'],
        $cita['cliente_nombre'],
        $cita['telefono'],
        $cita['correo'],
        $serviciosStr,
        '$' . $cita['total'],
        $cita['estado'],
        $cita['notas'] ?: 'N/A'
    ]);
}

fclose($output);
exit;
