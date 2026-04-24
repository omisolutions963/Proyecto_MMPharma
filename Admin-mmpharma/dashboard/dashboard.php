<?php
session_start();
require_once '../clinical_core/db.php';
$pdo = getDB();

// ── Cargar perfil del admin desde BD y sincronizar sesión ─────────────────────
try {
    $emailSesion = $_SESSION['admin_email'] ?? '';
    if ($emailSesion) {
        $stAdmin = $pdo->prepare(
            "SELECT id, nombre, email,
                    COALESCE(foto_perfil, '') AS foto_perfil
             FROM administradores WHERE email = ? LIMIT 1"
        );
        $stAdmin->execute([$emailSesion]);
        $adminRow = $stAdmin->fetch();
        if ($adminRow) {
            $_SESSION['admin_id']     = $adminRow['id'];
            $_SESSION['admin_nombre'] = $adminRow['nombre'];
            $_SESSION['admin_email']  = $adminRow['email'];
            $_SESSION['admin_foto']   = $adminRow['foto_perfil'];
        }
    }
} catch (Exception $e) {
    // tabla administradores aún no existe → usar lo que haya en sesión
}

// ── Datos del panel ───────────────────────────────────────────────────────────
$totalProd    = 0; $totalRedFria = 0; $sinPrecio = 0;
$totalClientes = 0; $clientesActivos = 0;
$solPendientes = 0; $contactoNuevos = 0;
$topProductos = []; $ultimasSolicitudes = [];

try { $totalProd     = (int)$pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn(); } catch(Exception $e){}
try { $totalRedFria  = (int)$pdo->query("SELECT COUNT(*) FROM productos WHERE tipo='RED FRIA'")->fetchColumn(); } catch(Exception $e){}
$totalSeco = $totalProd - $totalRedFria;
try { $sinPrecio     = (int)$pdo->query("SELECT COUNT(*) FROM productos WHERE precio_farmacia=0 AND precio_distribuidor=0")->fetchColumn(); } catch(Exception $e){}
try { $totalClientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn(); } catch(Exception $e){}
try { $clientesActivos=(int)$pdo->query("SELECT COUNT(*) FROM clientes WHERE estatus='ACTIVO'")->fetchColumn(); } catch(Exception $e){}
try { $solPendientes = (int)$pdo->query("SELECT COUNT(*) FROM solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn(); } catch(Exception $e){}
try { $contactoNuevos= (int)$pdo->query("SELECT COUNT(*) FROM contacto_mensajes WHERE leido=0")->fetchColumn(); } catch(Exception $e){}
try { $topProductos  = $pdo->query("SELECT nombre, precio_farmacia FROM productos WHERE precio_farmacia > 0 ORDER BY precio_farmacia DESC LIMIT 6")->fetchAll(); } catch(Exception $e){}
try { $ultimasSolicitudes = $pdo->query("SELECT razon_social, tipo_cliente, email, estatus, created_at FROM solicitudes_registro ORDER BY created_at DESC LIMIT 5")->fetchAll(); } catch(Exception $e){}

$salesData = ['daily' => ['labels' => [], 'data' => []], 'monthly' => ['labels' => [], 'data' => []]];
$topProductosVentas = [];
try {
    // Top 10 Productos
    $sqlTop = "SELECT d.nombre_producto, SUM(d.cantidad) as total_vendido, SUM(d.subtotal) as ingresos 
               FROM pedidos_detalle d 
               JOIN pedidos p ON d.pedido_id = p.id 
               WHERE p.estado_envio != 'CANCELADO' 
               GROUP BY d.producto_id, d.nombre_producto 
               ORDER BY total_vendido DESC LIMIT 10";
    $topProductosVentas = $pdo->query($sqlTop)->fetchAll(PDO::FETCH_ASSOC);

    // Sales Daily
    $sqlDaily = "SELECT DATE(fecha_pedido) as d, SUM(monto_total) as t 
                 FROM pedidos WHERE estado_envio != 'CANCELADO' AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                 GROUP BY DATE(fecha_pedido)";
    $resDaily = $pdo->query($sqlDaily)->fetchAll(PDO::FETCH_ASSOC);
    $dMap = []; foreach($resDaily as $r) $dMap[$r['d']] = (float)$r['t'];
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $salesData['daily']['labels'][] = date('d/m', strtotime("-$i days"));
        $salesData['daily']['data'][] = $dMap[$date] ?? 0;
    }

    // Sales Monthly
    $sqlMonthly = "SELECT DATE_FORMAT(fecha_pedido, '%Y-%m') as m, SUM(monto_total) as t 
                   FROM pedidos WHERE estado_envio != 'CANCELADO' AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) 
                   GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')";
    $resMonthly = $pdo->query($sqlMonthly)->fetchAll(PDO::FETCH_ASSOC);
    $mMap = []; foreach($resMonthly as $r) $mMap[$r['m']] = (float)$r['t'];
    
    $mesesES = ['Jan'=>'Ene','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Abr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Ago','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dic'];
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("first day of -$i month"));
        $mEn = date('M', strtotime("first day of -$i month"));
        $label = $mesesES[$mEn] . ' ' . date('y', strtotime("first day of -$i month"));
        $salesData['monthly']['labels'][] = $label;
        $salesData['monthly']['data'][] = $mMap[$month] ?? 0;
    }
} catch (Exception $e) {}

