<?php
if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
$is_cliente = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_logged_in = $is_cliente || $is_admin;

require_once '../includes/db.php';

try {
 $pdo = getDB();
} catch (Exception $e) {
 die('Error de conexión: ' . $e->getMessage());
}


// Obtener Categorías (Excluyendo Red Fría y Otros para manejo manual)
$stmt_cat = $pdo->query("SELECT * FROM catalogo_categorias WHERE nombre NOT LIKE '%RED FRIA%' AND nombre NOT LIKE '%OTROS%' ORDER BY nombre ASC");
$categorias_db = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Intentar obtener el ID de 'OTROS' para el final
$stmt_otros = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE nombre LIKE '%OTROS%' LIMIT 1");
$cat_otros = $stmt_otros->fetch(PDO::FETCH_ASSOC);

// Parámetros de búsqueda y filtros
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$categoria_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = $is_logged_in ? 50 : 15;
$offset = ($pagina - 1) * $por_pagina;

$cliente_tipo = $is_cliente ? $_SESSION['cliente_tipo'] : 'FARMACIA';

// Construir query con filtros
$where = [];
$params = [];

if ($busqueda) {
 $where[] = "(p.nombre LIKE ? OR p.sustancia LIKE ? OR c.nombre LIKE ?)";
 $params[] = "%$busqueda%";
 $params[] = "%$busqueda%";
 $params[] = "%$busqueda%";
}

if ($tipo === 'red_fria') {
 $where[] = "p.tipo = 'RED FRIA'";
} elseif ($tipo === 'seco') {
 $where[] = "p.tipo = 'SECO'";
}

if ($categoria_id > 0) {
 $where[] = "p.categoria_id = ?";
 $params[] = $categoria_id;
}

if ($cliente_tipo === 'EMPRESA') {
  $where[] = "(p.solo_empresa = 'SI' OR p.nombre LIKE '%ASPIRINA%' OR p.sustancia LIKE '%ASPIRINA%' OR p.nombre LIKE '%LORATADINA%' OR p.sustancia LIKE '%LORATADINA%' OR p.nombre LIKE '%LORATIDINA%' OR p.sustancia LIKE '%LORATIDINA%' OR p.nombre LIKE '%BUSCAPINA%' OR p.nombre LIKE '%BUTILHIOSCINA%' OR p.sustancia LIKE '%BUTILHIOSCINA%')";
}

// Excluir productos internos de ajuste (Anticipo y Descuento)
$where[] = "p.codigo NOT IN ('99999999999', 'DESCUENTO')";

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$precio_campo = 'precio_farmacia';
if ($cliente_tipo === 'DISTRIBUIDORA') $precio_campo = 'precio_distribuidor';
elseif ($cliente_tipo === 'EMPRESA') $precio_campo = 'precio_empresa';

// Orden
$orden_sql = match($orden) {
 'nombre_desc' => 'ORDER BY nombre DESC',
 'precio_asc' => "ORDER BY $precio_campo ASC",
 'precio_desc' => "ORDER BY $precio_campo DESC",
 default => 'ORDER BY nombre ASC',
};

