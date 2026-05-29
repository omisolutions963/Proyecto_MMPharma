<?php
require 'DASHBOARD_ADMIN/clinical_core/db.php';
$pdo = getDB();

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM catalogo_productos LIKE 'promocion_perfil'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $pdo->exec("ALTER TABLE catalogo_productos ADD COLUMN promocion_perfil VARCHAR(50) NOT NULL DEFAULT 'TODOS'");
        echo "Columna promocion_perfil añadida correctamente.\n";
    } else {
        echo "La columna promocion_perfil ya existe.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