$pageTitle  = 'MMPharma Portal - Dashboard';
$activePage = 'dashboard';
include('../includes/header.php');
include('../includes/sidebar.php');
?>

<main class="ml-64 pt-8 px-8 pb-12 min-h-screen" style="background:#071628">

<!-- ══ WELCOME HERO ══════════════════════════════════════════════════════════ -->
<div class="relative rounded-2xl overflow-hidden mb-8 p-8 animate-fade-up"
     style="background:linear-gradient(135deg,#001a3d 0%,#0a2d5e 50%,#0d3570 100%);
            border:1px solid rgba(74,144,217,0.2)">
    <!-- Glow orbs -->
    <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full"
         style="background:radial-gradient(circle,rgba(74,144,217,0.15) 0%,transparent 70%)"></div>
    <div class="absolute -bottom-8 left-1/3 w-48 h-48 rounded-full"
         style="background:radial-gradient(circle,rgba(52,196,122,0.08) 0%,transparent 70%)"></div>
    <div class="relative z-10 flex items-center justify-between gap-6 flex-wrap">
        <div>
            <p class="text-blue-400/70 text-xs font-bold uppercase tracking-widest mb-2">
                <?php
                $dias = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
                $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
                echo $dias[date('l')] . ', ' . date('d') . ' de ' . $meses[date('F')] . ' ' . date('Y');
                ?>
            </p>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">
                Bienvenido, <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?>
            </h1>
            <p class="text-blue-200/60 text-sm mt-1 max-w-lg">
                Panel de control MMPharma — <?= number_format($totalProd) ?> productos activos en catálogo.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="../G_Productos/productos.php"
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all
                      text-white hover:scale-105"
               style="background:rgba(74,144,217,0.25);border:1px solid rgba(74,144,217,0.4)">
                <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                Ver Catálogo
            </a>
            <a href="../S_Registro/solicitudes.php"
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all
                      hover:scale-105"
               style="background:rgba(74,144,217,0.9);color:#fff;border:1px solid #4a90d9">
                <span class="material-symbols-outlined text-[18px]">list_alt</span>
                Solicitudes <?php if($solPendientes): ?>
                    <span class="ml-1 bg-white/20 text-white text-xs font-black px-1.5 py-0.5 rounded-full"><?= $solPendientes ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</div>

