<?php
// Detección automática de puerto MySQL
$detected_port = '3307';
try {
    $test_dsn = 'mysql:host=127.0.0.1;port=3307;dbname=mm_pharma;charset=utf8mb4';
    $test_pdo = new PDO($test_dsn, 'root', '', [
        PDO::ATTR_TIMEOUT => 1,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    $detected_port = '3306';
}

if (!defined('DB_PORT')) {
    define('DB_PORT', $detected_port);
}

require_once '../clinical_core/db.php';
$pdo = getDB();

echo "=== INICIANDO DEPURACIÓN DE productos DUPLICADOS ===\n";
echo "Conectado a puerto: " . DB_PORT . "\n\n";

try {
    // 1. Obtener grupos duplicados por nombre
    $stmt = $pdo->query("
        SELECT LOWER(TRIM(nombre)) as name_lower, COUNT(*) as qty, GROUP_CONCAT(id ORDER BY id ASC) as ids
        FROM catalogo_productos
        GROUP BY name_lower
        HAVING qty > 1
    ");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($groups)) {
        echo "No se encontraron productos duplicados por nombre.\n";
        exit;
    }

    echo "Se encontraron " . count($groups) . " grupos de duplicados.\n\n";

    foreach ($groups as $group) {
        $ids = explode(',', $group['ids']);
        $kept_id = (int)$ids[0]; // Conservar el ID más bajo
        $dup_ids = array_slice($ids, 1);
        
        // Obtener el nombre del producto conservado para mostrarlo en el log
        $stmt_name = $pdo->prepare("SELECT nombre FROM catalogo_productos WHERE id = ?");
        $stmt_name->execute([$kept_id]);
        $kept_name = $stmt_name->fetchColumn();

        echo "Procesando Grupo: '{$kept_name}' (Conserva ID: {$kept_id})\n";

        // Asegurarse de que el producto conservado tenga una fila en la tabla de stock
        $pdo->prepare("INSERT IGNORE INTO admin_inventario_stock (producto_id, stock_actual) VALUES (?, 0)")->execute([$kept_id]);

        foreach ($dup_ids as $dup_id) {
            $dup_id = (int)$dup_id;
            echo "  -> Fusionando ID: {$dup_id}\n";

            // A. Sumar y transferir stock
            $stmt_stock_dup = $pdo->prepare("SELECT stock_actual FROM admin_inventario_stock WHERE producto_id = ?");
            $stmt_stock_dup->execute([$dup_id]);
            $stock_dup = (int)$stmt_stock_dup->fetchColumn();

            if ($stock_dup > 0) {
                $pdo->prepare("UPDATE admin_inventario_stock SET stock_actual = stock_actual + ? WHERE producto_id = ?")
                    ->execute([$stock_dup, $kept_id]);
                echo "     * Stock transferido: +{$stock_dup} unidades.\n";
            }

            // B. Reasignar movimientos de inventario
            $stmt_mov = $pdo->prepare("UPDATE admin_inventario_movimientos SET producto_id = ? WHERE producto_id = ?");
            $stmt_mov->execute([$kept_id, $dup_id]);
            $mov_count = $stmt_mov->rowCount();
            if ($mov_count > 0) {
                echo "     * Movimientos de almacén actualizados: {$mov_count}.\n";
            }

            // C. Reasignar carrito de compras
            $stmt_cart = $pdo->prepare("UPDATE IGNORE clientes_carrito SET producto_id = ? WHERE producto_id = ?");
            $stmt_cart->execute([$kept_id, $dup_id]);
            // Limpieza por si falló la actualización por clave única (duplicado en el mismo carrito)
            $pdo->prepare("DELETE FROM clientes_carrito WHERE producto_id = ?")->execute([$dup_id]);

            // D. Reasignar detalles de pedidos
            $stmt_ped = $pdo->prepare("UPDATE clientes_pedidos_detalle SET producto_id = ? WHERE producto_id = ?");
            $stmt_ped->execute([$kept_id, $dup_id]);
            $ped_count = $stmt_ped->rowCount();
            if ($ped_count > 0) {
                echo "     * Detalles de pedidos actualizados: {$ped_count}.\n";
            }

            // E. Eliminar registro de stock del duplicado
            $pdo->prepare("DELETE FROM admin_inventario_stock WHERE producto_id = ?")->execute([$dup_id]);

            // F. Eliminar producto duplicado
            $pdo->prepare("DELETE FROM catalogo_productos WHERE id = ?")->execute([$dup_id]);
            echo "     * Registro de producto ID {$dup_id} eliminado exitosamente.\n";
        }
        echo "--------------------------------------------------------\n";
    }

    echo "\n=== DEPURACIÓN FINALIZADA CON ÉXITO ===\n";

} catch (Exception $e) {
    echo "\n[ERROR] Falló la transacción: " . $e->getMessage() . "\n";
}
?>
