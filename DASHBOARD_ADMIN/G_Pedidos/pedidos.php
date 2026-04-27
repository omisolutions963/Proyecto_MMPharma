<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $action = $_POST['action'];
    $id = (int)$_POST['id'];
    
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE clientes_pedidos SET estado_envio = 'PROCESANDO' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE clientes_pedidos SET estado_envio = 'CANCELADO' WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM clientes_pedidos_detalle WHERE pedido_id = ?");
        $stmt->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM clientes_pedidos WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    header("Location: pedidos.php");
    exit;
}

// ── FILTROS Y PAGINACIÓN ──────────────────────────────────────────────────────
$q      = trim($_GET['q'] ?? '');
$estado = trim($_GET['estado'] ?? '');
$pg     = max(1, (int)($_GET['pg'] ?? 1));
$perPage = 15;
$offset  = ($pg - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];
if ($q) {
    $where .= " AND (p.folio LIKE ? OR c.razon_social LIKE ?)";
    $l = "%$q%"; $params[]=$l; $params[]=$l;
}
if ($estado) {
    $where .= " AND p.estado_envio = ?";
    $params[] = $estado;
}

// Datos
$sql = "SELECT p.*, c.razon_social as cliente_nombre, 
               (SELECT ruta_archivo FROM clientes_pedidos_comprobantes WHERE pedido_id = p.id ORDER BY fecha_subida DESC LIMIT 1) as comprobante_archivo,
               (SELECT estatus FROM clientes_pedidos_comprobantes WHERE pedido_id = p.id ORDER BY fecha_subida DESC LIMIT 1) as comprobante_estatus
        FROM clientes_pedidos p 
        JOIN clientes_usuarios c ON p.cliente_id = c.id 
        $where 
        ORDER BY p.fecha_pedido DESC 
        LIMIT $perPage OFFSET $offset";
$st = $pdo->prepare($sql);
$st->execute($params);
$pedidos = $st->fetchAll();

// ── RESPUESTA AJAX PARA INFINITE SCROLL ────────────────────────────────────────
if (isset($_GET['ajax'])) {
    if (empty($pedidos)) die("");
    foreach ($pedidos as $p): ?>
        <tr class="group hover:bg-surface-container-low/30 transition-colors">
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-sm font-black text-primary"><?= $p['folio'] ?></span>
              <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5"><?= date('d/m/Y', strtotime($p['fecha_pedido'])) ?></span>
            </div>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="text-sm font-bold text-on-surface"><?= htmlspecialchars($p['cliente_nombre']) ?></span>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="text-sm font-black text-on-surface">$<?= number_format($p['monto_total'], 2) ?></span>
          </td>
          <td class="px-8 py-4 text-center">
            <?php
              $stColor = match($p['estado_envio']){
                'ENTREGADO' => 'bg-tertiary-container/20 text-on-tertiary-container',
                'CANCELADO' => 'bg-error-container/20 text-error',
                'ENVIADO'   => 'bg-primary/10 text-primary border border-primary/20',
                default     => 'bg-surface-container-high text-on-surface-variant'
              };
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $stColor ?>">
              <?= $p['estado_envio'] ?>
            </span>
          </td>
          <td class="px-8 py-4">
            <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <?php if ($p['comprobante_archivo']): ?>
              <button onclick="verComprobante('<?= $p['comprobante_archivo'] ?>', '<?= $p['comprobante_estatus'] ?>', <?= $p['id'] ?>)" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-amber-500 hover:bg-amber-500 hover:text-white transition-all" title="Ver Comprobante de Pago">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
              </button>
              <?php endif; ?>
              <?php if ($p['estado_envio'] === 'PENDIENTE'): ?>
              <form method="POST" class="inline">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-green-600 hover:bg-green-600 hover:text-white transition-all" title="Aprobar Pedido">
                  <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </button>
              </form>
              <form method="POST" class="inline" onsubmit="return confirm('¿Rechazar pedido?')">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-orange-600 hover:bg-orange-600 hover:text-white transition-all" title="Rechazar Pedido">
                  <span class="material-symbols-outlined text-[18px]">cancel</span>
                </button>
              </form>
              <?php endif; ?>
              <a href="generar_pdf.php?id=<?= $p['id'] ?>" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-secondary hover:bg-secondary hover:text-white transition-all" title="Descargar Cotización PDF">
                <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
              </a>
              <button onclick="verDetalle(<?= $p['id'] ?>)" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all" title="Ver Detalle">
                <span class="material-symbols-outlined text-[18px]">visibility</span>
              </button>
              <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar pedido de la base de datos?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all" title="Eliminar Permanente">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
    <?php endforeach;
    exit;
}