<!-- ══ ALERTAS ══════════════════════════════════════════════════════════════ -->
<?php if ($sinPrecio > 0 || $solPendientes > 0 || $contactoNuevos > 0): ?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <?php if ($sinPrecio > 0): ?>
    <a href="../G_Productos/productos.php" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
       style="background:#1a0e00;border-left:3px solid #f28b82;border-top:1px solid rgba(242,139,130,0.15)">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(242,139,130,0.15)">
            <span class="material-symbols-outlined text-[18px]" style="color:#f28b82">price_check</span>
        </div>
        <div>
            <p class="text-xs font-bold" style="color:#f28b82"><?= $sinPrecio ?> productos sin precio</p>
            <p class="text-[10px]" style="color:rgba(242,139,130,0.6)">Requieren actualización en catálogo</p>
        </div>
    </a>
    <?php endif; ?>
    <?php if ($solPendientes > 0): ?>
    <a href="../S_Registro/solicitudes.php" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
       style="background:#0d1f3c;border-left:3px solid #4a90d9;border-top:1px solid rgba(74,144,217,0.15)">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(74,144,217,0.15)">
            <span class="material-symbols-outlined text-[18px] text-primary">pending</span>
        </div>
        <div>
            <p class="text-xs font-bold text-primary"><?= $solPendientes ?> solicitudes pendientes</p>
            <p class="text-[10px] text-on-surface-variant">Esperan revisión y aprobación</p>
        </div>
    </a>
    <?php endif; ?>
    <?php if ($contactoNuevos > 0): ?>
    <div class="flex items-center gap-3 p-4 rounded-xl"
         style="background:#0a1f14;border-left:3px solid #34c47a;border-top:1px solid rgba(52,196,122,0.15)">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(52,196,122,0.15)">
            <span class="material-symbols-outlined text-[18px] text-tertiary">mark_email_unread</span>
        </div>
        <div>
            <p class="text-xs font-bold text-tertiary"><?= $contactoNuevos ?> mensajes nuevos</p>
            <p class="text-[10px] text-on-surface-variant">En el formulario de contacto</p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ══ KPI CARDS ═════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <!-- Total Productos -->
    <div class="relative p-6 rounded-2xl overflow-hidden card-glow animate-fade-up"
         style="background:linear-gradient(135deg,#102245,#152a52);border:1px solid rgba(74,144,217,0.2)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#4a90d9,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(74,144,217,0.2)">
                    <span class="material-symbols-outlined text-[20px] text-primary"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">inventory_2</span>
                </div>
                <span class="text-[10px] font-bold text-primary uppercase tracking-wider px-2 py-1 rounded-full"
                      style="background:rgba(74,144,217,0.15)">TOTAL</span>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Productos</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalProd) ?></h3>
            <p class="text-on-surface-variant text-[11px] mt-2">en catálogo activo</p>
        </div>
    </div>

    <!-- Cadena Seca -->
    <div class="relative p-6 rounded-2xl overflow-hidden animate-fade-up delay-100"
         style="background:linear-gradient(135deg,#102245,#152a52);border:1px solid rgba(74,144,217,0.15)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#5bb8f5,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(91,184,245,0.15)">
                    <span class="material-symbols-outlined text-[20px] text-secondary"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">light_mode</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Cadena Seca</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalSeco) ?></h3>
            <div class="mt-3 w-full h-1 rounded-full" style="background:rgba(255,255,255,0.1)">
                <div class="h-full rounded-full" style="background:#5bb8f5;width:<?= $totalProd>0?round($totalSeco/$totalProd*100):0 ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Red Fría -->
    <div class="relative p-6 rounded-2xl overflow-hidden animate-fade-up delay-200"
         style="background:linear-gradient(135deg,#0a2030,#0d2a40);border:1px solid rgba(52,196,122,0.2)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#34c47a,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(52,196,122,0.15)">
                    <span class="material-symbols-outlined text-[20px] text-tertiary"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">ac_unit</span>
                </div>
                <span class="text-[10px] font-bold text-tertiary uppercase tracking-wider px-2 py-1 rounded-full"
                      style="background:rgba(52,196,122,0.1)">FRÍO</span>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Red Fría</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalRedFria) ?></h3>
            <div class="mt-3 w-full h-1 rounded-full" style="background:rgba(255,255,255,0.1)">
                <div class="h-full rounded-full" style="background:#34c47a;width:<?= $totalProd>0?round($totalRedFria/$totalProd*100):0 ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Clientes Activos -->
    <div class="relative p-6 rounded-2xl overflow-hidden animate-fade-up delay-300"
         style="background:linear-gradient(135deg,#102245,#152a52);border:1px solid rgba(74,144,217,0.15)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#4a90d9,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(74,144,217,0.2)">
                    <span class="material-symbols-outlined text-[20px] text-primary"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">group</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Clientes Activos</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($clientesActivos) ?></h3>
            <p class="text-on-surface-variant text-[11px] mt-2">de <?= number_format($totalClientes) ?> registrados</p>
        </div>
    </div>
</div>

