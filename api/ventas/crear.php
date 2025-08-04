<?php
require_once '../includes/secure_api.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$data = json_decode(file_get_contents("php://input"), true);
$productos = $data['productos'] ?? [];
$total = $data['total'] ?? 0;
$metodo = $data['metodo_pago'] ?? 'efectivo';
$usuario_id = $_SESSION['usuario']['id'] ?? null;

if (empty($productos) || !$usuario_id) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
  exit;
}

try {
  $pdo->beginTransaction();

  foreach ($productos as $p) {
    $checkStock = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
    $checkStock->execute([$p['id']]);
    $stockActual = $checkStock->fetchColumn();

    if ($stockActual === false || $stockActual < $p['cantidad']) {
      $pdo->rollBack();
      http_response_code(400);
      echo json_encode([
        'success' => false,
        'message' => "Stock insuficiente para el producto ID {$p['id']}. Solo hay {$stockActual} unidades disponibles."
      ]);
      exit;
    }
  }

  // Insertar venta
  $stmt = $pdo->prepare("INSERT INTO ventas (total, usuario_id, metodo_pago) VALUES (?, ?, ?)");
  $stmt->execute([$total, $usuario_id, $metodo]);
  $venta_id = $pdo->lastInsertId();

  // Insertar detalles y actualizar stock
  $detalleStmt = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");

  foreach ($productos as $p) {
    $subtotal = $p['precio'] * $p['cantidad'];
    $detalleStmt->execute([$venta_id, $p['id'], $p['cantidad'], $p['precio'], $subtotal]);

    $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?")
        ->execute([$p['cantidad'], $p['id']]);
  }

  $pdo->commit();

  // Generar ticket
  ob_start();
  require 'ticket.php';
  $html = ob_get_clean();

  $options = new Options();
  $options->set('isRemoteEnabled', true);

  $dompdf = new Dompdf($options);
  $dompdf->loadHtml($html);
  $dompdf->setPaper([0, 0, 220, 600 + count($productos) * 15]);
  $dompdf->render();

  $rutaTicket = "../../tickets/ticket_{$venta_id}.pdf";
  file_put_contents($rutaTicket, $dompdf->output());

  $url_publica = "/tickets/ticket_{$venta_id}.pdf";

  echo json_encode([
    'success' => true,
    'message' => 'Venta guardada con éxito',
    'venta_id' => $venta_id,
    'ticket_url' => $url_publica
  ]);

} catch (Exception $e) {
  $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Error en el servidor', 'detalle' => $e->getMessage()]);
}
