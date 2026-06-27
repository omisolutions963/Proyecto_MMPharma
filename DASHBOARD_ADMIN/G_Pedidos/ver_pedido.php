<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

function enviarCorreoCambioEstatus($email_cliente, $razon_social, $folio, $nuevo_estado, $pedido_id) {
    $url_pedido = getAppURL() . '/dashboard_cliente/cotizacion-detalle.php?id=' . $pedido_id;
    $asunto = "Actualización de tu pedido $folio — MMPharma";

    $color_bg = '#747780';
    switch ($nuevo_estado) {
        case 'PROCESANDO':
            $color_bg = '#1e60aa';
            break;
        case 'ENVIADO':
            $color_bg = '#003e79';
            break;
        case 'ENTREGADO':
            $color_bg = '#2ca1b5';
            break;
        case 'CANCELADO':
            $color_bg = '#ba1a1a';
            break;
    }

    $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f7ff;padding:30px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,62,121,.15)">
  <div style="background:#003e79;padding:24px 32px;text-align:center">
    <h1 style="margin:0;color:#fff;font-size:22px">Actualización de pedido</h1>
    <p style="margin:6px 0 0;color:#67e8f9;font-size:14px">Tu pedido tiene novedades en el portal de clientes</p>
  </div>
  <div style="padding:32px;color:#333;line-height:1.6">
    <p style="font-size:16px;font-weight:bold;color:#003e79;margin-top:0">Estimado(a) ' . htmlspecialchars($razon_social) . ',</p>
    <p>Queremos informarte que tu pedido con folio <strong>' . htmlspecialchars($folio) . '</strong> ha cambiado de estado.</p>
    
    <div style="text-align:center;margin:30px 0">
      <span style="display:inline-block;background:' . $color_bg . ';color:#fff;font-weight:bold;font-size:16px;padding:12px 30px;border-radius:50px;text-transform:uppercase;letter-spacing:1px">
        ' . htmlspecialchars($nuevo_estado) . '
      </span>
    </div>

    <p>Para ver los detalles del pedido, la dirección de entrega asignada o subir tu comprobante de pago, haz clic en el siguiente enlace:</p>
    
    <div style="text-align:center;margin:32px 0 16px">
      <a href="' . htmlspecialchars($url_pedido) . '"
         style="display:inline-block;background:#003e79;color:#fff;padding:14px 36px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px;box-shadow:0 4px 10px rgba(0,62,121,0.2)">
        Ver pedido en el portal
      </a>
    </div>
    
    <p style="font-size:12px;color:#777;margin-top:40px;border-top:1px solid #eee;padding-top:20px">
      Si consideras que este cambio es un error o requieres asistencia, ponte en contacto con tu agente de ventas asignado.
    </p>
  </div>
  <div style="background:#f0f5ff;padding:16px 32px;text-align:center;font-size:11px;color:#888">
    MMPharma &bull; Notificación automática
  </div>
</div></body></html>';

    require_once __DIR__ . '/../../includes/mailer.php';
    enviarCorreoPHPMailer($email_cliente, $asunto, $html);
}

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: pedidos.php");
    exit;
}

