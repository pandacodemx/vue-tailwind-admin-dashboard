<?php
require_once '../includes/secure_api.php';
date_default_timezone_set('America/Mexico_City');

$data = json_decode(file_get_contents("php://input"));
$id = $data->id;
$nueva_fecha = $data->nueva_fecha;

$archivoHorarios = '../configuracion/horarios.json';
$horarios = json_decode(file_get_contents($archivoHorarios), true);

$fechaCita = new DateTime($nueva_fecha);
$ahora = new DateTime();
if ($fechaCita < $ahora) {
    $mensaje = ($fechaCita->format('Y-m-d') == $ahora->format('Y-m-d'))
        ? "No puedes programar citas en horas pasadas (hora actual: ".$ahora->format('H:i').")"
        : "No puedes programar citas en fechas pasadas";
    
    echo json_encode([
        'success' => false,
        'error' => $mensaje,
        'detalles' => [
            'hora_cita' => $fechaCita->format('Y-m-d H:i:s'),
            'hora_actual' => $ahora->format('Y-m-d H:i:s')
        ]
    ]);
    exit;
}


$diaSemana = (int) $fechaCita->format('w');
$horaCita = $fechaCita->format('H:i:s');

$horarioEncontrado = array_filter($horarios, fn($h) => $h['dia'] == $diaSemana && $h['activo']);
$horarioEncontrado = reset($horarioEncontrado);

if (!$horarioEncontrado) {
    echo json_encode([
        'success' => false,
        'error' => '❌ Día no laborable',
        'debug' => [
            'dia' => $diaSemana,
            'hora_cita' => $horaCita,
            'horarios' => $horarios
        ]
    ]);
    exit;
}

$desde = strlen($horarioEncontrado['desde']) == 5 ? $horarioEncontrado['desde'].':00' : $horarioEncontrado['desde'];
$hasta = strlen($horarioEncontrado['hasta']) == 5 ? $horarioEncontrado['hasta'].':00' : $horarioEncontrado['hasta'];

if ($horaCita < $desde || $horaCita >= $hasta) {
    echo json_encode([
        'success' => false,
        'error' => '❌ Fuera de horario laboral',
        'detalles' => "Intento: $horaCita, Permitido: $desde-$hasta",
        'debug' => $fechaCita
    ]);
    exit;
}

$stmt = $pdo->prepare("UPDATE citas SET fecha = ? WHERE id = ?");
$success = $stmt->execute([$fechaCita->format('Y-m-d H:i:s'), $id]);

echo json_encode(['success' => $success, 'hora_actualizada' => $fechaCita->format('Y-m-d H:i:s')]);