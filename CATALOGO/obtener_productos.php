<?php
require_once '../INCLUDES/db.php';
$pdo = getDB();

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$tipo     = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$categoria_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$orden    = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';
$pagina   = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// Si $vista no viene definida desde el include, la tomamos de $_GET o por defecto 'lista'
if (!isset($vista)) {
    $vista = isset($_GET['vista']) ? $_GET['vista'] : 'lista';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_cliente = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$is_admin   = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$cliente_tipo = $is_cliente ? $_SESSION['cliente_tipo'] : 'FARMACIA';

$precio_campo = 'precio_farmacia';
if ($cliente_tipo === 'DISTRIBUIDORA') $precio_campo = 'precio_distribuidor';
elseif ($cliente_tipo === 'EMPRESA') $precio_campo = 'precio_empresa';

// Si $por_pagina no viene definida desde catalogo.php, la definimos
if (!isset($por_pagina)) {
    $por_pagina = $is_cliente || $is_admin ? 50 : 15;
}
$offset  = ($pagina - 1) * $por_pagina;

// Bloqueo para usuarios no logueados: solo permitimos la página 1
if (!($is_cliente || $is_admin) && $pagina > 1) {
    exit;
}

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

// Filtro de visibilidad según tipo de cliente
if ($cliente_tipo === 'EMPRESA') {
    $where[] = "p.solo_empresa = 'SI'";
}


$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$orden_sql = match($orden) {
    'nombre_desc' => 'ORDER BY nombre DESC',
    'precio_asc'  => "ORDER BY $precio_campo ASC",
    'precio_desc' => "ORDER BY $precio_campo DESC",
    default       => 'ORDER BY nombre ASC',
};

$stmt = $pdo->prepare("
    SELECT p.*, c.nombre as categoria_nombre 
    FROM catalogo_productos p 
    LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
    $where_sql $orden_sql LIMIT $por_pagina OFFSET $offset
");
$stmt->execute($params);
$productos = $stmt->fetchAll();

if (empty($productos)) {
    exit;
}

foreach ($productos as $p) {
    if ($vista === 'lista') {
        ?>
        <tr class="hover:bg-white/5 transition-colors cursor-pointer group"
            onclick="location.href='producto.php?id=<?= $p['id'] ?>'">
          <td class="px-8 py-5">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-white/10 transition-colors">
                <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
                  <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>" class="w-full h-full object-contain p-1">
                <?php else: ?>
                  <span class="material-symbols-outlined text-slate-400 text-xl">medication</span>
                <?php endif; ?>
              </div>
              <span class="text-sm font-bold text-primary-light leading-tight group-hover:text-white transition-colors"><?= htmlspecialchars($p['nombre']) ?></span>
            </div>
          </td>
          <td class="px-8 py-5 text-xs font-bold text-slate-300 hidden lg:table-cell text-center">
            <?= htmlspecialchars($p['sustancia'] ?? '—') ?>
          </td>
          <td class="px-8 py-5 text-[10px] font-black text-slate-500 hidden md:table-cell text-center uppercase tracking-wider">
            <?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin Categoría') ?>
          </td>
          <td class="px-8 py-5 text-center">
            <?php if ($is_cliente || $is_admin): ?>
              <p class="text-sm font-black text-primary-light">$<?= number_format($p[$precio_campo], 2) ?></p>
            <?php else: ?>
              <p class="text-[10px] font-bold text-slate-500 leading-tight">Inicia sesión<br>para ver precio</p>
            <?php endif; ?>
          </td>
          <td class="px-8 py-5 hidden md:table-cell text-center">
            <?php if ($p['tipo'] === 'RED FRIA'): ?>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-tertiary/20 text-tertiary-light text-[10px] font-black uppercase rounded-full whitespace-nowrap">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">ac_unit</span> Red Fría
              </span>
            <?php else: ?>
              <span class="px-3 py-1 bg-primary/20 text-primary-light text-[10px] font-black uppercase rounded-full">Seco</span>
            <?php endif; ?>
          </td>
          <td class="px-8 py-5 text-right">
            <?php if ($is_cliente): ?>
            <button type="button" onclick="event.stopPropagation(); agregarAlCarrito(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= (float)$p[$precio_campo] ?>, '<?= htmlspecialchars(addslashes($p['imagen'] ?? '')) ?>')" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 text-primary-light hover:bg-primary-light hover:text-surface transition-all" title="Añadir al carrito">
              <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
            </button>
            <?php elseif ($is_admin): ?>
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-background text-slate-500" title="Modo Administrador">
              <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
            </div>
            <?php else: ?>
            <button type="button" onclick="event.stopPropagation(); alert('Inicia sesión como cliente para poder cotizar y añadir productos.'); window.location.href='../LOGIN/login.php';" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 text-slate-500 hover:bg-white/10 hover:text-primary-light transition-all" title="Añadir al carrito">
              <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
            </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php
    } else {
        ?>
        <a href="producto.php?id=<?= $p['id'] ?>"
           class="bg-surface rounded-[2rem] transition-all duration-300 p-6 flex flex-col group animate-fade-up hover:-translate-y-2">
          <div class="w-full aspect-square bg-background rounded-2xl flex items-center justify-center mb-4 relative overflow-hidden group-hover:bg-white/5 transition-colors">
            <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
              <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>" class="w-full h-full object-contain p-2">
            <?php else: ?>
              <span class="material-symbols-outlined text-slate-500 text-5xl">medication</span>
            <?php endif; ?>
            <?php if ($p['tipo'] === 'RED FRIA'): ?>
              <span class="absolute top-3 right-3 inline-flex items-center justify-center w-8 h-8 bg-tertiary/20 text-tertiary-light rounded-full">
                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">ac_unit</span>
              </span>
            <?php endif; ?>
          </div>
          <p class="text-xs font-bold text-primary-light leading-tight mb-2 line-clamp-2 flex-1 group-hover:text-white transition-colors uppercase">
            <?= htmlspecialchars($p['nombre']) ?>
          </p>
          <p class="text-[10px] text-slate-400 mb-4 line-clamp-1 font-bold">
            <?= htmlspecialchars($p['sustancia'] ?? '') ?>
          </p>
          <div class="flex items-center justify-between mt-auto">
            <?php if ($is_cliente || $is_admin): ?>
              <p class="text-base font-black text-primary-light">$<?= number_format($p[$precio_campo], 2) ?></p>
            <?php else: ?>
              <p class="text-[10px] font-bold text-slate-500">Inicia sesión<br>para ver precio</p>
            <?php endif; ?>
            <?php if ($is_cliente): ?>
            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= (float)$p[$precio_campo] ?>, '<?= htmlspecialchars(addslashes($p['imagen'] ?? '')) ?>')" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 text-primary-light hover:bg-primary-light hover:text-surface transition-all" title="Añadir al carrito">
              <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
            </button>
            <?php elseif ($is_admin): ?>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-background text-slate-500" title="Modo Administrador">
              <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
            </div>
            <?php else: ?>
            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); alert('Inicia sesión como cliente para poder cotizar y añadir productos.'); window.location.href='../LOGIN/login.php';" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 text-slate-500 hover:bg-white/10 hover:text-primary-light transition-all" title="Añadir al carrito">
              <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
            </button>
            <?php endif; ?>
          </div>
        </a>
        <?php
    }
}
