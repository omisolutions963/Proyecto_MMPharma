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
             FROM admin_usuarios WHERE email = ? LIMIT 1"
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

try { $totalProd     = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos")->fetchColumn(); } catch(Exception $e){}
try { $totalRedFria  = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE tipo='RED FRIA'")->fetchColumn(); } catch(Exception $e){}
$totalSeco = $totalProd - $totalRedFria;
try { $sinPrecio     = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE precio_farmacia=0 AND precio_distribuidor=0")->fetchColumn(); } catch(Exception $e){}
try { $totalClientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios")->fetchColumn(); } catch(Exception $e){}
try { $clientesActivos=(int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='ACTIVO'")->fetchColumn(); } catch(Exception $e){}
try { $solPendientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn(); } catch(Exception $e){}
try { $contactoNuevos= (int)$pdo->query("SELECT COUNT(*) FROM clientes_contacto_mensajes WHERE leido=0")->fetchColumn(); } catch(Exception $e){}
try { $topProductos  = $pdo->query("SELECT nombre, precio_farmacia FROM catalogo_productos WHERE precio_farmacia > 0 ORDER BY precio_farmacia DESC LIMIT 6")->fetchAll(); } catch(Exception $e){}
try { $ultimasSolicitudes = $pdo->query("SELECT razon_social, tipo_cliente, email, estatus, created_at FROM clientes_solicitudes_registro ORDER BY created_at DESC LIMIT 5")->fetchAll(); } catch(Exception $e){}

$salesData = ['daily' => ['labels' => [], 'data' => []], 'monthly' => ['labels' => [], 'data' => []]];
$topProductosVentas = [];
try {
    // Top 10 Productos
    $sqlTop = "SELECT d.nombre_producto, SUM(d.cantidad) as total_vendido, SUM(d.subtotal) as ingresos 
               FROM clientes_pedidos_detalle d 
               JOIN clientes_pedidos p ON d.pedido_id = p.id 
               WHERE p.estado_envio != 'CANCELADO' 
               GROUP BY d.producto_id, d.nombre_producto 
               ORDER BY total_vendido DESC LIMIT 10";
    $topProductosVentas = $pdo->query($sqlTop)->fetchAll(PDO::FETCH_ASSOC);

    // Sales Daily
    $sqlDaily = "SELECT DATE(fecha_pedido) as d, SUM(monto_total) as t 
                 FROM clientes_pedidos WHERE estado_envio != 'CANCELADO' AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
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
                   FROM clientes_pedidos WHERE estado_envio != 'CANCELADO' AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) 
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

<main class="ml-64 pt-8 px-8 pb-12 min-h-screen bg-background text-on-surface">

<!-- ══ WELCOME HERO ══════════════════════════════════════════════════════════ -->
<div class="relative rounded-2xl overflow-hidden mb-8 p-8 animate-reveal"
     style="background:linear-gradient(135deg,#002e1c 0%,#005132 50%,#008151 100%);
            border:1px solid rgba(0,129,81,0.2)">
    <!-- Glow orbs -->
    <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full"
         style="background:radial-gradient(circle,rgba(0,129,81,0.15) 0%,transparent 70%)"></div>
    <div class="absolute -bottom-8 left-1/3 w-48 h-48 rounded-full"
         style="background:radial-gradient(circle,rgba(52,196,122,0.08) 0%,transparent 70%)"></div>
    <div class="relative z-10 flex items-center justify-between gap-6 flex-wrap">
        <div>
            <p class="text-emerald-400/70 text-xs font-bold uppercase tracking-widest mb-2">
                <?php
                $dias = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
                $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
                echo $dias[date('l')] . ', ' . date('d') . ' de ' . $meses[date('F')] . ' ' . date('Y');
                ?>
            </p>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">
                Bienvenido, <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?>
            </h1>
            <p class="text-emerald-200/60 text-sm mt-1 max-w-lg">
                Panel de control MMPharma — <?= number_format($totalProd) ?> productos activos en catálogo.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="../G_Productos/productos.php"
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all
                      text-white hover:scale-105"
               style="background:rgba(0,129,81,0.25);border:1px solid rgba(0,129,81,0.4)">
                <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                Ver Catálogo
            </a>
            <a href="../S_Registro/solicitudes.php"
               class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all
                      hover:scale-105"
               style="background:#008151;color:#fff;border:1px solid #00a669">
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
       style="background:#071a10;border-left:3px solid #008151;border-top:1px solid rgba(0,129,81,0.15)">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(0,129,81,0.15)">
            <span class="material-symbols-outlined text-[18px] text-emerald-500">pending</span>
        </div>
        <div>
            <p class="text-xs font-bold text-emerald-500"><?= $solPendientes ?> solicitudes pendientes</p>
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
    <div class="relative p-6 rounded-2xl overflow-hidden card-glow animate-reveal" style="animation-delay: 0.1s;
         background:linear-gradient(135deg,#072115,#0b2d1e);border:1px solid rgba(0,129,81,0.2)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#008151,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,129,81,0.2)">
                    <span class="material-symbols-outlined text-[20px] text-emerald-500"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">inventory_2</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider px-2 py-1 rounded-full"
                      style="background:rgba(0,129,81,0.15)">TOTAL</span>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Productos</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalProd) ?></h3>
            <p class="text-on-surface-variant text-[11px] mt-2">en catálogo activo</p>
        </div>
    </div>

    <!-- Cadena Seca -->
    <div class="relative p-6 rounded-2xl overflow-hidden animate-reveal" style="animation-delay: 0.2s;
         background:linear-gradient(135deg,#072115,#0b2d1e);border:1px solid rgba(0,129,81,0.15)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#00a669,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,129,81,0.15)">
                    <span class="material-symbols-outlined text-[20px] text-secondary"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">light_mode</span>
                </div>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Cadena Seca</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalSeco) ?></h3>
            <div class="mt-3 w-full h-1 rounded-full" style="background:rgba(255,255,255,0.1)">
                <div class="h-full rounded-full" style="background:#00a669;width:<?= $totalProd>0?round($totalSeco/$totalProd*100):0 ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Red Fría -->
    <div class="relative p-6 rounded-2xl overflow-hidden animate-reveal" style="animation-delay: 0.3s;
         background:linear-gradient(135deg,#072115,#0b2d1e);border:1px solid rgba(52,196,122,0.2)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#34c47a,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(14,165,233,0.15)">
                    <span class="material-symbols-outlined text-[20px] text-sky-500"
                          style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">ac_unit</span>
                </div>
                <span class="text-[10px] font-bold text-sky-500 uppercase tracking-wider px-2 py-1 rounded-full"
                      style="background:rgba(14,165,233,0.1)">FRÍO</span>
            </div>
            <p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Red Fría</p>
            <h3 class="text-3xl font-extrabold text-white tracking-tight"><?= number_format($totalRedFria) ?></h3>
            <div class="mt-3 w-full h-1 rounded-full" style="background:rgba(255,255,255,0.1)">
                <div class="h-full rounded-full" style="background:#0ea5e9;width:<?= $totalProd>0?round($totalRedFria/$totalProd*100):0 ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Clientes Activos -->
    <div class="relative p-6 rounded-2xl overflow-hidden animate-reveal" style="animation-delay: 0.4s;
         background:linear-gradient(135deg,#072115,#0b2d1e);border:1px solid rgba(0,129,81,0.15)">
        <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
             style="background:radial-gradient(circle,#008151,transparent)"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,129,81,0.2)">
                    <span class="material-symbols-outlined text-[20px] text-emerald-500"
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
<div class="mb-8 p-6 rounded-2xl animate-reveal" style="background:#071a10;border:1px solid rgba(0,129,81,0.15); animation-delay: 0.5s">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-500">trending_up</span>
            Rendimiento de Ventas
        </h2>
        <div class="flex bg-surface-container-lowest rounded-lg p-1 border border-outline-variant/10">
            <button onclick="updateChart('daily')" id="btnChartDaily" class="px-4 py-1.5 text-xs font-bold rounded-md bg-[#008151] text-white shadow transition-all">Diario</button>
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
        <div class="p-6 rounded-2xl animate-fade-up" style="background:#071a10;border:1px solid rgba(0,129,81,0.15); animation-delay: 0.4s">
            <h2 class="text-sm font-bold text-white mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500 text-[18px]">pie_chart</span>
                Distribución del Catálogo
            </h2>
            <div class="space-y-5">
                <?php
                $bars = [
                    ['label'=>'Cadena Seca',    'val'=>$totalSeco,    'color'=>'#00a669', 'pct'=> $totalProd>0?round($totalSeco/$totalProd*100):0],
                    ['label'=>'Red Fría',        'val'=>$totalRedFria, 'color'=>'#0ea5e9', 'pct'=> $totalProd>0?round($totalRedFria/$totalProd*100):0],
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
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.6s">
            <div class="px-8 py-4 bg-surface-container-low border-b border-outline-variant/10 flex items-center justify-between">
                <h2 class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500 text-[18px]">list_alt</span>
                    Últimas Solicitudes
                </h2>
                <a href="../S_Registro/solicitudes.php" class="text-[10px] font-black text-emerald-500 hover:underline uppercase tracking-widest">
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
                    <span class="text-xs font-black text-emerald-500">
                        <?= strtoupper(substr($s['razon_social'],0,2)) ?>
                    </span>
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-bold text-on-surface truncate"><?= htmlspecialchars($s['razon_social']) ?></p>
                    <p class="text-[10px] text-on-surface-variant font-bold uppercase tracking-wider"><?= $s['tipo_cliente'] ?> · <?= date('d/m/Y', strtotime($s['created_at'])) ?></p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase border border-outline-variant/10 <?= $badge['bg'] ?> <?= $badge['color'] === 'text-primary' ? 'text-emerald-500' : $badge['color'] ?>">
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
        <div class="p-6 rounded-2xl animate-fade-up" style="background:#071a10;border:1px solid rgba(0,129,81,0.15); animation-delay: 0.3s">
            <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500 text-[18px]">bolt</span>
                Acciones Rápidas
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <?php
                $links = [
                    ['href'=>'../G_Productos/productos.php', 'icon'=>'inventory_2', 'label'=>'Productos', 'color'=>'#008151'],
                    ['href'=>'../G_Productos/productos.php','icon'=>'warehouse',   'label'=>'Inventario','color'=>'#00a669'],
                    ['href'=>'../G_Clientes/clientes.php',   'icon'=>'group',       'label'=>'Clientes',  'color'=>'#34c47a'],
                    ['href'=>'../G_Pedidos/pedidos.php',     'icon'=>'shopping_cart','label'=>'Pedidos',   'color'=>'#005132'],
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
        <div class="p-6 rounded-2xl animate-fade-up" style="background:#071a10;border:1px solid rgba(0,129,81,0.15); animation-delay: 0.4s">
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
                        <div class="h-full rounded-full transition-all duration-1000" style="background:linear-gradient(90deg, #34c47a, #008151); width:<?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            <a href="../G_Pedidos/pedidos.php"
               class="flex items-center justify-center gap-2 w-full mt-6 py-3 bg-primary text-white px-6 rounded-xl font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                Ir a pedidos <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>

        <!-- Estado del Sistema -->
        <div class="p-6 rounded-2xl relative overflow-hidden animate-fade-up"
             style="background:linear-gradient(135deg,#001a10,#003822);border:1px solid rgba(0,129,81,0.25); animation-delay: 0.4s">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full"
                 style="background:radial-gradient(circle,rgba(0,129,81,0.2),transparent)"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-tertiary animate-pulse"></div>
                    <span class="text-[10px] font-bold text-tertiary uppercase tracking-widest">Sistema Activo</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-1">Portal Operativo</h3>
                <?php $pct = $totalProd>0?round(($totalProd-$sinPrecio)/$totalProd*100):0; ?>
                <p class="text-emerald-200/60 text-xs mb-4"><?= $pct ?>% del catálogo con precios activos.</p>
                <div class="w-full h-2 rounded-full mb-2" style="background:rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full" style="background:linear-gradient(90deg,#4a90d9,#34c47a);width:<?= $pct ?>%"></div>
                </div>
                <div class="flex justify-between text-[10px] text-emerald-200/40">
                    <span>0%</span>
                    <span class="font-bold text-white"><?= $pct ?>%</span>
                    <span>100%</span>
                </div>
                <p class="text-[10px] text-emerald-200/30 mt-4 uppercase tracking-widest">
                    Última sync: <?= date('d/m/Y H:i') ?>
                </p>
            </div>
        </div>

    </div><!-- /col-der -->
</div><!-- /grid -->

</main>


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
                borderColor: '#008151',
                backgroundColor: 'rgba(0, 129, 81, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#020d08',
                pointBorderColor: '#008151',
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
                    backgroundColor: 'rgba(2, 13, 8, 0.9)',
                    titleColor: '#fff',
                    bodyColor: '#008151',
                    borderColor: 'rgba(0, 129, 81, 0.3)',
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