<!-- ══ GRÁFICO DE VENTAS ═════════════════════════════════════════════════════ -->
<div class="mb-8 p-6 rounded-2xl animate-fade-up" style="background:#102245;border:1px solid rgba(74,144,217,0.15); animation-delay: 0.35s">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">trending_up</span>
            Rendimiento de Ventas
        </h2>
        <div class="flex bg-surface-container-lowest rounded-lg p-1 border border-outline-variant/10">
            <button onclick="updateChart('daily')" id="btnChartDaily" class="px-4 py-1.5 text-xs font-bold rounded-md bg-primary text-white shadow transition-all">Diario</button>
            <button onclick="updateChart('monthly')" id="btnChartMonthly" class="px-4 py-1.5 text-xs font-bold rounded-md text-on-surface-variant hover:text-white transition-all">Mensual</button>
        </div>
    </div>
    <div class="w-full h-[300px]">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<!-- ══ CONTENIDO PRINCIPAL ═══════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Columna izq: distribución + acciones rápidas -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Distribución del catálogo -->
        <div class="p-6 rounded-2xl animate-fade-up" style="background:#102245;border:1px solid rgba(74,144,217,0.15); animation-delay: 0.4s">
            <h2 class="text-sm font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">pie_chart</span>
                Distribución del Catálogo
            </h2>
            <div class="space-y-5">
                <?php
                $bars = [
                    ['label'=>'Cadena Seca',    'val'=>$totalSeco,    'color'=>'#4a90d9', 'pct'=> $totalProd>0?round($totalSeco/$totalProd*100):0],
                    ['label'=>'Red Fría',        'val'=>$totalRedFria, 'color'=>'#34c47a', 'pct'=> $totalProd>0?round($totalRedFria/$totalProd*100):0],
                    ['label'=>'Sin Precio',      'val'=>$sinPrecio,    'color'=>'#f28b82', 'pct'=> $totalProd>0?round($sinPrecio/$totalProd*100):0],
                ];
                foreach ($bars as $b): ?>
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs font-semibold text-on-surface-variant"><?= $b['label'] ?></span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white"><?= number_format($b['val']) ?></span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                  style="background:<?= $b['color'] ?>22;color:<?= $b['color'] ?>"><?= $b['pct'] ?>%</span>
                        </div>
                    </div>
                    <div class="w-full h-2 rounded-full" style="background:rgba(255,255,255,0.07)">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="background:<?= $b['color'] ?>;width:<?= $b['pct'] ?>%;
                                    box-shadow:0 0 8px <?= $b['color'] ?>66"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Últimas solicitudes -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden animate-scale-in delay-500">
            <div class="px-8 py-4 bg-surface-container-low border-b border-outline-variant/10 flex items-center justify-between">
                <h2 class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">list_alt</span>
                    Últimas Solicitudes
                </h2>
                <a href="../S_Registro/solicitudes.php" class="text-[10px] font-black text-primary hover:underline uppercase tracking-widest">
                    Ver todas →
                </a>
            </div>
            <?php if (empty($ultimasSolicitudes)): ?>
            <p class="px-8 py-10 text-center text-on-surface-variant text-sm">No se encontraron solicitudes.</p>
            <?php else: ?>
            <div class="divide-y divide-outline-variant/10">
            <?php foreach ($ultimasSolicitudes as $s):
                $badge = match($s['estatus']) {
                    'APROBADA'  => ['color'=>'text-tertiary','bg'=>'bg-tertiary-container/10','label'=>'Aprobada'],
                    'RECHAZADA' => ['color'=>'text-error','bg'=>'bg-error-container/10','label'=>'Rechazada'],
                    default     => ['color'=>'text-primary','bg'=>'bg-primary/10','label'=>'Pendiente'],
                };
            ?>
            <div class="px-8 py-4 flex items-center gap-4 hover:bg-surface-container-low/30 transition-colors">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-surface-container-low border border-outline-variant/10">
                    <span class="text-xs font-black text-primary">
                        <?= strtoupper(substr($s['razon_social'],0,2)) ?>
                    </span>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-bold text-on-surface truncate"><?= htmlspecialchars($s['razon_social']) ?></p>
                    <p class="text-[10px] text-on-surface-variant font-bold uppercase tracking-wider"><?= $s['tipo_cliente'] ?> · <?= date('d/m/Y', strtotime($s['created_at'])) ?></p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase border border-outline-variant/10 <?= $badge['bg'] ?> <?= $badge['color'] ?>">
                    <?= $badge['label'] ?>
                </span>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="bg-surface-container-low px-8 py-4 flex justify-between items-center border-t border-outline-variant/10">
                <p class="text-[11px] font-bold text-on-surface-variant">
                    Mostrando <span class="font-black text-on-surface"><?= count($ultimasSolicitudes) ?></span> solicitudes recientes.
                </p>
            </div>
        </div>
    </div>

    <!-- Columna der: top productos + acciones rápidas -->
    <div class="space-y-6 animate-fade-up" style="animation-delay: 0.2s">

        <!-- Acciones Rápidas -->
        <div class="p-6 rounded-2xl animate-fade-up" style="background:#102245;border:1px solid rgba(74,144,217,0.15); animation-delay: 0.3s">
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">bolt</span>
                Acciones Rápidas
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <?php
                $links = [
                    ['href'=>'../G_Productos/productos.php', 'icon'=>'inventory_2', 'label'=>'Productos', 'color'=>'#4a90d9'],
                    ['href'=>'../G_Productos/productos.php','icon'=>'warehouse',   'label'=>'Inventario','color'=>'#5bb8f5'],
                    ['href'=>'../G_Clientes/clientes.php',   'icon'=>'group',       'label'=>'Clientes',  'color'=>'#34c47a'],
                    ['href'=>'../G_Pedidos/pedidos.php',     'icon'=>'shopping_cart','label'=>'Pedidos',   'color'=>'#7fb3f5'],
                ];
                foreach ($links as $l): ?>
                <a href="<?= $l['href'] ?>"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all hover:scale-105 hover:shadow-lg text-center"
                   style="background:rgba(255,255,255,0.04);border:1px solid rgba(74,144,217,0.12)">
                    <span class="material-symbols-outlined text-[24px]"
                          style="color:<?= $l['color'] ?>;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">
                        <?= $l['icon'] ?>
                    </span>
                    <span class="text-xs font-semibold text-on-surface-variant"><?= $l['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Top 10 Productos Más Vendidos -->
        <div class="p-6 rounded-2xl animate-fade-up" style="background:#102245;border:1px solid rgba(74,144,217,0.15); animation-delay: 0.4s">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-tertiary text-[18px]">workspace_premium</span>
                Top 10 Más Vendidos
            </h2>
            <div class="space-y-4">
            <?php if (empty($topProductosVentas)): ?>
                <p class="text-xs text-on-surface-variant text-center py-4">Aún no hay ventas registradas.</p>
            <?php else: ?>
                <?php 
                // Encontrar el máximo vendido para la barra de progreso
                $maxVendido = max(array_column($topProductosVentas, 'total_vendido'));
                foreach ($topProductosVentas as $i => $p): 
                    $pct = $maxVendido > 0 ? round(($p['total_vendido'] / $maxVendido) * 100) : 0;
                ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="text-[10px] font-black text-tertiary w-4">#<?= $i+1 ?></span>
                            <span class="text-xs font-semibold text-white truncate" title="<?= htmlspecialchars($p['nombre_producto']) ?>"><?= htmlspecialchars($p['nombre_producto']) ?></span>
                        </div>
                        <span class="text-[10px] font-bold text-on-surface-variant ml-2 whitespace-nowrap"><?= number_format($p['total_vendido']) ?> uds</span>
                    </div>
                    <div class="w-full h-1.5 rounded-full" style="background:rgba(255,255,255,0.05)">
                        <div class="h-full rounded-full transition-all duration-1000" style="background:linear-gradient(90deg, #34c47a, #4a90d9); width:<?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            <a href="../G_Pedidos/pedidos.php"
               class="block text-center w-full mt-6 py-2.5 rounded-xl text-xs font-bold text-tertiary transition-colors hover:bg-tertiary/10"
               style="border:1px solid rgba(52,196,122,0.3)">
                Ir a Pedidos →
            </a>
        </div>

        <!-- Estado del Sistema -->
        <div class="p-6 rounded-2xl relative overflow-hidden animate-fade-up"
             style="background:linear-gradient(135deg,#001a3d,#003878);border:1px solid rgba(74,144,217,0.25); animation-delay: 0.4s">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full"
                 style="background:radial-gradient(circle,rgba(74,144,217,0.2),transparent)"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-tertiary animate-pulse"></div>
                    <span class="text-[10px] font-bold text-tertiary uppercase tracking-widest">Sistema Activo</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Portal Operativo</h3>
                <?php $pct = $totalProd>0?round(($totalProd-$sinPrecio)/$totalProd*100):0; ?>
                <p class="text-blue-200/60 text-xs mb-4"><?= $pct ?>% del catálogo con precios activos.</p>
                <div class="w-full h-2 rounded-full mb-2" style="background:rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full" style="background:linear-gradient(90deg,#4a90d9,#34c47a);width:<?= $pct ?>%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-blue-200/40">
                    <span>0%</span>
                    <span class="font-bold text-white"><?= $pct ?>%</span>
                    <span>100%</span>
                </div>
                <p class="text-[10px] text-blue-200/30 mt-4 uppercase tracking-widest">
                    Última sync: <?= date('d/m/Y H:i') ?>
                </p>
            </div>
        </div>

    </div><!-- /col-der -->
</div><!-- /grid -->

</main>

<!-- ══════════════════════════════════════════════════════════════════════════
     PANEL DE PERFIL (Slide-in drawer desde la derecha)
     ═════════════════════════════════════════════════════════════════════════ -->

<!-- Overlay -->
<div id="perfilOverlay" onclick="cerrarPerfil()"
     class="fixed inset-0 z-50 hidden transition-opacity duration-300"
     style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px)"></div>

