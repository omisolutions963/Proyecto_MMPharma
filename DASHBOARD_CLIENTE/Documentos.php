<?php
if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
 header("Location: ../login/login.php");
 exit;
}

require_once '../includes/db.php';
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
 'titulo' => 'Constancia de situación fiscal',
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
 'titulo' => 'Comprobante de domicilio',
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
$faltantes = 0;
$rechazados = 0;
$pendientes = 0;
$aprobados = 0;
$total_req = count($documentos_requeridos);

foreach ($documentos_requeridos as $tipo => $info) {
 if (!isset($docs_subidos[$tipo])) {
 $faltantes++;
 } else {
 $st = $docs_subidos[$tipo]['estatus_validacion'];
 if ($st === 'RECHAZADO') $rechazados++;
 elseif ($st === 'PENDIENTE') $pendientes++;
 elseif ($st === 'APROBADO') $aprobados++;
 }
}

$pageTitle = 'MMPharma Portal - Mis Documentos';
$activePage = 'documentos';
include('includes/header.php');
include('includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
 
 <!-- Header -->
 <div class="mb-8 animate-reveal">
 <h1 class="text-3xl font-extrabold text-white tracking-tight">Mis documentos</h1>
 </div>
  <!-- Alert Banner -->
  <?php if ($aprobados === $total_req): ?>
  <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-2xl p-5 md:p-6 mb-8 flex items-center gap-4 animate-reveal delay-100">
    <div class="w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
      <span class="material-symbols-outlined text-[24px]">check_circle</span>
    </div>
    <div>
      <h3 class="text-base font-bold text-white mb-1">Todos tus documentos están vigentes</h3>
      <p class="text-sm text-emerald-200/80">Tu cuenta se encuentra al día y autorizada para compras controladas.</p>
    </div>
  </div>
  <?php elseif ($faltantes > 0 || $rechazados > 0): ?>
  <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5 md:p-6 mb-8 flex items-center gap-4 animate-reveal delay-100">
    <div class="w-12 h-12 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(245,158,11,0.2)]">
      <span class="material-symbols-outlined text-[24px]">warning</span>
    </div>
    <div>
      <h3 class="text-base font-bold text-white mb-1">Atención requerida</h3>
      <p class="text-sm text-amber-200/80">Tienes documentos pendientes por subir o rechazados que requieren tu acción.</p>
    </div>
  </div>
  <?php elseif ($pendientes > 0): ?>
  <div class="bg-sky-500/10 border border-sky-500/30 rounded-2xl p-5 md:p-6 mb-8 flex items-center gap-4 animate-reveal delay-100">
    <div class="w-12 h-12 rounded-full bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(14,165,233,0.2)]">
      <span class="material-symbols-outlined text-[24px]">pending</span>
    </div>
    <div>
      <h3 class="text-base font-bold text-white mb-1">Documentos en revisión</h3>
      <p class="text-sm text-sky-200/80">Hemos recibido tus documentos. Nuestro equipo de operaciones los validará pronto.</p>
    </div>
  </div>
  <?php endif; ?>

  <!-- Documents Grid -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-200">
  
  <?php 
  $idx = 0;
  foreach($documentos_requeridos as $tipo => $info): 
    $idx++;
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
      $statusText = 'Documento aceptado.';
      $statusIcon = 'check_circle';
      $statusColor = 'text-tertiary';
    } elseif ($estatus === 'PENDIENTE') {
      $borderClass = 'border-secondary/30 hover:border-secondary/50';
      $bgIcon = 'bg-secondary/10 text-secondary';
      $badgeBg = 'bg-secondary/10 border-secondary/30 text-secondary';
      $badgeDot = 'bg-secondary';
      $badgeText = 'En revisión';
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
      $statusText = 'Faltante — este documento es requerido para realizar compras.';
      $statusIcon = 'warning';
      $statusColor = 'text-error';
    }
  ?>
  <div class="bg-surface-container-lowest border <?= $borderClass ?> rounded-2xl p-6 flex flex-col relative group transition-colors animate-reveal" style="animation-delay: <?= $idx * 0.1 ?>s">
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
    
    <div class="text-[10px] font-bold uppercase tracking-wider mb-5">
      <span class="block text-on-surface-variant/70 mb-1">Última carga</span>
      <span class="text-white"><?= $subido ? date('d M Y', strtotime($doc['fecha_subida'])) : 'Nunca' ?></span>
    </div>

    <!-- File Area -->
    <?php if($subido): 
        $full_path = '../' . $doc['ruta_archivo'];
        $file_size = '0 KB';
        $file_name = 'Documento no encontrado';
        if(file_exists($full_path)){
            $bytes = filesize($full_path);
            $file_size = $bytes > 1048576 ? round($bytes/1048576, 2).' MB' : round($bytes/1024, 2).' KB';
            $file_name = basename($doc['ruta_archivo']);
        }
    ?>
    <div class="bg-surface-container/20 rounded-xl p-4 flex flex-col gap-3 mt-auto border border-outline-variant/30">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center shrink-0 text-primary">
                <span class="material-symbols-outlined text-[20px]">description</span>
            </div>
            <div class="overflow-hidden flex-1">
                <p class="text-white text-xs font-bold truncate" title="<?= $file_name ?>"><?= $file_name ?></p>
                <p class="text-on-surface-variant text-[10px] mt-0.5"><?= $file_size ?></p>
            </div>
        </div>
        <div class="flex gap-2 mt-2">
            <a href="../<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank" class="flex-1 py-2 bg-surface hover:bg-surface-container-high text-primary text-xs font-bold rounded-lg transition-colors text-center flex items-center justify-center gap-1 border border-outline-variant/30"><span class="material-symbols-outlined text-[16px]">visibility</span> Ver</a>
            <div class="flex-1 relative">
                <button type="button" class="w-full py-2 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1 border border-primary/20"><span class="material-symbols-outlined text-[16px]">upload</span> Reemplazar</button>
                <input type="file" id="file_<?= $tipo ?>" name="documento_<?= $tipo ?>" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="file-upload-wrapper border-2 border-dashed border-outline-variant/50 bg-surface-container/10 hover:bg-surface-container/20 rounded-xl p-5 transition-all duration-300 relative flex flex-col items-center justify-center text-center cursor-pointer mt-auto group">
      <input type="file" id="file_<?= $tipo ?>" name="documento_<?= $tipo ?>" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png">
      <div class="pointer-events-none flex flex-col items-center gap-3">
        <span class="material-symbols-outlined text-primary text-3xl file-icon transition-transform group-hover:scale-110">cloud_upload</span>
        <p class="text-xs text-on-surface-variant file-name-display font-medium tracking-normal normal-case">Arrastra o haz clic para subir</p>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>

 <!-- Upload Area -->
 <div class="border-2 border-dashed border-outline-variant/50 bg-surface-container/20 rounded-3xl p-12 flex flex-col items-center justify-center text-center transition-colors hover:bg-surface-container/40 animate-reveal delay-300">
 <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center text-primary mb-6 ">
 <span class="material-symbols-outlined text-[32px]">upload_file</span>
 </div>
 <h3 class="text-xl font-bold text-white mb-2">Gestión segura</h3>
 <p class="text-on-surface-variant text-sm mb-8 max-w-md">Todos los documentos cargados son almacenados en un entorno seguro y validados por nuestro equipo de operaciones.</p>
 </div>

 <!-- Input file oculto para cargar archivos -->
 <form id="formUpload" style="display:none;" method="POST" enctype="multipart/form-data">
 <input type="file" id="inputFile" name="documento" accept=".pdf,.jpg,.jpeg,.png">
 <input type="hidden" id="tipoDocumento" name="tipo_documento">
 <input type="hidden" name="action" value="upload_documento">
 </form>

