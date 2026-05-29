<?php
require_once 'DASHBOARD_ADMIN/clinical_core/db.php';
$pdo = getDB();

try {
    $pdo->exec("ALTER TABLE catalogo_productos ADD COLUMN en_promocion TINYINT(1) NOT NULL DEFAULT 0 AFTER solo_empresa");
    echo "Column en_promocion added.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
try {
    $pdo->exec("ALTER TABLE catalogo_productos ADD COLUMN descuento_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER en_promocion");
    echo "Column descuento_porcentaje added.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
