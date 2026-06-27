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
 $_SESSION['admin_id'] = $adminRow['id'];
 $_SESSION['admin_nombre'] = $adminRow['nombre'];
 $_SESSION['admin_email'] = $adminRow['email'];
 $_SESSION['admin_foto'] = $adminRow['foto_perfil'];
 }
 }
} catch (Exception $e) {
 // tabla administradores aún no existe → usar lo que haya en sesión
}

// ── Datos del panel ───────────────────────────────────────────────────────────
$totalProd = 0; $totalRedFria = 0; $sinPrecio = 0;
$totalClientes = 0; $clientesActivos = 0;
$solPendientes = 0; $contactoNuevos = 0;
$topProductos = []; $ultimasSolicitudes = [];

try { $totalProd = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos")->fetchColumn(); } catch(Exception $e){}
try { $totalRedFria = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE tipo='RED FRIA'")->fetchColumn(); } catch(Exception $e){}
$totalSeco = $totalProd - $totalRedFria;
try { $sinPrecio = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE precio_farmacia=0 AND precio_distribuidor=0")->fetchColumn(); } catch(Exception $e){}
try { $totalClientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios")->fetchColumn(); } catch(Exception $e){}
try { $clientesActivos=(int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='ACTIVO'")->fetchColumn(); } catch(Exception $e){}
try { $solPendientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn(); } catch(Exception $e){}
try { $contactoNuevos= (int)$pdo->query("SELECT COUNT(*) FROM clientes_contacto_mensajes WHERE leido=0")->fetchColumn(); } catch(Exception $e){}
try { $topProductos = $pdo->query("SELECT nombre, precio_farmacia FROM catalogo_productos WHERE precio_farmacia > 0 ORDER BY precio_farmacia DESC LIMIT 6")->fetchAll(); } catch(Exception $e){}
try { $ultimasSolicitudes = $pdo->query("SELECT razon_social, tipo_cliente, email, estatus, created_at FROM clientes_solicitudes_registro ORDER BY created_at DESC LIMIT 5")->fetchAll(); } catch(Exception $e){}

$salesData = [
  'daily' => ['labels' => [], 'data' => [], 'orders' => []],
  'monthly' => ['labels' => [], 'data' => [], 'orders' => []]
];
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
  $sqlDaily = "SELECT DATE(fecha_pedido) as d, SUM(monto_total) as t, COUNT(id) as c 
  FROM clientes_pedidos WHERE estado_envio != 'CANCELADO' AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
  GROUP BY DATE(fecha_pedido)";
  $resDaily = $pdo->query($sqlDaily)->fetchAll(PDO::FETCH_ASSOC);
  $dMap = [];
  $dCount = [];
  foreach($resDaily as $r) {
      $dMap[$r['d']] = (float)$r['t'];
      $dCount[$r['d']] = (int)$r['c'];
  }
  for ($i = 29; $i >= 0; $i--) {
      $date = date('Y-m-d', strtotime("-$i days"));
      $salesData['daily']['labels'][] = date('d/m', strtotime("-$i days"));
      $salesData['daily']['data'][] = $dMap[$date] ?? 0;
      $salesData['daily']['orders'][] = $dCount[$date] ?? 0;
  }

  // Sales Monthly
  $sqlMonthly = "SELECT DATE_FORMAT(fecha_pedido, '%Y-%m') as m, SUM(monto_total) as t, COUNT(id) as c 
  FROM clientes_pedidos WHERE estado_envio != 'CANCELADO' AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH) 
  GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')";
  $resMonthly = $pdo->query($sqlMonthly)->fetchAll(PDO::FETCH_ASSOC);
  $mMap = [];
  $mCount = [];
  foreach($resMonthly as $r) {
      $mMap[$r['m']] = (float)$r['t'];
      $mCount[$r['m']] = (int)$r['c'];
  }
  
  $mesesES = ['Jan'=>'Ene','Feb'=>'Feb','Mar'=>'Mar','Apr'=>'Abr','May'=>'May','Jun'=>'Jun','Jul'=>'Jul','Aug'=>'Ago','Sep'=>'Sep','Oct'=>'Oct','Nov'=>'Nov','Dec'=>'Dic'];
  for ($i = 11; $i >= 0; $i--) {
      $month = date('Y-m', strtotime("first day of -$i month"));
      $mEn = date('M', strtotime("first day of -$i month"));
      $label = $mesesES[$mEn] . ' ' . date('y', strtotime("first day of -$i month"));
      $salesData['monthly']['labels'][] = $label;
      $salesData['monthly']['data'][] = $mMap[$month] ?? 0;
      $salesData['monthly']['orders'][] = $mCount[$month] ?? 0;
  }
} catch (Exception $e) {}

// Detección de imágenes duplicadas para mostrar alerta/error
$duplicateGroupsCount = 0;
try {
    $stmt = $pdo->query("SELECT id, imagen FROM catalogo_productos WHERE imagen IS NOT NULL AND imagen != 'PENDIENTE' AND imagen != ''");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $img_dir = '../../img/productos';
    $by_hash = [];
    foreach ($products as $p) {
        $file_path = $img_dir . '/' . $p['imagen'];
        if (is_file($file_path)) {
            $md5 = md5_file($file_path);
            if ($md5) {
                $by_hash[$md5][] = $p;
            }
        }
    }
    foreach ($by_hash as $hash => $list) {
        if (count($list) > 1) {
            $duplicateGroupsCount++;
        }
    }
} catch (Exception $e) {}

// Detección de productos sin stock
$sinStock = 0;
try {
    $sinStock = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos p LEFT JOIN admin_inventario_stock s ON p.id = s.producto_id WHERE COALESCE(s.stock_actual, 0) <= 0")->fetchColumn();
} catch (Exception $e) {}

// Detección de productos sin imagen
$sinImagen = 0;
try {
    $sinImagen = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE imagen IS NULL OR imagen = 'PENDIENTE' OR imagen = ''")->fetchColumn();
} catch (Exception $e) {}

$pageTitle = 'MMPharma Portal - Dashboard';
$activePage = 'dashboard';
include('../includes/header.php');
include('../includes/sidebar.php');
?>

<main class="ml-64 pt-8 px-8 pb-12 min-h-screen bg-background text-on-surface">

<!-- ══ WELCOME HERO ══════════════════════════════════════════════════════════ -->
<div class="relative rounded-2xl overflow-hidden mb-8 p-8 animate-reveal"
 style="background:linear-gradient(135deg,#003e79 0%,#1e60aa 100%);
 border:1px solid rgba(0,62,121,0.2)">
 <!-- Glow orbs -->
 <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full"
 style="background:radial-gradient(circle,rgba(0,62,121,0.15) 0%,transparent 70%)"></div>
 <div class="absolute -bottom-8 left-1/3 w-48 h-48 rounded-full"
 style="background:radial-gradient(circle,rgba(44,161,181,0.08) 0%,transparent 70%)"></div>
 <div class="relative z-10 flex items-center justify-between gap-6 flex-wrap">
 <div>
 <p class="text-sky-300/70 text-xs font-bold uppercase tracking-widest mb-2">
 <?php
 $dias = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
 $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
 echo $dias[date('l')] . ', ' . date('d') . ' de ' . $meses[date('F')] . ' ' . date('Y');
 ?>
 </p>
 <h1 class="text-3xl font-extrabold text-white tracking-tight">
 Bienvenido, <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?>
 </h1>
 <p class="text-sky-200/80 text-sm mt-1 max-w-lg">
 Panel de control MMPharma — <?= number_format($totalProd) ?> productos activos en catálogo.
 </p>
 </div>
 <div class="flex items-center gap-3">
 <a href="../g_productos/productos.php"
 class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all
 hover:scale-105"
 style="background:#003e79;color:#fff;border:1px solid #1e60aa">
 <span class="material-symbols-outlined text-[18px]">inventory_2</span>
 Ver catálogo
 </a>
 <a href="../s_registro/solicitudes.php"
 class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all
 hover:scale-105"
 style="background:#003e79;color:#fff;border:1px solid #1e60aa">
 <span class="material-symbols-outlined text-[18px]">list_alt</span>
 Solicitudes <?php if($solPendientes): ?>
 <span class="ml-1 bg-white/20 text-on-surface text-xs font-black px-1.5 py-0.5 rounded-full"><?= $solPendientes ?></span>
 <?php endif; ?>
 </a>
 </div>
 </div>
</div>

<!-- ══ ALERTAS ══════════════════════════════════════════════════════════════ -->
<?php if ($sinPrecio > 0 || $solPendientes > 0 || $contactoNuevos > 0 || $duplicateGroupsCount > 0 || $sinStock > 0 || $sinImagen > 0): ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <?php if ($sinPrecio > 0): ?>
  <a href="../g_productos/productos.php?filtro=sin_precio" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
  style="background:#fffbeb;border-left:3px solid #f59e0b;border-top:1px solid rgba(245,158,11,0.15)">
  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.15)">
  <span class="material-symbols-outlined text-[18px]" style="color:#f59e0b">price_check</span>
  </div>
  <div>
  <p class="text-xs font-bold" style="color:#d97706"><?= $sinPrecio ?> productos sin precio</p>
  <p class="text-[10px]" style="color:rgba(217,119,6,0.6)">Requieren actualización en catálogo</p>
  </div>
  </a>
  <?php endif; ?>
  <?php if ($sinStock > 0): ?>
  <a href="../g_productos/productos.php?filtro=sin_stock" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
  style="background:#fffbeb;border-left:3px solid #f59e0b;border-top:1px solid rgba(245,158,11,0.15)">
  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.15)">
  <span class="material-symbols-outlined text-[18px]" style="color:#f59e0b">production_quantity_limits</span>
  </div>
  <div>
  <p class="text-xs font-bold" style="color:#d97706"><?= $sinStock ?> productos sin stock</p>
  <p class="text-[10px]" style="color:rgba(217,119,6,0.6)">Existencias agotadas en inventario</p>
  </div>
  </a>
  <?php endif; ?>
  <?php if ($solPendientes > 0): ?>
  <a href="../s_registro/solicitudes.php" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
  style="background:#f0f9ff;border-left:3px solid #0ea5e9;border-top:1px solid rgba(14,165,233,0.15)">
  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(14,165,233,0.15)">
  <span class="material-symbols-outlined text-[18px] text-sky-500">pending</span>
  </div>
  <div>
  <p class="text-xs font-bold text-sky-600"><?= $solPendientes ?> solicitudes pendientes</p>
  <p class="text-[10px] text-slate-500">Esperan revisión y aprobación</p>
  </div>
  </a>
  <?php endif; ?>
  <?php if ($contactoNuevos > 0): ?>
  <div class="flex items-center gap-3 p-4 rounded-xl"
  style="background:#f0fdfa;border-left:3px solid #14b8a6;border-top:1px solid rgba(20,184,166,0.15)">
  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(20,184,166,0.15)">
  <span class="material-symbols-outlined text-[18px] text-teal-500">mark_email_unread</span>
  </div>
  <div>
  <p class="text-xs font-bold text-teal-600"><?= $contactoNuevos ?> mensajes nuevos</p>
  <p class="text-[10px] text-slate-500">En el formulario de contacto</p>
  </div>
  </div>
  <?php endif; ?>
  <?php if ($duplicateGroupsCount > 0): ?>
  <a href="../g_productos/sync_images.php" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
  style="background:#fffbeb;border-left:3px solid #f59e0b;border-top:1px solid rgba(245,158,11,0.15)">
  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.15)">
  <span class="material-symbols-outlined text-[18px]" style="color:#f59e0b">content_copy</span>
  </div>
  <div>
  <p class="text-xs font-bold" style="color:#d97706"><?= $duplicateGroupsCount ?> grupos duplicados</p>
  <p class="text-[10px]" style="color:rgba(217,119,6,0.6)">Imágenes repetidas detectadas en catálogo</p>
  </div>
  </a>
  <?php endif; ?>
  <?php if ($sinImagen > 0): ?>
  <a href="../g_productos/productos.php?filtro=sin_imagen" class="flex items-center gap-3 p-4 rounded-xl transition-all hover:scale-[1.01]"
  style="background:#fffbeb;border-left:3px solid #f59e0b;border-top:1px solid rgba(245,158,11,0.15)">
  <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.15)">
  <span class="material-symbols-outlined text-[18px]" style="color:#f59e0b">hide_image</span>
  </div>
  <div>
  <p class="text-xs font-bold" style="color:#d97706"><?= $sinImagen ?> productos sin imagen</p>
  <p class="text-[10px]" style="color:rgba(217,119,6,0.6)">Falta cargar imagen del producto</p>
  </div>
  </a>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ══ KPI CARDS ═════════════════════════════════════════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

 <!-- Total Productos -->
 <div class="relative p-6 rounded-2xl overflow-hidden shadow-sm animate-reveal" style="animation-delay: 0.1s;
 background:#ffffff;border:1px solid rgba(0,62,121,0.15)">
 <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
 style="background:radial-gradient(circle,#003e79,transparent)"></div>
 <div class="relative z-10">
 <div class="flex items-center justify-between mb-4">
 <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,62,121,0.1)">
 <span class="material-symbols-outlined text-[20px]" style="color:#003e79"
 style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">inventory_2</span>
 </div>
 <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full"
 style="color:#003e79;background:rgba(0,62,121,0.1)">TOTAL</span>
 </div>
 <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Productos</p>
 <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight"><?= number_format($totalProd) ?></h3>
 <p class="text-slate-500 text-[11px] mt-2">en catálogo activo</p>
 </div>
 </div>

 <!-- Cadena Seca -->
 <div class="relative p-6 rounded-2xl overflow-hidden shadow-sm animate-reveal" style="animation-delay: 0.2s;
 background:#ffffff;border:1px solid rgba(30,96,170,0.15)">
 <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
 style="background:radial-gradient(circle,#1e60aa,transparent)"></div>
 <div class="relative z-10">
 <div class="flex items-center justify-between mb-4">
 <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(30,96,170,0.1)">
 <span class="material-symbols-outlined text-[20px]" style="color:#1e60aa"
 style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">light_mode</span>
 </div>
 </div>
 <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Cadena Seca</p>
 <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight"><?= number_format($totalSeco) ?></h3>
 <div class="mt-3 w-full h-1 rounded-full" style="background:rgba(0,0,0,0.05)">
 <div class="h-full rounded-full" style="background:#1e60aa;width:<?= $totalProd>0?round($totalSeco/$totalProd*100):0 ?>%"></div>
 </div>
 </div>
 </div>

 <!-- Red Fría -->
 <div class="relative p-6 rounded-2xl overflow-hidden shadow-sm animate-reveal" style="animation-delay: 0.3s;
 background:#ffffff;border:1px solid rgba(44,161,181,0.2)">
 <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
 style="background:radial-gradient(circle,#2ca1b5,transparent)"></div>
 <div class="relative z-10">
 <div class="flex items-center justify-between mb-4">
 <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(44,161,181,0.1)">
 <span class="material-symbols-outlined text-[20px]" style="color:#2ca1b5"
 style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">ac_unit</span>
 </div>
 <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full"
 style="color:#2ca1b5;background:rgba(44,161,181,0.1)">FRÍO</span>
 </div>
 <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Red Fría</p>
 <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight"><?= number_format($totalRedFria) ?></h3>
 <div class="mt-3 w-full h-1 rounded-full" style="background:rgba(0,0,0,0.05)">
 <div class="h-full rounded-full" style="background:#2ca1b5;width:<?= $totalProd>0?round($totalRedFria/$totalProd*100):0 ?>%"></div>
 </div>
 </div>
 </div>

 <!-- Clientes Activos -->
 <div class="relative p-6 rounded-2xl overflow-hidden shadow-sm animate-reveal" style="animation-delay: 0.4s;
 background:#ffffff;border:1px solid rgba(0,62,121,0.15)">
 <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10"
 style="background:radial-gradient(circle,#003e79,transparent)"></div>
 <div class="relative z-10">
 <div class="flex items-center justify-between mb-4">
 <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,62,121,0.1)">
 <span class="material-symbols-outlined text-[20px]" style="color:#003e79"
 style="font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">group</span>
 </div>
 </div>
 <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mb-1">Clientes Activos</p>
 <h3 class="text-3xl font-extrabold text-slate-800 tracking-tight"><?= number_format($clientesActivos) ?></h3>
 <p class="text-slate-500 text-[11px] mt-2">de <?= number_format($totalClientes) ?> registrados</p>
 </div>
 </div>
