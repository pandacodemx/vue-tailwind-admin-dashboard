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
    ]);
    exit;
}

// SERVICIOS
$sql = "SELECT SUM(s.duracion) AS total_minutos
        FROM cita_servicios cs
        JOIN servicios s ON cs.servicio_id = s.id
        WHERE cs.cita_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$total_minutos = (int)$stmt->fetchColumn();
if ($total_minutos <= 0) $total_minutos = 30; // valor por defecto si algo falla

// FECHA FIN
$fechaFin = clone $fechaCita;
$fechaFin->modify("+{$total_minutos} minutes");

// UPDATE FECHA 
$stmt = $pdo->prepare("UPDATE citas SET fecha = ?, fecha_fin = ? WHERE id = ?");
$success = $stmt->execute([
    $fechaCita->format('Y-m-d H:i:s'),
    $fechaFin->format('Y-m-d H:i:s'),
    $id
]);

echo json_encode([
    'success' => $success,
    'hora_actualizada' => $fechaCita->format('Y-m-d H:i:s'),
    'hora_fin' => $fechaFin->format('Y-m-d H:i:s')
]);
