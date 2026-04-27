<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

// Productos sin precio (considerados "faltantes" de información)
$stmt = $pdo->query(
    "SELECT codigo, nombre, sustancia, tipo, precio_farmacia, precio_distribuidor, precio_empresa
     FROM catalogo_productos
     WHERE precio_farmacia = 0 AND precio_distribuidor = 0
     ORDER BY nombre ASC"
);
$faltantes = $stmt->fetchAll();

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Faltantes_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
  body { font-family: Arial, sans-serif; }
  .table { width: 100%; border-collapse: collapse; }
  .table th { background-color: #002451; color: #fff; padding: 10px; text-align: left; border: 1px solid #002451; }
  .table td { padding: 10px; border: 1px solid #c4c6d0; color: #051d30; }
  .table tr:nth-child(even) { background-color: #f7f9ff; }
  .title    { color: #002451; font-size: 24px; font-weight: bold; text-align: center; }
  .subtitle { color: #006397; font-size: 14px; text-align: center; }
  .alert-cell { color: #ba1a1a; font-weight: bold; }
</style>
</head>
<body>
<table>
  <tr><td colspan="5" class="title">MMPharma - Reporte de Productos Sin Precio</td></tr>
  <tr><td colspan="5" class="subtitle">Generado el: <?= date('d/m/Y H:i:s') ?> — Total: <?= count($faltantes) ?> productos</td></tr>
  <tr><td colspan="5"> </td></tr>
</table>
<table class="table">
  <thead>
    <tr>
      <th>Código</th>
      <th>Nombre del Producto</th>
      <th>Sustancia</th>
      <th>Tipo</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($faltantes as $p): ?>
  <tr>
    <td><?= htmlspecialchars($p['codigo']) ?></td>
    <td><?= htmlspecialchars($p['nombre']) ?></td>
    <td><?= htmlspecialchars($p['sustancia'] ?: '—') ?></td>
    <td><?= htmlspecialchars($p['tipo']) ?></td>
    <td class="alert-cell">SIN PRECIO</td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</body>
</html>
