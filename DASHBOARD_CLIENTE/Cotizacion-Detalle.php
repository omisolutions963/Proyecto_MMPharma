<?php
if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
 header("Location: ../login/login.php");
 exit;
}

require_once '../includes/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];
$pedido_id = $_GET['id'] ?? null;

if (!$pedido_id) {
 header("Location: cotizaciones.php");
 exit;
}

// Fetch Pedido
$stmt = $pdo->prepare("SELECT * FROM clientes_pedidos WHERE id = ? AND cliente_id = ?");
$stmt->execute([$pedido_id, $cliente_id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
 header("Location: cotizaciones.php");
 exit;
}

// Fetch Detalles
$stmt = $pdo->prepare("
 SELECT pd.*, cp.codigo, cp.sustancia, cp.tipo, cp.tasa_iva 
 FROM clientes_pedidos_detalle pd 
 LEFT JOIN catalogo_productos cp ON pd.producto_id = cp.id 
 WHERE pd.pedido_id = ?
");
$stmt->execute([$pedido_id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Comprobante
$stmt = $pdo->prepare("SELECT * FROM clientes_pedidos_comprobantes WHERE pedido_id = ? ORDER BY fecha_subida DESC LIMIT 1");
$stmt->execute([$pedido_id]);
$comprobante = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Client Info
$stmt = $pdo->prepare("SELECT * FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

$subtotal_productos = 0;
$total_items_sin_iva = 0;
$total_items_iva = 0;
foreach($detalles as $det) {
    $subtotal_linea = (float)$det['subtotal'];
    $subtotal_productos += $subtotal_linea;
    $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
    $item_sin_iva = $subtotal_linea;
    $item_iva = $subtotal_linea * $tasa;
    $total_items_sin_iva += $item_sin_iva;
    $total_items_iva += $item_iva;
}

$costo_envio = isset($pedido['costo_envio']) ? (float)$pedido['costo_envio'] : 0.00;
$monto_total = $subtotal_productos + $total_items_iva + $costo_envio;

$envio_sin_iva = $costo_envio; // Assuming shipping doesn't add more IVA here, or it's handled separately
$envio_iva = 0; // If shipping needs IVA, adjust this

$subtotal_sin_iva = $total_items_sin_iva + $envio_sin_iva;
$iva = $total_items_iva + $envio_iva;

$fecha_pedido = date('d M, Y', strtotime($pedido['created_at']));
$vence_pedido = date('d M, Y', strtotime($pedido['created_at'] . ' + 7 days'));

$pageTitle = 'MMPharma Portal - Detalle de cotización';
$activePage = 'cotizaciones';
include('includes/header.php');
include('includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)] bg-background text-on-surface">
 
 <!-- Top Nav -->
 <nav class="flex items-center gap-2 mb-6 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
 <a href="dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
 <span class="material-symbols-outlined text-[12px]">chevron_right</span>
 <a href="cotizaciones.php" class="hover:text-primary transition-colors">Cotizaciones</a>
 <span class="material-symbols-outlined text-[12px]">chevron_right</span>
 <span class="text-on-surface-variant">Detalle #<?= htmlspecialchars($pedido['folio']) ?></span>
 </nav>

 <!-- Header Actions -->
 <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-reveal">
 <h1 class="text-3xl font-extrabold text-white tracking-tight">Folio #<?= htmlspecialchars($pedido['folio']) ?></h1>
 <div class="flex items-center gap-3 mt-4 md:mt-0">
 <a href="descargar_cotizacion.php?id=<?= $pedido_id ?>&action=view" target="_blank" class="bg-surface-container-high/40 text-secondary px-5 py-2.5 rounded-xl flex items-center gap-2 font-bold border border-secondary/20 hover:bg-secondary/10 transition-all">
 <span class="material-symbols-outlined text-[18px]">visibility</span> Ver
 </a>
 <a href="descargar_cotizacion.php?id=<?= $pedido_id ?>&action=download" class="bg-surface-container-high/40 text-secondary px-5 py-2.5 rounded-xl flex items-center gap-2 font-bold border border-secondary/20 hover:bg-secondary/10 transition-all">
 <span class="material-symbols-outlined text-[18px]">download</span> PDF
 </a>
  <?php if ((!$comprobante || $comprobante['estatus_validacion'] === 'RECHAZADO') && $pedido['estado_envio'] !== 'CANCELADO'): ?>
  <input type="file" id="fileComprobante" class="hidden" accept=".pdf,.jpg,.jpeg,.png" onchange="procesarArchivo()">
  <button class="bg-primary text-white px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold hover:opacity-90 transition-all" onclick="document.getElementById('fileComprobante').click()">
  <span class="material-symbols-outlined text-[18px]">upload_file</span> Subir comprobante
  </button>
  <?php elseif ($comprobante): ?>
  <span class="px-4 py-2 bg-tertiary/10 text-tertiary text-sm font-bold rounded-xl flex items-center gap-2 border border-tertiary/20">
  <span class="material-symbols-outlined text-[18px]">receipt</span> Comprobante enviado (<?= htmlspecialchars($comprobante['estatus_validacion']) ?>)
  </span>
  <?php endif; ?>
  </div>
  </div>

  <?php if ($comprobante && $comprobante['estatus_validacion'] === 'RECHAZADO'): ?>
  <div class="mb-6 bg-error/15 border border-error/30 text-error px-6 py-4 rounded-2xl flex items-start gap-3 animate-reveal">
      <span class="material-symbols-outlined text-[22px] shrink-0 mt-0.5">error</span>
      <div>
          <h4 class="font-bold text-white text-sm">Comprobante de pago rechazado</h4>
          <p class="text-xs text-on-surface-variant mt-1">Tu comprobante anterior fue rechazado por el administrador.</p>
          <?php if (!empty($comprobante['notas_admin'])): ?>
          <p class="text-xs text-white font-semibold mt-2">Motivo: <span class="italic font-normal">"<?= htmlspecialchars($comprobante['notas_admin']) ?>"</span></p>
          <?php endif; ?>
          <p class="text-xs text-on-surface-variant mt-2 font-medium">Por favor, sube un nuevo comprobante válido utilizando el botón de arriba.</p>
      </div>
  </div>
  <?php endif; ?>

  <!-- Stepper de Seguimiento del Pedido -->
  <div class="mb-8 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 lg:p-8 animate-reveal">
      <h3 class="text-xs font-black text-on-surface-variant uppercase tracking-widest mb-6 flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px]">local_shipping</span>
          Seguimiento de tu pedido
      </h3>
      
      <!-- Stepper Container -->
      <div class="relative flex flex-col md:flex-row justify-between items-center gap-8 md:gap-4">
          <?php
          $estado = $pedido['estado_envio'];
          $pago_val = ($comprobante && $comprobante['estatus_validacion'] === 'APROBADO') || in_array($estado, ['PROCESANDO', 'ENVIADO', 'ENTREGADO']);
          $en_proceso = in_array($estado, ['PROCESANDO', 'ENVIADO', 'ENTREGADO']);
          $enviado = in_array($estado, ['ENVIADO', 'ENTREGADO']);
          $entregado = ($estado === 'ENTREGADO');
          
          $recoger_sucursal = ($estado === 'RECOGER EN SUCURSAL' || $estado === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || !empty($pedido['recoger_sucursal']) || $subtotal_productos < 4000.00);
          $label_paso4 = ($estado === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || $subtotal_productos < 4000.00) ? 'Listo para recolectar' : ($recoger_sucursal ? 'Listo para recoger' : 'Enviado');
          $icon_paso4 = $recoger_sucursal ? 'store' : 'local_shipping';
          
          if ($recoger_sucursal) {
              $enviado = ($estado === 'RECOGER EN SUCURSAL' || $estado === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || $estado === 'ENTREGADO');
          }
          
          $cancelado = ($estado === 'CANCELADO');
          
          $progress_width = 0;
          $line_color = $cancelado ? 'bg-error' : 'bg-tertiary';
          
          if (!$cancelado) {
              if ($entregado) $progress_width = 100;
              elseif ($enviado) $progress_width = 75;
              elseif ($en_proceso) $progress_width = 50;
              elseif ($pago_val) $progress_width = 25;
          } else {
              $progress_width = 100;
          }
          
          $steps = [
              ['label' => 'Pedido recibido', 'done' => !$cancelado, 'active' => !$cancelado && !$pago_val, 'icon' => 'receipt_long'],
              ['label' => 'Pago validado', 'done' => !$cancelado && $pago_val, 'active' => !$cancelado && $pago_val && !$en_proceso, 'icon' => 'payments'],
              ['label' => 'En proceso', 'done' => !$cancelado && $en_proceso, 'active' => !$cancelado && $en_proceso && !$enviado, 'icon' => 'package_2'],
              ['label' => $label_paso4, 'done' => !$cancelado && $enviado, 'active' => !$cancelado && $enviado && !$entregado, 'icon' => $icon_paso4],
              ['label' => 'Entregado', 'done' => !$cancelado && $entregado, 'active' => !$cancelado && $entregado, 'icon' => 'assignment_turned_in']
          ];
          
          if ($cancelado) {
              $steps = [
                  ['label' => 'Pedido recibido', 'done' => true, 'active' => false, 'icon' => 'receipt_long', 'is_cancel' => false],
                  ['label' => 'Pedido cancelado', 'done' => false, 'active' => true, 'icon' => 'cancel', 'is_cancel' => true]
              ];
          }
          ?>
          
          <!-- Horizontal Line background for Desktop -->
          <div class="absolute left-[10%] right-[10%] top-[28px] h-1 bg-outline-variant/30 hidden md:block z-0">
              <div class="h-full <?= $line_color ?> transition-all duration-1000" style="width: <?= $progress_width ?>%;"></div>
          </div>
          
          <?php foreach ($steps as $idx => $step): ?>
              <?php
              $circle_bg = 'bg-surface-container';
              $circle_border = 'border-outline-variant';
              $icon_color = 'text-on-surface-variant';
              $text_color = 'text-on-surface-variant';
              
              if (isset($step['is_cancel']) && $step['is_cancel']) {
                  $circle_bg = 'bg-error/20';
                  $circle_border = 'border-error';
                  $icon_color = 'text-error';
                  $text_color = 'text-error font-extrabold';
              } elseif ($step['done']) {
                  $circle_bg = 'bg-tertiary';
                  $circle_border = 'border-tertiary';
                  $icon_color = 'text-white';
                  $text_color = 'text-white font-bold';
              } elseif ($step['active']) {
                  $circle_bg = 'bg-primary';
                  $circle_border = 'border-primary';
                  $icon_color = 'text-white';
                  $text_color = 'text-primary font-black';
              }
              ?>
              
              <div class="relative z-10 flex flex-col items-center flex-1 w-full text-center">
                  <!-- Stepper Circle -->
                  <div class="w-14 h-14 rounded-full <?= $circle_bg ?> border-4 <?= $circle_border ?> flex items-center justify-center shadow-lg transition-all duration-300 transform hover:scale-110 <?= $step['active'] ? 'animate-pulse' : '' ?>" title="<?= htmlspecialchars($step['label']) ?>">
                      <span class="material-symbols-outlined text-2xl <?= $icon_color ?>"><?= $step['icon'] ?></span>
                  </div>
                  <!-- Stepper Text Label -->
                  <span class="mt-3 text-xs md:text-sm <?= $text_color ?> tracking-tight leading-tight px-2"><?= htmlspecialchars($step['label']) ?></span>
              </div>
          <?php endforeach; ?>
      </div>
  </div>

  <!-- Main Grid -->
  <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 animate-reveal delay-100">
 
 <!-- Document Area (Left 3 cols) -->
 <div class="xl:col-span-3 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 lg:p-12 ">
 
 <!-- Doc Header -->
 <div class="flex flex-col md:flex-row justify-between items-start mb-12 border-b border-outline-variant/30 pb-8">
 <div class="flex items-center gap-4 mb-6 md:mb-0">
 <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center text-white font-black text-2xl tracking-tighter">
 MM
 </div>
 <div>
 <h2 class="text-xl font-extrabold text-white">MMPharma S.A. de C.V.</h2>
 <p class="text-[10px] text-on-surface-variant max-w-xs leading-relaxed mt-1">Av. Insurgentes Sur 1200, Col. Extremadura Insurgentes, CP 03740, CDMX, México.</p>
 </div>
 </div>
 <div class="text-left md:text-right">
 <div class="inline-block bg-surface-container-high text-white text-sm font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-3">Cotización</div>
 <p class="text-xs text-on-surface-variant mb-1">Folio: <span class="text-white font-semibold"><?= htmlspecialchars($pedido['folio']) ?></span></p>
 <p class="text-xs text-on-surface-variant mb-1">Fecha: <span class="text-white font-semibold"><?= $fecha_pedido ?></span></p>
 <p class="text-xs text-on-surface-variant">Vence: <span class="text-white font-semibold"><?= $vence_pedido ?></span></p>
 </div>
 </div>

 <!-- Doc Info (Client & Delivery) -->
 <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
 <div class="bg-surface-container-low/50 rounded-xl p-6 border border-outline-variant/20">
 <h3 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Datos del cliente</h3>
 <p class="text-base font-bold text-white mb-1"><?= htmlspecialchars($cliente['razon_social'] ?? 'N/A') ?></p>
 <p class="text-xs text-on-surface-variant mb-3">RFC: <span class="text-white"><?= htmlspecialchars($cliente['rfc'] ?? 'Pendiente') ?></span></p>
 <p class="text-xs text-on-surface-variant leading-relaxed mb-4"><?= htmlspecialchars($cliente['domicilio_fiscal'] ?? 'Sin domicilio fiscal registrado.') ?></p>
 <p class="text-xs text-primary font-bold flex items-center gap-2">
 <span class="material-symbols-outlined text-[16px]">mail</span> <?= htmlspecialchars($cliente['email'] ?? '') ?>
 </p>
 </div>
 <div class="bg-surface-container-low/50 rounded-xl p-6 border border-outline-variant/20">
 <h3 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Lugar de entrega</h3>
 <p class="text-base font-bold text-white mb-2"><?= htmlspecialchars($cliente['receptor_entrega'] ?? $cliente['razon_social']) ?></p>
 <p class="text-xs text-on-surface-variant leading-relaxed mb-5"><?= htmlspecialchars($cliente['domicilio_entrega'] ?? 'Sin domicilio de entrega.') ?></p>
 <div class="flex gap-2">
 <span class="px-2 py-1 bg-surface-container border border-outline-variant/30 text-white text-[9px] font-black rounded-md uppercase tracking-wider flex items-center gap-1">
 <?= htmlspecialchars($pedido['tipo_cliente']) ?>
 </span>
 </div>
 </div>
 </div>

 <!-- Items Table -->
 <div class="mb-12 overflow-x-auto">
 <table class="w-full text-left whitespace-nowrap">
 <thead class="bg-surface-container border-b border-outline-variant/30 text-[10px] font-black text-white uppercase tracking-widest">
 <tr>
 <th class="py-3 px-4 rounded-tl-xl">Código</th>
 <th class="py-3 px-4">Producto</th>
 <th class="py-3 px-4 text-center">Cant.</th>
 <th class="py-3 px-4 text-right">Unitario</th>
 <th class="py-3 px-4 text-right rounded-tr-xl">Importe</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-outline-variant/10">
 <?php foreach($detalles as $det): ?>
 <tr>
 <td class="py-5 px-4 text-xs font-bold text-white"><?= htmlspecialchars($det['codigo'] ?? 'N/A') ?></td>
 <td class="py-5 px-4">
 <p class="text-sm font-bold text-white mb-0.5"><?= htmlspecialchars($det['nombre_producto']) ?></p>
 <div class="flex items-center gap-2">
 <p class="text-[10px] text-on-surface-variant max-w-xs truncate" title="<?= htmlspecialchars($det['sustancia'] ?? '') ?>">
 <?= htmlspecialchars($det['sustancia'] ?? '') ?>
 </p>
 <?php
      $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
      $subtotal_linea = (float)$det['subtotal'];
      $item_sin_iva = $subtotal_linea;
      $item_iva = $subtotal_linea * $tasa;
 ?>
 <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary/20 text-primary whitespace-nowrap">
 IVA: <?= $tasa * 100 ?>%
 </span>
 </div>
 </td>
 <td class="py-5 px-4 text-sm font-bold text-white text-center"><?= $det['cantidad'] ?></td>
 <td class="py-5 px-4 text-sm text-on-surface-variant text-right">$<?= number_format($det['precio_unitario'], 2) ?></td>
 <td class="py-5 px-4 text-sm font-bold text-white text-right">$<?= number_format($det['subtotal'], 2) ?></td>
 </tr>
 <?php endforeach; ?>
 </tbody>
 </table>
 </div>

 <!-- Totals -->
 <div class="flex flex-col items-end border-t border-outline-variant/30 pt-6">
 <div class="w-full max-w-sm">
 <div class="flex justify-between items-center mb-3">
 <span class="text-sm text-on-surface-variant">Subtotal productos:</span>
 <span class="text-sm font-bold text-white">$<?= number_format($subtotal_productos, 2) ?></span>
 </div>
  <div class="flex justify-between items-start mb-3 gap-4">
  <span class="text-sm text-on-surface-variant shrink-0">Envío:</span>
  <?php
  if ($subtotal_productos < 4000.00 || $pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || $pedido['estado_envio'] === 'RECOGER EN SUCURSAL' || !empty($pedido['recoger_sucursal'])) {
      echo '<div class="text-right">
              <span class="text-sm font-bold text-green-400 block">Recoger en almacén</span>
              <span class="text-[10px] text-green-400/80 block leading-tight mt-0.5 max-w-[200px] ml-auto">Su pedido estará listo para que pase a recolectarlo</span>
            </div>';
  } else {
      $texto_envio = $costo_envio > 0 ? '$' . number_format($costo_envio, 2) : 'Envío gratis';
      $color_envio = $costo_envio > 0 ? 'text-white' : 'text-green-400 font-bold';
      echo '<span class="text-sm ' . $color_envio . '">' . $texto_envio . '</span>';
  }
  ?>
  </div>
 <div class="flex justify-between items-center mb-3 pt-3 border-t border-outline-variant/10">
 <span class="text-sm text-on-surface-variant">Subtotal (sin IVA):</span>
 <span class="text-sm font-bold text-white">$<?= number_format($subtotal_sin_iva, 2) ?></span>
 </div>
 <div class="flex justify-between items-center mb-6">
 <span class="text-sm text-on-surface-variant">IVA:</span>
 <span class="text-sm font-bold text-white">$<?= number_format($iva, 2) ?></span>
 </div>
 <div class="flex justify-between items-center bg-surface-container-high rounded-xl p-4">
 <span class="text-lg font-black text-white uppercase tracking-widest">Total:</span>
 <span class="text-2xl font-extrabold text-primary">$<?= number_format($monto_total, 2) ?></span>
 </div>
 <p class="text-[9px] text-on-surface-variant/50 text-right mt-4 italic">* Los precios incluyen el IVA aplicable (16%). Moneda: MXN.</p>
 </div>
 </div>
 
 <?php if ($costo_envio > 0 || $pedido['estado_envio'] === 'RECOGER EN SUCURSAL' || $pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || !empty($pedido['recoger_sucursal']) || $subtotal_productos < 4000.00): ?>
 <div class="mt-8 bg-surface-container-high border border-outline-variant/30 rounded-xl p-4 text-sm text-white">
     <div class="flex items-center gap-2 text-primary font-bold mb-1">
         <span class="material-symbols-outlined text-[18px]">store</span>
         Información de entrega en sucursal
     </div>
     <p class="text-xs text-on-surface-variant mb-1">Horario de entrega: <strong class="text-white">De 9am a 6pm todos los días de la semana.</strong></p>
     <p class="text-[10px] text-on-surface-variant/80">El lugar donde pasará a recoger será proporcionado por un asesor de nosotros (para mantener la confidencialidad del lugar).</p>
 </div>
 <?php endif; ?>

 <div class="flex justify-between items-center mt-12 pt-4 border-t border-outline-variant/10 text-[9px] text-on-surface-variant">
 <span>Generado vía MMPharma Digital Portal</span>
 </div>

 </div>

 <!-- Sidebar Estatus (Right Col) -->
 <div class="space-y-6">
 
 <!-- Status Timeline -->
 <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 ">
 <h3 class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-6 flex items-center gap-2">
 <span class="material-symbols-outlined text-[16px]">history</span> Estatus de pedido
 </h3>
 
 <div class="relative pl-3">
 <!-- Line -->
 <div class="absolute left-[19px] top-4 bottom-8 w-0.5 bg-outline-variant/30"></div>

 <!-- Step 1: Generado -->
 <div class="relative flex items-start gap-4 mb-6">
 <div class="w-4 h-4 rounded-full bg-tertiary border-2 border-surface-container-lowest relative z-10 flex items-center justify-center text-white mt-0.5">
 <span class="material-symbols-outlined text-[10px] font-bold">check</span>
 </div>
 <div>
 <p class="text-sm font-bold text-white mb-0.5">Cotización generada</p>
 <p class="text-[10px] text-on-surface-variant"><?= $fecha_pedido ?></p>
 </div>
 </div>

 <!-- Current Step -->
 <div class="relative flex items-start gap-4 mb-6">
 <div class="w-6 h-6 -ml-1 rounded-full <?= $pedido['estado_envio'] === 'CANCELADO' ? 'bg-error/20 border-error text-error' : 'bg-primary/20 border-primary text-primary' ?> border-2 relative z-10 flex items-center justify-center animate-pulse">
 <span class="material-symbols-outlined text-[12px]"><?= $pedido['estado_envio'] === 'CANCELADO' ? 'close' : 'sync' ?></span>
 </div>
 <div>
 <p class="text-sm font-extrabold <?= $pedido['estado_envio'] === 'CANCELADO' ? 'text-error' : 'text-primary' ?> mb-0.5"><?= htmlspecialchars($pedido['estado_envio']) ?></p>
 <p class="text-xs text-white"><?= $pedido['estado_envio'] === 'PENDIENTE' ? 'Esperando validación de la orden' : 'Actualizado' ?></p>
 </div>
 </div>
 </div>
 </div>

 <!-- Support Box -->
 <div class="bg-primary-container border border-primary/20 rounded-2xl p-6 relative overflow-hidden ">
 <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-primary/30 rounded-full blur-xl"></div>
 <h3 class="text-sm font-bold text-white mb-2">Soporte MMPharma</h3>
 <p class="text-[10px] text-on-primary-container leading-relaxed mb-6">¿Tiene dudas sobre esta cotización? Envíe un mensaje.</p>
 
 <a href="contacto.php" class="flex items-center justify-center gap-3 bg-surface-container-lowest/40 hover:bg-surface-container-lowest transition-colors rounded-xl p-3 border border-outline-variant/30 backdrop-blur-sm relative z-10 font-bold text-xs text-white">
 <span class="material-symbols-outlined text-[18px]">support_agent</span> Contactar soporte
 </a>
 </div>

 </div>

 </div>

</main>
<?php include('includes/footer.php'); ?>

<script>
function procesarArchivo() {
 const fileInput = document.getElementById('fileComprobante');
 if (fileInput.files.length === 0) return;

 const file = fileInput.files[0];
 const formData = new FormData();
 formData.append('comprobante', file);
 formData.append('pedido_id', '<?= $pedido_id ?>');

 Swal.fire({
 title: 'Subiendo comprobante...',
 text: 'Por favor espera un momento.',
 allowOutsideClick: false,
 didOpen: () => { Swal.showLoading(); },
 background: '#071628',
 color: '#fff'
 });

 fetch('api_subir_comprobante.php', {
 method: 'POST',
 body: formData
 })
 .then(r => r.json())
 .then(data => {
 if (data.status === 'success') {
 Swal.fire({
 icon: 'success',
 title: '¡Éxito!',
 text: 'El comprobante ha sido enviado para validación.',
 background: '#071628',
 color: '#fff'
 }).then(() => {
 location.reload();
 });
 } else {
 Swal.fire({
 icon: 'error',
 title: 'Error',
 text: data.message || 'No se pudo subir el archivo.',
 background: '#071628',
 color: '#fff'
 });
 }
 })
 .catch(err => {
 console.error(err);
 Swal.fire({ icon: 'error', title: 'Error de red', background: '#071628', color: '#fff' });
 });
}
</script>
