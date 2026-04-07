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
<section class="relative min-h-[369px] flex items-center overflow-hidden">
  <img src="../IMG/23.webp" class="absolute inset-0 w-full h-full object-cover" alt="MMPharma instalaciones">
  <div class="absolute inset-0 bg-[#002451] opacity-80"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-8 py-20 w-full">
    <h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4">Catálogo</h1>
    <p class="text-lg text-blue-100/90 max-w-xl leading-relaxed">
      <?= number_format($total) ?> productos disponibles
      <?= $busqueda ? " — Resultados para \"<strong>" . htmlspecialchars($busqueda) . "</strong>\"" : '' ?>
    </p>
  </div>
</section>

<!-- ═══ FILTROS Y BUSCADOR ═══ -->
<section class="bg-surface-container-low border-b border-outline-variant/20 py-4 sticky top-[68px] z-30">
  <div class="max-w-7xl mx-auto px-8">
    <form method="GET" action="catalogo.php" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">

      <!-- Buscador -->
      <div class="relative flex-1">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-lg">search</span>
        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($busqueda) ?>"
          placeholder="Buscar por nombre o sustancia activa..."
          class="w-full bg-white rounded-xl pl-10 pr-4 py-2.5 text-on-surface text-sm focus:ring-2 focus:ring-primary-fixed outline-none border border-outline-variant/30 transition-all"
        >
      </div>

      <!-- Filtro tipo -->
      <select name="tipo" class="bg-white rounded-xl pl-4 pr-10 py-2.5 text-sm text-on-surface border border-outline-variant/30 focus:ring-2 focus:ring-primary-fixed outline-none appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%23747780%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.2em;">
        <option value="" <?= $tipo === '' ? 'selected' : '' ?>>Todos los tipos</option>
        <option value="seco" <?= $tipo === 'seco' ? 'selected' : '' ?>>Seco</option>
        <option value="red_fria" <?= $tipo === 'red_fria' ? 'selected' : '' ?>>Red Fría</option>
      </select>

      <!-- Ordenar -->
      <select name="orden" class="bg-white rounded-xl pl-4 pr-10 py-2.5 text-sm text-on-surface border border-outline-variant/30 focus:ring-2 focus:ring-primary-fixed outline-none appearance-none" style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 20 20%22 fill=%22none%22><path stroke=%22%23747780%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22M6 8l4 4 4-4%22/></svg>');background-repeat:no-repeat;background-position:right 0.75rem center;background-size:1.2em;">
        <option value="nombre_asc"  <?= $orden === 'nombre_asc'  ? 'selected' : '' ?>>Nombre A-Z</option>
        <option value="nombre_desc" <?= $orden === 'nombre_desc' ? 'selected' : '' ?>>Nombre Z-A</option>
        <option value="precio_asc"  <?= $orden === 'precio_asc'  ? 'selected' : '' ?>>Precio: menor a mayor</option>
        <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
      </select>

      <!-- Botón buscar -->
      <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">search</span>
        Buscar
      </button>

      <!-- Limpiar filtros -->
      <?php if ($busqueda || $tipo || $orden !== 'nombre_asc'): ?>
      <a href="catalogo.php" class="px-4 py-2.5 text-sm text-on-surface-variant hover:text-primary border border-outline-variant/30 rounded-xl transition-all text-center">
        Limpiar
      </a>
      <?php endif; ?>

      <!-- Separador visual -->
      <div class="hidden md:block w-px h-8 bg-outline-variant/30"></div>

      <!-- Toggle vista -->
      <div class="flex gap-1 bg-white border border-outline-variant/30 rounded-xl p-1">
        <button type="button" id="btn-lista" onclick="setVista('lista')"
          class="p-1.5 rounded-lg transition-all vista-btn activa">
          <span class="material-symbols-outlined text-lg">view_list</span>
        </button>
        <button type="button" id="btn-grid" onclick="setVista('grid')"
          class="p-1.5 rounded-lg transition-all vista-btn">
          <span class="material-symbols-outlined text-lg">grid_view</span>
        </button>
      </div>

    </form>
  </div>
</section>

