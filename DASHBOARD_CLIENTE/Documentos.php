<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    header("Location: ../LOGIN/login.php");
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

// Obtener documentos del cliente
$stmt = $pdo->prepare("
    SELECT tipo_documento, ruta_archivo, estatus_validacion, fecha_subida 
    FROM clientes_documentos 
    WHERE cliente_id = ? 
    ORDER BY fecha_subida DESC
");
$stmt->execute([$cliente_id]);
$docs_subidos = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!isset($docs_subidos[$row['tipo_documento']])) {
        $docs_subidos[$row['tipo_documento']] = $row;
    }
}

// Configuración de documentos requeridos
$documentos_requeridos = [
    'LICENCIA_SANITARIA' => [
        'titulo' => 'Licencia Sanitaria',
        'desc' => 'Permiso federal para la comercialización de insumos de salud y psicotrópicos.',
        'icono' => 'medical_services'
    ],
    'CONSTANCIA_FISCAL' => [
        'titulo' => 'Constancia Fiscal',
        'desc' => 'Situación fiscal actualizada (SAT) para facturación y validación de RFC.',
        'icono' => 'receipt_long'
    ],
    'AVISO_FUNCIONAMIENTO' => [
        'titulo' => 'Aviso de Funcionamiento',
        'desc' => 'Documentación de alta ante COFEPRIS para la operación del establecimiento.',
        'icono' => 'storefront'
    ]
];

// Determinar estatus global
$faltantes_o_rechazados = 0;
foreach ($documentos_requeridos as $tipo => $info) {
    if (!isset($docs_subidos[$tipo]) || $docs_subidos[$tipo]['estatus_validacion'] !== 'APROBADO') {
        $faltantes_o_rechazados++;
    }
}

