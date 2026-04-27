<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    header("Location: ../LOGIN/login.php");
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

// 1. Obtener datos del cliente
$stmt = $pdo->prepare("SELECT estatus FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);
$estatus_cliente = $cliente['estatus'] ?? 'ACTIVO';

// 2. Cotizaciones este mes
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes_pedidos WHERE cliente_id = ? AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stmt->execute([$cliente_id]);
$cotizaciones_mes = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 3. Última Cotización
$stmt = $pdo->prepare("SELECT monto_total, created_at FROM clientes_pedidos WHERE cliente_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$cliente_id]);
$ultima_cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);
$ultima_monto = $ultima_cotizacion ? '$' . number_format($ultima_cotizacion['monto_total'], 2) : '$0.00';
$ultima_fecha = $ultima_cotizacion ? date('d M, Y', strtotime($ultima_cotizacion['created_at'])) : 'Ninguna';

// 4. Productos Favoritos (Productos distintos comprados)
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT pd.producto_id) as total FROM clientes_pedidos_detalle pd JOIN clientes_pedidos p ON pd.pedido_id = p.id WHERE p.cliente_id = ?");
$stmt->execute([$cliente_id]);
$productos_favoritos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 5. Estadísticas de Actividad (Nueva sección)
$stmt = $pdo->prepare("SELECT COUNT(*) as total_pedidos, SUM(monto_total) as total_gastado, AVG(monto_total) as ticket_promedio FROM clientes_pedidos WHERE cliente_id = ? AND estado_envio NOT IN ('CANCELADO')");
$stmt->execute([$cliente_id]);
$stats_actividad = $stmt->fetch(PDO::FETCH_ASSOC);
$total_gastado = $stats_actividad['total_gastado'] ?? 0;
$ticket_promedio = $stats_actividad['ticket_promedio'] ?? 0;

$stmt = $pdo->prepare("SELECT estado_envio, COUNT(*) as count FROM clientes_pedidos WHERE cliente_id = ? GROUP BY estado_envio");
$stmt->execute([$cliente_id]);
$estados_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$pedidos_pendientes = $estados_raw['PENDIENTE'] ?? 0;
$pedidos_cancelados = $estados_raw['CANCELADO'] ?? 0;
$pedidos_exitosos = array_sum($estados_raw) - $pedidos_pendientes - $pedidos_cancelados;
$total_pedidos_all = array_sum($estados_raw);
$porcentaje_exito = $total_pedidos_all > 0 ? round(($pedidos_exitosos / $total_pedidos_all) * 100) : 0;

