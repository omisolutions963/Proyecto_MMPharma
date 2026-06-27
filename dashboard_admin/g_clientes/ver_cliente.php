<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header("Location: clientes.php");
    exit;
}

// ── ACCIONES POST PARA VALIDAR DOCUMENTOS ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $doc_id = (int)($_POST['doc_id'] ?? 0);
    $notas = trim($_POST['notas_admin'] ?? '');

    if ($doc_id > 0) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE clientes_documentos SET estatus_validacion = 'APROBADO', notas_admin = ? WHERE id = ? AND cliente_id = ?");
            $stmt->execute([$notas, $doc_id, $id]);
            header("Location: ver_cliente.php?id=$id&msg=approved");
            exit;
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE clientes_documentos SET estatus_validacion = 'RECHAZADO', notas_admin = ? WHERE id = ? AND cliente_id = ?");
            $stmt->execute([$notas, $doc_id, $id]);
            header("Location: ver_cliente.php?id=$id&msg=rejected");
            exit;
        }
    }
}

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$id]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: clientes.php");
    exit;
}

// Obtener documentos del cliente
$stmtDoc = $pdo->prepare("SELECT * FROM clientes_documentos WHERE cliente_id = ? ORDER BY fecha_subida DESC");
$stmtDoc->execute([$id]);
$documentos = $stmtDoc->fetchAll();

// Add registration documents from clientes_usuarios if they exist
$docs_registro = [
    'doc_constancia_fiscal' => 'Constancia Fiscal',
    'doc_licencia_sanitaria' => 'Licencia Sanitaria',
    'doc_comprobante_domicilio' => 'Comprobante Domicilio',
    'doc_alta_hacienda' => 'Alta Hacienda',
    'doc_identificacion_oficial' => 'Identificacion Oficial',
    'doc_acta_constitutiva' => 'Acta Constitutiva'
];

foreach ($docs_registro as $col => $label) {
    if (!empty($cliente[$col])) {
        $documentos[] = [
            'id' => 0,
            'cliente_id' => $id,
            'tipo_documento' => $label,
            'ruta_archivo' => 'uploads/documentos_registro/' . $cliente[$col],
            'estatus_validacion' => 'APROBADO',
            'fecha_subida' => $cliente['created_at'],
            'notas_admin' => 'Documento adjunto en el registro',
            'is_registro' => true
        ];
    }
}

