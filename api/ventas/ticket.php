<?php
// Reutiliza este archivo para generar el contenido HTML del ticket
$stmt = $pdo->prepare("SELECT v.*, u.nombre AS vendedor FROM ventas v JOIN usuarios u ON u.id = v.usuario_id WHERE v.id = ?");
$stmt->execute([$venta_id]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

$detalles = $pdo->prepare("
  SELECT vd.*, p.nombre 
  FROM venta_detalles vd 
  JOIN productos p ON p.id = vd.producto_id 
  WHERE vd.venta_id = ?
");
$detalles->execute([$venta_id]);
$productos = $detalles->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  body { font-family: Arial; font-size: 12px; }
  .center { text-align: center; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { padding: 4px; }
  .total { font-weight: bold; }
</style>

<div class="center">
  <h2>Barbería El Corte</h2>
  <p>-----------------------------</p>
  <p>Fecha: <?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></p>
  <p>Vendedor: <?= $venta['vendedor'] ?></p>
</div>

<table>
  <thead>
    <tr>
      <th>Producto</th>
      <th>Cant</th>
      <th>P/U</th>
      <th>Sub</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($productos as $p): ?>
      <tr>
        <td><?= $p['nombre'] ?></td>
        <td><?= $p['cantidad'] ?></td>
        <td>$<?= number_format($p['precio_unitario'], 2) ?></td>
        <td>$<?= number_format($p['subtotal'], 2) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<p class="center">-----------------------------</p>
<p class="total center">TOTAL: $<?= number_format($venta['total'], 2) ?></p>
<p class="center">Método de pago: <?= ucfirst($venta['metodo_pago']) ?></p>
<p class="center">¡Gracias por su compra!</p>