<!-- Drawer -->
<div id="perfilDrawer"
     class="fixed top-0 right-0 h-full w-full max-w-md z-50 overflow-y-auto
            transition-transform duration-300 translate-x-full"
     style="background:#0d1f3c;border-left:1px solid rgba(74,144,217,0.2);box-shadow:-20px 0 60px rgba(0,0,0,0.5)">

    <!-- Header del drawer -->
    <div class="flex items-center justify-between px-6 py-5 sticky top-0 z-10"
         style="background:#0d1f3c;border-bottom:1px solid rgba(74,144,217,0.15)">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">manage_accounts</span>
            <h2 class="text-base font-bold text-white">Mi Perfil</h2>
        </div>
        <button onclick="cerrarPerfil()"
                class="w-9 h-9 flex items-center justify-center rounded-xl text-on-surface-variant hover:bg-white/5 transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="px-6 py-6 space-y-6">

        <!-- ── FOTO DE PERFIL ── -->
        <div class="flex flex-col items-center py-6 px-4 rounded-2xl gap-4"
             style="background:linear-gradient(135deg,#102245,#152a52);border:1px solid rgba(74,144,217,0.15)">

            <!-- Avatar con botón de edición -->
            <div class="relative group cursor-pointer" onclick="document.getElementById('fotoInput').click()">
                <?php $fotoActual = $_SESSION['admin_foto'] ?? ''; ?>
                <?php if ($fotoActual): ?>
                <img id="previewFoto" src="<?= htmlspecialchars($fotoActual) ?>"
                     class="w-28 h-28 rounded-full object-cover border-4 border-primary/40"
                     alt="Foto de perfil">
                <?php else: ?>
                <div id="previewFoto"
                     class="w-28 h-28 rounded-full flex items-center justify-center text-3xl font-black text-primary border-4 border-primary/30"
                     style="background:rgba(74,144,217,0.2)">
                    <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
                </div>
                <?php endif; ?>
                <!-- Overlay hover -->
                <div class="absolute inset-0 rounded-full flex flex-col items-center justify-center
                            bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                    <span class="text-white text-[10px] font-bold mt-1">Cambiar</span>
                </div>
            </div>

            <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/webp" class="hidden"
                   onchange="subirFoto(this)">

            <div class="text-center">
                <p class="text-white font-bold text-lg" id="drawerNombre">
                    <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?>
                </p>
                <p class="text-on-surface-variant text-xs mt-0.5">
                    <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>
                </p>
            </div>

            <!-- Barra de progreso subida -->
            <div id="uploadProgress" class="w-full hidden">
                <div class="w-full h-1.5 rounded-full" style="background:rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full bg-primary animate-pulse w-full"></div>
                </div>
                <p class="text-xs text-on-surface-variant text-center mt-1">Subiendo foto...</p>
            </div>

            <p class="text-[10px] text-on-surface-variant text-center opacity-60">
                JPG, PNG o WEBP · Máximo 5 MB
            </p>
        </div>

        <!-- ── DATOS PERSONALES ── -->
        <div class="rounded-2xl overflow-hidden" style="background:#102245;border:1px solid rgba(74,144,217,0.15)">
            <div class="px-5 py-4" style="border-bottom:1px solid rgba(74,144,217,0.1)">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[16px]">person</span>
                    Datos Personales
                </h3>
            </div>
            <form id="formDatos" class="p-5 space-y-4" onsubmit="guardarDatos(event)">
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                        Nombre completo
                    </label>
                    <input type="text" name="nombre" required
                           value="<?= htmlspecialchars($_SESSION['admin_nombre'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                           style="background:#071628;border:1px solid rgba(74,144,217,0.25)">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                        Correo electrónico
                    </label>
                    <input type="email" name="email" required
                           value="<?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                           style="background:#071628;border:1px solid rgba(74,144,217,0.25)">
                </div>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90 flex items-center justify-center gap-2"
                        style="background:#4a90d9">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Guardar Cambios
                </button>
            </form>
        </div>

        <!-- ── CAMBIAR CONTRASEÑA ── -->
        <div class="rounded-2xl overflow-hidden" style="background:#102245;border:1px solid rgba(74,144,217,0.15)">
            <div class="px-5 py-4" style="border-bottom:1px solid rgba(74,144,217,0.1)">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[16px]">lock</span>
                    Cambiar Contraseña
                </h3>
            </div>
            <form id="formPassword" class="p-5 space-y-4" onsubmit="cambiarPassword(event)">
                <?php foreach (['actual'=>'Contraseña actual','nueva'=>'Nueva contraseña','confirmar'=>'Confirmar nueva'] as $n=>$l): ?>
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                        <?= $l ?>
                    </label>
                    <div class="relative">
                        <input type="password" name="<?= $n ?>" required minlength="<?= $n==='actual'?1:8 ?>"
                               class="w-full pl-4 pr-10 py-2.5 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                               style="background:#071628;border:1px solid rgba(74,144,217,0.25)">
                        <button type="button" onclick="togglePasswordVis(this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold transition-all hover:opacity-90 flex items-center justify-center gap-2"
                        style="background:rgba(74,144,217,0.15);color:#4a90d9;border:1px solid rgba(74,144,217,0.3)">
                    <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                    Actualizar Contraseña
                </button>
            </form>
        </div>

        <!-- ── INFO SESSION ── -->
        <div class="px-4 py-3 rounded-xl flex items-center gap-3"
             style="background:rgba(74,144,217,0.06);border:1px solid rgba(74,144,217,0.1)">
            <span class="material-symbols-outlined text-on-surface-variant text-[18px]">info</span>
            <div>
                <p class="text-xs text-on-surface-variant">Sesión iniciada como</p>
                <p class="text-xs font-bold text-primary"><?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></p>
            </div>
        </div>

    </div><!-- /px-6 py-6 -->