// Total de resultados
$count_stmt = $pdo->prepare("
 SELECT COUNT(*) 
 FROM catalogo_productos p 
 LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
 $where_sql
");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// Productos paginados
$stmt = $pdo->prepare("
 SELECT p.*, c.nombre as categoria_nombre 
 FROM catalogo_productos p 
 LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
 $where_sql $orden_sql LIMIT $por_pagina OFFSET $offset
");
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper para mantener parámetros en links de paginación
function queryStr($extra = []) {
 $params = array_merge($_GET, $extra);
 unset($params['pagina']);
 return http_build_query($params);
}

// Obtener Banners Promocionales Activos
$banners = [];
try {
 if ($is_logged_in && isset($_SESSION['cliente_id'])) {
  $stmt_banners = $pdo->prepare("SELECT * FROM admin_banners_promocionales WHERE activo = 1 AND (cliente_id = 0 OR cliente_id = ?) ORDER BY orden ASC");
  $stmt_banners->execute([$_SESSION['cliente_id']]);
 } else {
  $stmt_banners = $pdo->query("SELECT * FROM admin_banners_promocionales WHERE activo = 1 AND cliente_id = 0 ORDER BY orden ASC");
 }
 $banners = $stmt_banners->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
 // Silencioso
}
?>

<?php
$titulo = 'Catálogo | MMPharma';
$pagina_actual = 'catalogo'; // marca el link activo en el nav
$base = '../'; // si estás en subcarpeta como catalogo/
require_once '../includes/header.php';
?>

<!-- ── HERO ── -->
<section class="relative min-h-[250px] md:min-h-[369px] flex items-center overflow-hidden bg-slate-900">
 <div class="absolute inset-0 z-0 overflow-hidden">
 <img src="../img/23.webp" class="w-full h-full object-cover opacity-50 parallax-bg scale-125 origin-top" data-speed="0.2">
 <div class="absolute inset-0 bg-primary/70"></div>
 </div>
 <div class="relative z-10 max-w-[1369px] mx-auto px-6 md:px-8 py-12 md:py-20 w-full" data-aos="fade-up">
 <h1 class="text-4xl md:text-7xl font-black tracking-tight leading-tight text-white mb-2">Catálogo</h1>
 <?php if ($is_logged_in): ?>
  <p class="text-base md:text-lg text-white font-medium"><?= number_format($total) ?> productos disponibles</p>
  <?php else: ?>
  <p class="text-base md:text-lg text-white font-medium">Conoce nuestro catálogo de productos.</p>
  <?php endif; ?>
 </div>
</section>


<!-- ═══ FILTROS Y BUSCADOR ═══ -->
<section class="w-full bg-tertiary py-5 md:py-7 z-30 <?= $is_logged_in ? 'sticky top-[64px] md:top-[72px]' : 'pointer-events-none opacity-60' ?>">
 <div class="max-w-[1369px] mx-auto px-4 lg:px-12" data-aos="fade" data-aos-delay="200">
  <form method="GET" action="catalogo.php" class="flex flex-col gap-3 relative">
    <!-- Fila Superior: Buscador, Botón Filtros, Botón Buscar, Vista -->
    <div class="flex flex-wrap lg:flex-nowrap gap-2 items-center w-full">
      <!-- Buscador -->
      <div class="relative flex-1 min-w-[200px] group">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-lg transition-colors group-focus-within:text-primary">search</span>
        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($busqueda) ?>"
          placeholder="Buscar producto..."
          class="w-full h-11 bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-0 text-slate-800 placeholder-slate-400 text-sm focus:border-primary/50 focus:ring-4 focus:ring-primary/10 outline-none transition-all"
        >
      </div>

      <!-- Botón Filtros -->
      <button type="button" id="btn-toggle-filtros" onclick="toggleFiltros()" class="h-11 px-3.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-slate-600 font-semibold flex items-center justify-center gap-1.5 transition-all shadow-sm shrink-0">
        <span class="material-symbols-outlined text-[20px]">tune</span>
        <span class="hidden sm:inline text-xs">Filtros</span>
        <?php 
        $filtros_activos = ($tipo !== '' || $categoria_id > 0 || $orden !== 'nombre_asc');
        if ($filtros_activos): ?>
          <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
        <?php endif; ?>
      </button>

      <!-- Botón Buscar -->
      <button type="submit" class="h-11 bg-primary text-white px-3.5 sm:px-4 lg:px-6 py-0 rounded-xl text-sm font-semibold hover:brightness-110 transition-all flex items-center justify-center gap-2 whitespace-nowrap shrink-0">
        <span class="material-symbols-outlined text-lg">search</span>
        <span class="hidden sm:inline">Buscar</span>
      </button>

      <!-- Toggle vista -->
      <div class="hidden lg:flex gap-1 h-11 bg-slate-100 rounded-xl p-1 border border-slate-200 shrink-0">
        <button type="button" id="btn-lista" onclick="setVista('lista')"
          class="w-9 h-full flex items-center justify-center rounded-lg transition-all vista-btn" title="Vista lista">
          <span class="material-symbols-outlined text-lg">view_list</span>
        </button>
        <button type="button" id="btn-grid" onclick="setVista('grid')"
          class="w-9 h-full flex items-center justify-center rounded-lg transition-all vista-btn activa" title="Vista cuadrícula">
          <span class="material-symbols-outlined text-lg">grid_view</span>
        </button>
      </div>
    </div>

    <!-- Panel de Filtros Secundarios (Flotante) -->
    <div id="panel-filtros-secundarios" class="flex flex-col gap-3 w-full absolute left-0 right-0 lg:left-auto lg:right-0 lg:w-[600px] top-[calc(100%+12px)] z-50 bg-white p-4 rounded-2xl border border-slate-200/60 shadow-xl mt-1">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full items-stretch">
        
        <!-- Filtro Categoría -->
        <div class="flex flex-col gap-1">
          <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 px-1">Categoría</span>
          <select name="cat" class="w-full h-11 bg-white border border-slate-200 rounded-xl pl-4 pr-10 py-0 text-sm text-slate-700 font-bold focus:border-primary/50 focus:ring-4 focus:ring-primary/10 outline-none appearance-none cursor-pointer truncate" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%2394a3b8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.1em;">
            <option value="0" <?= $categoria_id === 0 ? 'selected' : '' ?>>Todas las categorías</option>
            <?php foreach($categorias_db as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $categoria_id === $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
            <?php if ($cat_otros): ?>
              <option value="<?= $cat_otros['id'] ?>" <?= $categoria_id === $cat_otros['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat_otros['nombre']) ?></option>
            <?php endif; ?>
          </select>
        </div>

        <!-- Filtro tipo -->
        <div class="flex flex-col gap-1">
          <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 px-1">Conservación</span>
          <select name="tipo" class="w-full h-11 bg-white border border-slate-200 rounded-xl pl-4 pr-10 py-0 text-sm text-slate-700 font-bold focus:border-primary/50 focus:ring-4 focus:ring-primary/10 outline-none appearance-none cursor-pointer truncate" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%2394a3b8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.1em;">
            <option value="" <?= $tipo === '' ? 'selected' : '' ?>>Todos los tipos</option>
            <option value="seco" <?= $tipo === 'seco' ? 'selected' : '' ?>>Seco</option>
            <option value="red_fria" <?= $tipo === 'red_fria' ? 'selected' : '' ?>>Red Fría</option>
          </select>
        </div>

        <!-- Ordenar -->
        <div class="flex flex-col gap-1">
          <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 px-1">Ordenar por</span>
          <select name="orden" class="w-full h-11 bg-white border border-slate-200 rounded-xl pl-4 pr-10 py-0 text-sm text-slate-700 font-bold focus:border-primary/50 focus:ring-4 focus:ring-primary/10 outline-none appearance-none cursor-pointer truncate" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%2394a3b8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.1em;">
            <option value="nombre_asc" <?= $orden === 'nombre_asc' ? 'selected' : '' ?>>Nombre A-Z</option>
            <option value="nombre_desc" <?= $orden === 'nombre_desc' ? 'selected' : '' ?>>Nombre Z-A</option>
            <option value="precio_asc" <?= $orden === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
            <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
          </select>
        </div>

        <!-- Acciones de Filtros -->
        <div class="flex gap-2 w-full sm:col-span-3 items-center mt-2 border-t border-slate-100 pt-3">
          <?php if ($busqueda || $tipo || $categoria_id > 0 || $orden !== 'nombre_asc'): ?>
            <a href="catalogo.php" class="flex-1 h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 border border-slate-200" title="Limpiar filtros">
              <span class="material-symbols-outlined text-sm">refresh</span>
              Limpiar filtros
            </a>
          <?php endif; ?>
          <button type="submit" class="flex-1 h-11 bg-primary hover:bg-primary/95 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
            <span class="material-symbols-outlined text-sm">done</span>
            Aplicar filtros
          </button>
        </div>

      </div>
    </div>
  </form>
 </div>
</section>

<!-- ═══ productos ═══ -->
<main class="bg-white py-12 min-h-screen relative">
 <div class="max-w-[1369px] mx-auto px-8 <?= !$is_logged_in ? 'pointer-events-none' : '' ?>">
 
 <div class="<?= !$is_logged_in ? 'filter blur-[8px] opacity-50 select-none' : '' ?>">

  <!-- BANNERS PROMOCIONALES -->
  <?php if(!empty($banners)): ?>
  <div class="mb-8 grid grid-cols-1 md:grid-cols-<?= min(count($banners), 3) ?> gap-6" data-aos="fade-up">
    <?php foreach($banners as $banner): ?>
      <a href="<?= htmlspecialchars($banner['enlace_url'] ?? '#') ?>" class="block rounded-2xl overflow-hidden border border-slate-200/60 group hover:shadow-[0_0_20px_rgba(74,144,217,0.15)] hover:border-primary/30 transition-all relative">
        <img src="<?= $base ?? '' ?><?= htmlspecialchars($banner['ruta_imagen']) ?>" alt="<?= htmlspecialchars($banner['titulo']) ?>" class="w-full h-32 md:h-40 object-cover group-hover:scale-105 transition-transform duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-5">
          <h3 class="text-white font-bold text-lg"><?= htmlspecialchars($banner['titulo']) ?></h3>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

 <?php if (empty($productos)): ?>
 <div class="text-center py-24 text-slate-400 bg-white border border-slate-200 rounded-2xl " data-aos="zoom-in">
 <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">search_off</span>
 <p class="text-lg font-bold mb-2 text-slate-900">No se encontraron productos</p>
 <p class="text-sm mb-6 text-slate-900">Intenta con otro término de búsqueda</p>
 <a href="catalogo.php" class="text-primary font-bold hover:underline">Ver todos los productos</a>
 </div>

 <?php else: ?>

 <!-- ─── VISTA LISTA ─── -->
 <div id="vista-lista" class="hidden bg-white rounded-[2rem] overflow-hidden border border-slate-200" data-aos="fade-up">
 <table class="w-full">
 <thead>
 <tr class="bg-primary">
 <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white">Producto</th>
 <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white hidden lg:table-cell">Sustancia activa</th>
 <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white hidden md:table-cell">Categoría</th>
 <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white">Precio</th>
 <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white hidden md:table-cell">Tipo</th>
 <th class="px-8 py-5"></th>
 </tr>
 </thead>
 <tbody id="contenedor-lista" class="divide-y divide-slate-100">
 <?php 
 $vista = 'lista';
 include 'obtener_productos.php'; 
 ?>
 </tbody>
 </table>
 </div>

 <!-- ─── VISTA GRID ─── -->
 <div id="vista-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6" data-aos="fade-up">
 <?php 
 $vista = 'grid';
 include 'obtener_productos.php'; 
 ?>
 </div>

 <!-- Centinela para Infinite Scroll -->
 <div id="infinite-scroll-trigger" class="flex justify-center py-12">
 <div id="loader" class="hidden">
 <div class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
 </div>
 </div>

 <?php endif; ?>
 </div>
 </div>

 <?php if (!$is_logged_in): ?>
 <!-- Overlay CTA para usuarios no registrados -->
 <div class="absolute inset-0 z-40 flex items-center justify-center bg-white/80 backdrop-blur-[4px]">
  <div class="max-w-md w-full mx-4 bg-primary p-10 rounded-3xl text-center animate-reveal">
  <div class="w-20 h-20 bg-white/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
  <span class="material-symbols-outlined text-white text-4xl">lock</span>
 </div>
  <h2 class="text-3xl font-black text-white tracking-tight mb-4">Catálogo exclusivo</h2>
  <p class="text-white/80 font-medium mb-8 leading-relaxed">
 Para ver nuestros precios y existencias en tiempo real, es necesario contar con una cuenta aprobada.
 </p>
 <div class="flex flex-col gap-3">
  <a href="../seleccion_registro/seleccion_registro.php" class="w-full py-4 bg-tertiary text-white font-bold rounded-2xl hover:bg-tertiary/90 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center">
 Solicitar acceso
 </a>
  <a href="../login/login.php" class="w-full py-4 bg-white text-primary font-bold rounded-2xl hover:bg-slate-100 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center">
 Ya tengo cuenta
 </a>
 </div>
 </div>
 </div>
 <?php endif; ?>
</main>

<script>
let paginaActual = 1;
let cargando = false;
let finDeCatalogo = false;
let vistaActual = localStorage.getItem('mm_vista') || 'grid';
if (window.innerWidth < 1024) {
  vistaActual = 'grid';
}

function toggleFiltros() {
  const panel = document.getElementById('panel-filtros-secundarios');
  const btn = document.getElementById('btn-toggle-filtros');
  panel.classList.toggle('show');
  btn.classList.toggle('bg-slate-100');
}

// Cerrar filtros al hacer click fuera
document.addEventListener('click', function(event) {
  const panel = document.getElementById('panel-filtros-secundarios');
  const btn = document.getElementById('btn-toggle-filtros');
  if (panel && btn && !panel.contains(event.target) && !btn.contains(event.target)) {
    panel.classList.remove('show');
    btn.classList.remove('bg-slate-100');
  }
});


function setVista(v) {
 if (window.innerWidth < 1024) {
  v = 'grid';
 }
 vistaActual = v;
 const lista = document.getElementById('vista-lista');
 const grid = document.getElementById('vista-grid');
 const btnL = document.getElementById('btn-lista');
 const btnG = document.getElementById('btn-grid');

 if (v === 'grid') {
 lista.classList.add('hidden');
 grid.classList.remove('hidden');
 grid.classList.add('grid');
 btnG.classList.add('activa');
 btnL.classList.remove('activa');
 } else {
 grid.classList.add('hidden');
 grid.classList.remove('grid');
 lista.classList.remove('hidden');
 btnL.classList.add('activa');
 btnG.classList.remove('activa');
 }
 localStorage.setItem('mm_vista', v);
}

// Inicializar vista
setVista(vistaActual);

// Listener para forzar grid si se redimensiona a móvil
window.addEventListener('resize', () => {
  if (window.innerWidth < 1024 && vistaActual !== 'grid') {
    setVista('grid');
  }
});


async function cargarMasProductos() {
 if (cargando || finDeCatalogo) return;
 
 cargando = true;
 const loader = document.getElementById('loader');
 if (loader) loader.classList.remove('hidden');
 
 paginaActual++;
 
 const params = new URLSearchParams(window.location.search);
 params.set('pagina', paginaActual);
 params.set('vista', vistaActual);
 
 try {
 const response = await fetch(`obtener_productos.php?${params.toString()}`);
 const html = await response.text();
 
 if (html.trim() === '') {
 finDeCatalogo = true;
 document.getElementById('infinite-scroll-trigger').innerHTML = '<p class="text-slate-400 font-bold text-xs uppercase tracking-[0.2em]">Fin del catálogo</p>';
 } else {
 const contenedor = vistaActual === 'lista' ? document.getElementById('contenedor-lista') : document.getElementById('vista-grid');
 contenedor.insertAdjacentHTML('beforeend', html);
 }
 } catch (error) {
 console.error('Error cargando productos:', error);
 } finally {
 cargando = false;
 if (loader) loader.classList.add('hidden');
 }
}

// Intersection Observer para scroll infinito
const trigger = document.getElementById('infinite-scroll-trigger');
if (trigger) {
 const observer = new IntersectionObserver((entries) => {
 if (entries[0].isIntersecting) {
 cargarMasProductos();
 }
 }, { threshold: 0.1 });
 observer.observe(trigger);
}
</script>

<style>
 .vista-btn { color: #94a3b8; }
 .vista-btn.activa { background: #003e79 !important; color: white !important; }
 
 /* Animación de filtros en móvil y escritorio (flotante) */
 #panel-filtros-secundarios {
   display: flex !important;
   opacity: 0;
   transform: translateY(8px) scale(0.95);
   pointer-events: none;
   transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
   transform-origin: top center;
 }
 #panel-filtros-secundarios.show {
   opacity: 1 !important;
   transform: translateY(0) scale(1) !important;
   pointer-events: auto !important;
 }
 @media (min-width: 1024px) {
   #panel-filtros-secundarios {
     transform-origin: top right;
   }
 }
</style>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
