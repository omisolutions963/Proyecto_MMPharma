<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}
if (isset($_SESSION['debe_cambiar_password']) && $_SESSION['debe_cambiar_password'] == 1) {
    echo json_encode(['success' => false, 'message' => 'Debe cambiar su contraseña antes de realizar un pedido.']);
    exit;
}

require_once '../includes/db.php';
require_once '../includes/shipping_calculator.php';
$pdo = getDB();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['carrito'])) {
    echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
    exit;
}

$carrito         = $data['carrito'];
$cliente_id      = $_SESSION['cliente_id'];
$tipo_cliente    = $_SESSION['cliente_tipo'] ?? 'FARMACIA';

try {
    $pdo->beginTransaction();

    // ── Calcular subtotal ────────────────────────────────────────────────────
    $subtotal = 0;
    foreach ($carrito as $item) {
        $subtotal += (float)$item['precio'] * (int)$item['cantidad'];
    }
    $monto_total = $subtotal;

    // ── Generar folio ────────────────────────────────────────────────────────
    $stmt    = $pdo->query("SELECT id FROM clientes_pedidos ORDER BY id DESC LIMIT 1");
    $last_id = $stmt->fetchColumn();
    $next_id = $last_id ? $last_id + 1 : 1;
    $folio   = 'ORD-' . date('Y') . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

    $direccion_id     = $data['direccion_id'] ?? null;
    $costo_envio      = 0.00;
    $recoger_sucursal = isset($data['recoger_sucursal']) && $data['recoger_sucursal'];
    if ($subtotal < 4000.00) {
        $recoger_sucursal = true;
    }
    $estado_envio     = $recoger_sucursal ? ($subtotal < 4000.00 ? 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' : 'RECOGER EN SUCURSAL') : 'PENDIENTE';
    $estado_destino   = null;
    $lat = null;
    $lng = null;

    // ── Info de dirección ────────────────────────────────────────────────────
    if ($direccion_id) {
        $stmtDir = $pdo->prepare("SELECT estado, latitud, longitud FROM clientes_direcciones WHERE id = ?");
        $stmtDir->execute([$direccion_id]);
        $dirInfo = $stmtDir->fetch(PDO::FETCH_ASSOC);
        if ($dirInfo) {
            $estado_destino = strtoupper(trim($dirInfo['estado']));
            $lat = $dirInfo['latitud'] !== null ? (float)$dirInfo['latitud'] : null;
            $lng = $dirInfo['longitud'] !== null ? (float)$dirInfo['longitud'] : null;
            $calc = calcularCostoEnvio($subtotal, $dirInfo['estado'], $lat, $lng);
            $costo_envio  = $recoger_sucursal ? 0.00 : $calc['costo'];
            $monto_total += $costo_envio;
        }
    }

    // ── VALIDACIÓN RED FRÍA ──────────────────────────────────────────────────
    // Si el destino es foráneo (fuera de Jalisco) y NO es recoger en sucursal,
    // bloqueamos el pedido si contiene productos de Red Fría.
    if (!$recoger_sucursal && $estado_destino && $estado_destino !== 'JALISCO') {
        $ids_carrito  = array_column($carrito, 'id');
        if (!empty($ids_carrito)) {
            $placeholders = implode(',', array_fill(0, count($ids_carrito), '?'));
            $stmtRF = $pdo->prepare(
                "SELECT nombre FROM catalogo_productos WHERE id IN ($placeholders) AND tipo = 'RED FRIA'"
            );
            $stmtRF->execute($ids_carrito);
            $prods_rf = $stmtRF->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($prods_rf)) {
                $pdo->rollBack();
                $muestra = implode(', ', array_map(fn($n) => mb_substr($n, 0, 40), array_slice($prods_rf, 0, 3)));
                $extra   = count($prods_rf) > 3 ? ' y ' . (count($prods_rf) - 3) . ' más' : '';
                echo json_encode([
                    'success'  => false,
                    'red_fria' => true,
                    'message'  => "Tu pedido contiene producto(s) de Red Fría ($muestra$extra) que no pueden enviarse a un estado foráneo sin coordinación especial de paquetería fría. Por favor comunícate con tu asesor o selecciona \"Recoger en sucursal\".",
                ]);
                exit;
            }
        }
    }

    // ── Insertar pedido ──────────────────────────────────────────────────────
    $stmt = $pdo->prepare(
        "INSERT INTO clientes_pedidos
            (folio, cliente_id, tipo_cliente, direccion_id, fecha_pedido,
             monto_total, costo_envio, estado_envio, recoger_sucursal)
         VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)"
    );
    $stmt->execute([
        $folio, $cliente_id, $tipo_cliente, $direccion_id,
        $monto_total, $costo_envio, $estado_envio,
        $recoger_sucursal ? 1 : 0
    ]);
    $pedido_id = $pdo->lastInsertId();

    // ── Insertar detalles ────────────────────────────────────────────────────
    $stmtDetalle = $pdo->prepare(
        "INSERT INTO clientes_pedidos_detalle
            (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $nombres_productos = [];
    foreach ($carrito as $item) {
        $pid    = (int)$item['id'];
        $nombre = $item['nombre'];
        $cant   = (int)$item['cantidad'];
        $precio = (float)$item['precio'];
        $sub    = $cant * $precio;
        $stmtDetalle->execute([$pedido_id, $pid, $nombre, $cant, $precio, $sub]);
        $nombres_productos[] = $cant . 'x ' . mb_substr($nombre, 0, 50);
    }

    $pdo->commit();

    // ── CORREO A AGENTES DE VENTAS ───────────────────────────────────────────
    $stmtCli = $pdo->prepare(
        "SELECT razon_social, email, telefono_local, telefono_celular FROM clientes_usuarios WHERE id = ?"
    );
    $stmtCli->execute([$cliente_id]);
    $cli = $stmtCli->fetch(PDO::FETCH_ASSOC);

    $razon_social  = $cli['razon_social'] ?? 'Cliente';
    $email_cliente = $cli['email']        ?? '';
    $tel           = trim(($cli['telefono_local'] ?? '') . ' / ' . ($cli['telefono_celular'] ?? ''), ' /');
    $total_fmt     = '$' . number_format($monto_total, 2);

    $lista_li = '';
    foreach ($nombres_productos as $np) {
        $lista_li .= '<li style="padding:3px 0">' . htmlspecialchars($np) . '</li>';
    }

    $url_pedido = getAppURL() . '/dashboard_admin/g_pedidos/ver_pedido.php?id=' . $pedido_id;

    $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f7ff;padding:30px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,62,121,.15)">
  <div style="background:#003e79;padding:24px 32px">
    <h1 style="margin:0;color:#fff;font-size:20px">Nuevo pedido — MMPharma</h1>
    <p style="margin:6px 0 0;color:#67e8f9;font-size:13px">Se acaba de registrar un nuevo pedido en el portal de clientes.</p>
  </div>
  <div style="padding:32px">
    <table style="width:100%;border-collapse:collapse;font-size:14px;color:#333">
      <tr><td style="padding:8px 0;color:#666;width:130px">Folio</td>
          <td style="font-weight:bold;color:#003e79">' . htmlspecialchars($folio) . '</td></tr>
      <tr><td style="padding:8px 0;color:#666">Cliente</td>
          <td style="font-weight:bold">' . htmlspecialchars($razon_social) . '</td></tr>
      <tr><td style="padding:8px 0;color:#666">Tipo</td>
          <td>' . htmlspecialchars($tipo_cliente) . '</td></tr>
      <tr><td style="padding:8px 0;color:#666">Email</td>
          <td><a href="mailto:' . htmlspecialchars($email_cliente) . '" style="color:#003e79">' . htmlspecialchars($email_cliente) . '</a></td></tr>
      <tr><td style="padding:8px 0;color:#666">Teléfono</td>
          <td>' . htmlspecialchars($tel) . '</td></tr>
      <tr><td style="padding:8px 0;color:#666">Envío</td>
          <td>' . htmlspecialchars($estado_envio) . '</td></tr>
      <tr style="background:#f0f5ff">
          <td style="padding:12px;color:#003e79;font-weight:bold;font-size:15px">TOTAL</td>
          <td style="padding:12px;font-size:22px;font-weight:bold;color:#003e79">' . $total_fmt . '</td></tr>
    </table>
    <h3 style="color:#003e79;margin-top:24px;margin-bottom:8px">Productos:</h3>
    <ul style="margin:0;padding-left:20px;color:#333;font-size:13px;line-height:1.8">' . $lista_li . '</ul>
    <div style="margin-top:28px;text-align:center">
      <a href="' . htmlspecialchars($url_pedido) . '"
         style="display:inline-block;background:#003e79;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px">
        Ver pedido en el panel
      </a>
    </div>
  </div>
  <div style="background:#f0f5ff;padding:16px 32px;text-align:center;font-size:11px;color:#888">
    MMPharma &bull; Notificación automática
  </div>
</div></body></html>';

    require_once __DIR__ . '/../includes/mailer.php';
    $asunto  = "Nuevo Pedido $folio — $razon_social [$tipo_cliente] — $total_fmt";

    // ── Lista de agentes de ventas ───────────────────────────────────────────
    $agentes = [
        'ventas@mmpharma.com',
        'ventas1@mmpharma.com',
        'ventas3@mmpharma.com',
    ];
    foreach ($agentes as $agente) {
        enviarCorreoPHPMailer($agente, $asunto, $html);
    }

    // ── Correo de confirmación al cliente ────────────────────────────────────
    $url_pedido_cliente = getAppURL() . '/dashboard_cliente/cotizacion-detalle.php?id=' . $pedido_id;

    $html_cliente = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f7ff;padding:30px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,62,121,.15)">
  <div style="background:#003e79;padding:24px 32px">
    <h1 style="margin:0;color:#fff;font-size:20px">¡Gracias por tu pedido!</h1>
    <p style="margin:6px 0 0;color:#67e8f9;font-size:13px">Hemos recibido tu solicitud y ya la estamos procesando.</p>
  </div>
  <div style="padding:32px">
    <p style="margin-top:0;font-size:15px;color:#333">Hola <strong>' . htmlspecialchars($razon_social) . '</strong>,</p>
    <p style="color:#555;font-size:14px">Te confirmamos que hemos registrado tu pedido. Un asesor de ventas validará las existencias en almacén y se pondrá en contacto contigo a la brevedad para coordinar la entrega y el pago.</p>
    
    <table style="width:100%;border-collapse:collapse;font-size:14px;color:#333;margin-top:20px">
      <tr><td style="padding:8px 0;color:#666;width:130px">Folio</td>
          <td style="font-weight:bold;color:#003e79">' . htmlspecialchars($folio) . '</td></tr>
      <tr><td style="padding:8px 0;color:#666">Envío</td>
          <td>' . htmlspecialchars($estado_envio) . '</td></tr>
      <tr style="background:#f0f5ff">
          <td style="padding:12px;color:#003e79;font-weight:bold;font-size:15px">TOTAL</td>
          <td style="padding:12px;font-size:22px;font-weight:bold;color:#003e79">' . $total_fmt . '</td></tr>
    </table>
    
    <h3 style="color:#003e79;margin-top:24px;margin-bottom:8px">Productos:</h3>
    <ul style="margin:0;padding-left:20px;color:#333;font-size:13px;line-height:1.8">' . $lista_li . '</ul>
    
    <div style="margin-top:28px;text-align:center">
      <a href="' . htmlspecialchars($url_pedido_cliente) . '"
         style="display:inline-block;background:#003e79;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px">
        Ver detalles / subir pago
      </a>
    </div>
  </div>
  <div style="background:#f0f5ff;padding:16px 32px;text-align:center;font-size:11px;color:#888">
    MMPharma &bull; Notificación automática
  </div>
</div></body></html>';

    $asunto_cliente  = "Recibimos tu Pedido $folio — MMPharma";
    
    if (!empty($email_cliente)) {
        enviarCorreoPHPMailer($email_cliente, $asunto_cliente, $html_cliente);
    }

    echo json_encode(['success' => true, 'folio' => $folio]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error en BD: ' . $e->getMessage()]);
}
