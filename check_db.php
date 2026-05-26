<?php
require_once 'c:\xampp\htdocs\Proyecto_MMPharma\INCLUDES\db.php';
$pdo = getDB();

try {
    // Add the column if it doesn't exist
    $pdo->query("ALTER TABLE clientes_pedidos ADD COLUMN recoger_sucursal TINYINT(1) NOT NULL DEFAULT 0");
    echo "Column recoger_sucursal added.\n";
    
    // Update existing orders that are 'RECOGER EN SUCURSAL'
    $pdo->query("UPDATE clientes_pedidos SET recoger_sucursal = 1 WHERE estado_envio = 'RECOGER EN SUCURSAL'");
    echo "Updated existing orders.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
