<?php
$host = 'localhost';
$port = 3307;
$dbname = 'mm_pharma';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Parámetros de búsqueda y filtros
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$tipo     = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$orden    = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';
$pagina   = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = 50;
$offset  = ($pagina - 1) * $por_pagina;

// Construir query con filtros
$where = [];
$params = [];

if ($busqueda) {
    $where[] = "(nombre LIKE ? OR sustancia LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if ($tipo === 'red_fria') {
    $where[] = "tipo = 'RED FRIA'";
} elseif ($tipo === 'seco') {
    $where[] = "tipo = 'SECO'";
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Orden
$orden_sql = match($orden) {
    'nombre_desc' => 'ORDER BY nombre DESC',
    'precio_asc'  => 'ORDER BY precio_farmacia ASC',
    'precio_desc' => 'ORDER BY precio_farmacia DESC',
    default       => 'ORDER BY nombre ASC',
};

// Total de resultados
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM productos $where_sql");
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// Productos paginados
$stmt = $pdo->prepare("SELECT * FROM productos $where_sql $orden_sql LIMIT $por_pagina OFFSET $offset");
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper para mantener parámetros en links de paginación
function queryStr($extra = []) {
    $params = array_merge($_GET, $extra);
    unset($params['pagina']);
    return http_build_query($params);
}
?>

<?php
$titulo = 'Catálogo | MMPharma';
$pagina_actual = 'catalogo'; // marca el link activo en el nav
$base = '../';               // si estás en subcarpeta como CATALOGO/
require_once '../includes/header.php';
?>

<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden bg-gradient-to-br from-[#003e79] to-[#1e60aa]">
  <div class="relative z-10 max-w-[1600px] mx-auto px-8 py-20 w-full" data-aos="fade-up">
    <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-tight text-white mb-2">Catálogo</h1>
    <p class="text-lg text-blue-100/90 font-medium"><?= number_format($total) ?> productos disponibles</p>
  </div>
</section>

<!-- ═══ FILTROS Y BUSCADOR ═══ -->
<section class="w-full bg-primary border-b border-white/10 py-5 sticky top-[72px] z-30 shadow-xl">
  <div class="max-w-[1600px] mx-auto px-12" data-aos="fade" data-aos-delay="200">
    <form method="GET" action="catalogo.php" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">

      <!-- Buscador -->
      <div class="relative flex-1 group">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-lg transition-colors group-focus-within:text-secondary">search</span>
        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($busqueda) ?>"
          placeholder="Buscar producto..."
          class="w-full h-11 bg-white border-none rounded-xl pl-10 pr-4 py-0 text-slate-900 placeholder-slate-400 text-sm focus:ring-2 focus:ring-secondary outline-none transition-all"
        >
      </div>

      <!-- Filtro tipo -->
      <select name="tipo" class="h-11 bg-white border-none rounded-xl pl-4 pr-10 py-0 text-sm text-slate-900 font-bold focus:ring-2 focus:ring-secondary outline-none appearance-none cursor-pointer" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%2364748b%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.1em;">
        <option value="" <?= $tipo === '' ? 'selected' : '' ?>>Todos los tipos</option>
        <option value="seco" <?= $tipo === 'seco' ? 'selected' : '' ?>>Seco</option>
        <option value="red_fria" <?= $tipo === 'red_fria' ? 'selected' : '' ?>>Red Fría</option>
      </select>

      <!-- Ordenar -->
      <select name="orden" class="h-11 bg-white border-none rounded-xl pl-4 pr-10 py-0 text-sm text-slate-900 font-bold focus:ring-2 focus:ring-secondary outline-none appearance-none cursor-pointer" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%2364748b%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.1em;">
        <option value="nombre_asc"  <?= $orden === 'nombre_asc'  ? 'selected' : '' ?>>Nombre A-Z</option>
        <option value="nombre_desc" <?= $orden === 'nombre_desc' ? 'selected' : '' ?>>Nombre Z-A</option>
        <option value="precio_asc"  <?= $orden === 'precio_asc'  ? 'selected' : '' ?>>Precio: menor a mayor</option>
        <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
      </select>

      <!-- Botón buscar -->
      <button type="submit" class="h-11 bg-secondary text-white px-6 py-0 rounded-xl text-sm font-black hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 shadow-lg shadow-secondary/20 whitespace-nowrap">
        <span class="material-symbols-outlined text-lg">search</span>
        Buscar
      </button>

      <!-- Limpiar filtros -->
      <?php if ($busqueda || $tipo || $orden !== 'nombre_asc'): ?>
      <a href="catalogo.php" class="w-11 h-11 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all flex items-center justify-center shadow-lg" title="Limpiar filtros">
        <span class="material-symbols-outlined text-lg">refresh</span>
      </a>
      <?php endif; ?>

      <!-- Toggle vista -->
      <div class="flex gap-1 h-11 bg-white border border-white/10 rounded-xl p-1 shadow-lg">
        <button type="button" id="btn-lista" onclick="setVista('lista')"
          class="flex-1 w-9 h-full flex items-center justify-center rounded-lg transition-all vista-btn activa">
          <span class="material-symbols-outlined text-lg">view_list</span>
        </button>
        <button type="button" id="btn-grid" onclick="setVista('grid')"
          class="flex-1 w-9 h-full flex items-center justify-center rounded-lg transition-all vista-btn">
          <span class="material-symbols-outlined text-lg">grid_view</span>
        </button>
      </div>

    </form>
  </div>
</section>

<!-- ═══ PRODUCTOS ═══ -->
<main class="bg-background py-12 min-h-screen">
  <div class="max-w-[1600px] mx-auto px-8">

  <?php if (empty($productos)): ?>
  <div class="text-center py-24 text-on-surface-variant clinical-shadow bg-surface-container-low border border-white/50 rounded-2xl" data-aos="zoom-in">
    <span class="material-symbols-outlined text-6xl text-outline mb-4">search_off</span>
    <p class="text-lg font-medium mb-2">No se encontraron productos</p>
    <p class="text-sm mb-6">Intenta con otro término de búsqueda</p>
    <a href="catalogo.php" class="text-secondary font-bold hover:underline">Ver todos los productos</a>
  </div>

  <?php else: ?>

  <!-- ─── VISTA LISTA ─── -->
  <div id="vista-lista" class="bg-white rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.05)] overflow-hidden border border-primary/10" data-aos="fade-up">
    <table class="w-full">
      <thead>
        <tr class="bg-primary border-b border-primary/10">
          <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white">Producto</th>
          <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white hidden lg:table-cell">Sustancia activa</th>
          <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white">Precio</th>
          <th class="px-8 py-5 text-center text-sm font-black uppercase tracking-widest text-white hidden md:table-cell">Tipo</th>
          <th class="px-8 py-5"></th>
        </tr>
      </thead>
      <tbody id="contenedor-lista" class="divide-y divide-primary/5">
        <?php 
        $vista = 'lista';
        include 'obtener_productos.php'; 
        ?>
      </tbody>
    </table>
  </div>

  <!-- ─── VISTA GRID ─── -->
  <div id="vista-grid" class="hidden grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6" data-aos="fade-up">
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
</main>

<script>
let paginaActual = 1;
let cargando = false;
let finDeCatalogo = false;
let vistaActual = localStorage.getItem('mm_vista') || 'lista';

function setVista(v) {
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
  .vista-btn { color: #64748b; }
  .vista-btn.activa { background: #1e60aa !important; color: white !important; }
</style>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>
