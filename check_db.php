<?php
require_once 'c:\xampp\htdocs\Proyecto_MMPharma\INCLUDES\db.php';
$pdo = getDB();

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'clientes_documentos'");
    if ($stmt->rowCount() > 0) {
        echo "Table clientes_documentos exists.\n";
        $stmt2 = $pdo->query("DESCRIBE clientes_documentos");
        $cols = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        print_r($cols);
    } else {
        echo "Table clientes_documentos DOES NOT exist.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
