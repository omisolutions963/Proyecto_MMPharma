<?php
require_once 'INCLUDES/db.php';
$pdo = getDB();

$cols = [
    'doc_constancia_fiscal',
    'doc_licencia_sanitaria',
    'doc_comprobante_domicilio',
    'doc_alta_hacienda',
    'doc_identificacion_oficial',
    'doc_acta_constitutiva'
];

foreach (['clientes_solicitudes_registro', 'clientes_usuarios'] as $table) {
    foreach ($cols as $col) {
        try {
            $pdo->exec("ALTER TABLE $table ADD COLUMN $col VARCHAR(255) DEFAULT NULL");
            echo "Added $col to $table\n";
        } catch (Exception $e) {
            echo "Column $col already exists in $table or error: " . $e->getMessage() . "\n";
        }
    }
}
echo "Done.\n";
