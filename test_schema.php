<?php
require_once 'INCLUDES/db.php';
$pdo = getDB();
$stmt = $pdo->query('DESCRIBE clientes_documentos');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "\n====\n";
$stmt2 = $pdo->query('DESCRIBE clientes_usuarios');
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