</div><!-- /drawer -->

<!-- ══════════════════════════════════════════════════════════════════════════
     MODAL DE RECORTE (Cropper)
     ═════════════════════════════════════════════════════════════════════════ -->
<div id="cropperModal" class="fixed inset-0 z-[60] hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,0.8); backdrop-filter:blur(8px)">
    <div class="bg-[#0d1f3c] w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">crop</span>
                Recortar Foto de Perfil
            </h3>
            <button onclick="cerrarCropper()" class="text-on-surface-variant hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <div class="aspect-square w-full overflow-hidden rounded-2xl bg-black/20 mb-6">
                <img id="cropperImage" src="" class="max-w-full block">
            </div>
            <div class="flex gap-3">
                <button onclick="cerrarCropper()" class="flex-1 py-3 rounded-xl font-bold text-on-surface-variant bg-white/5 hover:bg-white/10 transition-all">
                    Cancelar
                </button>
                <button id="btnConfirmarRecorte" class="flex-1 py-3 rounded-xl font-bold text-white bg-primary hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Aplicar y Subir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cropper = null;

// ── Panel abrir/cerrar ────────────────────────────────────────────────────────
function abrirPerfil() {
    document.getElementById('perfilOverlay').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('perfilDrawer').classList.remove('translate-x-full');
    }, 10);
}
function cerrarPerfil() {
    document.getElementById('perfilDrawer').classList.add('translate-x-full');
    setTimeout(() => {
        document.getElementById('perfilOverlay').classList.add('hidden');
    }, 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { cerrarPerfil(); cerrarCropper(); } });

