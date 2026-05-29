<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: pedidos.php");
    exit;
}

// ── ACCIONES POST PARA ACTUALIZAR ESTATUS ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $nuevo_estado = $_POST['nuevo_estado'] ?? '';
        $valid_states = ['PENDIENTE', 'PROCESANDO', 'ENVIADO', 'ENTREGADO', 'CANCELADO'];
        if (in_array($nuevo_estado, $valid_states)) {
            $stmt = $pdo->prepare("UPDATE clientes_pedidos SET estado_envio = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $id]);
            header("Location: ver_pedido.php?id=$id&msg=status_updated");
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
$stmtDetalle = $pdo->prepare("SELECT * FROM clientes_pedidos_detalle WHERE pedido_id = ?");
$stmtDetalle->execute([$id]);
$detalles = $stmtDetalle->fetchAll();

// Obtener comprobante de pago (si hay)
$stmtComp = $pdo->prepare("SELECT * FROM clientes_pedidos_comprobantes WHERE pedido_id = ? ORDER BY fecha_subida DESC LIMIT 1");
$stmtComp->execute([$id]);
$comprobante = $stmtComp->fetch();

$pageTitle = "MMPharma Portal - Detalle del Pedido " . $pedido['folio'];
$activePage = "pedidos";
include("../Includes/header.php");
include("../Includes/sidebar.php");
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
            <span class="text-on-surface-variant">Detalle del Pedido</span>
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
                Gestión de Envío
            </h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_status">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Estatus Actual</label>
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
                    Actualizar Estatus
                </button>
            </form>
        </div>

        <!-- Tarjeta: Info del Cliente -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-black uppercase tracking-widest text-tertiary flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                    Datos del Cliente
                </h2>
                <a href="../G_Clientes/ver_cliente.php?id=<?= $pedido['cliente_id'] ?>" class="text-[10px] font-bold uppercase tracking-widest text-primary hover:underline">Ver Perfil</a>
            </div>
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Razón Social</span>
                    <p class="text-sm font-medium text-white"><?= htmlspecialchars($pedido['razon_social']) ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Persona de Contacto</span>
                    <p class="text-sm font-medium text-white"><?= htmlspecialchars($pedido['persona_contacto'] ?: 'No asignado') ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Email / Teléfonos</span>
                    <p class="text-sm font-medium text-white">
                        <?= htmlspecialchars($pedido['email']) ?><br>
                        <?= htmlspecialchars($pedido['telefono_local'] ?: '') ?> <?= $pedido['telefono_celular'] ? ' / ' . htmlspecialchars($pedido['telefono_celular']) : '' ?>
                    </p>
                </div>
                <div class="pt-2 border-t border-outline-variant/10">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Notas del Pedido</span>
                    <p class="text-sm font-medium text-white italic text-on-surface-variant"><?= nl2br(htmlspecialchars($pedido['notas'] ?: 'Sin notas.')) ?></p>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Detalle de Pago -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <h2 class="text-sm font-black uppercase tracking-widest text-secondary mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                Información de Pago
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-2 border-b border-outline-variant/10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Método</span>
                    <span class="text-sm font-black text-white"><?= str_replace('_', ' ', $pedido['metodo_pago']) ?></span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-outline-variant/10">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Costo Envío</span>
                    <span class="text-sm font-black text-white">$<?= number_format($pedido['costo_envio'], 2) ?></span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-outline-variant/10">
                    <span class="text-[12px] font-black uppercase tracking-widest text-primary">Monto Total</span>
                    <span class="text-2xl font-black text-primary">$<?= number_format($pedido['monto_total'], 2) ?></span>
                </div>
                
                <?php if ($comprobante): ?>
                <div class="pt-2">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Comprobante Recibido</span>
                    <a href="../../<?= htmlspecialchars($comprobante['ruta_archivo']) ?>" target="_blank" class="w-full bg-surface-container-high border border-outline-variant/30 text-white py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-secondary hover:border-secondary transition-all flex justify-center items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                        Ver Comprobante
                    </a>
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

    <!-- COLUMNA DERECHA: LISTA DE PRODUCTOS -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        
        <!-- Tarjeta: Productos del Pedido -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-xl overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center bg-primary/5">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">inventory_2</span>
                        Productos en el Pedido
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
                                    <span class="block text-[10px] text-on-surface-variant font-medium mt-0.5">ID: <?= $det['producto_id'] ?></span>
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
                            <td colspan="3" class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-on-surface-variant">Subtotal Productos:</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-white">$<?= number_format($suma_subtotales, 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-on-surface-variant border-t border-outline-variant/10">Costo de Envío:</td>
                            <td class="px-6 py-4 text-right text-sm font-black text-white border-t border-outline-variant/10">$<?= number_format($pedido['costo_envio'], 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-[13px] font-black uppercase tracking-widest text-primary border-t border-outline-variant/20">Total:</td>
                            <td class="px-6 py-4 text-right text-lg font-black text-primary border-t border-outline-variant/20">$<?= number_format($pedido['monto_total'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

</main>

<!-- SweetAlert Notificaciones -->
<?php if (isset($_GET['msg'])): ?>
<script>
    Swal.fire({
        title: '¡Estatus Actualizado!',
        text: 'El estado del envío ha sido actualizado exitosamente.',
        icon: 'success',
        confirmButtonColor: '#008151',
        background: '#05160e',
        color: '#f1fdf7'
    });
</script>
<?php endif; ?>

<?php include("../Includes/footer.php"); ?>
