<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
$pdo = getDB();

if (!isset($_SESSION['cliente_id'])) {
 echo json_encode(['status' => 'error', 'message' => 'No session']);
 exit;
}

$cliente_id = $_SESSION['cliente_id'];
$cliente_tipo = $_SESSION['cliente_tipo'] ?? 'FARMACIA';
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? $_GET['action'] ?? '';

if ($action === 'save') {
 $items = $data['items'] ?? [];
 
 // Begin transaction
 $pdo->beginTransaction();
 try {
 // Clear current cart
 $stmt = $pdo->prepare("DELETE FROM clientes_carrito WHERE cliente_id = ?");
 $stmt->execute([$cliente_id]);
 
 // Insert new items
 $stmt = $pdo->prepare("INSERT INTO clientes_carrito (cliente_id, producto_id, cantidad) VALUES (?, ?, ?)");
  $stmt_check = $pdo->prepare("SELECT solo_empresa, nombre, sustancia FROM catalogo_productos WHERE id = ?");
  
  foreach ($items as $item) {
  $stmt_check->execute([$item['id']]);
  $prod = $stmt_check->fetch();
  if (!$prod) continue;
  if ($cliente_tipo === 'EMPRESA') {
    $nombre_lower = mb_strtolower($prod['nombre']);
    $sustancia_lower = mb_strtolower($prod['sustancia'] ?? '');
    $es_excepcion = (
      strpos($nombre_lower, 'aspirina') !== false || strpos($sustancia_lower, 'aspirina') !== false ||
      strpos($nombre_lower, 'loratadina') !== false || strpos($sustancia_lower, 'loratadina') !== false ||
      strpos($nombre_lower, 'loratidina') !== false || strpos($sustancia_lower, 'loratidina') !== false ||
      strpos($nombre_lower, 'buscapina') !== false ||
      strpos($nombre_lower, 'butilhioscina') !== false || strpos($sustancia_lower, 'butilhioscina') !== false
    );
    if ($prod['solo_empresa'] !== 'SI' && !$es_excepcion) {
      continue;
    }
  }
  $stmt->execute([$cliente_id, $item['id'], $item['cantidad']]);
  }
 
 $pdo->commit();
 echo json_encode(['status' => 'success']);
 } catch (Exception $e) {
 $pdo->rollBack();
 echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
 }
} elseif ($action === 'get') {
  $stmt = $pdo->prepare("
   SELECT c.cantidad, p.id, p.nombre, p.imagen, 
   p.precio_farmacia, p.precio_distribuidor, p.precio_empresa, p.precio_red_fria, p.tipo,
   p.en_promocion, p.descuento_porcentaje, p.tasa_iva
   FROM clientes_carrito c
   JOIN catalogo_productos p ON c.producto_id = p.id
  WHERE c.cliente_id = ?
  ");
  $stmt->execute([$cliente_id]);
  $db_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  $items = [];
  foreach ($db_items as $db_item) {
   $precio_campo = 'precio_farmacia';
   if ($cliente_tipo === 'DISTRIBUIDORA') $precio_campo = 'precio_distribuidor';
   elseif ($cliente_tipo === 'EMPRESA') $precio_campo = 'precio_empresa';
   
   $precio_base = (float)$db_item[$precio_campo];
   $precio_final = $precio_base;
   if ($db_item['en_promocion'] && $db_item['descuento_porcentaje'] > 0) {
   $precio_final = $precio_base * (1 - ($db_item['descuento_porcentaje'] / 100));
   }

   $items[] = [
   'id' => $db_item['id'],
   'nombre' => $db_item['nombre'],
   'precio' => $precio_final,
   'imagen' => $db_item['imagen'],
   'cantidad' => (int)$db_item['cantidad'],
   'tasa_iva' => (float)$db_item['tasa_iva'],
   'tipo' => $db_item['tipo'],
   'precio_red_fria' => (float)$db_item['precio_red_fria']
   ];
 }
 
 echo json_encode(['status' => 'success', 'items' => $items]);
}
