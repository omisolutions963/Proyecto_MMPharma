<?php
require_once 'INCLUDES/db.php';
$pdo = getDB();
$stmt = $pdo->query("SHOW COLUMNS FROM clientes_documentos");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($columns, JSON_PRETTY_PRINT);

$stmt2 = $pdo->query("SHOW COLUMNS FROM clientes_usuarios");
$columns2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
echo "\n\nUsuarios:\n";
echo json_encode($columns2, JSON_PRETTY_PRINT);
?>
