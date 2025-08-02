<?php
require_once '../conexion.php';
require_once '../cabeceras.php';
date_default_timezone_set('America/Mexico_City');

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;
$nueva_fecha = $data->nueva_fecha;

$archivoHorarios = __DIR__ . '/horarios.json';
$horarios = [];

if (file_exists($archivoHorarios)) {
    $contenido = file_get_contents($archivoHorarios);
    $horarios = json_decode($contenido, true);
}

$fecha = new DateTime($nueva_fecha);
$diaSemana = (int) $fecha->format('w'); // 0 = domingo, 6 = sábado
$horaActual = $fecha->format('H:i');

$diaHorario = array_filter($horarios, fn($h) => $h['dia'] === $diaSemana);
$horario = reset($diaHorario);

if (!$horario || !$horario['activo']) {
    echo json_encode(['success' => false, 'error' => 'Ese día no está habilitado']);
    exit;
}

if ($horaActual < $horario['desde'] || $horaActual >= $horario['hasta']) {
    echo json_encode(['success' => false, 'error' => 'Horario fuera de rango permitido']);
    exit;
}

$sql = "UPDATE citas SET fecha = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nueva_fecha, $id]);

echo json_encode(['success' => true]);
