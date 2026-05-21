<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once '../INCLUDES/db.php';
require_once '../INCLUDES/shipping_calculator.php';

$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$direccion_id = $data['direccion_id'] ?? null;
$subtotal = (float)($data['subtotal'] ?? 0);

if (!$direccion_id) {
    echo json_encode(['success' => false, 'message' => 'Falta direccion_id']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT estado, latitud, longitud FROM clientes_direcciones WHERE id = ? AND cliente_id = ?");
    $stmt->execute([$direccion_id, $_SESSION['cliente_id']]);
    $direccion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$direccion) {
        echo json_encode(['success' => false, 'message' => 'Dirección no encontrada']);
        exit;
    }

    $estado = $direccion['estado'];
    $lat = $direccion['latitud'] !== null ? (float)$direccion['latitud'] : null;
    $lng = $direccion['longitud'] !== null ? (float)$direccion['longitud'] : null;

    $calculo = calcularCostoEnvio($subtotal, $estado, $lat, $lng);

    echo json_encode(['success' => true, 'calculo' => $calculo]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error de BD: ' . $e->getMessage()]);
}
