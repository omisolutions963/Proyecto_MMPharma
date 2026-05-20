<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
$id = $data['id'];

try {
    // Delete the address
    $stmt = $pdo->prepare("DELETE FROM clientes_direcciones WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$id, $cliente_id]);
    
    // Check if there's any default address left. If not, make the first one default.
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes_direcciones WHERE cliente_id = ? AND predeterminada = 1");
    $stmt->execute([$cliente_id]);
    if ($stmt->fetchColumn() == 0) {
        $stmt2 = $pdo->prepare("SELECT id FROM clientes_direcciones WHERE cliente_id = ? ORDER BY id ASC LIMIT 1");
        $stmt2->execute([$cliente_id]);
        $first_id = $stmt2->fetchColumn();
        if ($first_id) {
            $pdo->prepare("UPDATE clientes_direcciones SET predeterminada = 1 WHERE id = ?")->execute([$first_id]);
        }
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar la dirección: ' . $e->getMessage()]);
}
