<?php
$titulo = 'MMPharma | Distribuidora Farmacéutica';
$pagina_actual = 'inicio';
$base = '../';

// ── Guardar solicitud de empresa en BD ────────────────────────────────────────
$solicitud_ok = false;
$solicitud_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $campos = [
 'tipo_cliente' => 'EMPRESA',
 'razon_social' => trim($_POST['razon_social'] ?? ''),
 'rfc' => trim($_POST['rfc'] ?? ''),
 'regimen_fiscal' => trim($_POST['regimen_fiscal'] ?? ''),
 'domicilio_fiscal' => trim($_POST['domicilio'] ?? ''),
 'colonia' => trim($_POST['colonia'] ?? ''),
 'cp' => trim($_POST['cp'] ?? ''),
 'ciudad' => trim($_POST['ciudad'] ?? ''),
 'estado' => trim($_POST['estado'] ?? ''),
 'representante' => trim($_POST['representante_legal'] ?? ''),
 'nombre_comercial' => trim($_POST['nombre_comercial']?? ''),
 'giro' => trim($_POST['giro'] ?? ''),
 'persona_contacto' => trim($_POST['persona_contacto']?? ''),
 'volumen_mensual' => trim($_POST['volumen_mensual'] ?? ''),
 'telefono_local' => trim($_POST['telefono_local'] ?? ''),
 'telefono_celular' => trim($_POST['telefono_celular']?? ''),
 'email' => trim($_POST['email'] ?? ''),
 'documento_tipo' => strtoupper(trim($_POST['doc_type'] ?? 'FACTURA')),
 'metodo_pago' => strtoupper(trim($_POST['payment_method_chip'] ?? 'TRANSFERENCIA')),
 'uso_cfdi' => trim($_POST['uso_cfdi'] ?? ''),
 'domicilio_entrega'=> trim($_POST['domicilio_entrega']?? ''),
 'colonia_entrega' => trim($_POST['colonia_entrega'] ?? ''),
 'cp_entrega' => trim($_POST['cp_entrega'] ?? ''),
 'ciudad_entrega' => trim($_POST['ciudad_entrega'] ?? ''),
 'municipio_entrega'=> trim($_POST['municipio_entrega']?? ''),
 'estado_entrega' => trim($_POST['estado_entrega'] ?? ''),
 'receptor_entrega' => trim($_POST['receptor_entrega']?? ''),
 'horario_entrega' => trim($_POST['horario_entrega'] ?? ''),
 'ip_origen' => $_SERVER['REMOTE_ADDR'] ?? null,
 ];

 if ($campos['razon_social']) {
 try {
 require_once '../INCLUDES/db.php';
 $pdo = getDB();

 // --- File Upload Logic ---
 $upload_dir = '../uploads/documentos_registro/';
 $doc_constancia_fiscal = null;
 
 if (isset($_FILES['constancia_fiscal']) && $_FILES['constancia_fiscal']['error'] === UPLOAD_ERR_OK) {
 $ext = strtolower(pathinfo($_FILES['constancia_fiscal']['name'], PATHINFO_EXTENSION));
 $new_name = 'constancia_' . time() . '_' . uniqid() . '.' . $ext;
 if (move_uploaded_file($_FILES['constancia_fiscal']['tmp_name'], $upload_dir . $new_name)) {
 $doc_constancia_fiscal = $new_name;
 }
 }
 $campos['doc_constancia_fiscal'] = $doc_constancia_fiscal;
 // -------------------------

 $sql = "INSERT INTO clientes_solicitudes_registro
 (tipo_cliente,razon_social,rfc,regimen_fiscal,domicilio_fiscal,colonia,cp,ciudad,estado,
 representante,nombre_comercial,giro,persona_contacto,volumen_mensual,telefono_local,
 telefono_celular,email,documento_tipo,metodo_pago,uso_cfdi,domicilio_entrega,
 colonia_entrega,cp_entrega,ciudad_entrega,municipio_entrega,estado_entrega,
 receptor_entrega,horario_entrega,ip_origen,doc_constancia_fiscal)
 VALUES
 (:tipo_cliente,:razon_social,:rfc,:regimen_fiscal,:domicilio_fiscal,:colonia,:cp,:ciudad,:estado,
 :representante,:nombre_comercial,:giro,:persona_contacto,:volumen_mensual,:telefono_local,
 :telefono_celular,:email,:documento_tipo,:metodo_pago,:uso_cfdi,:domicilio_entrega,
 :colonia_entrega,:cp_entrega,:ciudad_entrega,:municipio_entrega,:estado_entrega,
 :receptor_entrega,:horario_entrega,:ip_origen,:doc_constancia_fiscal)";
 $pdo->prepare($sql)->execute($campos);
 header("Location: ../CONFIRMACION_REGISTRO/confirmacion.php");
 exit;
 } catch (Exception $e) {
 $solicitud_error = true;
 }
 } else {
 $solicitud_error = true;
 }
}

