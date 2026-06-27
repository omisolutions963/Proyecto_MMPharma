<?php
require_once '../includes/db.php';
$pdo = getDB();

// Obtener ID del producto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
 header('Location: catalogo.php');
 exit;
}

// Buscar el producto
$stmt = $pdo->prepare("
 SELECT p.*, COALESCE(s.stock_actual, 0) as stock, c.nombre as categoria_nombre 
 FROM catalogo_productos p 
 LEFT JOIN admin_inventario_stock s ON p.id = s.producto_id 
 LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
 WHERE p.id = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
 header('Location: catalogo.php');
 exit;
}

// Lógica de visibilidad por tipo de cliente
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$is_cliente_check = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$cliente_tipo_check = $is_cliente_check ? $_SESSION['cliente_tipo'] : 'FARMACIA';

if ($cliente_tipo_check === 'EMPRESA') {
  $nombre_lower = mb_strtolower($p['nombre']);
  $sustancia_lower = mb_strtolower($p['sustancia'] ?? '');
  $es_excepcion = (
    strpos($nombre_lower, 'aspirina') !== false || strpos($sustancia_lower, 'aspirina') !== false ||
    strpos($nombre_lower, 'loratadina') !== false || strpos($sustancia_lower, 'loratadina') !== false ||
    strpos($nombre_lower, 'loratidina') !== false || strpos($sustancia_lower, 'loratidina') !== false ||
    strpos($nombre_lower, 'buscapina') !== false ||
    strpos($nombre_lower, 'butilhioscina') !== false || strpos($sustancia_lower, 'butilhioscina') !== false
  );
  if ($p['solo_empresa'] !== 'SI' && !$es_excepcion) {
    header('Location: catalogo.php');
    exit;
  }
}

// Productos relacionados (misma sustancia, diferente id)
$where_rel = ["sustancia LIKE ?", "id != ?", "codigo NOT IN ('99999999999', 'DESCUENTO')"];
$params_rel = ['%' . explode(' ', $p['sustancia'])[0] . '%', $id];

if ($cliente_tipo_check === 'EMPRESA') {
  $where_rel[] = "(solo_empresa = 'SI' OR nombre LIKE '%ASPIRINA%' OR sustancia LIKE '%ASPIRINA%' OR nombre LIKE '%LORATADINA%' OR sustancia LIKE '%LORATADINA%' OR nombre LIKE '%LORATIDINA%' OR sustancia LIKE '%LORATIDINA%' OR nombre LIKE '%BUSCAPINA%' OR nombre LIKE '%BUTILHIOSCINA%' OR sustancia LIKE '%BUTILHIOSCINA%')";
}

$rel_sql = "SELECT * FROM catalogo_productos WHERE " . implode(' AND ', $where_rel) . " LIMIT 4";
$rel = $pdo->prepare($rel_sql);
$rel->execute($params_rel);
$relacionados = $rel->fetchAll(PDO::FETCH_ASSOC);

if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
$is_cliente = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_logged_in = $is_cliente || $is_admin;
$cliente_tipo = $is_cliente ? $_SESSION['cliente_tipo'] : 'FARMACIA';

$precio_campo = 'precio_farmacia';
if ($cliente_tipo === 'DISTRIBUIDORA') $precio_campo = 'precio_distribuidor';
elseif ($cliente_tipo === 'EMPRESA') $precio_campo = 'precio_empresa';

$precio_base = (float)$p[$precio_campo];
$precio_mostrar = $precio_base;
 if (($p['en_promocion'] ?? 0) && ($p['descuento_porcentaje'] ?? 0) > 0 && (!isset($p['promocion_perfil']) || $p['promocion_perfil'] === 'TODOS' || $p['promocion_perfil'] === $cliente_tipo)) {
 $precio_mostrar = $precio_base * (1 - ($p['descuento_porcentaje'] / 100));
 }
?>

