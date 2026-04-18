<?php
require_once '../INCLUDES/db.php';
$pdo = getDB();

$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$tipo     = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$orden    = isset($_GET['orden']) ? $_GET['orden'] : 'nombre_asc';
$pagina   = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// Si $vista no viene definida desde el include, la tomamos de $_GET o por defecto 'lista'
if (!isset($vista)) {
    $vista = isset($_GET['vista']) ? $_GET['vista'] : 'lista';
}

$por_pagina = 30; 
$offset  = ($pagina - 1) * $por_pagina;

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
$orden_sql = match($orden) {
    'nombre_desc' => 'ORDER BY nombre DESC',
    'precio_asc'  => 'ORDER BY precio_farmacia ASC',
    'precio_desc' => 'ORDER BY precio_farmacia DESC',
    default       => 'ORDER BY nombre ASC',
};

$stmt = $pdo->prepare("SELECT * FROM productos $where_sql $orden_sql LIMIT $por_pagina OFFSET $offset");
$stmt->execute($params);
$productos = $stmt->fetchAll();

if (empty($productos)) {
    exit;
}

foreach ($productos as $p) {
    if ($vista === 'lista') {
        ?>
        <tr class="hover:bg-primary/5 transition-colors cursor-pointer group"
            onclick="location.href='producto.php?id=<?= $p['id'] ?>'">
          <td class="px-8 py-5">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center flex-shrink-0 border border-primary/10 group-hover:bg-white transition-colors">
                <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
                  <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>" class="w-full h-full object-contain p-1">
                <?php else: ?>
                  <span class="material-symbols-outlined text-primary/30 text-xl">medication</span>
                <?php endif; ?>
              </div>
              <span class="text-sm font-bold text-primary leading-tight group-hover:text-secondary transition-colors"><?= htmlspecialchars($p['nombre']) ?></span>
            </div>
          </td>
          <td class="px-8 py-5 text-xs font-bold text-slate-900 hidden lg:table-cell text-center">
            <?= htmlspecialchars($p['sustancia'] ?? '—') ?>
          </td>
          <td class="px-8 py-5 text-center">
            <p class="text-sm font-black text-primary">$<?= number_format($p['precio_farmacia'], 2) ?></p>
          </td>
          <td class="px-8 py-5 hidden md:table-cell text-center">
            <?php if ($p['tipo'] === 'RED FRIA'): ?>
              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-secondary/10 text-secondary text-[10px] font-black uppercase rounded-full border border-secondary/20 whitespace-nowrap">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">ac_unit</span> Red Fría
              </span>
            <?php else: ?>
              <span class="px-3 py-1 bg-primary/5 text-primary text-[10px] font-black uppercase rounded-full border border-primary/10">Seco</span>
            <?php endif; ?>
          </td>
          <td class="px-8 py-5 text-right">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary/5 text-primary group-hover:bg-secondary group-hover:text-white transition-all">
              <span class="material-symbols-outlined text-xl">chevron_right</span>
            </div>
          </td>
        </tr>
        <?php
    } else {
        ?>
        <a href="producto.php?id=<?= $p['id'] ?>"
           class="bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all border border-slate-200 p-6 flex flex-col group animate-fade-up">
          <div class="w-full aspect-square bg-slate-50 rounded-2xl flex items-center justify-center mb-4 relative overflow-hidden group-hover:bg-white transition-colors">
            <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
              <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>" class="w-full h-full object-contain p-2">
            <?php else: ?>
              <span class="material-symbols-outlined text-slate-300 text-5xl">medication</span>
            <?php endif; ?>
            <?php if ($p['tipo'] === 'RED FRIA'): ?>
              <span class="absolute top-3 right-3 inline-flex items-center justify-center w-8 h-8 bg-secondary text-white rounded-full shadow-lg">
                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">ac_unit</span>
              </span>
            <?php endif; ?>
          </div>
          <p class="text-xs font-bold text-primary leading-tight mb-2 line-clamp-2 flex-1 group-hover:text-secondary transition-colors uppercase">
            <?= htmlspecialchars($p['nombre']) ?>
          </p>
          <p class="text-[10px] text-slate-900 mb-4 line-clamp-1 font-bold">
            <?= htmlspecialchars($p['sustancia'] ?? '') ?>
          </p>
          <div class="flex items-center justify-between">
            <p class="text-base font-black text-primary">$<?= number_format($p['precio_farmacia'], 2) ?></p>
            <span class="material-symbols-outlined text-slate-300 group-hover:text-secondary transition-colors">add_circle</span>
          </div>
        </a>
        <?php
    }
}