?>

<!DOCTYPE html><html class="light" lang="es"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Alta de Empresa - MMPharma</title>
<link rel="icon" type="image/png" href="../logos/MMPharma-Isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<style>
 body { font-family: 'Inter', sans-serif; }
 .material-symbols-outlined {
 font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
 vertical-align: middle;
 }
 </style>
 <script id="tailwind-config">
 tailwind.config = {
 darkMode: "class",
 theme: {
 extend: {
 colors: {
 "primary": "#003e79",
 "secondary": "#1e60aa",
 "tertiary": "#2ca1b5",
 "primary-light": "#60a5fa",
 "secondary-light": "#93c5fd",
 "tertiary-light": "#67e8f9",
 "background": "#0a192f",
 "surface": "#112240",
 "surface-container-low": "#1a365d",
 "surface-container": "#2a4365",
 "surface-container-high": "#2c5282",
 },
 fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] },
 borderRadius: { "DEFAULT": "0.375rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
 },
 },
 }
 </script>
</head>
<body class="bg-background text-slate-300 antialiased min-h-screen flex flex-col relative overflow-x-hidden">

<!-- Decorative Background -->
<div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
</div>

<!-- Top Navigation Bar -->
<header class="relative z-50 flex justify-between items-center w-full px-4 sm:px-8 py-4 sm:py-6 bg-background/80 backdrop-blur-md border-b border-white/5 sticky top-0">
 <a href="../SELECCIÓN_REGISTRO/selección_registro.php" class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-2xl transition-all group">
  <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
 </a>

 <a href="../INDEX/index.php" class="absolute left-1/2 -translate-x-1/2">
  <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-6 sm:h-8 w-auto hover:scale-105 transition-transform duration-300">
 </a>

 <div class="w-12 h-12"></div>
</header>

<main class="max-w-[1440px] mx-auto px-6 py-16 flex flex-col items-center relative z-10 w-full" data-aos="fade-up">
<!-- Main Form Card -->
<div class="w-full max-w-4xl bg-surface/85 backdrop-blur-xl text-slate-200 rounded-3xl overflow-hidden border border-white/10 relative shadow-2xl">
 <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary via-secondary to-tertiary"></div>
<!-- Header Section -->
<div class="px-12 py-10 border-b border-white/5">
<h1 class="text-3xl font-black tracking-tight text-white mb-3" style="">Solicitud de Empresa</h1>
<p class="text-slate-400 leading-relaxed max-w-2xl text-base font-medium" style="">
 Inicia tu proceso de registro corporativo. Toda la información será tratada bajo estrictos protocolos de confidencialidad y cumplimiento normativo.
 </p>
