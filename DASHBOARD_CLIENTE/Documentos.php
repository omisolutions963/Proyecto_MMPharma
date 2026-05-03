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
$stmt = $pdo->prepare("SELECT regimen_fiscal FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente_info = $stmt->fetch(PDO::FETCH_ASSOC);
$regimen_fiscal = $cliente_info['regimen_fiscal'] ?? '';
$tipo_cliente = $_SESSION['cliente_tipo'] ?? 'EMPRESA';

if ($tipo_cliente === 'EMPRESA') {
    $documentos_requeridos = [
        'CONSTANCIA_FISCAL' => [
            'titulo' => 'Constancia de Situación Fiscal',
            'desc' => 'Situación fiscal actualizada (SAT) para facturación y validación de RFC.',
            'icono' => 'receipt_long'
        ]
    ];
} else {
    $documentos_requeridos = [
        'AVISO_FUNCIONAMIENTO' => [
            'titulo' => 'Aviso de funcionamiento o Licencia Sanitaria',
            'desc' => 'Documentación de alta ante COFEPRIS para la operación del establecimiento.',
            'icono' => 'medical_services'
        ],
        'COMPROBANTE_DOMICILIO' => [
            'titulo' => 'Comprobante de Domicilio',
            'desc' => 'Recibo no mayor a 3 meses.',
            'icono' => 'home'
        ],
        'ALTA_HACIENDA' => [
            'titulo' => 'Alta de Hacienda',
            'desc' => 'Documento de alta ante el SAT.',
            'icono' => 'account_balance'
        ],
        'IDENTIFICACION_OFICIAL' => [
            'titulo' => 'Identificación oficial del representante legal o propietario',
            'desc' => 'INE o Pasaporte vigente.',
            'icono' => 'badge'
        ]
    ];

    if (strtolower($regimen_fiscal) === 'moral' || strtolower($regimen_fiscal) === 'personas morales') {
        $documentos_requeridos['ACTA_CONSTITUTIVA'] = [
            'titulo' => 'Copia del acta constitutiva',
            'desc' => 'Documento notarial de la empresa.',
            'icono' => 'description'
        ];
    }
}

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
                    $statusText = 'Documento Aceptado.';
                    $statusIcon = 'check_circle';
                    $statusColor = 'text-tertiary';
                } elseif ($estatus === 'PENDIENTE') {
                    $borderClass = 'border-secondary/30 hover:border-secondary/50';
                    $bgIcon = 'bg-secondary/10 text-secondary';
                    $badgeBg = 'bg-secondary/10 border-secondary/30 text-secondary';
                    $badgeDot = 'bg-secondary';
                    $badgeText = 'En Revisión';
                    $statusText = 'Tu documento está siendo validado.';
                    $statusIcon = 'pending';
                    $statusColor = 'text-secondary';
                } elseif ($estatus === 'RECHAZADO') {
                    $borderClass = 'border-error/30 hover:border-error/50';
                    $bgIcon = 'bg-error/10 text-error';
                    $badgeBg = 'bg-error/10 border-error/30 text-error';
                    $badgeDot = 'bg-error';
                    $badgeText = 'Rechazado';
                    $statusText = 'Tu documento fue rechazado. Sube uno nuevo.';
                    $statusIcon = 'error';
                    $statusColor = 'text-error';
                } else {
                    $borderClass = 'border-error/30 hover:border-error/50';
                    $bgIcon = 'bg-error/10 text-error';
                    $badgeBg = 'bg-error/10 border-error/30 text-error';
                    $badgeDot = 'bg-error';
                    $badgeText = 'Faltante';
                    $statusText = 'Faltante — Este documento es requerido para realizar compras.';
                    $statusIcon = 'warning';
                    $statusColor = 'text-error';
                }
            ?>
        <div class="bg-surface-container-lowest border <?= $borderClass ?> rounded-2xl p-6 flex flex-col shadow-sm relative group transition-colors animate-reveal" style="animation-delay: <?= $idx * 0.1 ?>s">
            <div class="absolute top-6 right-6">
                <span class="px-2.5 py-1 <?= $badgeBg ?> border text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full <?= $badgeDot ?>"></span> <?= $badgeText ?>
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl <?= $bgIcon ?> flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[20px]"><?= $statusIcon ?></span>
            </div>
            <h3 class="text-base font-bold text-white mb-2"><?= $info['titulo'] ?></h3>
            <p class="text-xs font-bold <?= $statusColor ?> leading-relaxed mb-4"><?= $statusText ?></p>
            
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider mb-5">
                <div>
                    <span class="block text-on-surface-variant/70 mb-1">Última Carga</span>
                    <span class="text-white"><?= $subido ? date('d M Y', strtotime($doc['fecha_subida'])) : 'Nunca' ?></span>
                </div>
            </div>

            <!-- Upload Area -->
            <div class="file-upload-wrapper border-2 border-dashed border-outline-variant/50 bg-surface-container/10 rounded-xl p-4 transition-all duration-300 relative flex flex-col items-center justify-center text-center group-hover:bg-surface-container/20 mt-auto">
                <input type="file" id="file_<?= $tipo ?>" name="documento_<?= $tipo ?>" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png">
                <div class="pointer-events-none flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-2xl file-icon">cloud_upload</span>
                    <p class="text-xs text-on-surface-variant file-name-display">Arrastra o haz clic para subir</p>
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <?php if($subido): ?>
                <a href="../<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank" class="flex-1 py-2 bg-surface-container hover:bg-surface-container-high text-primary text-xs font-bold rounded-xl transition-colors text-center flex items-center justify-center gap-1"><span class="material-symbols-outlined text-[16px]">visibility</span> Ver</a>
                <button class="flex-none px-3 py-2 bg-error/10 hover:bg-error/20 text-error text-xs font-bold rounded-xl transition-colors flex items-center justify-center" onclick="eliminarDocumento('<?= $tipo ?>')"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                <?php endif; ?>
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

