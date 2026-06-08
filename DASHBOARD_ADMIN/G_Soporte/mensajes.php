<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../LOGIN/login.php");
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

// ── Acción: Marcar Leído / Eliminar ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)$_POST['id'];
    if ($_POST['action'] === 'leer') {
        $pdo->prepare("UPDATE clientes_contacto_mensajes SET leido = 1 WHERE id = ?")->execute([$id]);
        echo json_encode(['ok' => true]);
        exit;
    } elseif ($_POST['action'] === 'eliminar') {
        $pdo->prepare("DELETE FROM clientes_contacto_mensajes WHERE id = ?")->execute([$id]);
        header("Location: mensajes.php?msg=eliminado");
        exit;
    }
}

$msgFlash = $_GET['msg'] ?? '';

// ── KPIs ──────────────────────────────────────────────────────────────────────
$total = (int)$pdo->query("SELECT COUNT(*) FROM clientes_contacto_mensajes")->fetchColumn();
$no_leidos = (int)$pdo->query("SELECT COUNT(*) FROM clientes_contacto_mensajes WHERE leido = 0")->fetchColumn();
$leidos = (int)$pdo->query("SELECT COUNT(*) FROM clientes_contacto_mensajes WHERE leido = 1")->fetchColumn();

// ── Listar Mensajes ───────────────────────────────────────────────────────────
$filtro = $_GET['filtro'] ?? '';
$where = '';
if ($filtro === 'no_leido') {
    $where = 'WHERE leido = 0';
} elseif ($filtro === 'leido') {
    $where = 'WHERE leido = 1';
}

$mensajes = $pdo->query(
    "SELECT * FROM clientes_contacto_mensajes $where ORDER BY created_at DESC LIMIT 50"
)->fetchAll();

$pageTitle = 'MMPharma Portal - Centro de Soporte';
$activePage = 'soporte';
include('../Includes/header.php');
include('../Includes/sidebar.php');
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

    <?php if ($msgFlash === 'eliminado'): ?>
    <div class="mb-6 bg-error-container/40 border border-error/20 text-on-error-container px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 animate-fade-in">
        <span class="material-symbols-outlined text-red-400">delete</span> Mensaje eliminado correctamente.
    </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
                <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-on-surface-variant">Soporte</span>
            </nav>
            <h2 class="text-3xl font-extrabold tracking-tight text-on-surface animate-reveal">Bandeja de soporte</h2>
            <p class="text-on-surface-variant text-sm mt-1">Administra los mensajes recibidos desde el centro de contacto de clientes.</p>
        </div>
        <div class="flex gap-2 flex-wrap bg-surface-container-low p-1.5 rounded-2xl">
            <a href="?filtro=" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= !$filtro ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Todos</a>
            <a href="?filtro=no_leido" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='no_leido' ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Sin leer</a>
            <a href="?filtro=leido" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='leido' ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Leídos</a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-emerald-500/40 animate-reveal">
            <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Mensajes totales</span>
                <span class="material-symbols-outlined text-emerald-500/30 scale-75">chat</span>
            </div>
            <h3 class="text-2xl font-black text-on-surface"><?= $total ?></h3>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-primary/40 animate-reveal" style="animation-delay: 0.1s">
            <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Sin leer</span>
                <span class="material-symbols-outlined text-primary/30 scale-75">mark_chat_unread</span>
            </div>
            <h3 class="text-2xl font-black text-on-surface"><?= $no_leidos ?></h3>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-outline-variant/40 animate-reveal" style="animation-delay: 0.2s">
            <div class="flex justify-between items-center mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Leídos</span>
                <span class="material-symbols-outlined text-outline-variant/50 scale-75">mark_chat_read</span>
            </div>
            <h3 class="text-2xl font-black text-on-surface"><?= $leidos ?></h3>
        </div>
    </div>

    <!-- Tabla de Mensajes -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.3s">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-4">Cliente / empresa</th>
                        <th class="px-8 py-4">Asunto / mensaje</th>
                        <th class="px-8 py-4 text-center">Contacto</th>
                        <th class="px-8 py-4 text-center">Fecha envío</th>
                        <th class="px-8 py-4 text-center">Estatus</th>
                        <th class="px-8 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php if (empty($mensajes)): ?>
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-fade-in">
                            No hay mensajes registrados con este criterio.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($mensajes as $m):
                        $badgeClass = $m['leido'] 
                            ? 'bg-outline-variant/20 text-on-surface-variant/80 border border-outline-variant/20' 
                            : 'bg-primary/10 text-primary border border-primary/20';
                        
                        // Parse simple Asunto from message structure if possible, else default
                        $raw_msg = $m['mensaje'];
                        $asunto = 'Mensaje de soporte';
                        if (preg_match('/Asunto:\s*(.*)/i', $raw_msg, $matches)) {
                            $asunto = trim($matches[1]);
                        }
                        
                        // Extract text body
                        $snippet = $raw_msg;
                        if (preg_match('/Cuerpo del mensaje:\s*([\s\S]*)/i', $raw_msg, $matches)) {
                            $snippet = trim($matches[1]);
                        }
                        
                        $snippet_short = mb_strimwidth($snippet, 0, 75, '...');
                    ?>
                    <tr id="mensaje-row-<?= $m['id'] ?>" class="hover:bg-surface-container-low/30 transition-colors group animate-fade-in <?= $m['leido'] ? '' : 'font-black text-white' ?>">
                        <td class="px-8 py-5">
                            <p class="text-sm leading-tight"><?= htmlspecialchars($m['nombre']) ?></p>
                            <p class="text-[10px] text-on-surface-variant font-black uppercase mt-0.5"><?= htmlspecialchars($m['empresa'] ?: 'SIN EMPRESA') ?></p>
                        </td>
                        <td class="px-8 py-5 max-w-xs md:max-w-md">
                            <p class="text-sm truncate <?= $m['leido'] ? 'text-on-surface/90' : 'text-white' ?>"><?= htmlspecialchars($asunto) ?></p>
                            <p class="text-xs text-on-surface-variant/80 truncate mt-0.5"><?= htmlspecialchars($snippet_short) ?></p>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-semibold"><?= htmlspecialchars($m['email']) ?></span>
                                <span class="text-[10px] text-on-surface-variant"><?= htmlspecialchars($m['telefono'] ?: '—') ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center text-[11px] text-on-surface-variant"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $badgeClass ?>">
                                <?= $m['leido'] ? 'Leído' : 'Sin leer' ?>
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex justify-center gap-2">
                                <button onclick="verMensaje(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['nombre'])) ?>', '<?= htmlspecialchars(addslashes($m['email'])) ?>', '<?= htmlspecialchars(addslashes($m['telefono'])) ?>', '<?= htmlspecialchars(addslashes($m['empresa'])) ?>', '<?= htmlspecialchars(addslashes($m['mensaje'])) ?>', '<?= date('d/m/Y H:i', strtotime($m['created_at'])) ?>', '<?= htmlspecialchars($m['ip_origen']) ?>')" title="Ver mensaje" class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary/10 text-on-primary-container hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <button onclick="confirmarEliminar(<?= $m['id'] ?>)" title="Eliminar" class="w-9 h-9 flex items-center justify-center rounded-lg bg-error-container/20 text-error hover:bg-error hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 bg-surface-container-low text-[10px] text-on-surface-variant font-black uppercase tracking-widest border-t border-outline-variant/10">
            Total mensajes: <span class="text-on-surface"><?= count($mensajes) ?></span>
        </div>
    </div>
