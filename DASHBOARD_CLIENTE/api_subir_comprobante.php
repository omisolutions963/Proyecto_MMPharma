<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../INCLUDES/db.php';
$pdo = getDB();

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No session']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id = $_POST['pedido_id'] ?? null;
    $cliente_id = $_SESSION['cliente_id'];
    
    if (!$pedido_id || !isset($_FILES['comprobante'])) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }
    
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM clientes_pedidos WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$pedido_id, $cliente_id]);
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
        exit;
    }
    
    $file = $_FILES['comprobante'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "COMP_" . $pedido_id . "_" . time() . "." . $ext;
    $upload_dir = "../uploads/comprobantes/";
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $stmt = $pdo->prepare("INSERT INTO clientes_pedidos_comprobantes (pedido_id, ruta_archivo, estatus) VALUES (?, ?, 'PENDIENTE')");
        $stmt->execute([$pedido_id, $filename]);
        
        echo json_encode(['status' => 'success', 'filename' => $filename]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo']);
    }
}
