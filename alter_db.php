<?php
require_once 'INCLUDES/db.php';
$pdo = getDB();
try {
    $pdo->exec("ALTER TABLE clientes_documentos MODIFY tipo_documento VARCHAR(50) NOT NULL");
    echo "Tabla clientes_documentos modificada correctamente.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