</main>

<script>
async function verMensaje(id, nombre, email, telefono, empresa, mensaje, fecha, ip) {
    try {
        const fd = new FormData();
        fd.append('action', 'leer');
        fd.append('id', id);
        const res = await fetch('mensajes.php', { method: 'POST', body: fd });
        const data = await res.json();
        
        // Dynamic row class update
        const row = document.getElementById(`mensaje-row-${id}`);
        if (row && row.classList.contains('font-black')) {
            row.classList.remove('font-black', 'text-white');
        }
    } catch (e) {
        console.error(e);
    }

    // Modal view
    Swal.fire({
        title: 'Detalle de mensaje',
        html: `
            <div class="text-left space-y-4 font-sans text-slate-300 max-w-full">
                <div>
                    <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-0.5">Cliente:</strong>
                    <span class="text-sm font-bold text-white">${nombre}</span>
                </div>
                ${empresa ? `
                <div>
                    <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-0.5">Empresa / razón social:</strong>
                    <span class="text-sm text-white">${empresa}</span>
                </div>` : ''}
                <div class="grid grid-cols-2 gap-4 border-t border-emerald-500/10 pt-3">
                    <div>
                        <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-0.5">Email:</strong>
                        <span class="text-xs text-white">${email}</span>
                    </div>
                    <div>
                        <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-0.5">Teléfono:</strong>
                        <span class="text-xs text-white">${telefono || '—'}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-0.5">Enviado el:</strong>
                        <span class="text-xs text-white">${fecha}</span>
                    </div>
                    <div>
                        <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-0.5">IP origen:</strong>
                        <span class="text-xs text-white">${ip || '—'}</span>
                    </div>
                </div>
                <div class="border-t border-emerald-500/10 pt-3">
                    <strong class="text-emerald-400 block text-[10px] font-black uppercase tracking-widest mb-1.5">Contenido completo:</strong>
                    <div class="bg-surface-container-low border border-outline-variant/30 rounded-xl p-4 text-xs text-white max-h-60 overflow-y-auto whitespace-pre-wrap leading-relaxed">${mensaje}</div>
                </div>
            </div>
        `,
        background: '#051a10',
        color: '#f1fdf7',
        showCancelButton: true,
        confirmButtonColor: '#008151',
        cancelButtonColor: '#5c1010',
        confirmButtonText: 'Entendido',
        cancelButtonText: 'Eliminar mensaje',
        customClass: {
            popup: 'rounded-3xl border border-white/5',
            confirmButton: 'rounded-xl px-5 py-2.5 font-bold uppercase text-[10px] tracking-wider',
            cancelButton: 'rounded-xl px-5 py-2.5 font-bold uppercase text-[10px] tracking-wider'
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            confirmarEliminar(id);
        } else {
            location.reload(); // Reload to refresh sidebar badge and local counts
        }
    });
}

function confirmarEliminar(id) {
    confirmAction(
        '¿Eliminar mensaje?',
        'Esta acción es definitiva y no se puede deshacer.',
        'Eliminar',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'mensajes.php';

            const actInput = document.createElement('input');
            actInput.type = 'hidden';
            actInput.name = 'action';
            actInput.value = 'eliminar';
            form.appendChild(actInput);

            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            form.appendChild(idInput);

            document.body.appendChild(form);
            form.submit();
        }
    );
}
</script>

<?php include('../Includes/footer.php'); ?>