<!-- ═══ PRODUCTOS ═══ -->
<main class="max-w-7xl mx-auto px-8 py-8">

  <?php if (empty($productos)): ?>
  <div class="text-center py-24 text-on-surface-variant clinical-shadow bg-surface-container-lowest rounded-xl">
    <span class="material-symbols-outlined text-6xl text-outline mb-4">search_off</span>
    <p class="text-lg font-medium mb-2">No se encontraron productos</p>
    <p class="text-sm mb-6">Intenta con otro término de búsqueda</p>
    <a href="catalogo.php" class="text-secondary font-bold hover:underline">Ver todos los productos</a>
  </div>

  <?php else: ?>

  <!-- ─── VISTA LISTA ─── -->
  <div id="vista-lista" class="bg-surface-container-lowest rounded-xl clinical-shadow overflow-hidden">
    <table class="w-full">
      <thead>
        <tr class="bg-surface-container-low">
          <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Producto</th>
          <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant hidden lg:table-cell">Sustancia activa</th>
          <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant">Precio</th>
          <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-on-surface-variant hidden md:table-cell">Tipo</th>
          <th class="px-6 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-surface-container">
        <?php foreach ($productos as $i => $p): ?>
        <tr class="<?= $i % 2 === 0 ? 'bg-white' : 'bg-surface' ?> hover:bg-surface-container-low transition-colors cursor-pointer"
            onclick="location.href='producto.php?id=<?= $p['id'] ?>'">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-surface-container-low rounded-lg flex items-center justify-center flex-shrink-0">
                <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
                  <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>" class="w-full h-full object-contain rounded-lg">
                <?php else: ?>
                  <span class="material-symbols-outlined text-outline text-lg">medication</span>
                <?php endif; ?>
              </div>
              <span class="text-sm font-medium text-on-surface leading-tight"><?= htmlspecialchars($p['nombre']) ?></span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-on-surface-variant hidden lg:table-cell">
            <?= htmlspecialchars($p['sustancia'] ?? '—') ?>
          </td>
          <td class="px-6 py-4 text-sm font-bold text-primary whitespace-nowrap">
            $<?= number_format($p['precio_farmacia'], 2) ?>
          </td>
          <td class="px-6 py-4 hidden md:table-cell">
            <?php if ($p['tipo'] === 'RED FRIA'): ?>
              <span class="inline-flex items-center gap-1 px-2 py-1 bg-tertiary-container text-white text-xs font-bold rounded-full whitespace-nowrap">
                <span class="material-symbols-outlined text-xs">ac_unit</span> Red Fría
              </span>
            <?php else: ?>
              <span class="px-2 py-1 bg-surface-container text-on-surface-variant text-xs rounded-full">Seco</span>
            <?php endif; ?>
          </td>
          <td class="px-6 py-4 text-right">
            <span class="text-xs font-bold text-secondary whitespace-nowrap">Ver detalle →</span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- ─── VISTA GRID ─── -->
  <div id="vista-grid" class="hidden grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    <?php foreach ($productos as $p): ?>
    <a href="producto.php?id=<?= $p['id'] ?>"
       class="bg-surface-container-lowest rounded-xl clinical-shadow hover:shadow-xl transition-all border border-transparent hover:border-outline-variant/20 p-5 flex flex-col group">

      <!-- Imagen -->
      <div class="w-full aspect-square bg-surface-container-low rounded-lg flex items-center justify-center mb-3 relative overflow-hidden">
        <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
          <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>"
               class="w-full h-full object-contain p-2">
        <?php else: ?>
          <span class="material-symbols-outlined text-outline/40 text-5xl">medication</span>
        <?php endif; ?>
        <?php if ($p['tipo'] === 'RED FRIA'): ?>
          <span class="absolute top-2 right-2 inline-flex items-center gap-0.5 px-1.5 py-0.5 bg-tertiary-container text-white text-[10px] font-bold rounded-full">
            <span class="material-symbols-outlined text-xs">ac_unit</span>
          </span>
        <?php endif; ?>
      </div>

      <!-- Nombre -->
      <p class="text-xs font-medium text-on-surface leading-tight mb-2 line-clamp-3 flex-1 group-hover:text-primary transition-colors">
        <?= htmlspecialchars($p['nombre']) ?>
      </p>

      <!-- Sustancia -->
      <p class="text-[10px] text-on-surface-variant mb-3 line-clamp-1">
        <?= htmlspecialchars($p['sustancia'] ?? '') ?>
      </p>

      <!-- Precio -->
      <p class="text-sm font-black text-primary">$<?= number_format($p['precio_farmacia'], 2) ?></p>

    </a>
    <?php endforeach; ?>
  </div>

  <!-- ─── PAGINACIÓN ─── -->
  <?php if ($total_paginas > 1): ?>
  <div class="mt-8 flex justify-center items-center gap-2">
    <?php if ($pagina > 1): ?>
    <a href="?<?= queryStr() ?>&pagina=<?= $pagina - 1 ?>"
       class="px-4 py-2 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-sm font-medium hover:bg-surface-container-low transition-all">
      ← Anterior
    </a>
    <?php endif; ?>
    <?php
    $inicio = max(1, $pagina - 2);
    $fin = min($total_paginas, $pagina + 2);
    for ($i = $inicio; $i <= $fin; $i++):
    ?>
    <a href="?<?= queryStr() ?>&pagina=<?= $i ?>"
       class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= $i === $pagina ? 'bg-primary text-white' : 'bg-surface-container-lowest border border-outline-variant/30 hover:bg-surface-container-low' ?>">
      <?= $i ?>
    </a>
    <?php endfor; ?>
    <?php if ($pagina < $total_paginas): ?>
    <a href="?<?= queryStr() ?>&pagina=<?= $pagina + 1 ?>"
       class="px-4 py-2 bg-surface-container-lowest border border-outline-variant/30 rounded-lg text-sm font-medium hover:bg-surface-container-low transition-all">
      Siguiente →
    </a>
    <?php endif; ?>
  </div>
  <p class="text-center text-xs text-on-surface-variant mt-3">
    Página <?= $pagina ?> de <?= $total_paginas ?> — <?= number_format($total) ?> productos en total
  </p>
  <?php endif; ?>

  <?php endif; ?>
</main>

<style>
  .vista-btn { color: #747780; }
  .vista-btn.activa { background: #edf4ff; color: #002451; }
</style>

<script>
function setVista(v) {
  const lista = document.getElementById('vista-lista');
  const grid  = document.getElementById('vista-grid');
  const btnL  = document.getElementById('btn-lista');
  const btnG  = document.getElementById('btn-grid');

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

// Recordar la vista preferida
const vistaGuardada = localStorage.getItem('mm_vista');
if (vistaGuardada === 'grid') setVista('grid');
</script>

<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

</body>
</html>