// ── ACCIONES POST PARA ACTUALIZAR ESTATUS Y PAGOS ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $nuevo_estado = $_POST['nuevo_estado'] ?? '';
        $valid_states = ['PENDIENTE', 'PROCESANDO', 'ENVIADO', 'ENTREGADO', 'CANCELADO'];
        if (in_array($nuevo_estado, $valid_states)) {
            $stmt = $pdo->prepare("UPDATE clientes_pedidos SET estado_envio = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $id]);

            // Obtener email y razon social del cliente para enviarle la notificación
            $sqlInfo = "SELECT p.folio, c.email, c.razon_social 
                        FROM clientes_pedidos p 
                        JOIN clientes_usuarios c ON p.cliente_id = c.id 
                        WHERE p.id = ?";
            $stmtInfo = $pdo->prepare($sqlInfo);
            $stmtInfo->execute([$id]);
            $info = $stmtInfo->fetch(PDO::FETCH_ASSOC);

            if ($info && !empty($info['email'])) {
                enviarCorreoCambioEstatus($info['email'], $info['razon_social'], $info['folio'], $nuevo_estado, $id);
            }

            header("Location: ver_pedido.php?id=$id&msg=status_updated");
            exit;
        }
    }
    
    if ($action === 'update_payment_status') {
        $nuevo_estatus_val = $_POST['nuevo_estatus_val'] ?? '';
        $notas_admin = trim($_POST['notas_admin'] ?? '');
        $comp_id = (int)($_POST['comprobante_id'] ?? 0);
        
        $valid_estatus = ['PENDIENTE', 'APROBADO', 'RECHAZADO'];
        if ($comp_id && in_array($nuevo_estatus_val, $valid_estatus)) {
            $stmt = $pdo->prepare("UPDATE clientes_pedidos_comprobantes SET estatus_validacion = ?, notas_admin = ? WHERE id = ?");
            $stmt->execute([$nuevo_estatus_val, $notas_admin, $comp_id]);
            
            // Si el comprobante es aprobado y el pedido está PENDIENTE, pasarlo a PROCESANDO
            if ($nuevo_estatus_val === 'APROBADO') {
                $pdo->prepare("UPDATE clientes_pedidos SET estado_envio = 'PROCESANDO' WHERE id = ? AND estado_envio = 'PENDIENTE'")->execute([$id]);
            }
            
            header("Location: ver_pedido.php?id=$id&msg=payment_updated");
            exit;
        }
    }
}

// Obtener datos del pedido y del cliente
$sql = "SELECT p.*, c.razon_social, c.rfc, c.email, c.telefono_local, c.telefono_celular, c.persona_contacto, c.tipo as tipo_cliente_cat
        FROM clientes_pedidos p
        JOIN clientes_usuarios c ON p.cliente_id = c.id
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$pedido = $stmt->fetch();

if (!$pedido) {
    header("Location: pedidos.php");
    exit;
}