// ── Lógica de Cropping ────────────────────────────────────────────────────────
function cerrarCropper() {
    document.getElementById('cropperModal').classList.add('hidden');
    if(cropper) {
        cropper.destroy();
        cropper = null;
    }
}

document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (event) => {
        document.getElementById('cropperImage').src = event.target.result;
        document.getElementById('cropperModal').classList.remove('hidden');
        
        const image = document.getElementById('cropperImage');
        if(cropper) cropper.destroy();
        
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 2,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    };
    reader.readAsDataURL(file);
});

document.getElementById('btnConfirmarRecorte').addEventListener('click', function() {
    if (!cropper) return;
    
    // Obtener el canvas recortado con alta calidad
    const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    canvas.toBlob((blob) => {
        const file = new File([blob], "perfil.jpg", { type: "image/jpeg" });
        ejecutarSubida(file);
        cerrarCropper();
    }, 'image/jpeg', 0.9);
});

// ── Subida real al servidor ──────────────────────────────────────────────────
async function ejecutarSubida(file) {
    document.getElementById('uploadProgress').classList.remove('hidden');
    const fd = new FormData();
    fd.append('action', 'upload_foto');
    fd.append('foto', file);

    try {
        const res = await fetch('../clinical_core/perfil_update.php', {method:'POST', body:fd});
        const data = await res.json();
        document.getElementById('uploadProgress').classList.add('hidden');
        if (data.ok) {
            actualizarFotos(data.url);
            // Actualizar preview en drawer
            const prev = document.getElementById('previewFoto');
            if (prev.tagName === 'IMG') {
                prev.src = data.url + '?t=' + Date.now();
            }
            Swal.fire({title:'¡Foto actualizada!', icon:'success', background:'#0d1f3c', color:'#fff', timer:2000, showConfirmButton:false});
        } else {
            Swal.fire({title:'Error', text:data.msg, icon:'error', background:'#0d1f3c', color:'#fff'});
        }
    } catch (e) {
        document.getElementById('uploadProgress').classList.add('hidden');
        Swal.fire({title:'Error de red', icon:'error', background:'#0d1f3c', color:'#fff'});
    }
}

