<?php
session_start();
header('Content-Type: application/json');
require_once '../INCLUDES/db.php';

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? (int)$data['id'] : 0;
$cliente_id = $_SESSION['cliente_id'];

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID de cotización no proporcionado']);
    exit;
}

try {
    $pdo = getDB();
    
    // Verificar que la cotización pertenezca al cliente y esté en estado PENDIENTE o CANCELADO (opcional, pero seguro)
    // El usuario pidió "poder de borrar", así que permitiré borrar cualquiera que sea suya.
    $stmt = $pdo->prepare("SELECT id, estado_envio FROM clientes_pedidos WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$id, $cliente_id]);
    $pedido = $stmt->fetch();

    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Cotización no encontrada o no pertenece a tu cuenta']);
        exit;
    }

    // Opcional: No permitir borrar si ya está aprobada/enviada para mantener integridad (opcional)
    /*
    if ($pedido['estado_envio'] !== 'PENDIENTE' && $pedido['estado_envio'] !== 'CANCELADO') {
        echo json_encode(['success' => false, 'message' => 'No puedes borrar una cotización que ya ha sido procesada']);
        exit;
    }
    */

    $pdo->beginTransaction();

    // 1. Borrar detalles
    $stmt = $pdo->prepare("DELETE FROM clientes_pedidos_detalle WHERE pedido_id = ?");
    $stmt->execute([$id]);

    // 2. Borrar cabecera
    $stmt = $pdo->prepare("DELETE FROM clientes_pedidos WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($pdo)) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
}