</div>
<form id="registroForm" action="" method="POST" class="px-12 py-12 space-y-16" enctype="multipart/form-data">
<!-- Section 1: Datos Generales -->
<section>
<div class="flex items-center gap-3 mb-8">
<div class="p-2 bg-white/5 border border-white/10 rounded-lg text-tertiary-light">
<span class="material-symbols-outlined text-2xl" style="">domain</span>
</div>
<h2 class="text-xl font-bold text-white" style="">Sección 1: Datos Generales</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-6 gap-6">
<div class="md:col-span-6">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Nombre o Razón Social</label>
<input name="razon_social" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl transition-all text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. Grupo Empresarial del Norte S.A. de C.V." type="text" required>
</div>
<div class="md:col-span-3">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">RFC</label>
<input name="rfc" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. GEN010101ABC" type="text" required>
</div>
<div class="md:col-span-3">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Régimen Fiscal</label>
<select name="regimen_fiscal" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm" required>
<option value="601 - General de Ley Personas Morales" class="bg-surface text-white">601 - General de Ley Personas Morales</option>
<option value="603 - Personas Morales con Fines no Lucrativos" class="bg-surface text-white">603 - Personas Morales con Fines no Lucrativos</option>
</select>
</div>
<div class="md:col-span-6">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Domicilio Fiscal (Calle y Número)</label>
<input name="domicilio" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. Av. Juárez 1234, Local 5" type="text" required>
</div>
<div class="md:col-span-2">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Colonia</label>
<input name="colonia" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. Centro Histórico" type="text" required>
</div>
<div class="md:col-span-1">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">C.P.</label>
<input name="cp" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. 44100" type="text" required>
</div>
<div class="md:col-span-3">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Ciudad / Municipio</label>
<input name="ciudad" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. Guadalajara" type="text" required>
</div>
<div class="md:col-span-3">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Estado</label>
<select name="estado" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm" required>
<option disabled="" selected="" value="" class="bg-surface text-slate-400">Selecciona tu estado</option>
<option value="Jalisco" class="bg-surface text-white">Jalisco</option>
<option value="Aguascalientes" class="bg-surface text-white">Aguascalientes</option><option value="Baja California" class="bg-surface text-white">Baja California</option><option value="Baja California Sur" class="bg-surface text-white">Baja California Sur</option><option value="Campeche" class="bg-surface text-white">Campeche</option><option value="Chiapas" class="bg-surface text-white">Chiapas</option><option value="Chihuahua" class="bg-surface text-white">Chihuahua</option><option value="Ciudad de México" class="bg-surface text-white">Ciudad de México</option><option value="Coahuila" class="bg-surface text-white">Coahuila</option><option value="Colima" class="bg-surface text-white">Colima</option><option value="Durango" class="bg-surface text-white">Durango</option><option value="Estado de México" class="bg-surface text-white">Estado de México</option><option value="Guanajuato" class="bg-surface text-white">Guanajuato</option><option value="Guerrero" class="bg-surface text-white">Guerrero</option><option value="Hidalgo" class="bg-surface text-white">Hidalgo</option><option value="Michoacán" class="bg-surface text-white">Michoacán</option><option value="Morelos" class="bg-surface text-white">Morelos</option><option value="Nayarit" class="bg-surface text-white">Nayarit</option><option value="Nuevo León" class="bg-surface text-white">Nuevo León</option><option value="Oaxaca" class="bg-surface text-white">Oaxaca</option><option value="Puebla" class="bg-surface text-white">Puebla</option><option value="Querétaro" class="bg-surface text-white">Querétaro</option><option value="Quintana Roo" class="bg-surface text-white">Quintana Roo</option><option value="San Luis Potosí" class="bg-surface text-white">San Luis Potosí</option><option value="Sinaloa" class="bg-surface text-white">Sinaloa</option><option value="Sonora" class="bg-surface text-white">Sonora</option><option value="Tabasco" class="bg-surface text-white">Tabasco</option><option value="Tamaulipas" class="bg-surface text-white">Tamaulipas</option><option value="Tlaxcala" class="bg-surface text-white">Tlaxcala</option><option value="Veracruz" class="bg-surface text-white">Veracruz</option><option value="Yucatán" class="bg-surface text-white">Yucatán</option><option value="Zacatecas" class="bg-surface text-white">Zacatecas</option>
</select>
</div>
<div class="md:col-span-3">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Teléfono Institucional</label>
<input name="telefono_local" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. 3312345678" type="tel" required>
</div>
<div class="md:col-span-6">
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Representante Legal</label>
<input name="representante_legal" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. Juan García Martínez" type="text" required>
</div>
</div>
</section>
<!-- Section 2: Información Empresarial -->
<section class="p-8 rounded-2xl bg-background/40 border border-white/5">
<div class="flex items-center gap-3 mb-8">
<div class="p-2 bg-white/5 border border-white/10 rounded-lg text-tertiary-light">
<span class="material-symbols-outlined text-2xl" style="">analytics</span>
</div>
<h2 class="text-xl font-bold text-white" style="">Sección 2: Información Empresarial</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div>
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Nombre Comercial</label>
<input name="nombre_comercial" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. Grupo Norte" type="text" required>
</div>
<div>
<label class="block text-sm font-semibold text-slate-300 mb-2" style="">Persona de contacto</label>
<input name="persona_contacto" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. María López Hernández" type="text" required>
</div>
<div>
<label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2" style="">
<span class="material-symbols-outlined text-sm" style="">smartphone</span>Teléfono Celular
 </label>
<input name="telefono_celular" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. 3312345678" type="tel" required>
</div>
<div>
<label class="block text-sm font-semibold text-slate-300 mb-2 flex items-center gap-2" style="">
<span class="material-symbols-outlined text-sm" style="">mail</span>Correo electrónico institucional
 </label>