// ... Resto de lógica de pedidos ...

$pageTitle = "MMPharma Portal - Gestión de Pedidos";
$activePage = "pedidos";
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
        <span class="text-on-surface-variant">Pedidos</span>
    </nav>
    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Gestión de Pedidos</h2>
    <p class="text-on-surface-variant text-sm mt-1">Monitoreo de transacciones y logística en tiempo real.</p>
  </div>
  <div class="flex gap-3">
    <a href="export_pedidos.php" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
      <span class="material-symbols-outlined text-[18px]">download</span> Exportar
    </a>
  </div>
</div>

<!-- KPIs Pedidos -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php
    $tot_p = (int)$pdo->query("SELECT COUNT(*) FROM clientes_pedidos")->fetchColumn();
    $tot_pend = (int)$pdo->query("SELECT COUNT(*) FROM clientes_pedidos WHERE estado_envio='PENDIENTE'")->fetchColumn();
    $tot_env = (int)$pdo->query("SELECT COUNT(*) FROM clientes_pedidos WHERE estado_envio='ENVIADO'")->fetchColumn();
    $ingresos = (float)$pdo->query("SELECT SUM(monto_total) FROM clientes_pedidos WHERE estado_envio!='CANCELADO'")->fetchColumn();
    
    $kpis = [
        ['l'=>'Total Pedidos', 'v'=>$tot_p, 'i'=>'shopping_cart', 'b'=>'border-primary/40'],
        ['l'=>'Pendientes', 'v'=>$tot_pend, 'i'=>'pending', 'b'=>'border-secondary/40'],
        ['l'=>'En Camino', 'v'=>$tot_env, 'i'=>'local_shipping', 'b'=>'border-tertiary/40'],
        ['l'=>'Ingresos Totales', 'v'=>'$'.number_format($ingresos,0), 'i'=>'payments', 'b'=>'border-amber-500/40'],
    ];
    foreach($kpis as $index => $k): ?>
    <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 <?= $k['b'] ?> shadow-sm animate-reveal" style="animation-delay: <?= $index * 0.1 ?>s">
        <div class="flex justify-between items-center mb-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant"><?= $k['l'] ?></span>
            <span class="material-symbols-outlined text-on-surface-variant/30 scale-75"><?= $k['i'] ?></span>
        </div>
        <h3 class="text-2xl font-black text-on-surface"><?= $k['v'] ?></h3>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filtros -->
<form method="GET" class="bg-surface-container-low p-4 rounded-2xl flex items-center gap-4 mb-8 animate-reveal" style="animation-delay: 0.35s">
    <div class="flex-1 relative">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por folio o cliente..." class="w-full bg-white border-none rounded-xl py-3 pl-12 pr-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none shadow-sm"/>
    </div>
    <select name="estado" class="bg-white border-none rounded-xl py-3 px-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none shadow-sm w-48 font-bold">
        <option value="">Todos los estados</option>
        <option value="PENDIENTE" <?= $estado==='PENDIENTE'?'selected':'' ?>>Pendiente</option>
        <option value="PROCESANDO" <?= $estado==='PROCESANDO'?'selected':'' ?>>Procesando</option>
        <option value="ENVIADO" <?= $estado==='ENVIADO'?'selected':'' ?>>Enviado</option>
        <option value="ENTREGADO" <?= $estado==='ENTREGADO'?'selected':'' ?>>Entregado</option>
        <option value="CANCELADO" <?= $estado==='CANCELADO'?'selected':'' ?>>Cancelado</option>
    </select>
    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">Filtrar</button>