<style>
.drag-over {
    border-color: #32b4ca !important;
    background-color: rgba(50, 180, 202, 0.1) !important;
}
</style>

<script>
    const maxFileSize = 15 * 1024 * 1024; // 15MB

    document.querySelectorAll('.file-input').forEach(input => {
        const wrapper = input.closest('.file-upload-wrapper');
        const nameDisplay = wrapper.querySelector('.file-name-display');
        const icon = wrapper.querySelector('.file-icon');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => wrapper.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => wrapper.classList.remove('drag-over'), false);
        });

        wrapper.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if(files.length > 0) {
                input.files = files; 
                validateAndUpload(input.files[0], nameDisplay, icon, input);
            }
        });

        input.addEventListener('change', (e) => {
            if(input.files.length > 0) {
                validateAndUpload(input.files[0], nameDisplay, icon, input);
            }
        });
    });

    function validateAndUpload(file, displayElement, iconElement, inputElement) {
        if (file.size > maxFileSize) {
            Swal.fire({icon: 'error', title: 'Archivo muy grande', text: 'El archivo supera el límite de 15MB.', background: '#071628', color: '#fff'});
            inputElement.value = ''; 
            displayElement.textContent = "Arrastra o haz clic para subir";
            iconElement.textContent = "cloud_upload";
            return;
        }

        displayElement.textContent = file.name;
        iconElement.textContent = "check_circle";
        iconElement.classList.replace('text-primary', 'text-tertiary');
        
        // Aquí iría la lógica para enviar por AJAX el archivo inmediatamente
        Swal.fire({
            title: 'Subiendo archivo...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        // Simular subida y recargar
        setTimeout(() => {
            Swal.fire({icon: 'success', title: '¡Documento subido!', background: '#071628', color: '#fff', showConfirmButton: false, timer: 1500}).then(() => location.reload());
        }, 1500);
    }

function eliminarDocumento(tipo) {
    Swal.fire({
        title: '¿Eliminar documento?',
        text: '¿Estás seguro que deseas eliminar este documento?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ba1a1a',
        cancelButtonColor: '#747780',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#071628',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            // Simular eliminación y recargar
            setTimeout(() => {
                Swal.fire({icon: 'success', title: 'Documento eliminado', background: '#071628', color: '#fff', showConfirmButton: false, timer: 1500}).then(() => location.reload());
            }, 1000);
        }
    });
}
</script>