<input name="email" class="w-full px-4 py-3 bg-background/50 border border-white/10 focus:border-tertiary focus:ring-2 focus:ring-tertiary/20 rounded-xl text-white outline-none text-sm placeholder:text-slate-500" placeholder="Ej. pedidos@empresa.com" type="email" required>
</div>
<div class="md:col-span-2">
<label class="block text-sm font-semibold text-slate-300 mb-4" style="">Método de pago preferente</label>
<div class="flex flex-wrap gap-3">
<label class="cursor-pointer group" style="">
<input class="peer hidden" name="payment_method_chip" type="radio" value="transferencia" checked>
<span class="px-6 py-2.5 rounded-xl border border-white/10 text-sm font-semibold bg-background/50 peer-checked:bg-tertiary peer-checked:text-white peer-checked:border-tertiary group-hover:border-tertiary/50 transition-all inline-block" style="">Transferencia</span>
</label>
<label class="cursor-pointer group" style="">
<input class="peer hidden" name="payment_method_chip" type="radio" value="cheque">
<span class="px-6 py-2.5 rounded-xl border border-white/10 text-sm font-semibold bg-background/50 peer-checked:bg-tertiary peer-checked:text-white peer-checked:border-tertiary group-hover:border-tertiary/50 transition-all inline-block" style="">Cheque</span>
</label>
<label class="cursor-pointer group" style="">
<input class="peer hidden" name="payment_method_chip" type="radio" value="efectivo">
<span class="px-6 py-2.5 rounded-xl border border-white/10 text-sm font-semibold bg-background/50 peer-checked:bg-tertiary peer-checked:text-white peer-checked:border-tertiary group-hover:border-tertiary/50 transition-all inline-block" style="">Efectivo</span>
</label>
</div>
</div>
</div>
</section>

<!-- Section 3: Documentación -->
<section class="p-8 rounded-2xl bg-background/40 border border-white/5">
<div class="flex items-center gap-3 mb-8">
<div class="p-2 bg-white/5 border border-white/10 rounded-lg text-tertiary-light">
<span class="material-symbols-outlined text-2xl" style="">folder</span>
</div>
<h2 class="text-xl font-bold text-white" style="">Sección 3: Documentación Requerida</h2>
</div>
<p class="text-sm text-slate-400 mb-6">Sube el documento escaneado. Formatos permitidos: PDF, JPG, PNG. Tamaño máximo por archivo: 15MB.</p>

<div class="space-y-4">
 <div class="file-upload-wrapper bg-background/60 border border-white/10 border-dashed rounded-xl p-5 transition-all duration-300 relative group hover:border-tertiary/50 hover:bg-tertiary/5">
 <input type="file" name="constancia_fiscal" id="constancia_fiscal" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png" required>
 <div class="flex items-center justify-between pointer-events-none">
  <div class="flex items-center gap-4">
   <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-slate-400 transition-colors icon-container group-hover:bg-tertiary/10 group-hover:text-tertiary">
    <span class="material-symbols-outlined file-icon">upload_file</span>
   </div>
   <div>
    <p class="text-base font-bold text-white">Constancia de situación fiscal <span class="text-red-600">*</span></p>
    <p class="text-sm text-slate-400 file-name-display mt-0.5">Arrastra y suelta tu archivo o haz clic para explorar</p>
   </div>
  </div>
   <div class="w-10 h-10 rounded-xl bg-white/5 group-hover:bg-tertiary group-hover:text-white border border-white/10 group-hover:border-tertiary flex items-center justify-center text-slate-300 transition-all duration-300">
    <span class="material-symbols-outlined text-lg">upload</span>
   </div>
 </div>
 </div>
</div>
</section>
<!-- Section 4: Contrato de Uso de Medicamento -->
<section class="bg-amber-950/20 rounded-2xl p-8 border border-amber-500/20">
<div class="flex items-start gap-5">
<div class="bg-amber-500/10 p-2.5 rounded-xl text-amber-400">
<span class="material-symbols-outlined text-2xl" style="">verified_user</span>
</div>
<div class="flex-1">
<h2 class="text-xl font-extrabold text-amber-400 mb-3" style="">Cláusula de Uso de Medicamento</h2>
<p class="text-sm text-amber-200/80 leading-relaxed mb-6 font-medium" style="">
 Declaro bajo protesta de decir verdad que los insumos médicos adquiridos serán destinados única y exclusivamente para los fines clínicos autorizados. Me comprometo al cumplimiento estricto de la Norma Oficial Mexicana respecto al manejo de insumos y Red Fría.
 </p>
