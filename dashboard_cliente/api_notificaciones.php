<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once '../includes/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$action = $data['action'] ?? '';

if ($action === 'marcar_leida') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID de notificación inválido']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE admin_alertas_notificaciones SET leida = 1 WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$id, $cliente_id]);
    
    echo json_encode(['success' => true]);
    exit;
} elseif ($action === 'marcar_todas_leidas') {
    $stmt = $pdo->prepare("UPDATE admin_alertas_notificaciones SET leida = 1 WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);
    
    echo json_encode(['success' => true]);
    exit;
} elseif ($action === 'listar') {
    $stmt = $pdo->prepare("SELECT * FROM admin_alertas_notificaciones WHERE cliente_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([$cliente_id]);
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'notificaciones' => $list]);
    exit;
} elseif ($action === 'eliminar') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID de notificación inválido']);
        exit;
    }
    
    $stmt = $pdo->prepare("DELETE FROM admin_alertas_notificaciones WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$id, $cliente_id]);
    
    echo json_encode(['success' => true]);
    exit;
} elseif ($action === 'eliminar_todas') {
    $stmt = $pdo->prepare("DELETE FROM admin_alertas_notificaciones WHERE cliente_id = ?");
    $stmt->execute([$cliente_id]);
    
    echo json_encode(['success' => true]);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    exit;
}