</form>

<!-- Tabla Centrada con Infinite Scroll -->
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.4s">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
          <th class="px-8 py-4 text-center">Folio / Fecha</th>
          <th class="px-8 py-4 text-center">Cliente</th>
          <th class="px-8 py-4 text-center">Monto</th>
          <th class="px-8 py-4 text-center">Estado Envío</th>
          <th class="px-8 py-4 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="tableBody" class="divide-y divide-outline-variant/10">
        <?php if (empty($pedidos)): ?>
        <tr><td colspan="5" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-reveal">No se encontraron pedidos.</td></tr>
        <?php else: ?>
        <?php foreach ($pedidos as $p): ?>
        <tr class="group hover:bg-surface-container-low/30 transition-colors animate-fade-in">
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center">
              <span class="text-sm font-black text-primary"><?= $p['folio'] ?></span>
              <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5"><?= date('d/m/Y', strtotime($p['fecha_pedido'])) ?></span>
            </div>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="text-sm font-bold text-on-surface"><?= htmlspecialchars($p['cliente_nombre']) ?></span>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="text-sm font-black text-on-surface">$<?= number_format($p['monto_total'], 2) ?></span>
          </td>
          <td class="px-8 py-4 text-center">
            <?php
              $stColor = match($p['estado_envio']){
                'ENTREGADO' => 'bg-tertiary-container/20 text-on-tertiary-container',
                'CANCELADO' => 'bg-error-container/20 text-error',
                'ENVIADO'   => 'bg-primary/10 text-primary border border-primary/20',
                default     => 'bg-surface-container-high text-on-surface-variant'
              };
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $stColor ?>">
              <?= $p['estado_envio'] ?>
            </span>
          </td>
          <td class="px-8 py-4">
            <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <?php if ($p['estado_envio'] === 'PENDIENTE'): ?>
              <form method="POST" class="inline">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-green-600 hover:bg-green-600 hover:text-white transition-all" title="Aprobar Pedido">
                  <span class="material-symbols-outlined text-[18px]">check_circle</span>
                </button>
              </form>
              <form method="POST" class="inline" onsubmit="return confirm('¿Rechazar pedido?')">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-orange-600 hover:bg-orange-600 hover:text-white transition-all" title="Rechazar Pedido">
                  <span class="material-symbols-outlined text-[18px]">cancel</span>
                </button>
              </form>
              <?php endif; ?>
              <a href="generar_pdf.php?id=<?= $p['id'] ?>" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-secondary hover:bg-secondary hover:text-white transition-all" title="Descargar Cotización PDF">
                <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
              </a>
              <button onclick="verDetalle(<?= $p['id'] ?>)" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all" title="Ver Detalle">
                <span class="material-symbols-outlined text-[18px]">visibility</span>
              </button>
              <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar pedido de la base de datos?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all" title="Eliminar Permanente">
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
        const response = await fetch(`pedidos.php?ajax=1&pg=${currentPage}&q=<?= urlencode($q) ?>&estado=<?= urlencode($estado) ?>`);
        const html = await response.text();
        if (html.trim() === "") { hasMore = false; } 
        else { document.getElementById('tableBody').insertAdjacentHTML('beforeend', html); }
    } catch (e) { console.error("Error", e); } 
    finally {
        loading = false;
        document.getElementById('loading').classList.add('hidden');
    }
}

function verComprobante(ruta, estatus, id) {
    Swal.fire({
        title: 'Comprobante de Pago',
        text: 'Estatus actual: ' + estatus,
        imageUrl: '../../uploads/comprobantes/' + ruta,
        imageAlt: 'Comprobante',
        showCancelButton: true,
        confirmButtonText: 'Ver archivo completo',
        cancelButtonText: 'Cerrar',
        background: '#102245',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.open('../../uploads/comprobantes/' + ruta, '_blank');
        }
    });
}

function verDetalle(id) {
    Swal.fire({ title: 'Cargando detalle...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    // Aquí iría el fetch real al modal de detalles
    setTimeout(() => Swal.close(), 500);
}
</script>


<?php include("../Includes/footer.php"); ?>
