<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

// ── Acción: Aprobar / Rechazar ────────────────────────────────────────────────
$msgFlash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];
    if ($action === 'aprobar') {
        $pdo->prepare("UPDATE clientes_solicitudes_registro SET estatus='APROBADA' WHERE id=?")->execute([$id]);
        $msgFlash = 'aprobada';
    } elseif ($action === 'rechazar') {
        $pdo->prepare("UPDATE clientes_solicitudes_registro SET estatus='RECHAZADA' WHERE id=?")->execute([$id]);
        $msgFlash = 'rechazada';
    }
    header("Location: solicitudes.php?msg=$msgFlash");
    exit;
}
$msgFlash = $_GET['msg'] ?? '';

// ── KPIs ──────────────────────────────────────────────────────────────────────
$pendientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn();
$aprobadas  = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='APROBADA'")->fetchColumn();
$rechazadas = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='RECHAZADA'")->fetchColumn();

// ── Lista ─────────────────────────────────────────────────────────────────────
$filtro = $_GET['filtro'] ?? '';
$where  = $filtro ? "WHERE estatus = " . $pdo->quote($filtro) : "";
$solicitudes = $pdo->query(
    "SELECT * FROM clientes_solicitudes_registro $where ORDER BY created_at DESC LIMIT 50"
)->fetchAll();

$pageTitle  = 'MMPharma Portal - Solicitudes de Registro';
$activePage = 'solicitudes';
include('../Includes/header.php');
include('../Includes/sidebar.php');
?>
<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<?php if ($msgFlash === 'aprobada'): ?>
<div class="mb-6 bg-tertiary/10 border border-on-tertiary-container/20 text-on-tertiary-container px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 animate-fade-in">
  <span class="material-symbols-outlined">check_circle</span> Solicitud aprobada correctamente.
</div>
<?php elseif ($msgFlash === 'rechazada'): ?>
<div class="mb-6 bg-error-container/40 border border-error/20 text-on-error-container px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 animate-fade-in">
  <span class="material-symbols-outlined">block</span> Solicitud rechazada.
</div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
  <div>
    <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
        <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="text-on-surface-variant">Solicitudes</span>
    </nav>
    <h2 class="text-3xl font-extrabold tracking-tight text-on-surface animate-reveal">Solicitudes de Registro</h2>
    <p class="text-on-surface-variant text-sm mt-1">Solicitudes enviadas desde el sitio público.</p>
  </div>
  <div class="flex gap-2 flex-wrap bg-surface-container-low p-1.5 rounded-2xl">
    <a href="?filtro=" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= !$filtro ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Todas</a>
    <a href="?filtro=PENDIENTE" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='PENDIENTE' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Pendientes</a>
    <a href="?filtro=APROBADA" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='APROBADA' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Aprobadas</a>
    <a href="?filtro=RECHAZADA" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='RECHAZADA' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Rechazadas</a>
  </div>
</div>

<!-- KPIs Unificados -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-primary/40 shadow-sm animate-reveal">
        <div class="flex justify-between items-center mb-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Pendientes</span>
            <span class="material-symbols-outlined text-primary/30 scale-75">pending</span>
        </div>
        <h3 class="text-2xl font-black text-on-surface"><?= $pendientes ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-tertiary/40 shadow-sm animate-reveal" style="animation-delay: 0.1s">
        <div class="flex justify-between items-center mb-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Aprobadas</span>
            <span class="material-symbols-outlined text-tertiary/30 scale-75">check_circle</span>
        </div>
        <h3 class="text-2xl font-black text-on-surface"><?= $aprobadas ?></h3>
    </div>
    <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-error/40 shadow-sm animate-reveal" style="animation-delay: 0.2s">
        <div class="flex justify-between items-center mb-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Rechazadas</span>
            <span class="material-symbols-outlined text-error/30 scale-75">block</span>
        </div>
        <h3 class="text-2xl font-black text-on-surface"><?= $rechazadas ?></h3>
    </div>
</div>

<!-- Tabla Centrada -->
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.3s">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
          <th class="px-8 py-4 text-center">Solicitante / RFC</th>
          <th class="px-8 py-4 text-center">Tipo Cliente</th>
          <th class="px-8 py-4 text-center">Contacto</th>
          <th class="px-8 py-4 text-center">Fecha Envío</th>
          <th class="px-8 py-4 text-center">Estatus</th>
          <th class="px-8 py-4 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant/10">
      <?php if (empty($solicitudes)): ?>
        <tr><td colspan="6" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-fade-in">
          No hay solicitudes registradas con este criterio.
        </td></tr>
      <?php else: ?>
      <?php foreach ($solicitudes as $s):
        $badgeClass = match($s['estatus']) {
            'APROBADA'  => 'bg-tertiary-container/20 text-on-tertiary-container',
            'RECHAZADA' => 'bg-error-container/20 text-error',
            default     => 'bg-primary/10 text-primary border border-primary/20',
        };
      ?>
      <tr class="hover:bg-surface-container-low/30 transition-colors group animate-fade-in">
        <td class="px-8 py-5 text-center">
          <p class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($s['razon_social']) ?></p>
          <p class="text-[10px] text-on-surface-variant font-black uppercase mt-0.5"><?= htmlspecialchars($s['rfc'] ?: 'SIN RFC') ?></p>
        </td>
        <td class="px-8 py-5 text-center">
          <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant text-[10px] font-black rounded-lg uppercase tracking-wider">
            <?= htmlspecialchars($s['tipo_cliente']) ?>
          </span>
        </td>
        <td class="px-8 py-5 text-center">
            <div class="flex flex-col items-center">
                <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($s['persona_contacto'] ?: '—') ?></span>
                <span class="text-[10px] text-on-surface-variant"><?= htmlspecialchars($s['email']) ?></span>
            </div>
        </td>
        <td class="px-8 py-5 text-center text-[11px] font-bold text-on-surface-variant"><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
        <td class="px-8 py-5 text-center">
          <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $badgeClass ?>">
            <?= $s['estatus'] ?>
          </span>
        </td>
        <td class="px-8 py-5">
          <div class="flex justify-center gap-2">
            <?php if ($s['estatus'] === 'PENDIENTE'): ?>
              <form method="POST">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <input type="hidden" name="action" value="aprobar">
                <button title="Aprobar" class="w-9 h-9 flex items-center justify-center rounded-lg bg-tertiary/10 text-on-tertiary-container hover:bg-tertiary hover:text-white transition-all shadow-sm">
                  <span class="material-symbols-outlined text-[18px]">done_all</span>
                </button>
              </form>
              <form method="POST">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <input type="hidden" name="action" value="rechazar">
                <button title="Rechazar" class="w-9 h-9 flex items-center justify-center rounded-lg bg-error-container/20 text-error hover:bg-error hover:text-white transition-all shadow-sm">
                  <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
              </form>
            <?php else: ?>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface-variant/30 cursor-not-allowed">
                <span class="material-symbols-outlined text-[18px]">visibility_off</span>
              </button>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="px-8 py-4 bg-surface-container-low text-[10px] text-on-surface-variant font-black uppercase tracking-widest border-t border-outline-variant/10">
    Total solicitudes: <span class="text-on-surface"><?= count($solicitudes) ?></span>
  </div>
</div>
</main>


<?php include('../Includes/footer.php'); ?>
