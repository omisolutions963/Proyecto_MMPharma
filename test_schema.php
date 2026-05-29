<?php
require_once 'DASHBOARD_ADMIN/clinical_core/db.php';
$pdo = getDB();
$stmt = $pdo->query('DESCRIBE catalogo_productos');
print_r($stmt->fetchAll());
echo "\n====\n";
$stmt2 = $pdo->query('DESCRIBE admin_inventario_stock');
print_r($stmt2->fetchAll());
