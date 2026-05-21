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

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
$id = trim($data['id'] ?? '');
$alias = trim($data['alias'] ?? '');
$calle = trim($data['calle'] ?? '');
$colonia = trim($data['colonia'] ?? '');
$cp = trim($data['cp'] ?? '');
$ciudad = trim($data['ciudad'] ?? '');
$municipio = trim($data['municipio'] ?? '');
if (!empty($municipio)) {
    $ciudad .= ', ' . $municipio;
}
$estado = trim($data['estado'] ?? '');
$telefono = trim($data['telefono'] ?? '');
$lat = $data['latitud'] ?? null;
$lng = $data['longitud'] ?? null;

if($lat === '') $lat = null;
if($lng === '') $lng = null;

$costo_envio = 0.00;

// Si no hay direcciones anteriores, esta será la predeterminada
$stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes_direcciones WHERE cliente_id = ?");
$stmt->execute([$cliente_id]);
$count = $stmt->fetchColumn();
$predeterminada = ($count == 0) ? 1 : 0;

try {
    if ($id) {
        // Update existing address
        $stmt = $pdo->prepare("UPDATE clientes_direcciones 
            SET alias = ?, calle = ?, colonia = ?, codigo_postal = ?, ciudad = ?, estado = ?, telefono = ?, latitud = ?, longitud = ?, costo_envio = ? 
            WHERE id = ? AND cliente_id = ?");
        $stmt->execute([
            $alias, $calle, $colonia, $cp, $ciudad, $estado, $telefono, $lat, $lng, $costo_envio, $id, $cliente_id
        ]);
    } else {
        // Insert new address
        $stmt = $pdo->prepare("INSERT INTO clientes_direcciones 
            (cliente_id, alias, calle, colonia, codigo_postal, ciudad, estado, telefono, latitud, longitud, costo_envio, predeterminada) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $cliente_id, $alias, $calle, $colonia, $cp, $ciudad, $estado, $telefono, $lat, $lng, $costo_envio, $predeterminada
        ]);
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error al guardar la dirección: ' . $e->getMessage()]);
}