// Obtener detalles (productos) del pedido
$stmtDetalle = $pdo->prepare("
    SELECT pd.*, cp.codigo, cp.sustancia, cp.tasa_iva, cp.tipo 
    FROM clientes_pedidos_detalle pd 
    LEFT JOIN catalogo_productos cp ON pd.producto_id = cp.id 
    WHERE pd.pedido_id = ?
");
$stmtDetalle->execute([$id]);
$detalles = $stmtDetalle->fetchAll();

$total_normal_con_iva = 0;
$tiene_red_fria = false;
foreach ($detalles as $det) {
    $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
    $tipo = isset($det['tipo']) ? strtoupper($det['tipo']) : 'SECO';
    $subtotal_linea = (float)$det['subtotal'];
    $item_iva = $subtotal_linea * $tasa;
    
    if ($tipo === 'RED FRIA') {
        $tiene_red_fria = true;
    } else {
        $total_normal_con_iva += ($subtotal_linea + $item_iva);
    }
}

// Obtener comprobante de pago (si hay)
$stmtComp = $pdo->prepare("SELECT * FROM clientes_pedidos_comprobantes WHERE pedido_id = ? ORDER BY fecha_subida DESC LIMIT 1");
$stmtComp->execute([$id]);
$comprobante = $stmtComp->fetch();

$pageTitle = "MMPharma Portal - Detalle del pedido " . $pedido['folio'];
$activePage = "pedidos";
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<!-- Header con Breadcrumb -->
<div class="flex justify-between items-end mb-8 animate-reveal">
    <div>
        <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
            <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <a href="pedidos.php" class="hover:text-primary transition-colors">Pedidos</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface-variant">Detalle del pedido</span>
        </nav>
        <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
            <?= htmlspecialchars($pedido['folio']) ?>
            <?php
            $stColor = match($pedido['estado_envio']){
                'ENTREGADO' => 'bg-tertiary-container/20 text-on-tertiary-container border border-tertiary-container/30',
                'CANCELADO' => 'bg-error-container/20 text-error border border-error/30',
                'ENVIADO' => 'bg-primary/10 text-primary border border-primary/20',
                'PROCESANDO' => 'bg-secondary/10 text-secondary border border-secondary/20',
                default => 'bg-surface-container-high text-on-surface-variant border border-outline-variant/30'
            };
            ?>
            <span class="text-[11px] px-3 py-1 rounded-full uppercase tracking-widest font-black <?= $stColor ?>">
                <?= $pedido['estado_envio'] ?>
            </span>
        </h1>
        <p class="text-on-surface-variant text-sm mt-1">Fecha de creación: <span class="font-bold text-white"><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></span></p>
    </div>
    <div class="flex gap-3">
        <a href="generar_pdf.php?id=<?= $pedido['id'] ?>" target="_blank" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-secondary/30 text-secondary hover:bg-secondary hover:text-white transition-all text-xs font-bold uppercase tracking-widest">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
            Descargar PDF
        </a>
        <a href="pedidos.php" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-outline-variant/30 text-on-surface-variant hover:bg-surface-container-high hover:text-white transition-all text-xs font-bold uppercase tracking-widest">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Volver
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-reveal" style="animation-delay: 0.2s;">
    <!-- COLUMNA IZQUIERDA: RESUMEN Y ACCIONES -->
    <div class="lg:col-span-1 flex flex-col gap-6">
        
        <!-- Tarjeta: Gestión de Estatus -->
        <div class="bg-surface-container-lowest rounded-3xl border border-primary/20 p-6 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
            <h2 class="text-sm font-black uppercase tracking-widest text-primary mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                Gestión de envío
            </h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_status">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Estatus actual</label>
                    <select name="nuevo_estado" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-white font-bold">
                        <option value="PENDIENTE" <?= $pedido['estado_envio'] === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="PROCESANDO" <?= $pedido['estado_envio'] === 'PROCESANDO' ? 'selected' : '' ?>>Procesando</option>
                        <option value="ENVIADO" <?= $pedido['estado_envio'] === 'ENVIADO' ? 'selected' : '' ?>>Enviado</option>
                        <option value="ENTREGADO" <?= $pedido['estado_envio'] === 'ENTREGADO' ? 'selected' : '' ?>>Entregado</option>
                        <option value="CANCELADO" <?= $pedido['estado_envio'] === 'CANCELADO' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold uppercase tracking-widest text-[11px] hover:opacity-90 transition-all flex justify-center items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Actualizar estatus
                </button>
            </form>
        </div>

        <!-- Tarjeta: Info del Cliente -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-black uppercase tracking-widest text-tertiary flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                    Datos del cliente
                </h2>
                <a href="../g_clientes/ver_cliente.php?id=<?= $pedido['cliente_id'] ?>" class="text-[10px] font-bold uppercase tracking-widest text-primary hover:underline">Ver perfil</a>
            </div>
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Razón social</span>
                    <p class="text-sm font-medium text-white"><?= htmlspecialchars($pedido['razon_social']) ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Persona de contacto</span>
                    <p class="text-sm font-medium text-white"><?= htmlspecialchars($pedido['persona_contacto'] ?: 'No asignado') ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Email / teléfonos</span>
                    <p class="text-sm font-medium text-white">
                        <?= htmlspecialchars($pedido['email']) ?><br>
                        <?= htmlspecialchars($pedido['telefono_local'] ?: '') ?> <?= $pedido['telefono_celular'] ? ' / ' . htmlspecialchars($pedido['telefono_celular']) : '' ?>
                    </p>
                </div>
                <div class="pt-2 border-t border-outline-variant/10">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Notas del pedido</span>
                    <p class="text-sm font-medium text-white italic text-on-surface-variant"><?= nl2br(htmlspecialchars($pedido['notas'] ?: 'Sin notas.')) ?></p>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Detalle de Pago -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <h2 class="text-sm font-black uppercase tracking-widest text-secondary mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                Información de pago
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-2 border-b border-outline-variant/10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Método</span>
                    <span class="text-sm font-black text-white"><?= str_replace('_', ' ', $pedido['metodo_pago']) ?></span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-outline-variant/10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Costo envío</span>
                    <span class="text-sm font-black text-white">$<?= number_format($pedido['costo_envio'], 2) ?></span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-outline-variant/10">
                    <span class="text-[12px] font-black uppercase tracking-widest text-primary">Monto total</span>
                    <span class="text-2xl font-black text-primary">$<?= number_format($pedido['monto_total'], 2) ?></span>
                </div>
                
                <?php if ($comprobante): ?>
                <div class="pt-2 space-y-4">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Comprobante recibido</span>
                        <a href="../../uploads/comprobantes/<?= htmlspecialchars($comprobante['ruta_archivo']) ?>" target="_blank" class="w-full bg-surface-container-high border border-outline-variant/30 text-white py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-secondary hover:border-secondary transition-all flex justify-center items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                            Ver comprobante
                        </a>
                        <?php
                        $badgePayment = match($comprobante['estatus_validacion']){
                            'APROBADO' => 'bg-tertiary-container/20 text-on-tertiary-container border border-tertiary-container/30',
                            'RECHAZADO' => 'bg-error-container/20 text-error border border-error/30',
                            default => 'bg-surface-container-high text-on-surface-variant border border-outline-variant/30'
                        };
                        ?>
                        <div class="flex justify-between items-center mt-3 mb-2">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Estatus validación:</span>
                            <span class="text-[9px] px-2.5 py-0.5 rounded-full uppercase tracking-wider font-bold <?= $badgePayment ?>">
                                <?= $comprobante['estatus_validacion'] ?>
                            </span>
                        </div>
                    </div>
                    
                    <form method="POST" class="pt-3 border-t border-outline-variant/10 space-y-3">
                        <input type="hidden" name="action" value="update_payment_status">
                        <input type="hidden" name="comprobante_id" value="<?= $comprobante['id'] ?>">
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Validar pago</label>
                            <select name="nuevo_estatus_val" class="w-full bg-surface-container-low border-none rounded-xl p-2.5 text-xs focus:ring-1 focus:ring-primary outline-none text-white font-semibold">
                                <option value="PENDIENTE" <?= $comprobante['estatus_validacion'] === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente de validar</option>
                                <option value="APROBADO" <?= $comprobante['estatus_validacion'] === 'APROBADO' ? 'selected' : '' ?>>Aprobado / pago verificado</option>
                                <option value="RECHAZADO" <?= $comprobante['estatus_validacion'] === 'RECHAZADO' ? 'selected' : '' ?>>Rechazado (sube otro)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Notas / motivo de rechazo</label>
                            <textarea name="notas_admin" rows="2" class="w-full bg-surface-container-low border-none rounded-xl p-2.5 text-xs text-white placeholder:text-outline/70 focus:ring-1 focus:ring-primary outline-none resize-none" placeholder="Escribe observaciones o motivo si rechazas el comprobante..."><?= htmlspecialchars($comprobante['notas_admin'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="w-full bg-secondary text-white py-2.5 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:opacity-90 transition-all flex justify-center items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">done</span>
                            Guardar validación
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="pt-2">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Comprobante</span>
                    <p class="text-xs text-on-surface-variant italic">El cliente aún no ha subido comprobante.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </div>

    <!-- COLUMNA DERECHA: LISTA DE productos -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        
        <!-- Tarjeta: Productos del Pedido -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-xl overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center bg-primary/5">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">inventory_2</span>
                        Productos en el pedido
                    </h2>
                    <p class="text-[11px] text-on-surface-variant mt-1">Lista detallada de artículos seleccionados por el cliente.</p>
                </div>
            </div>
            
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
                            <th class="px-6 py-4">Producto</th>
                            <th class="px-6 py-4 text-center">Cantidad</th>
                            <th class="px-6 py-4 text-right">P. Unitario</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        <?php if (empty($detalles)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-on-surface-variant text-sm font-medium italic">
                                    No hay productos en este pedido.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $suma_subtotales = 0;
                            foreach ($detalles as $det): 
                                $suma_subtotales += $det['subtotal'];
                            ?>
                            <tr class="group hover:bg-surface-container-low/30 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-on-surface"><?= htmlspecialchars($det['nombre_producto']) ?></span>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-[10px] text-on-surface-variant font-medium max-w-xs truncate" title="<?= htmlspecialchars($det['sustancia'] ?? '') ?>">
                                            <?= htmlspecialchars($det['sustancia'] ?? '') ?> 
                                        </p>
                                        <?php
                                            $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
                                            $subtotal_linea = (float)$det['subtotal'];
                                            $item_sin_iva = $subtotal_linea;
                                            $item_iva = $subtotal_linea * $tasa;
                                        ?>
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary/20 text-primary whitespace-nowrap">
                                            IVA: <?= $tasa * 100 ?>%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-lg bg-surface-container-high text-white text-xs font-black">
                                        <?= $det['cantidad'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-medium text-on-surface-variant">$<?= number_format($det['precio_unitario'], 2) ?></span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-primary">$<?= number_format($det['subtotal'], 2) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-surface-container-low/20">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-on-surface-variant">Subtotal productos:</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-white">$<?= number_format($suma_subtotales, 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-on-surface-variant border-t border-outline-variant/10">Costo de envío:</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-white border-t border-outline-variant/10">
                                <?php if ($pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || ($tiene_red_fria && $total_normal_con_iva == 0)): ?>
                                    <div class="text-right inline-block">
                                        <span class="text-sm font-bold text-green-400 block">Recoger en almacén</span>
                                        <span class="text-[10px] text-green-400/80 block leading-tight mt-0.5 max-w-[200px] ml-auto">Su pedido estará listo para que pase a recolectarlo</span>
                                    </div>
                                <?php else: ?>
                                    $<?= number_format($pedido['costo_envio'], 2) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-[13px] font-black uppercase tracking-widest text-primary border-t border-outline-variant/20">Total:</td>
                            <td class="px-6 py-4 text-right text-lg font-black text-primary border-t border-outline-variant/20">$<?= number_format($pedido['monto_total'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if ($tiene_red_fria): ?>
        <div class="mt-4 bg-red-900/30 border border-red-500/50 rounded-xl p-4 text-sm text-white">
            <div class="flex items-center gap-2 text-red-400 font-bold mb-1">
                <span class="material-symbols-outlined text-[18px]">severe_cold</span>
                Productos de Red Fría
            </div>
            <p class="text-xs text-red-200/80 mb-1">Este pedido incluye productos de Red Fría. El cliente debe organizar su propio transporte.</p>
        </div>
        <?php endif; ?>

    </div>
</div>

</main>

<!-- SweetAlert Notificaciones -->
<?php if (isset($_GET['msg'])): ?>
<script>
    const msg = '<?= htmlspecialchars($_GET['msg']) ?>';
    if (msg === 'status_updated') {
        Swal.fire({
            title: '¡Estatus actualizado!',
            text: 'El estado del envío ha sido actualizado exitosamente.',
            icon: 'success',
            confirmButtonColor: '#008151',
            background: '#05160e',
            color: '#f1fdf7'
        });
    } else if (msg === 'payment_updated') {
        Swal.fire({
            title: '¡Pago validado!',
            text: 'La validación del comprobante de pago se guardó exitosamente.',
            icon: 'success',
            confirmButtonColor: '#008151',
            background: '#05160e',
            color: '#f1fdf7'
        });
    }
</script>
<?php endif; ?>

<?php include("../includes/footer.php"); ?>