<?php
// Buscar imágenes adicionales (galería / laterales)
$imagenes_adicionales = [];
if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE') {
    $info_img = pathinfo($p['imagen']);
    $base_name = $info_img['filename'];
    $extension = $info_img['extension'] ?? 'jpg';

    for ($i = 1; $i <= 5; $i++) {
        $img_name = $base_name . '_' . $i . '.' . $extension;
        $file_path = __DIR__ . '/../img/productos/' . $img_name;
        if (file_exists($file_path)) {
            $imagenes_adicionales[] = $img_name;
        } else {
            // Probar variantes de extensión
            foreach (['jpg','JPG','png','PNG','webp','WEBP','jpeg','JPEG'] as $ext) {
                if ($ext === $extension) continue;
                $alt = $base_name . '_' . $i . '.' . $ext;
                if (file_exists(__DIR__ . '/../img/productos/' . $alt)) {
                    $imagenes_adicionales[] = $alt;
                    break;
                }
            }
        }
    }
}
?>

<?php
$titulo = htmlspecialchars($p['nombre']); 
$pagina_actual = 'catalogo'; // marca el link activo en el nav
$base = '../'; // si estás en subcarpeta como catalogo/
require_once '../includes/header.php';
?>

<!-- ═══ BREADCRUMB ═══ -->
<div class="max-w-[1600px] mx-auto px-12 py-8" data-aos="fade-down">
 <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-slate-500">
 <a href="../index/index.php" class="hover:text-primary-light transition-colors">Inicio</a>
 <span class="material-symbols-outlined text-sm">chevron_right</span>
 <a href="catalogo.php" class="hover:text-primary-light transition-colors">Catálogo</a>
 <span class="material-symbols-outlined text-sm">chevron_right</span>
 <span class="text-tertiary font-bold"><?= htmlspecialchars($p['nombre']) ?></span>
 </div>
</div>