</div>

<!-- ══ GRÁFICO DE VENTAS ═════════════════════════════════════════════════════ -->
<div class="mb-8 p-6 rounded-2xl shadow-sm animate-reveal" style="background:#ffffff;border:1px solid rgba(0,62,121,0.15); animation-delay: 0.5s">
 <div class="flex items-center justify-between mb-6">
 <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
 <span class="material-symbols-outlined text-sky-500">trending_up</span>
 Rendimiento de ventas
 </h2>
 <div class="flex bg-slate-100 rounded-lg p-1 border border-slate-200">
 <button onclick="updateChart('daily')" id="btnChartDaily" class="px-4 py-1.5 text-xs font-bold rounded-md bg-[#003e79] text-white transition-all">Diario</button>
 <button onclick="updateChart('monthly')" id="btnChartMonthly" class="px-4 py-1.5 text-xs font-bold rounded-md text-slate-500 hover:text-slate-800 transition-all">Mensual</button>
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
 <div class="p-6 rounded-2xl shadow-sm animate-fade-up" style="background:#ffffff;border:1px solid rgba(0,62,121,0.15); animation-delay: 0.4s">
 <h2 class="text-sm font-bold text-slate-800 mb-6 flex items-center gap-2">
 <span class="material-symbols-outlined text-sky-500 text-[18px]">pie_chart</span>
 Distribución del catálogo
 </h2>
 <div class="space-y-5">
 <?php
 $bars = [
 ['label'=>'Cadena Seca', 'val'=>$totalSeco, 'color'=>'#1e60aa', 'pct'=> $totalProd>0?round($totalSeco/$totalProd*100):0],
 ['label'=>'Red Fría', 'val'=>$totalRedFria, 'color'=>'#2ca1b5', 'pct'=> $totalProd>0?round($totalRedFria/$totalProd*100):0],
 ['label'=>'Sin Precio', 'val'=>$sinPrecio, 'color'=>'#f59e0b', 'pct'=> $totalProd>0?round($sinPrecio/$totalProd*100):0],
 ];
 foreach ($bars as $b): ?>
 <div>
 <div class="flex justify-between items-center mb-1.5">
 <span class="text-xs font-semibold text-slate-500"><?= $b['label'] ?></span>
 <div class="flex items-center gap-2">
 <span class="text-xs font-bold text-on-surface"><?= number_format($b['val']) ?></span>
 <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
 style="background:<?= $b['color'] ?>22;color:<?= $b['color'] ?>"><?= $b['pct'] ?>%</span>
 </div>
 </div>
 <div class="w-full h-2 rounded-full" style="background:rgba(255,255,255,0.07)">
 <div class="h-full rounded-full transition-all duration-700"
 style="background:<?= $b['color'] ?>;width:<?= $b['pct'] ?>%;
 box-:0 0 8px <?= $b['color'] ?>66"></div>
 </div>
 </div>
 <?php endforeach; ?>
 </div>
 </div>

 <!-- Últimas solicitudes -->
 <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-reveal" style="animation-delay: 0.6s">
 <div class="px-8 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
 <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-500 flex items-center gap-2">
 <span class="material-symbols-outlined text-sky-500 text-[18px]">list_alt</span>
 Últimas solicitudes
 </h2>
 <a href="../s_registro/solicitudes.php" class="text-[10px] font-black text-sky-500 hover:underline uppercase tracking-widest">
 Ver todas →
 </a>
 </div>
 <?php if (empty($ultimasSolicitudes)): ?>
 <p class="px-8 py-10 text-center text-slate-500 text-sm">No se encontraron solicitudes.</p>
 <?php else: ?>
 <div class="divide-y divide-slate-100">
 <?php foreach ($ultimasSolicitudes as $s):
 $badge = match($s['estatus']) {
 'APROBADA' => ['color'=>'text-teal-600','bg'=>'bg-teal-50','label'=>'Aprobada'],
 'RECHAZADA' => ['color'=>'text-red-600','bg'=>'bg-red-50','label'=>'Rechazada'],
 default => ['color'=>'text-sky-600','bg'=>'bg-sky-50','label'=>'Pendiente'],
 };
 ?>
 <div class="px-8 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
 <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-slate-100 border border-slate-200">
 <span class="text-xs font-black text-sky-600">
 <?= strtoupper(substr($s['razon_social'],0,2)) ?>
 </span>
 </div>
 <div class="flex-1 overflow-hidden">
 <p class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars($s['razon_social']) ?></p>
 <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider"><?= $s['tipo_cliente'] ?> · <?= date('d/m/Y', strtotime($s['created_at'])) ?></p>
 </div>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase border border-slate-200 <?= $badge['bg'] ?> <?= $badge['color'] ?>">
 <?= $badge['label'] ?>
 </span>
 </div>
 <?php endforeach; ?>
 </div>
 <?php endif; ?>
 <div class="bg-slate-50 px-8 py-4 flex justify-between items-center border-t border-slate-200">
 <p class="text-[11px] font-bold text-slate-500">
 Mostrando <span class="font-black text-slate-800"><?= count($ultimasSolicitudes) ?></span> solicitudes recientes.
 </p>
 </div>
 </div>
 </div>

 <!-- Columna der: top productos + acciones rápidas -->
 <div class="space-y-6 animate-fade-up" style="animation-delay: 0.2s">

 <!-- Acciones Rápidas -->
 <div class="p-6 rounded-2xl shadow-sm animate-fade-up" style="background:#ffffff;border:1px solid rgba(0,62,121,0.15); animation-delay: 0.3s">
 <h2 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
 <span class="material-symbols-outlined text-sky-500 text-[18px]">bolt</span>
 Acciones rápidas
 </h2>
 <div class="grid grid-cols-2 gap-3">
 <?php
 $links = [
 ['href'=>'../g_productos/productos.php', 'icon'=>'inventory_2', 'label'=>'Productos', 'color'=>'#003e79'],
 ['href'=>'../g_productos/productos.php','icon'=>'warehouse', 'label'=>'Inventario','color'=>'#1e60aa'],
 ['href'=>'../g_clientes/clientes.php', 'icon'=>'group', 'label'=>'Clientes', 'color'=>'#2ca1b5'],
 ['href'=>'../g_pedidos/pedidos.php', 'icon'=>'shopping_cart','label'=>'Pedidos', 'color'=>'#002a52'],
 ];
 foreach ($links as $l): ?>
 <a href="<?= $l['href'] ?>"
 class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all hover:scale-105 hover:bg-slate-50 text-center"
 style="border:1px solid rgba(0,0,0,0.05)">
 <span class="material-symbols-outlined text-[24px]"
 style="color:<?= $l['color'] ?>;font-variation-settings:'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24">
 <?= $l['icon'] ?>
 </span>
 <span class="text-xs font-semibold text-slate-600"><?= $l['label'] ?></span>
 </a>
 <?php endforeach; ?>
 </div>
 </div>

 <!-- Top 10 Productos Más Vendidos -->
 <div class="p-6 rounded-2xl shadow-sm animate-fade-up" style="background:#ffffff;border:1px solid rgba(0,62,121,0.15); animation-delay: 0.4s">
 <h2 class="text-sm font-bold text-slate-800 mb-5 flex items-center gap-2">
 <span class="material-symbols-outlined text-sky-500 text-[18px]">workspace_premium</span>
 Top 10 más vendidos
 </h2>
 <div class="space-y-4">
 <?php if (empty($topProductosVentas)): ?>
 <p class="text-xs text-slate-500 text-center py-4">Aún no hay ventas registradas.</p>
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
 <span class="text-[10px] font-black text-sky-500 w-4">#<?= $i+1 ?></span>
 <span class="text-xs font-semibold text-slate-800 truncate" title="<?= htmlspecialchars($p['nombre_producto']) ?>"><?= htmlspecialchars($p['nombre_producto']) ?></span>
 </div>
 <span class="text-[10px] font-bold text-slate-500 ml-2 whitespace-nowrap"><?= number_format($p['total_vendido']) ?> uds</span>
 </div>
 <div class="w-full h-1.5 rounded-full" style="background:rgba(0,0,0,0.05)">
 <div class="h-full rounded-full transition-all duration-1000" style="background:linear-gradient(90deg, #1e60aa, #003e79); width:<?= $pct ?>%"></div>
 </div>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>
 </div>
 <a href="../g_pedidos/pedidos.php"
 class="flex items-center justify-center gap-2 w-full mt-6 py-3 bg-[#003e79] text-white px-6 rounded-xl font-bold hover:opacity-90 transition-all">
 Ir a pedidos <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
 </a>
 </div>

 <!-- Estado del Sistema -->
 <div class="p-6 rounded-2xl relative overflow-hidden shadow-sm animate-fade-up"
 style="background:linear-gradient(135deg,#001a33,#002a52);border:1px solid rgba(0,62,121,0.25); animation-delay: 0.4s">
 <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full"
 style="background:radial-gradient(circle,rgba(0,62,121,0.2),transparent)"></div>
 <div class="relative z-10">
 <div class="flex items-center gap-2 mb-3">
 <div class="w-2.5 h-2.5 rounded-full bg-sky-400 animate-pulse"></div>
 <span class="text-[10px] font-bold text-sky-400 uppercase tracking-widest">Sistema Activo</span>
 </div>
 <h3 class="text-lg font-bold text-on-surface mb-1">Portal Operativo</h3>
 <?php $pct = $totalProd>0?round(($totalProd-$sinPrecio)/$totalProd*100):0; ?>
 <p class="text-sky-200/60 text-xs mb-4"><?= $pct ?>% del catálogo con precios activos.</p>
 <div class="w-full h-2 rounded-full mb-2" style="background:rgba(255,255,255,0.1)">
 <div class="h-full rounded-full" style="background:linear-gradient(90deg,#2ca1b5,#1e60aa);width:<?= $pct ?>%"></div>
 </div>
 <div class="flex justify-between text-[10px] text-sky-200/40">
 <span>0%</span>
 <span class="font-bold text-on-surface"><?= $pct ?>%</span>
 <span>100%</span>
 </div>
 <p class="text-[10px] text-sky-200/30 mt-4 uppercase tracking-widest">
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
  
 Chart.defaults.color = '#64748b';
 Chart.defaults.font.family = "'Inter', sans-serif";
  
 salesChart = new Chart(ctx, {
 data: {
 labels: salesData.daily.labels,
 datasets: [
   {
     type: 'line',
     label: 'Ingresos (MXN)',
     data: salesData.daily.data,
     borderColor: '#2ca1b5',
     backgroundColor: 'rgba(44, 161, 181, 0.1)',
     borderWidth: 3,
     pointBackgroundColor: '#eef4fc',
     pointBorderColor: '#2ca1b5',
     pointBorderWidth: 2,
     pointRadius: 4,
     pointHoverRadius: 6,
     fill: true,
     tension: 0.4,
     yAxisID: 'y'
   },
   {
     type: 'bar',
     label: 'Pedidos',
     data: salesData.daily.orders,
     borderColor: '#0ea5e9',
     backgroundColor: 'rgba(14, 165, 233, 0.25)',
     borderWidth: 1.5,
     borderRadius: 6,
     barThickness: 16,
     yAxisID: 'y1'
   }
 ]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: { 
   display: true,
   position: 'top',
   labels: {
     color: '#475569',
     font: { size: 11, weight: 'bold' }
   }
 },
 tooltip: {
   backgroundColor: '#0f172a',
   titleColor: '#fff',
   bodyColor: '#cbd5e1',
   borderColor: 'rgba(44, 161, 181, 0.3)',
   borderWidth: 1,
   padding: 12,
   displayColors: true,
   callbacks: {
     label: function(context) {
       if (context.datasetIndex === 0) {
         return 'Ingresos: $' + context.parsed.y.toLocaleString('es-MX', {minimumFractionDigits: 2}) + ' MXN';
       } else {
         return 'Pedidos: ' + context.parsed.y + ' uds';
       }
     }
   }
 }
 },
 scales: {
 y: {
   type: 'linear',
   display: true,
   position: 'left',
   beginAtZero: true,
   grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
   ticks: {
     callback: function(value) { return '$' + value.toLocaleString('es-MX'); }
   }
 },
 y1: {
   type: 'linear',
   display: true,
   position: 'right',
   beginAtZero: true,
   grid: { drawOnChartArea: false },
   ticks: {
     stepSize: 1,
     callback: function(value) { 
       if (Number.isInteger(value)) {
         return value + (value === 1 ? ' pedido' : ' pedidos'); 
       }
       return null;
     }
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
  btnDaily.className = "px-4 py-1.5 text-xs font-bold rounded-md bg-[#003e79] text-white transition-all";
  btnMonthly.className = "px-4 py-1.5 text-xs font-bold rounded-md text-slate-500 hover:text-slate-800 transition-all";
 } else {
  btnMonthly.className = "px-4 py-1.5 text-xs font-bold rounded-md bg-[#003e79] text-white transition-all";
  btnDaily.className = "px-4 py-1.5 text-xs font-bold rounded-md text-slate-500 hover:text-slate-800 transition-all";
 }
 
 salesChart.data.labels = salesData[period].labels;
 salesChart.data.datasets[0].data = salesData[period].data;
 salesChart.data.datasets[1].data = salesData[period].orders;
 salesChart.update();
}

document.addEventListener('DOMContentLoaded', () => {
 initChart();
});
</script>

<?php include('../includes/footer.php'); ?>
