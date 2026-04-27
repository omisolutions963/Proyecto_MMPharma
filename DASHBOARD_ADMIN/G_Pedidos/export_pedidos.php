<?php
require_once '../clinical_core/db.php';
$pdo    = getDB();
$pedidos= $pdo->query(
    "SELECT p.folio, c.razon_social AS cliente, p.tipo_cliente,
            p.fecha_pedido, p.monto_total, p.metodo_pago, p.estado_envio
     FROM clientes_pedidos p
     LEFT JOIN clientes_usuarios c ON c.id = p.cliente_id
     ORDER BY p.fecha_pedido DESC"
)->fetchAll();

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Ventas_Pedidos_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html lang="es"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  body{font-family:Arial,sans-serif}
  .table{width:100%;border-collapse:collapse}
  .table th{background:#002451;color:#fff;padding:10px;text-align:left;border:1px solid #002451}
  .table td{padding:10px;border:1px solid #c4c6d0;color:#051d30}
  .table tr:nth-child(even){background:#f7f9ff}
  .title{color:#002451;font-size:24px;font-weight:bold;text-align:center}
  .subtitle{color:#006397;font-size:14px;text-align:center}
</style>
</head><body>
<table>
  <tr><td colspan="6" class="title">MMPharma — Reporte de Ventas (Pedidos)</td></tr>
  <tr><td colspan="6" class="subtitle">Generado: <?= date('d/m/Y H:i:s') ?> — Total: <?= count($pedidos) ?> pedidos</td></tr>
  <tr><td colspan="6"> </td></tr>
</table>
<table class="table">
  <thead><tr>
    <th>Folio</th><th>Cliente</th><th>Tipo</th>
    <th>Fecha</th><th>Monto Total</th><th>Método Pago</th><th>Estado Envío</th>
  </tr></thead>
  <tbody>
  <?php foreach ($pedidos as $p): ?>
  <tr>
    <td><?= htmlspecialchars($p['folio']) ?></td>
    <td><?= htmlspecialchars($p['cliente'] ?: '(Sin cliente)') ?></td>
    <td><?= $p['tipo_cliente'] ?></td>
    <td><?= date('d/m/Y', strtotime($p['fecha_pedido'])) ?></td>
    <td>$<?= number_format($p['monto_total'], 2) ?></td>
    <td><?= str_replace('_',' ',$p['metodo_pago']) ?></td>
    <td><?= $p['estado_envio'] ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body></html>
