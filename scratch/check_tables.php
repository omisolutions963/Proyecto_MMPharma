<?php
require_once 'c:/xampp/htdocs/Proyecto_MMPharma/includes/db.php';
$pdo = getDB();

echo "=== CATALOG PRODUCTS BY TASA_IVA ===\n";
try {
    $stmt = $pdo->query("SELECT tasa_iva, COUNT(*) as qty FROM catalogo_productos GROUP BY tasa_iva");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("Tasa IVA: %s | Count: %d\n", $row['tasa_iva'], $row['qty']);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
