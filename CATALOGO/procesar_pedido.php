<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['carrito'])) {
    echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
    exit;
}

$carrito = $data['carrito'];
$cliente_id = $_SESSION['cliente_id'];
$tipo_cliente = $_SESSION['cliente_tipo'] ?? 'FARMACIA';

try {
    $pdo->beginTransaction();

    // Calcular total
    $monto_total = 0;
    foreach ($carrito as $item) {
        $monto_total += (float)$item['precio'] * (int)$item['cantidad'];
    }

    // Generar folio
    $stmt = $pdo->query("SELECT id FROM clientes_pedidos ORDER BY id DESC LIMIT 1");
    $last_id = $stmt->fetchColumn();
    $next_id = $last_id ? $last_id + 1 : 1;
    $folio = 'ORD-' . date('Y') . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

    $direccion_id = $data['direccion_id'] ?? null;

    // Insertar pedido
    $stmt = $pdo->prepare("INSERT INTO clientes_pedidos (folio, cliente_id, tipo_cliente, direccion_id, fecha_pedido, monto_total, estado_envio) VALUES (?, ?, ?, ?, CURDATE(), ?, 'PENDIENTE')");
    $stmt->execute([$folio, $cliente_id, $tipo_cliente, $direccion_id, $monto_total]);
    $pedido_id = $pdo->lastInsertId();

    // Insertar detalles
    $stmtDetalle = $pdo->prepare("INSERT INTO clientes_pedidos_detalle (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($carrito as $item) {
        $pid = (int)$item['id'];
        $nombre = $item['nombre'];
        $cant = (int)$item['cantidad'];
        $precio = (float)$item['precio'];
        $sub = $cant * $precio;

        $stmtDetalle->execute([$pedido_id, $pid, $nombre, $cant, $precio, $sub]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'folio' => $folio]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en BD: ' . $e->getMessage()]);
}
