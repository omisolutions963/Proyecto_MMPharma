<?php
require_once 'DASHBOARD_ADMIN/clinical_core/db.php';
$pdo = getDB();
try {
    $sql = "INSERT INTO catalogo_productos (nombre, codigo, tipo, categoria_id, precio_farmacia, precio_distribuidor, precio_empresa, en_promocion, descuento_porcentaje, imagen) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $pdo->prepare($sql)->execute(['Test Prod', '123', 'SECO', null, 100, 90, 80, 1, 10, null]);
    $id = $pdo->lastInsertId();
    echo "Inserted product $id\n";
    $pdo->prepare("INSERT INTO admin_inventario_stock (producto_id, stock_actual) VALUES (?, ?) ON DUPLICATE KEY UPDATE stock_actual = ?")
      ->execute([$id, 50, 50]);
    echo "Inserted stock for $id\n";
    $pdo->exec("DELETE FROM catalogo_productos WHERE id = $id");
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