</main>
<?php include('includes/footer.php'); ?>

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

 // Only set up drag-drop for the dashed upload zone (not the replace button)
 if (wrapper) {
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
 } else {
 // This is a replace button input — just listen for change
 input.addEventListener('change', (e) => {
 if(input.files.length > 0) {
 validateAndUpload(input.files[0], null, null, input);
 }
 });
 }
 });

 function validateAndUpload(file, displayElement, iconElement, inputElement) {
 if (file.size > maxFileSize) {
 Swal.fire({icon: 'error', title: 'Archivo muy grande', text: 'El archivo supera el límite de 15MB.', background: '#071628', color: '#fff'});
 inputElement.value = ''; 
 if (displayElement) displayElement.textContent = "Arrastra o haz clic para subir";
 if (iconElement) iconElement.textContent = "cloud_upload";
 return;
 }

 if (displayElement) {
 displayElement.textContent = file.name;
 }
 if (iconElement) {
 iconElement.textContent = "check_circle";
 iconElement.classList.replace('text-primary', 'text-tertiary');
 }
 
 const tipoDocumento = inputElement.name.replace('documento_', '');
 const formData = new FormData();
 formData.append('documento', file);
 formData.append('tipo_documento', tipoDocumento);
 formData.append('action', 'upload');

 Swal.fire({
 title: 'Subiendo archivo...',
 text: 'Por favor espera',
 allowOutsideClick: false,
 didOpen: () => { Swal.showLoading(); }
 });
 
 fetch('api_documentos.php', {
 method: 'POST',
 body: formData
 })
 .then(r => r.json())
 .then(data => {
 if(data.status === 'success') {
 Swal.fire({icon: 'success', title: '¡Documento subido!', background: '#071628', color: '#fff', showConfirmButton: false, timer: 1500}).then(() => location.reload());
 } else {
 Swal.fire({icon: 'error', title: 'Error', text: data.message, background: '#071628', color: '#fff'});
 if (displayElement) displayElement.textContent = "Arrastra o haz clic para subir";
 if (iconElement) iconElement.textContent = "cloud_upload";
 inputElement.value = '';
 }
 }).catch(e => {
 Swal.fire({icon: 'error', title: 'Error de red', background: '#071628', color: '#fff'});
 });
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
 
 const formData = new FormData();
 formData.append('tipo_documento', tipo);
 formData.append('action', 'delete');

 fetch('api_documentos.php', {
 method: 'POST',
 body: formData
 })
 .then(r => r.json())
 .then(data => {
 if(data.status === 'success') {
 Swal.fire({icon: 'success', title: 'Documento eliminado', background: '#071628', color: '#fff', showConfirmButton: false, timer: 1500}).then(() => location.reload());
 } else {
 Swal.fire({icon: 'error', title: 'Error', text: data.message, background: '#071628', color: '#fff'});
 }
 }).catch(e => {
 Swal.fire({icon: 'error', title: 'Error de red', background: '#071628', color: '#fff'});
 });
 }
 });
}
</script>
