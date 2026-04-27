<?php
require_once '../clinical_core/db.php';
$pdo      = getDB();
$clientes = $pdo->query(
    "SELECT razon_social, rfc, tipo, persona_contacto, email, telefono_local, estatus, created_at
     FROM clientes_usuarios ORDER BY razon_social ASC"
)->fetchAll();

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Clientes_" . date('Y-m-d') . ".xls");
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
  <tr><td colspan="7" class="title">MMPharma — Reporte General de Clientes</td></tr>
  <tr><td colspan="7" class="subtitle">Generado: <?= date('d/m/Y H:i:s') ?> — Total: <?= count($clientes) ?></td></tr>
  <tr><td colspan="7"> </td></tr>
</table>
<table class="table">
  <thead><tr>
    <th>Razón Social</th><th>RFC</th><th>Tipo</th>
    <th>Contacto</th><th>Email</th><th>Teléfono</th><th>Estatus</th>
  </tr></thead>
  <tbody>
  <?php foreach ($clientes as $c): ?>
  <tr>
    <td><?= htmlspecialchars($c['razon_social']) ?></td>
    <td><?= htmlspecialchars($c['rfc'] ?: '—') ?></td>
    <td><?= $c['tipo'] ?></td>
    <td><?= htmlspecialchars($c['persona_contacto'] ?: '—') ?></td>
    <td><?= htmlspecialchars($c['email'] ?: '—') ?></td>
    <td><?= htmlspecialchars($c['telefono_local'] ?: '—') ?></td>
    <td><?= str_replace('_',' ',$c['estatus']) ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body></html>
