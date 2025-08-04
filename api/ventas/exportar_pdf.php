<?php
require_once '../includes/secure_api.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$inicio = $_GET['fecha_inicio'] ?? null;
$fin = $_GET['fecha_fin'] ?? null;

$sql = "SELECT v.*, u.nombre AS vendedor FROM ventas v 
        JOIN usuarios u ON u.id = v.usuario_id 
        WHERE 1";

$params = [];
if ($inicio) {
  $sql .= " AND v.fecha >= ?";
  $params[] = $inicio . ' 00:00:00';
}
if ($fin) {
  $sql .= " AND v.fecha <= ?";
  $params[] = $fin . ' 23:59:59';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// HTML para el PDF
ob_start();
?>

<style>
  body { font-family: Arial, sans-serif; font-size: 12px; }
  h2, th, td { text-align: center; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { border: 1px solid #ccc; padding: 4px; }
</style>

<h2>Reporte de Ventas</h2>
<p>Desde: <?= $inicio ?? '---' ?> | Hasta: <?= $fin ?? '---' ?></p>

<table>
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Vendedor</th>
      <th>Total</th>
      <th>Método</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($ventas as $v): ?>
      <tr>
        <td><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
        <td><?= $v['vendedor'] ?></td>
        <td>$<?= number_format($v['total'], 2) ?></td>
        <td><?= ucfirst($v['metodo_pago']) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_ventas.pdf", ["Attachment" => false]);