function actualizarFotos(url) {
    const t = Date.now();
    ['headerProfileImg','sidebarProfileImg'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === 'IMG') {
            el.src = url + '?t=' + t;
        } else {
            const img = document.createElement('img');
            img.id = id;
            img.src = url + '?t=' + t;
            img.className = el.className.split(' ').filter(c => !c.includes('text-') && !c.includes('font-')).join(' ') + ' object-cover';
            el.replaceWith(img);
        }
    });
}

// ── Datos personales y Password (sin cambios) ───────────────────────────────
async function guardarDatos(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'update_perfil');
    const btn = e.target.querySelector('button[type=submit]');
    const originalText = btn.innerHTML;
    btn.disabled = true; btn.textContent = 'Guardando...';
    try {
        const res = await fetch('../clinical_core/perfil_update.php', {method:'POST', body:fd});
        const data = await res.json();
        if (data.ok) {
            document.getElementById('drawerNombre').textContent = data.nombre;
            const sn = document.getElementById('sidebarNombre'); if(sn) sn.textContent = data.nombre;
            Swal.fire({title:'¡Datos guardados!', icon:'success', timer:1800, showConfirmButton:false, background:'#0d1f3c', color:'#fff'});
        } else {
            Swal.fire({title:'Error', text:data.msg, icon:'error', background:'#0d1f3c', color:'#fff'});
        }
    } catch(e) {
        Swal.fire({title:'Error de conexión', icon:'error', background:'#0d1f3c', color:'#fff'});
    }
    btn.disabled = false; btn.innerHTML = originalText;
}

async function cambiarPassword(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'change_password');
    const btn = e.target.querySelector('button[type=submit]');
    const originalText = btn.innerHTML;
    btn.disabled = true; btn.textContent = 'Actualizando...';
    try {
        const res = await fetch('../clinical_core/perfil_update.php', {method:'POST', body:fd});
        const data = await res.json();
        if (data.ok) {
            e.target.reset();
            Swal.fire({title:'¡Contraseña actualizada!', icon:'success', timer:2000, showConfirmButton:false, background:'#0d1f3c', color:'#fff'});
        } else {
            Swal.fire({title:'Error', text:data.msg, icon:'error', background:'#0d1f3c', color:'#fff'});
        }
    } catch(e) {
        Swal.fire({title:'Error de conexión', icon:'error', background:'#0d1f3c', color:'#fff'});
    }
    btn.disabled = false; btn.innerHTML = originalText;
}

function togglePasswordVis(btn) {
    const inp = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('.material-symbols-outlined');
    if (inp.type === 'password') { inp.type = 'text'; icon.textContent = 'visibility_off'; }
    else { inp.type = 'password'; icon.textContent = 'visibility'; }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Gráfico de Ventas ────────────────────────────────────────────────────────
const salesData = <?= json_encode($salesData) ?>;
let salesChart = null;

function initChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    
    Chart.defaults.color = 'rgba(255, 255, 255, 0.5)';
    Chart.defaults.font.family = "'Inter', sans-serif";
    
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.daily.labels,
            datasets: [{
                label: 'Ingresos (MXN)',
                data: salesData.daily.data,
                borderColor: '#4a90d9',
                backgroundColor: 'rgba(74, 144, 217, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#071628',
                pointBorderColor: '#4a90d9',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(7, 22, 40, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#4a90d9',
                    borderColor: 'rgba(74, 144, 217, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString('es-MX', {minimumFractionDigits: 2}) + ' MXN';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: {
                        callback: function(value) { return '$' + value.toLocaleString('es-MX'); }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
}

function updateChart(period) {
    if (!salesChart) return;
    
    const btnDaily = document.getElementById('btnChartDaily');
    const btnMonthly = document.getElementById('btnChartMonthly');
    
    if (period === 'daily') {
        btnDaily.className = "px-4 py-1.5 text-xs font-bold rounded-md bg-primary text-white shadow transition-all";
        btnMonthly.className = "px-4 py-1.5 text-xs font-bold rounded-md text-on-surface-variant hover:text-white transition-all";
    } else {
        btnMonthly.className = "px-4 py-1.5 text-xs font-bold rounded-md bg-primary text-white shadow transition-all";
        btnDaily.className = "px-4 py-1.5 text-xs font-bold rounded-md text-on-surface-variant hover:text-white transition-all";
    }
    
    salesChart.data.labels = salesData[period].labels;
    salesChart.data.datasets[0].data = salesData[period].data;
    salesChart.update();
}

document.addEventListener('DOMContentLoaded', () => {
    initChart();
});
</script>

<?php include('../includes/footer.php'); ?>
