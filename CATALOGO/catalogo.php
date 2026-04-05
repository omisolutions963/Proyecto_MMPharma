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
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Catálogo de Productos | MMPharma B2B</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="logos/MMPharma-Isotipo.png">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "on-tertiary-container": "#39bb6c",
                    "surface-bright": "#f7f9ff",
                    "secondary-container": "#71c0fe",
                    "surface-container-lowest": "#ffffff",
                    "secondary-fixed-dim": "#92ccff",
                    "tertiary-fixed": "#7efba4",
                    "outline-variant": "#c4c6d0",
                    "primary-container": "#1a3a6b",
                    "inverse-primary": "#abc7ff",
                    "surface-container-low": "#edf4ff",
                    "on-secondary-fixed-variant": "#004b73",
                    "surface-dim": "#c6dcf6",
                    "surface-container-highest": "#cfe5ff",
                    "secondary-fixed": "#cce5ff",
                    "secondary": "#006397",
                    "tertiary-container": "#004520",
                    "primary-fixed-dim": "#abc7ff",
                    "on-background": "#051d30",
                    "surface-container": "#e3efff",
                    "on-surface": "#051d30",
                    "on-primary-fixed": "#001b3f",
                    "error-container": "#ffdad6",
                    "surface": "#f7f9ff",
                    "on-secondary": "#ffffff",
                    "on-primary-container": "#89a5dd",
                    "on-secondary-fixed": "#001d31",
                    "surface-container-high": "#d9eaff",
                    "on-secondary-container": "#004d77",
                    "inverse-surface": "#1d3246",
                    "surface-tint": "#415e91",
                    "on-primary-fixed-variant": "#284678",
                    "error": "#ba1a1a",
                    "tertiary": "#002c13",
                    "on-tertiary-fixed": "#00210c",
                    "on-error-container": "#93000a",
                    "primary-fixed": "#d7e2ff",
                    "on-error": "#ffffff",
                    "on-tertiary-fixed-variant": "#005228",
                    "on-tertiary": "#ffffff",
                    "surface-variant": "#cfe5ff",
                    "inverse-on-surface": "#e8f2ff",
                    "primary": "#002451",
                    "tertiary-fixed-dim": "#61de8a",
                    "background": "#f7f9ff",
                    "outline": "#747780",
                    "on-surface-variant": "#43474f",
                    "on-primary": "#ffffff"
                },
                fontFamily: {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"]
                },
                borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
            },
        },
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .clinical-shadow { box-shadow: 0 10px 40px -10px rgba(0, 36, 81, 0.08); }
</style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<!-- ═══ HEADER (igual que quienes_somos) ═══ -->
<header class="sticky top-0 z-50 bg-[#f7f9ff] shadow-sm">
<nav class="flex justify-between items-center w-full px-8 py-4 max-w-7xl mx-auto font-['Inter'] font-medium text-sm antialiased">
  <div class="flex items-center gap-12">
    <img src="../logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
    <div class="hidden md:flex gap-8">
      <a class="text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200" href="../index.html">Inicio</a>
      <a class="text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200" href="../QUIENES_SOMOS/quienes_somos.html">¿Quiénes somos?</a>
      <a class="text-[#002451] font-semibold border-b-2 border-[#002451] pb-1" href="../CATALOGO/catalogo.php">Catálogo</a>
      <a class="text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200" href="../CONTACTO/Contacto.html">Contacto</a>
    </div>
  </div>
  <div class="flex items-center gap-4">
    <a href="../LOGIN/login.html"><button class="px-4 py-2 text-[#1A3A6B] font-medium hover:bg-[#edf4ff] rounded-lg transition-all">Iniciar sesión</button></a>
    <a href="../SELECCIÓN_REGISTRO/selección_registro.html"><button class="px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow-sm hover:opacity-90 active:scale-95 transition-all">Solicitar acceso</button></a>
  </div>
</nav>
</header>

<!-- ═══ HERO PEQUEÑO ═══ -->
<section class="bg-gradient-to-br from-primary to-primary-container py-10">
  <div class="max-w-7xl mx-auto px-8">
    <span class="inline-block py-1 px-3 mb-4 rounded-full bg-white/10 text-white font-semibold text-xs tracking-widest uppercase">Portal B2B</span>
    <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-2">Catálogo de Productos</h1>
    <p class="text-primary-fixed-dim text-base">
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

<!-- ═══ FOOTER (igual que quienes_somos) ═══ -->
<footer class="bg-[#0D2137] text-white pt-20 pb-10">
  <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
    <div class="space-y-6">
      <img src="logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
      <p class="text-blue-100/60 text-sm leading-relaxed">Distribución farmacéutica con estándares de excelencia. Innovación y compromiso en cada entrega.</p>
      <div class="flex gap-4">
        <a class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-colors" href="#">
          <span class="material-symbols-outlined text-xl">share</span>
        </a>
        <a class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-colors" href="#">
          <span class="material-symbols-outlined text-xl">mail</span>
        </a>
      </div>
    </div>
    <div>
      <h4 class="font-bold text-lg mb-6">Enlaces</h4>
      <ul class="space-y-4 text-blue-100/60 text-sm">
        <li><a class="hover:text-white transition-colors" href="index.html">Inicio</a></li>
        <li><a class="hover:text-white transition-colors" href="QUIENES_SOMOS/quienes_somos.html">Nosotros</a></li>
        <li><a class="hover:text-white transition-colors" href="catalogo.php">Catálogo</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Contacto</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-bold text-lg mb-6">Legal</h4>
      <ul class="space-y-4 text-blue-100/60 text-sm">
        <li><a class="hover:text-white transition-colors" href="#">Aviso de Privacidad</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Términos y Condiciones</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Cumplimiento COFEPRIS</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Política de Devoluciones</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-bold text-lg mb-6">Contacto</h4>
      <ul class="space-y-4 text-blue-100/60 text-sm">
        <li class="flex items-start gap-3">
          <span class="material-symbols-outlined text-secondary-container">location_on</span>
          Av Obsidiana 3644 Residencial Loma Bonita, Zapopan, Jalisco.
        </li>
        <li class="flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary-container">call</span>
          33 4348 0581 / 33 4348 0582
        </li>
        <li class="flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary-container">support_agent</span>
          Soporte en horario de oficina
        </li>
      </ul>
    </div>
  </div>
  <div class="max-w-7xl mx-auto px-8 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100/40">
    <p>© 2026 MMPharma. Todos los derechos reservados.</p>
    <div class="flex gap-8">
      <a class="hover:text-white transition-colors" href="#">Facebook</a>
      <a class="hover:text-white transition-colors" href="#">LinkedIn</a>
      <a class="hover:text-white transition-colors" href="#">Twitter</a>
    </div>
  </div>
</footer>

</body>
</html>