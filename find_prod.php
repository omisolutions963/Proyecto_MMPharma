<?php
require 'DASHBOARD_ADMIN/clinical_core/db.php';
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM catalogo_productos WHERE nombre LIKE '%A.S. COR%'");
print_r($stmt->fetchAll());