// 6. Cotizaciones recientes (Últimas 5)
$stmt = $pdo->prepare("
    SELECT p.id, p.folio, p.monto_total, p.estado_envio, p.created_at, COUNT(pd.id) as total_items 
    FROM clientes_pedidos p
    LEFT JOIN clientes_pedidos_detalle pd ON p.id = pd.pedido_id
    WHERE p.cliente_id = ?
    GROUP BY p.id
    ORDER BY p.created_at DESC LIMIT 5
");
$stmt->execute([$cliente_id]);
$cotizaciones_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7. Banners Promocionales
$stmt = $pdo->query("SELECT * FROM admin_banners_promocionales WHERE activo = 1 ORDER BY orden ASC");
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle  = 'MMPharma Portal - Dashboard';
$activePage = 'dashboard';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)] bg-background text-on-surface">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-reveal">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                Dashboard Principal <span class="text-2xl">👋</span>
            </h1>
            <p class="text-on-surface-variant mt-1 text-sm">Aquí tienes un resumen de tu actividad reciente, <?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente') ?>.</p>
        </div>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <a href="Cotizaciones.php" class="px-5 py-2.5 bg-surface-container border border-outline-variant/50 hover:bg-surface-container-high text-white text-sm font-semibold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">download</span> Estado de Cuenta
            </a>
            <a href="../CATALOGO/catalogo.php" class="px-5 py-2.5 bg-primary hover:bg-primary-container text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Nueva Cotización
            </a>
        </div>
    </div>

    <!-- BANNERS -->
    <?php if(!empty($banners)): ?>
    <div class="mb-8 animate-reveal grid grid-cols-1 md:grid-cols-<?= min(count($banners), 3) ?> gap-6">
        <?php foreach($banners as $banner): ?>
        <a href="<?= htmlspecialchars($banner['enlace_url'] ?? '#') ?>" class="block rounded-2xl overflow-hidden shadow-lg border border-outline-variant/30 group hover:shadow-[0_0_20px_rgba(74,144,217,0.3)] transition-all relative">
            <img src="<?= htmlspecialchars($banner['ruta_imagen']) ?>" alt="<?= htmlspecialchars($banner['titulo']) ?>" class="w-full h-32 md:h-40 object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-5">
                <h3 class="text-white font-bold text-lg"><?= htmlspecialchars($banner['titulo']) ?></h3>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ROW 1: CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8 animate-reveal delay-100">
        
        <!-- Alerta Documentos (Col-span-2) -->
        <?php if ($estatus_cliente === 'DOCS_PENDIENTES'): ?>
        <div class="md:col-span-2 bg-[#422c10] border border-[#a66a1d] rounded-2xl p-6 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-yellow-500/10 rotate-12">
                <span class="material-symbols-outlined" style="font-size: 120px; font-variation-settings: 'FILL' 1;">warning</span>
            </div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-[#eab308]/20 text-[#eab308] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span>
                </div>
                <div>
                    <h3 class="text-[#fde047] font-bold text-base mb-1">Documentos por vencer</h3>
                    <p class="text-[#fef08a]/80 text-xs mb-3 leading-relaxed">Tu cuenta requiere cargar algunos documentos (aviso de funcionamiento, licencia) para estar completamente activa.</p>
                    <a href="Documentos.php" class="text-[#facc15] text-xs font-bold hover:underline">Actualizar ahora</a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="md:col-span-2 bg-tertiary-container/30 border border-tertiary/20 rounded-2xl p-6 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-tertiary/5 rotate-12">
                <span class="material-symbols-outlined" style="font-size: 120px; font-variation-settings: 'FILL' 1;">verified</span>
            </div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-tertiary/20 text-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                </div>
                <div>
                    <h3 class="text-tertiary font-bold text-base mb-1">Cuenta Activa</h3>
                    <p class="text-tertiary/80 text-xs mb-3 leading-relaxed">Tus documentos están al día. Puedes cotizar y comprar sin restricciones.</p>
                    <a href="Documentos.php" class="text-tertiary text-xs font-bold hover:underline">Ver documentos</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Metric 1: Cotizaciones -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Cotizaciones este mes</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-extrabold text-white leading-none"><?= $cotizaciones_mes ?></span>
            </div>
        </div>

        <!-- Metric 2: Última Cotización -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Última Cotización</span>
            <div>
                <span class="text-2xl font-extrabold text-white block mb-1"><?= $ultima_monto ?></span>
                <span class="text-[10px] text-on-surface-variant italic"><?= $ultima_fecha ?></span>
            </div>
        </div>

        <!-- Metric 3: Productos Favoritos -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Productos Comprados</span>
            <div class="flex items-center gap-2">
                <span class="text-3xl font-extrabold text-white leading-none"><?= $productos_favoritos ?></span>
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1">inventory_2</span>
            </div>
        </div>

        <!-- Metric 4: Estado de Cuenta -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Estado de Cuenta</span>
            <div>
                <?php if($estatus_cliente === 'ACTIVO'): ?>
                <span class="px-2 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider mb-2 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> ACTIVO</span>
                <span class="block text-[10px] text-on-surface-variant leading-tight">Sin adeudos<br>pendientes</span>
                <?php else: ?>
                <span class="px-2 py-1 bg-error/20 text-error text-[10px] font-bold rounded-md uppercase tracking-wider mb-2 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-error"></span> INACTIVO</span>
                <span class="block text-[10px] text-on-surface-variant leading-tight">Requiere<br>atención</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ROW 2: Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-200">
        
        <!-- Inversión Histórica -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-tertiary/5 rounded-full blur-2xl group-hover:bg-tertiary/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Inversión Total</h3>
                    <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Histórico</p>
                </div>
            </div>
            <p class="text-4xl font-extrabold text-white relative z-10">$<?= number_format($total_gastado, 2) ?></p>
        </div>

        <!-- Ticket Promedio -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Ticket Promedio</h3>
                    <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Por Cotización</p>
                </div>
            </div>
            <p class="text-4xl font-extrabold text-white relative z-10">$<?= number_format($ticket_promedio, 2) ?></p>
        </div>

        <!-- Efectividad -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-secondary/5 rounded-full blur-2xl group-hover:bg-secondary/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">pie_chart</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Efectividad</h3>
                    <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Tasa de Aprobación</p>
                </div>
            </div>
            <div class="flex items-end gap-3 relative z-10">
                <p class="text-4xl font-extrabold text-white"><?= $porcentaje_exito ?>%</p>
                <div class="flex-1 mb-2.5 h-2 bg-surface-container rounded-full overflow-hidden">
                    <div class="h-full bg-secondary rounded-full" style="width: <?= $porcentaje_exito ?>%"></div>
                </div>
            </div>
            <div class="flex gap-4 mt-3 text-[10px] text-on-surface-variant font-bold relative z-10">
                <span class="text-tertiary flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary"></span> <?= $pedidos_exitosos ?> Aprobados</span>
                <span class="text-primary flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span> <?= $pedidos_pendientes ?> Pendientes</span>
            </div>
        </div>

    </div>

    <!-- ROW 3: Tabla Cotizaciones Recientes -->
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-sm overflow-hidden animate-reveal delay-300">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/20">
            <h3 class="text-base font-bold text-on-surface">Cotizaciones recientes</h3>
            <a href="Cotizaciones.php" class="text-xs font-bold text-primary hover:text-primary-container transition-colors">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
                    <tr>
                        <th class="py-3 px-6">Folio</th>
                        <th class="py-3 px-6">Fecha</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-right">Total</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php if (empty($cotizaciones_recientes)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-on-surface-variant text-sm">No tienes cotizaciones recientes.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($cotizaciones_recientes as $cot): ?>
                        <tr class="hover:bg-surface-container/30 transition-colors group">
                            <td class="py-4 px-6 text-sm font-bold text-white"><?= htmlspecialchars($cot['folio']) ?></td>
                            <td class="py-4 px-6 text-xs text-on-surface-variant"><?= date('d M, Y', strtotime($cot['created_at'])) ?></td>
                            <td class="py-4 px-6 text-xs text-on-surface-variant text-center"><?= $cot['total_items'] ?> items</td>
                            <td class="py-4 px-6 text-sm font-bold text-white text-right">$<?= number_format($cot['monto_total'], 2) ?></td>
                            <td class="py-4 px-6 text-center">
                                <?php if ($cot['estado_envio'] === 'PENDIENTE'): ?>
                                <span class="px-2 py-1 bg-primary/20 text-primary text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">schedule</span>Pendiente</span>
                                <?php elseif ($cot['estado_envio'] === 'CANCELADO'): ?>
                                <span class="px-2 py-1 bg-error/20 text-error text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">cancel</span>Cancelado</span>
                                <?php else: ?>
                                <span class="px-2 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">local_shipping</span><?= htmlspecialchars($cot['estado_envio']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="Cotizacion-Detalle.php?id=<?= $cot['id'] ?>" class="text-on-surface-variant hover:text-white transition-colors"><span class="material-symbols-outlined text-[18px]">visibility</span></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>
<?php include('Includes/footer.php'); ?>
<script>
    console.log("MMPharma Dashboard Loaded");
</script>