<label class="flex items-center gap-4 cursor-pointer group" style="">
<input class="w-6 h-6 rounded border-amber-500/30 bg-background/50 text-amber-500 focus:ring-amber-500/20 transition-all cursor-pointer" required="" type="checkbox">
<span class="text-sm font-bold text-amber-200 group-hover:text-amber-100 transition-colors" style="">Acepto los términos y condiciones de uso de medicamento</span>
</label>
</div>
</div>
</section>
<!-- Final Section: Privacy and Submit -->
<section class="pt-8 border-t border-white/5">
<div class="space-y-8">
<div class="bg-background/40 p-6 rounded-xl border border-white/5 flex justify-center items-center">
<label class="flex items-center gap-4 cursor-pointer group" style="">
<input class="w-5 h-5 rounded border-white/10 bg-background/50 text-tertiary focus:ring-tertiary/20 transition-all" required="" type="checkbox">
<span class="text-sm text-slate-300 font-medium leading-relaxed" style="">
 Confirmo que los datos proporcionados son verídicos.
 </span>
</label>
</div>
<div class="flex flex-col items-center">
<button class="w-full md:max-w-md py-4 bg-tertiary text-white font-extrabold text-lg rounded-2xl hover:bg-tertiary/90 hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 shadow-lg shadow-tertiary/10" type="submit" style="">
<span class="" style="">Enviar solicitud de alta</span>
<span class="material-symbols-outlined font-normal" style="">send</span>
</button>
<p class="text-center text-xs text-slate-400 mt-6 font-medium" style="">
 Su solicitud pasará por un proceso de verificación documental (24-48h hábiles).
 </p>
</div>
</div>
</section>
</form>
</div>
</main>

<script>
 // File Upload Logic (Drag and Drop, Size, Preview)
 const maxFileSize = 15 * 1024 * 1024; // 15MB

 document.querySelectorAll('.file-input').forEach(input => {
 const wrapper = input.closest('.file-upload-wrapper');
 const nameDisplay = wrapper.querySelector('.file-name-display');
 const icon = wrapper.querySelector('.file-icon');
 const iconContainer = wrapper.querySelector('.icon-container');

 ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
 wrapper.addEventListener(eventName, preventDefaults, false);
 });

 function preventDefaults(e) {
 e.preventDefault();
 e.stopPropagation();
 }

 ['dragenter', 'dragover'].forEach(eventName => {
 wrapper.addEventListener(eventName, () => {
 wrapper.classList.add('border-primary', 'bg-primary/5');
 }, false);
 });

 ['dragleave', 'drop'].forEach(eventName => {
 wrapper.addEventListener(eventName, () => {
 wrapper.classList.remove('border-primary', 'bg-primary/5');
 }, false);
 });

 wrapper.addEventListener('drop', (e) => {
 const dt = e.dataTransfer;
 const files = dt.files;
 if(files.length > 0) {
 input.files = files; 
 validateAndDisplay(input.files[0], nameDisplay, icon, iconContainer, input);
 }
 });

 input.addEventListener('change', (e) => {
 if(input.files.length > 0) {
 validateAndDisplay(input.files[0], nameDisplay, icon, iconContainer, input);
 }
 });
 });

 function validateAndDisplay(file, displayElement, iconElement, iconContainer, inputElement) {
 if (file.size > maxFileSize) {
 alert('El archivo "' + file.name + '" supera el límite de 15MB. Por favor, selecciona un archivo más pequeño.');
 inputElement.value = ''; 
 displayElement.textContent = "Arrastra y suelta tu archivo o haz clic para explorar";
 displayElement.classList.remove('text-green-400', 'font-semibold');
 iconElement.textContent = "upload_file";
 iconContainer.classList.remove('bg-green-500/10', 'text-green-400');
 iconContainer.classList.add('bg-white/5', 'text-slate-400');
 return;
 }

 displayElement.textContent = file.name;
 displayElement.classList.add('text-green-400', 'font-semibold');
 iconElement.textContent = "check_circle";
 iconContainer.classList.remove('bg-white/5', 'text-slate-400');
 iconContainer.classList.add('bg-green-500/10', 'text-green-400');
 }
</script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
 AOS.init({
  duration: 800,
  once: true,
 });
</script>

<?php require_once '../includes/footer.php'; ?>