$pageTitle  = 'MMPharma Portal - Mis Documentos';
$activePage = 'documentos';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Header -->
    <div class="mb-8 animate-reveal">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Mis Documentos</h1>
    </div>

    <!-- Alert Banner -->
    <?php if ($faltantes_o_rechazados === 0): ?>
    <div class="bg-tertiary-container/20 border border-tertiary/40 rounded-2xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm animate-reveal delay-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-tertiary/20 text-tertiary flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(52,196,122,0.3)]">
                <span class="material-symbols-outlined text-[24px]">check_circle</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white mb-0.5">Todos tus documentos están vigentes</h3>
                <p class="text-sm text-tertiary/80">Tu cuenta se encuentra al día y autorizada para compras controladas.</p>
            </div>
        </div>
        <button class="px-5 py-2.5 bg-tertiary hover:bg-tertiary-fixed-dim text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-tertiary/20 flex items-center gap-2 w-full md:w-auto justify-center">
            <span class="material-symbols-outlined text-[18px]">download</span> Descargar Expediente
        </button>
    </div>
    <?php else: ?>
    <div class="bg-[#422c10] border border-[#a66a1d] rounded-2xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm animate-reveal delay-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-[#eab308]/20 text-[#eab308] flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(234,179,8,0.3)]">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white mb-0.5">Atención Requerida</h3>
                <p class="text-sm text-[#fef08a]/80">Tienes documentos pendientes por subir, en revisión o rechazados.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Documents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-200">
        
        <?php 
        $idx = 0;
        foreach($documentos_requeridos as $tipo => $info): 
            $idx++;
        ?>
            <?php 
                $subido = isset($docs_subidos[$tipo]);
                $doc = $subido ? $docs_subidos[$tipo] : null;
                $estatus = $doc ? $doc['estatus_validacion'] : 'FALTANTE';
                
                // Colores y badges
                if ($estatus === 'APROBADO') {
                    $borderClass = 'border-tertiary/30 hover:border-tertiary/50';
                    $bgIcon = 'bg-tertiary/10 text-tertiary';
                    $badgeBg = 'bg-tertiary/10 border-tertiary/30 text-tertiary';
                    $badgeDot = 'bg-tertiary';
                    $badgeText = 'Aprobado';
                } elseif ($estatus === 'PENDIENTE') {
                    $borderClass = 'border-secondary/30 hover:border-secondary/50';
                    $bgIcon = 'bg-secondary/10 text-secondary';
                    $badgeBg = 'bg-secondary/10 border-secondary/30 text-secondary';
                    $badgeDot = 'bg-secondary';
                    $badgeText = 'En Revisión';
                } elseif ($estatus === 'RECHAZADO') {
                    $borderClass = 'border-error/30 hover:border-error/50';
                    $bgIcon = 'bg-error/10 text-error';
                    $badgeBg = 'bg-error/10 border-error/30 text-error';
                    $badgeDot = 'bg-error';
                    $badgeText = 'Rechazado';
                } else {
                    $borderClass = 'border-outline-variant/30 hover:border-outline-variant';
                    $bgIcon = 'bg-surface-container-high text-on-surface';
                    $badgeBg = 'bg-surface-container-high border-outline-variant/30 text-on-surface-variant';
                    $badgeDot = 'bg-on-surface-variant';
                    $badgeText = 'Faltante';
                }
            ?>
        <div class="bg-surface-container-lowest border <?= $borderClass ?> rounded-2xl p-6 flex flex-col shadow-sm relative group transition-colors animate-reveal" style="animation-delay: <?= $idx * 0.1 ?>s">
            <div class="absolute top-6 right-6">
                <span class="px-2.5 py-1 <?= $badgeBg ?> border text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full <?= $badgeDot ?>"></span> <?= $badgeText ?>
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl <?= $bgIcon ?> flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[20px]"><?= $info['icono'] ?></span>
            </div>
            <h3 class="text-base font-bold text-white mb-2"><?= $info['titulo'] ?></h3>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-6 flex-1"><?= $info['desc'] ?></p>
            
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider mb-5">
                <div>
                    <span class="block text-on-surface-variant/70 mb-1">Última Carga</span>
                    <span class="text-white"><?= $subido ? date('d M Y', strtotime($doc['fecha_subida'])) : 'Nunca' ?></span>
                </div>
            </div>

            <div class="flex gap-2">
                <?php if($subido): ?>
                <a href="../<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank" class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high text-primary text-sm font-bold rounded-xl transition-colors text-center block" style="line-height: 20px;">Ver Archivo</a>
                <?php endif; ?>
                <button class="flex-1 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-primary/20" onclick="subirDocumento('<?= $tipo ?>')">
                    <?= $subido ? 'Actualizar' : 'Subir Archivo' ?>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Upload Area -->
    <div class="border-2 border-dashed border-outline-variant/50 bg-surface-container/20 rounded-3xl p-12 flex flex-col items-center justify-center text-center transition-colors hover:bg-surface-container/40 animate-reveal delay-300">
        <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center text-primary mb-6 shadow-md">
            <span class="material-symbols-outlined text-[32px]">upload_file</span>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Gestión Segura</h3>
        <p class="text-on-surface-variant text-sm mb-8 max-w-md">Todos los documentos cargados son almacenados en un entorno seguro y validados por nuestro equipo de operaciones.</p>
    </div>

    <!-- Input file oculto para cargar archivos -->
    <form id="formUpload" style="display:none;" method="POST" enctype="multipart/form-data">
        <input type="file" id="inputFile" name="documento" accept=".pdf,.jpg,.jpeg,.png">
        <input type="hidden" id="tipoDocumento" name="tipo_documento">
        <input type="hidden" name="action" value="upload_documento">
    </form>

</main>
<?php include('Includes/footer.php'); ?>

<script>
function subirDocumento(tipo) {
    Swal.fire({
        title: 'Próximamente',
        text: 'La función de subida de archivos se habilitará en la siguiente fase.',
        icon: 'info',
        background: '#071628',
        color: '#fff'
    });
}
</script>