<!-- ═══ CONTENIDO PRINCIPAL ═══ -->
<main class="max-w-[1600px] mx-auto px-12 pb-24 relative">
 <div class="<?= !$is_logged_in ? 'filter blur-[10px] opacity-40 select-none pointer-events-none' : '' ?>">
 <div class="grid md:grid-cols-2 gap-10 mb-16">

  <!-- Imagen del producto -->
  <div class="flex flex-col gap-5" data-aos="fade-right">
    <div class="flex items-center justify-center min-h-[400px] p-4 relative border border-slate-100 rounded-3xl bg-white group overflow-hidden">
      <?php if ($p['tipo'] === 'RED FRIA'): ?>
      <span class="absolute top-4 left-4 inline-flex items-center gap-1 px-3 py-1.5 bg-tertiary/10 text-tertiary text-xs font-bold rounded-full z-10">
      <span class="material-symbols-outlined text-sm">ac_unit</span>
      Requiere Red Fría
      </span>
      <?php elseif (($p['en_promocion'] ?? 0) && ($p['descuento_porcentaje'] ?? 0) > 0 && (!isset($p['promocion_perfil']) || $p['promocion_perfil'] === 'TODOS' || $p['promocion_perfil'] === $cliente_tipo)): ?>
      <span class="absolute top-4 left-4 z-10 px-3 py-1 bg-error text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-error/30">
      -<?= (float)$p['descuento_porcentaje'] ?>% DESC
      </span>
      <?php endif; ?>

      <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
      <div class="relative overflow-hidden w-full h-[380px] flex items-center justify-center cursor-zoom-in" id="zoom-container">
        <img src="../includes/image_cache.php?img=<?= urlencode($p['imagen']) ?>&w=600"
          alt="<?= htmlspecialchars($p['nombre']) ?>"
          id="main-product-image"
          class="max-h-[380px] w-auto object-contain mix-blend-multiply rounded-3xl transition-transform duration-250 origin-center">
      </div>
      <?php else: ?>
      <div class="text-center">
        <span class="material-symbols-outlined text-8xl text-slate-300">medication</span>
        <p class="text-xs text-slate-400 mt-3">Imagen próximamente</p>
      </div>
      <?php endif; ?>
    </div>

    <!-- Galería de miniaturas (Thumbnails) -->
    <?php if (!empty($imagenes_adicionales)): ?>
    <div class="flex gap-3 justify-center">
      <button type="button" class="w-16 h-16 border-2 border-primary rounded-xl overflow-hidden bg-white p-1 hover:border-primary transition-all thumbnail-btn active-thumbnail" onclick="changeProductImage('<?= htmlspecialchars($p['imagen']) ?>', this)">
        <img src="../includes/image_cache.php?img=<?= urlencode($p['imagen']) ?>&w=150" class="w-full h-full object-contain mix-blend-multiply">
      </button>
      <?php foreach ($imagenes_adicionales as $add_img): ?>
      <button type="button" class="w-16 h-16 border border-slate-200 rounded-xl overflow-hidden bg-white p-1 hover:border-primary transition-all thumbnail-btn" onclick="changeProductImage('<?= htmlspecialchars($add_img) ?>', this)">
        <img src="../includes/image_cache.php?img=<?= urlencode($add_img) ?>&w=150" class="w-full h-full object-contain mix-blend-multiply">
      </button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

 <!-- Información del producto -->
 <div class="flex flex-col justify-between" data-aos="fade-left">

 <div>
 <!-- Código -->
 <?php if (!empty($p['codigo'])): ?>
 <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">
 Código: <?= htmlspecialchars($p['codigo']) ?>
 </p>
 <?php endif; ?>

 <!-- Nombre -->
 <h1 class="text-2xl font-extrabold text-primary tracking-tight mb-3 leading-tight">
 <?= htmlspecialchars($p['nombre']) ?>
 </h1>

 <!-- Sustancia activa -->
 <div class="flex items-start gap-2 mb-6">
 <span class="material-symbols-outlined text-primary text-lg mt-0.5">science</span>
 <div>
 <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Sustancia activa</p>
 <p class="text-sm text-slate-600"><?= htmlspecialchars($p['sustancia'] ?? 'No especificada') ?></p>
 </div>
 </div>

 <!-- Aviso Red Fría -->
 <?php if ($p['tipo'] === 'RED FRIA'): ?>
 <div class="bg-tertiary/10 rounded-xl p-4 mb-6 flex gap-3">
 <span class="material-symbols-outlined text-tertiary-light text-xl flex-shrink-0">ac_unit</span>
 <div>
 <p class="text-sm font-bold text-tertiary-light mb-1">Producto de Red Fría</p>
 <p class="text-xs text-slate-400 leading-relaxed">
 Este producto requiere refrigeración. El cliente debe gestionar su propio transporte — ya sea enviando guía prepagada o mandando su transportista al almacén de MM Pharma.
 </p>
 </div>
 </div>
 <?php endif; ?>
 </div>

 <!-- Precios por nivel de cliente y Controles -->
 <?php if ($is_admin || $is_cliente): ?>
 <?php if ($is_admin): ?>
 <div class="mb-10 animate-reveal">
 <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-4 flex items-center gap-2">
 <span class="material-symbols-outlined text-sm">visibility</span> Vista de Administrador (Todos los niveles)
 </p>
 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
 <div class="bg-primary/10 p-5 rounded-2xl transition-transform hover:-translate-y-1">
 <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1 opacity-80">Farmacia</p>
 <p class="text-2xl font-black text-primary">$<?= number_format($p['precio_farmacia'], 2) ?></p>
 </div>
 <div class="bg-secondary/10 p-5 rounded-2xl transition-transform hover:-translate-y-1">
 <p class="text-[10px] font-black text-secondary uppercase tracking-widest mb-1 opacity-80">Distribuidor</p>
 <p class="text-2xl font-black text-secondary">$<?= number_format($p['precio_distribuidor'], 2) ?></p>
 </div>
 <div class="bg-tertiary/10 p-5 rounded-2xl transition-transform hover:-translate-y-1">
 <p class="text-[10px] font-black text-tertiary uppercase tracking-widest mb-1 opacity-80">Empresa</p>
 <p class="text-2xl font-black text-tertiary">$<?= number_format($p['precio_empresa'], 2) ?></p>
 </div>
 </div>
 </div>
 <?php else: ?>
 <div class="mb-10 animate-reveal">
 <?php 
 $box_class = 'bg-primary/10 text-primary';
 if ($cliente_tipo === 'DISTRIBUIDORA') $box_class = 'bg-secondary/10 text-secondary';
 elseif ($cliente_tipo === 'EMPRESA') $box_class = 'bg-tertiary/10 text-tertiary';
 ?>
 <div class="<?= $box_class ?> p-6 rounded-2xl flex items-center justify-between transition-transform hover:-translate-y-1">
  <div>
  <p class="text-[10px] font-black uppercase tracking-widest mb-1 opacity-80"><?= htmlspecialchars($cliente_tipo) ?></p>
   <?php if ($precio_mostrar < $precio_base): ?>
   <p class="text-sm line-through opacity-70 font-bold mb-1">$<?= number_format($precio_base, 2) ?></p>
   <p class="text-4xl font-black text-error flex items-center gap-2">
   $<?= number_format($precio_mostrar, 2) ?>
   <span class="text-sm font-bold bg-error text-white px-2 py-0.5 rounded-full">-<?= (float)$p['descuento_porcentaje'] ?>%</span>
   </p>
   <?php else: ?>
   <p class="text-4xl font-black">$<?= number_format($precio_mostrar, 2) ?></p>
   <?php endif; ?>
  </div>
  <span class="material-symbols-outlined text-4xl opacity-30">verified</span>
  </div>
 </div>
 <?php endif; ?>

 <!-- Controles de Carrito (Solo para Clientes) -->
 <?php if ($is_cliente): ?>
 <div class="mb-6 animate-reveal">
 <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl <?= $p['stock']>0 ? 'bg-tertiary-fixed-dim/20 text-tertiary-fixed-dim' : 'bg-error/10 text-error' ?> font-bold text-sm border <?= $p['stock']>0 ? 'border-tertiary-fixed-dim/30' : 'border-error/20' ?>">
 <span class="material-symbols-outlined text-[18px]">inventory_2</span>
 Existencias disponibles: <?= $p['stock'] ?>
 </span>
 </div>
 <div class="space-y-4">
 <div class="flex items-end gap-4">
 <div class="flex flex-col">
 <label class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Cantidad</label>
 <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl h-[58px] px-2 w-32">
 <button type="button" onclick="const qty=document.getElementById('qty'); qty.value=Math.max(1, parseInt(qty.value)-1);" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary transition-colors hover:bg-slate-200 rounded-lg">
 <span class="material-symbols-outlined">remove</span>
 </button>
 <input type="number" id="qty" value="1" min="1" class="w-full text-center font-bold text-primary bg-transparent border-none focus:ring-0 p-0 text-lg">
 <button type="button" onclick="const qty=document.getElementById('qty'); qty.value=parseInt(qty.value)+1;" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary transition-colors hover:bg-slate-200 rounded-lg">
 <span class="material-symbols-outlined">add</span>
 </button>
 </div>
 </div>
 
 <button type="button" onclick="anadirMultipleAlCarrito()" class="flex-1 h-[58px] bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 hover:-translate-y-0.5 active:scale-95 transition-all text-base flex items-center justify-center gap-2 group">
 <span class="material-symbols-outlined text-xl group-hover:translate-x-0.5 transition-transform">add_shopping_cart</span>
 Añadir al carrito
 </button>
 </div>
 <a href="catalogo.php" class="w-full h-[50px] bg-white text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 text-sm flex items-center justify-center gap-2 border border-slate-200 group">
 <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
 Volver al catálogo
 </a>
 </div>
 
 <script>
 function anadirMultipleAlCarrito() {
 try {
 const qty = parseInt(document.getElementById('qty').value) || 1;
 for(let i=0; i<qty; i++) {
 agregarAlCarrito(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= (float)$precio_mostrar ?>, '<?= htmlspecialchars(addslashes($p['imagen'] ?? '')) ?>');
 }
 } catch(e) {
 console.error("Error al añadir:", e);
 alert("Error técnico: " + e.message + "\n\nPor favor envíame este mensaje.");
 }
 }
 </script>
 <?php elseif ($is_admin): ?>
 <div class="space-y-4">
 <div class="bg-primary/5 rounded-xl p-5 flex items-center gap-4">
 <span class="material-symbols-outlined text-primary text-2xl">admin_panel_settings</span>
 <p class="text-sm text-slate-600 leading-relaxed">
 <strong class="text-primary block mb-1">Modo Administrador</strong>
 Estás visualizando el catálogo como administrador. Las funciones de cotización y carrito están deshabilitadas para este rol.
 </p>
 </div>
 <a href="catalogo.php" class="w-full h-[50px] bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 text-sm flex items-center justify-center gap-2 group">
 <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
 Volver al catálogo
 </a>
 </div>
 <?php endif; ?>
 
 <?php else: ?>
 <!-- Acciones (Usuario no logueado) -->
 <div class="space-y-4">
 <div class="bg-primary/5 rounded-xl p-5 flex items-center gap-4">
 <span class="material-symbols-outlined text-primary text-2xl">lock</span>
 <p class="text-sm text-slate-600 leading-relaxed">
 <strong class="text-primary block mb-1">¿Quieres ver precios y cotizar este producto?</strong>
 Inicia sesión como cliente o solicita acceso para visualizar nuestro catálogo con precios.
 </p>
 </div>
 <div class="flex flex-col sm:flex-row gap-4">
 <a href="../login/login.php"
 class="flex-1 h-[58px] bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 hover:-translate-y-0.5 active:scale-95 transition-all text-base flex items-center justify-center gap-2 group">
 Iniciar sesión para acceder
 <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>
 <a href="catalogo.php"
 class="px-8 h-[58px] bg-white border border-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition-all text-base flex items-center justify-center gap-2 group">
 <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
 Volver
 </a>
 </div>
 </div>
 <?php endif; ?>

 </div>
 </div>

 <!-- ═══ productos RELACIONADOS ═══ -->
 <?php if (!empty($relacionados)): ?>
 <div>
 <h2 class="text-lg font-bold text-primary mb-6">Productos con sustancia similar</h2>
 <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" data-aos="fade-up">
 <?php foreach ($relacionados as $r): ?>
 <a href="producto.php?id=<?= $r['id'] ?>"
 class="bg-white border border-slate-200 rounded-[3rem] transition-all duration-300 p-8 flex flex-col group animate-fade-up min-h-[420px] hover:-translate-y-2 hover:border-primary/30 hover:">
 
 <!-- Contenedor de Imagen -->
 <div class="w-full aspect-square flex items-center justify-center mb-6 relative group-hover:scale-105 transition-transform duration-500 overflow-hidden">
  <?php if (!empty($r['imagen']) && $r['imagen'] !== 'PENDIENTE'): ?>
  <img src="../includes/image_cache.php?img=<?= urlencode($r['imagen']) ?>&w=300"
  class="w-full h-full object-contain p-2 mix-blend-multiply rounded-2xl">
 <?php else: ?>
 <span class="material-symbols-outlined text-slate-300 text-7xl">medication</span>
 <?php endif; ?>
 <?php if ($r['tipo'] === 'RED FRIA'): ?>
 <span class="absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 bg-tertiary/10 text-tertiary rounded-full">
 <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">ac_unit</span>
 </span>
 <?php endif; ?>
 </div>

 <!-- Info -->
 <div class="flex-1">
 <p class="text-sm font-black text-slate-700 leading-tight mb-2 group-hover:text-primary transition-colors uppercase tracking-tight">
 <?= htmlspecialchars($r['nombre']) ?>
 </p>
 <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-6">
 <?= htmlspecialchars($r['sustancia'] ?? '') ?>
 </p>
 </div>

 <!-- Precio y Carrito -->
  <div class="flex items-center justify-between">
  <?php
  $r_precio_base = (float)($r[$precio_campo] ?? $r['precio_farmacia']);
  $r_precio_final = $r_precio_base;
  if (($r['en_promocion'] ?? 0) && ($r['descuento_porcentaje'] ?? 0) > 0) {
  $r_precio_final = $r_precio_base * (1 - ($r['descuento_porcentaje'] / 100));
  }
  ?>
  <?php if ($r_precio_final < $r_precio_base): ?>
  <div class="flex flex-col">
  <span class="text-[10px] text-slate-400 line-through font-bold leading-none mb-0.5">$<?= number_format($r_precio_base, 2) ?></span>
  <span class="text-xl font-black text-error leading-none">$<?= number_format($r_precio_final, 2) ?></span>
  </div>
  <?php else: ?>
  <p class="text-xl font-black text-primary">$<?= number_format($r_precio_base, 2) ?></p>
  <?php endif; ?>
  
  <?php if ($is_cliente): ?>
  <button type="button" 
  onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['nombre'])) ?>', <?= $r_precio_final ?>, '<?= htmlspecialchars(addslashes($r['imagen'] ?? '')) ?>')"
 class="w-12 h-12 rounded-2xl bg-primary/5 text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center">
 <span class="material-symbols-outlined text-2xl">shopping_cart</span>
 </button>
 <?php elseif ($is_admin): ?>
 <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center" title="Modo Administrador">
 <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
 </div>
 <?php else: ?>
 <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
 <span class="material-symbols-outlined text-2xl">lock</span>
 </div>
 <?php endif; ?>
 </div>
 </a>
 <?php endforeach; ?>
 </div>
 </div>
 <?php endif; ?>

 </div>

 <?php if (!$is_logged_in): ?>
 <!-- Overlay CTA para usuarios no registrados -->
 <div class="absolute inset-0 z-40 flex items-center justify-center bg-white/80 backdrop-blur-[4px]">
 <div class="max-w-md w-full mx-4 bg-white border border-slate-100 p-10 rounded-[2.5rem] text-center animate-reveal">
 <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-slate-100">
 <span class="material-symbols-outlined text-primary text-4xl">lock</span>
 </div>
 <h2 class="text-3xl font-black text-primary tracking-tight mb-4">Detalles exclusivos</h2>
 <p class="text-slate-500 font-medium mb-8 leading-relaxed">
 Para ver precios detallados, existencias y poder cotizar este producto, es necesario contar con una cuenta aprobada.
 </p>
 <div class="flex flex-col gap-3">
 <a href="../seleccion_registro/seleccion_registro.php" class="w-full py-4 bg-primary text-white font-semibold rounded-xl hover:bg-primary/90 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 group">
 Solicitar acceso
 <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>
 <a href="../login/login.php" class="w-full py-4 bg-white text-primary font-semibold rounded-xl hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 border border-slate-200 flex items-center justify-center gap-2 group">
 Iniciar sesión
 <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">login</span>
 </a>
 </div>
 <a href="catalogo.php" class="inline-flex items-center gap-1 mt-6 text-slate-500 font-semibold text-sm hover:text-primary transition-colors group">
 <span class="material-symbols-outlined text-base group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
 Volver al catálogo
 </a>
 </div>
 </div>
 <?php endif; ?>
