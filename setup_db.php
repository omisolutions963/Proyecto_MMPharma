<?php
require_once 'INCLUDES/db.php';

$pdo = getDB();

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM clientes_usuarios LIKE 'password_hash'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE clientes ADD COLUMN password_hash varchar(255) DEFAULT NULL AFTER email");
        echo "Column added.\n";
    }

    // Check if mock client exists
    $email = 'cliente@mmpharma.com';
    $stmt = $pdo->prepare("SELECT id FROM clientes_usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() == 0) {
        $hash = password_hash('password123', PASSWORD_BCRYPT);
        $pdo->exec("INSERT INTO clientes_usuarios (tipo, razon_social, email, password_hash, estatus) VALUES ('FARMACIA', 'Farmacia de Prueba S.A. de C.V.', 'cliente@mmpharma.com', '$hash', 'ACTIVO')");
        echo "Mock client inserted.\n";
    } else {
        $hash = password_hash('password123', PASSWORD_BCRYPT);
        $pdo->exec("UPDATE clientes_usuarios SET password_hash = '$hash' WHERE email = 'cliente@mmpharma.com'");
        echo "Mock client updated.\n";
    }

    echo "Success.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
