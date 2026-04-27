<?php
/**
 * MMPharma — Setup de columnas faltantes en la BD
 * Ejecutar UNA VEZ: http://localhost/Proyecto_MMPharma/DASHBOARD_ADMIN/clinical_core/setup_columns.php
 * Borra o protege este archivo después de ejecutarlo.
 */
require_once 'db.php';
$pdo = getDB();
$results = [];

$alters = [
    "ALTER TABLE `admin_usuarios` ADD COLUMN IF NOT EXISTS `foto_perfil` VARCHAR(500) DEFAULT NULL AFTER `activo`",
    "ALTER TABLE `admin_usuarios` ADD COLUMN IF NOT EXISTS `telefono`    VARCHAR(30)  DEFAULT NULL AFTER `foto_perfil`",
];

foreach ($alters as $sql) {
    try {
        $pdo->exec($sql);
        $results[] = ['ok' => true, 'sql' => $sql];
    } catch (Exception $e) {
        $results[] = ['ok' => false, 'sql' => $sql, 'error' => $e->getMessage()];
    }
}

// Verificar columnas resultantes
$cols = $pdo->query("DESCRIBE administradores")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>MMPharma — Setup Columnas</title>
<style>
  body{font-family:monospace;background:#020d08;color:#f1fdf7;padding:2rem}
  h1{color:#008151}
  .ok{color:#34c47a} .err{color:#f28b82}
  table{border-collapse:collapse;width:100%;margin-top:1rem}
  th,td{border:1px solid #153d27;padding:.5rem 1rem;text-align:left}
  th{background:#030f09;color:#a3d7bd}
</style>
</head>
<body>
<h1>🔧 Setup — Columnas de administradores</h1>
<h2>Alteraciones ejecutadas:</h2>
<ul>
<?php foreach ($results as $r): ?>
  <li class="<?= $r['ok']?'ok':'err' ?>">
    <?= $r['ok'] ? '✓' : '✗' ?> <?= htmlspecialchars($r['sql']) ?>
    <?= !$r['ok'] ? ' → '.$r['error'] : '' ?>
  </li>
<?php endforeach; ?>
</ul>

<h2>Estructura actual de <code>administradores</code>:</h2>
<table>
  <thead><tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Default</th></tr></thead>
  <tbody>
  <?php foreach ($cols as $c): ?>
  <tr>
    <td><?= $c['Field'] ?></td>
    <td><?= $c['Type'] ?></td>
    <td><?= $c['Null'] ?></td>
    <td><?= $c['Default'] ?? '(null)' ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<p style="margin-top:2rem;color:#8aaad4;font-size:.8rem">
  ⚠️ Borra este archivo después de ejecutarlo:<br>
  <code>c:\xampp\htdocs\Proyecto_MMPharma\DASHBOARD_ADMIN\clinical_core\setup_columns.php</code>
</p>
<p><a href="../dashboard/dashboard.php" style="color:#4a90d9">← Volver al Dashboard</a></p>
</body>
</html>