</main>

<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

<script>
function changeProductImage(imgSrc, btn) {
    const mainImg = document.getElementById('main-product-image');
    if (mainImg) {
        const ts = new Date().getTime();
        mainImg.src = `../includes/image_cache.php?img=${encodeURIComponent(imgSrc)}&w=600&t=${ts}`;
    }
    document.querySelectorAll('.thumbnail-btn').forEach(t => {
        t.classList.remove('border-primary', 'border-2');
        t.classList.add('border-slate-200', 'border');
    });
    btn.classList.remove('border-slate-200', 'border');
    btn.classList.add('border-primary', 'border-2');
}

document.addEventListener('DOMContentLoaded', function() {
    const zoomContainer = document.getElementById('zoom-container');
    const mainImage = document.getElementById('main-product-image');
    if (zoomContainer && mainImage) {
        zoomContainer.addEventListener('mousemove', function(e) {
            const rect = zoomContainer.getBoundingClientRect();
            const xPercent = ((e.clientX - rect.left) / rect.width) * 100;
            const yPercent = ((e.clientY - rect.top) / rect.height) * 100;
            mainImage.style.transformOrigin = `${xPercent}% ${yPercent}%`;
            mainImage.style.transform = 'scale(2)';
        });
        zoomContainer.addEventListener('mouseleave', function() {
            mainImage.style.transform = 'scale(1)';
            mainImage.style.transformOrigin = 'center';
        });
    }
});
</script>

</body>
</html>