$pageTitle = "MMPharma Portal - Detalles de Cliente";
$activePage = "clientes";
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<!-- Header con Breadcrumb -->
<div class="flex justify-between items-end mb-8 animate-reveal">
    <div>
        <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
            <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <a href="clientes.php" class="hover:text-primary transition-colors">Clientes</a>
            <span class="material-symbols-outlined text-[12px]">chevron_right</span>
            <span class="text-on-surface-variant">Detalles del Socio Comercial</span>
        </nav>
        <h1 class="text-3xl font-black tracking-tight flex items-center gap-3">
            <?= htmlspecialchars($cliente['razon_social']) ?>
            <?php
            $stColor = match($cliente['estatus']){
                'ACTIVO' => 'bg-tertiary-container/20 text-on-tertiary-container border border-tertiary-container/30',
                'INACTIVO' => 'bg-error-container/20 text-error border border-error/30',
                default => 'bg-surface-container-high text-on-surface-variant border border-outline-variant/30'
            };
            ?>
            <span class="text-[11px] px-3 py-1 rounded-full uppercase tracking-widest font-black <?= $stColor ?>">
                <?= $cliente['estatus'] ?>
            </span>
        </h1>
        <p class="text-on-surface-variant text-sm mt-1">RFC: <span class="font-bold text-on-surface"><?= $cliente['rfc'] ?: 'No registrado' ?></span> &bull; Tipo: <span class="text-primary font-bold"><?= $cliente['tipo'] ?></span></p>
    </div>
    <div class="flex gap-3">
        <a href="clientes.php" class="flex items-center gap-2 px-5 py-2.5 rounded-xl border border-outline-variant/30 text-on-surface-variant hover:bg-surface-container-high hover:text-white transition-all text-xs font-bold uppercase tracking-widest">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Volver
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-reveal" style="animation-delay: 0.2s;">
    <!-- COLUMNA IZQUIERDA: INFORMACIÓN GENERAL -->
    <div class="lg:col-span-1 flex flex-col gap-6">
        
        <!-- Tarjeta: Datos Generales y Contacto -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <h2 class="text-sm font-black uppercase tracking-widest text-primary mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">account_box</span>
                Datos de Contacto
            </h2>
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Nombre Comercial</span>
                    <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['nombre_comercial'] ?: 'N/A') ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Persona de Contacto</span>
                    <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['persona_contacto'] ?: 'No asignado') ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Email Profesional</span>
                    <p class="text-sm font-medium text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-[14px] text-tertiary">mail</span>
                        <a href="mailto:<?= $cliente['email'] ?>" class="hover:text-tertiary transition-colors"><?= htmlspecialchars($cliente['email']) ?></a>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Teléfono Local</span>
                        <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['telefono_local'] ?: 'N/A') ?></p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Celular</span>
                        <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['telefono_celular'] ?: 'N/A') ?></p>
                    </div>
                </div>
                <div class="pt-2 border-t border-outline-variant/10">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Representante Legal</span>
                    <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['representante_legal'] ?: 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Tarjeta: Información Fiscal -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <h2 class="text-sm font-black uppercase tracking-widest text-tertiary mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                Información Fiscal
            </h2>
            <div class="space-y-4">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Régimen Fiscal</span>
                    <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['regimen_fiscal'] ?: 'No especificado') ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Uso de CFDI</span>
                    <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['uso_cfdi'] ?: 'No especificado') ?></p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Domicilio Fiscal</span>
                    <p class="text-sm font-medium text-on-surface leading-relaxed">
                        <?= htmlspecialchars($cliente['domicilio_fiscal']) ?> <?= htmlspecialchars($cliente['colonia_fiscal']) ?><br>
                        <?= htmlspecialchars($cliente['ciudad_fiscal']) ?>, <?= htmlspecialchars($cliente['estado_fiscal']) ?> C.P. <?= htmlspecialchars($cliente['cp_fiscal']) ?>
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-outline-variant/10">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Doc. Requerido</span>
                        <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['documento_tipo']) ?></p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Método de Pago</span>
                        <p class="text-sm font-medium text-on-surface"><?= htmlspecialchars($cliente['metodo_pago']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <!-- COLUMNA DERECHA: DOCUMENTOS Y ENTREGAS -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        
        <!-- Tarjeta: Gestión de Documentos -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-xl overflow-hidden flex flex-col h-full">
            <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center bg-primary/5">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">folder_open</span>
                        Expediente de Documentos
                    </h2>
                    <p class="text-[11px] text-on-surface-variant mt-1">Valide la documentación legal y fiscal proporcionada por el cliente.</p>
                </div>
            </div>
            
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
                            <th class="px-6 py-4">Documento</th>
                            <th class="px-6 py-4">Fecha Subida</th>
                            <th class="px-6 py-4 text-center">Estatus</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        <?php if (empty($documentos)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-on-surface-variant text-sm font-medium italic">
                                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3 opacity-20">inventory_2</span>
                                    Este cliente aún no ha subido ningún documento.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documentos as $doc): ?>
                            <tr class="group hover:bg-surface-container-low/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-on-surface"><?= str_replace('_', ' ', $doc['tipo_documento']) ?></span>
                                        <?php if ($doc['notas_admin']): ?>
                                            <span class="text-[10px] text-on-surface-variant mt-1 italic max-w-xs truncate" title="<?= htmlspecialchars($doc['notas_admin']) ?>">Nota: <?= htmlspecialchars($doc['notas_admin']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-on-surface-variant font-medium"><?= date('d M Y, H:i', strtotime($doc['fecha_subida'])) ?></span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $estColor = match($doc['estatus_validacion']){
                                        'APROBADO' => 'bg-tertiary-container/20 text-on-tertiary-container',
                                        'RECHAZADO' => 'bg-error-container/20 text-error',
                                        default => 'bg-surface-container-high text-primary'
                                    };
                                    ?>
                                    <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $estColor ?>">
                                        <?= $doc['estatus_validacion'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        <!-- Botón Ver Archivo -->
                                        <a href="../../<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all" title="Ver documento">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        </a>
                                        
                                        <?php if (empty($doc['is_registro'])): ?>
                                        <!-- Botón Validar (Abre Modal) -->
                                        <button onclick='abrirValidacion(<?= json_encode($doc) ?>)' class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-tertiary hover:bg-tertiary hover:text-white transition-all" title="Validar documento">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
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
        </div>

        <!-- Tarjeta: Datos de Entrega y Notas -->
        <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 p-6 shadow-xl">
            <h2 class="text-sm font-black uppercase tracking-widest text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                Datos de Entrega y Logística
            </h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Domicilio de Entrega</span>
                    <p class="text-sm font-medium text-on-surface leading-relaxed">
                        <?= htmlspecialchars($cliente['domicilio_entrega'] ?: 'N/A') ?> <?= htmlspecialchars($cliente['colonia_entrega'] ?: '') ?><br>
                        <?= htmlspecialchars($cliente['ciudad_entrega'] ?: '') ?>, <?= htmlspecialchars($cliente['estado_entrega'] ?: '') ?> C.P. <?= htmlspecialchars($cliente['cp_entrega'] ?: '') ?>
                    </p>
                </div>
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Detalles Logísticos</span>
                    <ul class="text-sm font-medium text-slate-800 space-y-1">
                        <li><span class="text-on-surface-variant">Receptor:</span> <?= htmlspecialchars($cliente['receptor_entrega'] ?: 'N/A') ?></li>
                        <li><span class="text-on-surface-variant">Horario:</span> <?= htmlspecialchars($cliente['horario_entrega'] ?: 'N/A') ?></li>
                    </ul>
                </div>
                <div class="col-span-2 pt-2 border-t border-outline-variant/10">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Notas Administrativas</span>
                    <div class="bg-surface-container-low p-4 rounded-xl text-sm text-on-surface font-medium border-l-4 border-primary">
                        <?= nl2br(htmlspecialchars($cliente['notas'] ?: 'Sin notas registradas para este cliente.')) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</main>

<!-- MODAL DE VALIDACIÓN DE DOCUMENTO -->
<div id="modalValidacion" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
    <div onclick="cerrarValidacion()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div id="modalValidacionPanel" class="relative bg-surface rounded-3xl w-full max-w-md border border-outline-variant/20 shadow-2xl scale-95 opacity-0 transition-all duration-300 overflow-hidden">
        <div class="p-6 border-b border-outline-variant/10 bg-primary/5 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-black text-on-surface tracking-tight">Validar Documento</h3>
                <p id="valDocType" class="text-xs text-primary font-bold uppercase mt-1"></p>
            </div>
            <button onclick="cerrarValidacion()" class="text-on-surface-variant hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form method="POST" class="p-6 space-y-6">
            <input type="hidden" name="action" id="valAction" value="">
            <input type="hidden" name="doc_id" id="valDocId" value="">
            
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Notas / Motivo (Opcional)</label>
                <textarea name="notas_admin" id="valNotas" rows="3" class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface transition-all resize-none" placeholder="Escriba el motivo de rechazo o una nota de aprobación..."></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="submitValidacion('reject')" class="flex-1 py-3 bg-error-container/20 text-error hover:bg-error hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">Rechazar</button>
                <button type="button" onclick="submitValidacion('approve')" class="flex-1 py-3 bg-tertiary/20 text-tertiary hover:bg-tertiary hover:text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">Aprobar</button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert Notificaciones -->
<?php if (isset($_GET['msg'])): ?>
<script>
    Swal.fire({
        title: '¡Actualizado!',
        text: '<?= $_GET['msg'] === 'approved' ? 'El documento ha sido APROBADO exitosamente.' : 'El documento ha sido RECHAZADO.' ?>',
        icon: 'success',
        confirmButtonColor: '#003e79',
        background: '#ffffff',
        color: '#0f172a'
    });
</script>
<?php endif; ?>

<script>
function abrirValidacion(doc) {
    document.getElementById('valDocId').value = doc.id;
    document.getElementById('valDocType').innerText = doc.tipo_documento.replace(/_/g, ' ');
    document.getElementById('valNotas').value = doc.notas_admin || '';
    
    document.getElementById('modalValidacion').classList.remove('hidden');
    setTimeout(() => {
        const panel = document.getElementById('modalValidacionPanel');
        panel.classList.remove('scale-95', 'opacity-0');
        panel.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function cerrarValidacion() {
    const panel = document.getElementById('modalValidacionPanel');
    panel.classList.remove('scale-100', 'opacity-100');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => document.getElementById('modalValidacion').classList.add('hidden'), 300);
}

function submitValidacion(actionStr) {
    document.getElementById('valAction').value = actionStr;
    document.querySelector('#modalValidacionPanel form').submit();
}
</script>

<?php include("../includes/footer.php"); ?>
