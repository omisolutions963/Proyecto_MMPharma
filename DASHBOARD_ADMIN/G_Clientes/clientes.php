<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

// ── FILTROS Y PAGINACIÓN ──────────────────────────────────────────────────────
$q    = trim($_GET['q'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');
$pg   = max(1, (int)($_GET['pg'] ?? 1));
$perPage = 15;
$offset  = ($pg - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];
if ($q) {
    $where .= " AND (razon_social LIKE ? OR rfc LIKE ? OR email LIKE ?)";
    $l = "%$q%"; $params[]=$l; $params[]=$l; $params[]=$l;
}
if ($tipo) {
    $where .= " AND tipo = ?";
    $params[] = $tipo;
}

// Datos
$sql = "SELECT * FROM clientes_usuarios $where ORDER BY razon_social ASC LIMIT $perPage OFFSET $offset";
$st = $pdo->prepare($sql);
$st->execute($params);
$clientes = $st->fetchAll();

// ── RESPUESTA AJAX PARA INFINITE SCROLL ────────────────────────────────────────
if (isset($_GET['ajax'])) {
    if (empty($clientes)) die("");
    foreach ($clientes as $c): ?>
        <tr class="group hover:bg-surface-container-low/30 transition-colors">
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($c['razon_social']) ?></span>
              <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5"><?= $c['rfc'] ?: 'Sin RFC' ?></span>
            </div>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase bg-primary/10 text-primary">
              <?= $c['tipo'] ?>
            </span>
          </td>
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($c['persona_contacto'] ?: 'No asignado') ?></span>
              <span class="text-[10px] text-on-surface-variant"><?= $c['email'] ?></span>
            </div>
          </td>
          <td class="px-8 py-4 text-center">
            <?php
              $stColor = match($c['estatus']){
                'ACTIVO' => 'bg-tertiary-container/20 text-on-tertiary-container',
                'INACTIVO' => 'bg-error-container/20 text-error',
                default => 'bg-surface-container-high text-on-surface-variant'
              };
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $stColor ?>">
              <?= $c['estatus'] ?>
            </span>
          </td>
          <td class="px-8 py-4">
            <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button onclick='abrirEditar(<?= json_encode($c) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <form method="POST" onsubmit="return confirm('¿Eliminar cliente?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
    <?php endforeach;
    exit;
}

// ── ACCIONES POST (UPSERT/DELETE) ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = (int)($_POST['id'] ?? 0);
    if ($action === 'delete' && $id) {
        try {
            $pdo->prepare("DELETE FROM clientes_usuarios WHERE id = ?")->execute([$id]);
            header("Location: clientes.php?msg=deleted"); exit;
        } catch (PDOException $e) {
            header("Location: clientes.php?err=fk"); exit;
        }
    }
    // ... lógica de guardado si fuera necesaria (omitida para brevedad o implementada según necesidad)
}

$pageTitle = "MMPharma Portal - Gestión de Clientes";
$activePage = "clientes";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<!-- Header -->
<div class="flex justify-between items-end mb-8 animate-reveal">
  <div>
    <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
        <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="text-on-surface-variant">Clientes</span>
    </nav>
    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Gestión de Clientes</h2>
    <p class="text-on-surface-variant text-sm mt-1">Directorio de farmacias, distribuidoras y empresas.</p>
  </div>
  <button onclick="abrirModal()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
    <span class="material-symbols-outlined text-[18px]">person_add</span> Nuevo cliente
  </button>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php
    $total_c = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios")->fetchColumn();
    $activos = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='ACTIVO'")->fetchColumn();
    $pendien = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='DOCS_PENDIENTES'")->fetchColumn();
    $inactiv = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='INACTIVO'")->fetchColumn();
    $kpis = [
        ['l'=>'Total Clientes', 'v'=>$total_c, 'i'=>'group', 'b'=>'border-primary/40'],
        ['l'=>'Activos', 'v'=>$activos, 'i'=>'check_circle', 'b'=>'border-tertiary/40'],
        ['l'=>'Pendientes', 'v'=>$pendien, 'i'=>'pending', 'b'=>'border-secondary/40'],
        ['l'=>'Inactivos', 'v'=>$inactiv, 'i'=>'cancel', 'b'=>'border-error/40'],
    ];
    foreach($kpis as $index => $k): ?>
    <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 <?= $k['b'] ?> shadow-sm animate-reveal" style="animation-delay: <?= $index * 0.1 ?>s">
        <div class="flex justify-between items-center mb-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant"><?= $k['l'] ?></span>
            <span class="material-symbols-outlined text-on-surface-variant/30 scale-75"><?= $k['i'] ?></span>
        </div>
        <h3 class="text-2xl font-black text-on-surface"><?= number_format($k['v']) ?></h3>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filtros -->
<form method="GET" class="bg-surface-container-low p-4 rounded-2xl flex items-center gap-4 mb-8 animate-reveal" style="animation-delay: 0.35s">
    <div class="flex-1 relative">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar cliente, RFC o email..." class="w-full bg-white border-none rounded-xl py-3 pl-12 pr-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none shadow-sm"/>
    </div>
    <select name="tipo" class="bg-white border-none rounded-xl py-3 px-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none shadow-sm w-48 font-bold">
        <option value="">Todos los tipos</option>
        <option value="FARMACIA" <?= $tipo==='FARMACIA'?'selected':'' ?>>Farmacia</option>
        <option value="DISTRIBUIDORA" <?= $tipo==='DISTRIBUIDORA'?'selected':'' ?>>Distribuidora</option>
        <option value="EMPRESA" <?= $tipo==='EMPRESA'?'selected':'' ?>>Empresa</option>
    </select>
    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">Filtrar</button>
</form>

<!-- Tabla Centrada -->
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.4s">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
          <th class="px-8 py-4 text-center">Socio Comercial</th>
          <th class="px-8 py-4 text-center">Tipo</th>
          <th class="px-8 py-4 text-center">Contacto</th>
          <th class="px-8 py-4 text-center">Estatus</th>
          <th class="px-8 py-4 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="tableBody" class="divide-y divide-outline-variant/10">
        <?php if (empty($clientes)): ?>
        <tr><td colspan="5" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-reveal">No se encontraron clientes.</td></tr>
        <?php else: ?>
        <?php foreach ($clientes as $c): ?>
        <tr class="group hover:bg-surface-container-low/30 transition-colors animate-fade-in">
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($c['razon_social']) ?></span>
              <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5"><?= $c['rfc'] ?: 'Sin RFC' ?></span>
            </div>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase bg-primary/10 text-primary">
              <?= $c['tipo'] ?>
            </span>
          </td>
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($c['persona_contacto'] ?: 'No asignado') ?></span>
              <span class="text-[10px] text-on-surface-variant"><?= $c['email'] ?></span>
            </div>
          </td>
          <td class="px-8 py-4 text-center">
            <?php
              $stColor = match($c['estatus']){
                'ACTIVO' => 'bg-tertiary-container/20 text-on-tertiary-container',
                'INACTIVO' => 'bg-error-container/20 text-error',
                default => 'bg-surface-container-high text-on-surface-variant'
              };
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $stColor ?>">
              <?= $c['estatus'] ?>
            </span>
          </td>
          <td class="px-8 py-4">
            <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button onclick='abrirEditar(<?= json_encode($c) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <form method="POST" onsubmit="return confirm('¿Eliminar cliente?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <!-- Loading Indicator -->
  <div id="loading" class="hidden px-8 py-6 text-center">
     <div class="inline-block w-6 h-6 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
  </div>
</div>

</main>

<script>
let currentPage = 1;
let loading = false;
let hasMore = true;

window.addEventListener('scroll', () => {
    if (loading || !hasMore) return;
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
        loadMore();
    }
});

async function loadMore() {
    loading = true;
    document.getElementById('loading').classList.remove('hidden');
    currentPage++;
    try {
        const response = await fetch(`clientes.php?ajax=1&pg=${currentPage}&q=<?= urlencode($q) ?>&tipo=<?= urlencode($tipo) ?>`);
        const html = await response.text();
        if (html.trim() === "") { hasMore = false; } 
        else { document.getElementById('tableBody').insertAdjacentHTML('beforeend', html); }
    } catch (e) { console.error("Error", e); } 
    finally {
        loading = false;
        document.getElementById('loading').classList.add('hidden');
    }
}
// ... resto de funciones de modal similares a productos ...
</script>


<?php include("../Includes/footer.php"); ?>
