<?php
require_once '../includes/secure_api.php';

// ESTE ARCHIVO RETORNA LOS HORARIOS DE ATENCION GUARDADIOS, SI NO EXISTE DEVUELVE HORARIOS POR DEFECTO
$archivo = __DIR__ . '/horarios.json';

if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $json = json_decode($contenido, true);

    if (is_array($json)) {
        echo json_encode($json);
    } else {
        echo json_encode($horariosPorDefecto());
    }
} else {
    echo json_encode($horariosPorDefecto());
}

function horariosPorDefecto() {
    $horarios = [];
    for ($i = 0; $i < 7; $i++) {
        $horarios[] = [
            'dia' => $i,
            'activo' => in_array($i, [1, 2, 3, 4, 5]),
            'desde' => '09:00',
            'hasta' => '18:00',
        ];
    }
    return $horarios;
}
