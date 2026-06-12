<?php
$titulo = 'MMPharma | Registro Distribuidora';
$pagina_actual = 'inicio';
$base = '../';

$solicitud_ok = false;
$solicitud_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = [
        'tipo_cliente'      => 'DISTRIBUIDORA',
        'razon_social'      => trim($_POST['razon_social'] ?? ''),
        'rfc'               => trim($_POST['rfc'] ?? ''),
        'regimen_fiscal'    => trim($_POST['regimen_fiscal'] ?? ''),
        'domicilio_fiscal'  => trim($_POST['domicilio_fiscal'] ?? ''),
        'colonia'           => trim($_POST['colonia'] ?? ''),
        'cp'                => trim($_POST['cp'] ?? ''),
        'ciudad'            => trim($_POST['ciudad'] ?? ''),
        'estado'            => trim($_POST['estado'] ?? ''),
        'representante'     => trim($_POST['representante_legal'] ?? ''),
        'nombre_comercial'  => trim($_POST['nombre_comercial'] ?? ''),
        'giro'              => 'Distribuidora',
        'persona_contacto'  => trim($_POST['persona_contacto'] ?? ''),
        'volumen_mensual'   => null,
        'telefono_local'    => trim($_POST['telefono'] ?? ''),
        'telefono_celular'  => trim($_POST['telefono_celular'] ?? ''),
        'email'             => trim($_POST['email'] ?? ''),
        'documento_tipo'    => 'FACTURA',
        'metodo_pago'       => 'TRANSFERENCIA',
        'uso_cfdi'          => null,
        'domicilio_entrega' => null,
        'colonia_entrega'   => null,
        'cp_entrega'        => null,
        'ciudad_entrega'    => null,
        'municipio_entrega' => null,
        'estado_entrega'    => null,
        'receptor_entrega'  => null,
        'horario_entrega'   => null,
        'ip_origen'         => $_SERVER['REMOTE_ADDR'] ?? null,
    ];

    if ($campos['razon_social']) {
        try {
            require_once '../INCLUDES/db.php';
            $pdo = getDB();
            
            // --- File Upload Logic ---
            $upload_dir = '../uploads/documentos_registro/';
            $docs_keys = ['licencia_sanitaria', 'comprobante_domicilio', 'alta_hacienda', 'identificacion_oficial', 'acta_constitutiva'];
            foreach ($docs_keys as $key) {
                $campos["doc_{$key}"] = null;
                if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION));
                    $new_name = $key . '_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES[$key]['tmp_name'], $upload_dir . $new_name)) {
                        $campos["doc_{$key}"] = $new_name;
                    }
                }
            }
            // -------------------------

            $sql = "INSERT INTO clientes_solicitudes_registro
            (tipo_cliente, razon_social, rfc, regimen_fiscal, domicilio_fiscal, colonia, cp, ciudad, estado,
            representante, nombre_comercial, giro, persona_contacto, volumen_mensual, telefono_local,
            telefono_celular, email, documento_tipo, metodo_pago, uso_cfdi, domicilio_entrega,
            colonia_entrega, cp_entrega, ciudad_entrega, municipio_entrega, estado_entrega,
            receptor_entrega, horario_entrega, ip_origen, doc_licencia_sanitaria, doc_comprobante_domicilio,
            doc_alta_hacienda, doc_identificacion_oficial, doc_acta_constitutiva)
            VALUES
            (:tipo_cliente, :razon_social, :rfc, :regimen_fiscal, :domicilio_fiscal, :colonia, :cp, :ciudad, :estado,
            :representante, :nombre_comercial, :giro, :persona_contacto, :volumen_mensual, :telefono_local,
            :telefono_celular, :email, :documento_tipo, :metodo_pago, :uso_cfdi, :domicilio_entrega,
            :colonia_entrega, :cp_entrega, :ciudad_entrega, :municipio_entrega, :estado_entrega,
            :receptor_entrega, :horario_entrega, :ip_origen, :doc_licencia_sanitaria, :doc_comprobante_domicilio,
            :doc_alta_hacienda, :doc_identificacion_oficial, :doc_acta_constitutiva)";
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
<!DOCTYPE html>
<html class="light" lang="es">
<head>
 <meta charset="utf-8">
 <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $titulo ?></title>
  <link rel="icon" type="image/png" href="../logos/MMPharma-Isotipo.png">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- Include fonts and tailwind -->
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
 <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
 <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
 },
 },
 }
 </script>
 <style>
 body { font-family: 'Inter', sans-serif; }
 .material-symbols-outlined {
 font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle;
 }
 .drag-over {
 border-color: #003e79 !important;
 background-color: #e0f2ff !important;
 }
 </style>
</head>
<body class="bg-background text-slate-300 antialiased min-h-screen flex flex-col relative overflow-x-hidden">

<!-- Decorative Background -->
<div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
</div>

<!-- TopNavBar -->
<header class="relative z-50 flex justify-between items-center w-full px-4 sm:px-8 py-4 sm:py-6 bg-background/80 backdrop-blur-md border-b border-white/5 sticky top-0">
 <a href="../SELECCIÓN_REGISTRO/selección_registro.php" class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-2xl transition-all group">
  <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
 </a>

 <a href="../INDEX/index.php" class="absolute left-1/2 -translate-x-1/2">
  <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-6 sm:h-8 w-auto hover:scale-105 transition-transform duration-300">
 </a>

 <div class="w-12 h-12"></div>
</header>

<main class="max-w-4xl mx-auto px-4 py-8 md:py-12 flex-grow flex flex-col items-center w-full relative z-10" data-aos="fade-up">
 <div class="w-full bg-surface/85 backdrop-blur-xl text-slate-200 rounded-3xl border border-white/10 overflow-hidden relative shadow-2xl">
  <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary via-secondary to-tertiary"></div>
  <div class="p-8 md:p-12">
   <header class="mb-10 text-center">
    <div class="w-16 h-16 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 text-tertiary-light">
     <span class="material-symbols-outlined text-4xl">local_shipping</span>
    </div>
    <h1 class="text-3xl font-black text-white tracking-tight mb-3">Registro de Distribuidora</h1>
    <p class="text-slate-400 text-base font-medium">Completa tus datos y sube tus documentos para iniciar el proceso de alta.</p>
   </header>

   <form action="" method="POST" class="space-y-12" id="registroForm" enctype="multipart/form-data">
    <!-- Sección 1: Datos Generales -->
    <section>
     <h2 class="flex items-center gap-2 text-sm font-bold text-tertiary-light uppercase tracking-widest mb-6 border-b border-white/5 pb-3">
      <span class="material-symbols-outlined">badge</span> 1. Datos Generales
     </h2>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="md:col-span-2">
       <label class="block text-xs font-semibold text-slate-300 mb-2">Nombre o Razón Social <span class="text-red-500">*</span></label>
       <input name="razon_social" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="Ej. Distribuidora Médica S.A. de C.V." type="text" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">Nombre Comercial <span class="text-red-500">*</span></label>
       <input name="nombre_comercial" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="Ej. DistriMed" type="text" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">RFC <span class="text-red-500">*</span></label>
       <input name="rfc" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="Ej. DME010101ABC" type="text" required>
      </div>
      <div class="md:col-span-2">
       <label class="block text-xs font-semibold text-slate-300 mb-2">Régimen Fiscal <span class="text-red-500">*</span></label>
       <select id="regimen_fiscal" name="regimen_fiscal" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all outline-none" required>
        <option value="" disabled selected class="bg-surface text-slate-400">Selecciona tu régimen fiscal</option>
        <option value="moral" class="bg-surface text-white">General de Ley Personas Morales</option>
        <option value="fisica" class="bg-surface text-white">Personas Físicas con Actividades Empresariales</option>
        <option value="resico" class="bg-surface text-white">Régimen Simplificado de Confianza</option>
       </select>
      </div>
      <div class="md:col-span-2">
       <label class="block text-xs font-semibold text-slate-300 mb-2">Representante Legal <span class="text-red-500">*</span></label>
       <input name="representante_legal" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="Nombre completo del representante legal" type="text" required>
      </div>
     </div>
    </section>

    <!-- Sección 2: Dirección y Contacto -->
    <section>
     <h2 class="flex items-center gap-2 text-sm font-bold text-tertiary-light uppercase tracking-widest mb-6 border-b border-white/5 pb-3">
      <span class="material-symbols-outlined">location_on</span> 2. Dirección y Contacto
     </h2>
     <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="md:col-span-3">
       <label class="block text-xs font-semibold text-slate-300 mb-2">Domicilio Fiscal (Validación) <span class="text-red-500">*</span></label>
       <input name="domicilio_fiscal" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="Calle, número exterior e interior" type="text" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">Colonia <span class="text-red-500">*</span></label>
       <input name="colonia" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all outline-none" type="text" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">C.P. <span class="text-red-500">*</span></label>
       <input name="cp" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all outline-none" type="text" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">Ciudad/Municipio <span class="text-red-500">*</span></label>
       <input name="ciudad" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all outline-none" type="text" required>
      </div>
      <div class="md:col-span-3">
       <label class="block text-xs font-semibold text-slate-300 mb-2">Estado <span class="text-red-500">*</span></label>
       <select name="estado" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all outline-none" required>
        <option value="" disabled selected class="bg-surface text-slate-400">Selecciona tu estado</option>
        <option value="Aguascalientes" class="bg-surface text-white">Aguascalientes</option>
        <option value="Baja California" class="bg-surface text-white">Baja California</option>
        <option value="Baja California Sur" class="bg-surface text-white">Baja California Sur</option>
        <option value="Campeche" class="bg-surface text-white">Campeche</option>
        <option value="Chiapas" class="bg-surface text-white">Chiapas</option>
        <option value="Chihuahua" class="bg-surface text-white">Chihuahua</option>
        <option value="Ciudad de México" class="bg-surface text-white">Ciudad de México</option>
        <option value="Coahuila" class="bg-surface text-white">Coahuila</option>
        <option value="Colima" class="bg-surface text-white">Colima</option>
        <option value="Durango" class="bg-surface text-white">Durango</option>
        <option value="Estado de México" class="bg-surface text-white">Estado de México</option>
        <option value="Guanajuato" class="bg-surface text-white">Guanajuato</option>
        <option value="Guerrero" class="bg-surface text-white">Guerrero</option>
        <option value="Hidalgo" class="bg-surface text-white">Hidalgo</option>
        <option value="Jalisco" class="bg-surface text-white">Jalisco</option>
        <option value="Michoacán" class="bg-surface text-white">Michoacán</option>
        <option value="Morelos" class="bg-surface text-white">Morelos</option>
        <option value="Nayarit" class="bg-surface text-white">Nayarit</option>
        <option value="Nuevo León" class="bg-surface text-white">Nuevo León</option>
        <option value="Oaxaca" class="bg-surface text-white">Oaxaca</option>
        <option value="Puebla" class="bg-surface text-white">Puebla</option>
        <option value="Querétaro" class="bg-surface text-white">Querétaro</option>
        <option value="Quintana Roo" class="bg-surface text-white">Quintana Roo</option>
        <option value="San Luis Potosí" class="bg-surface text-white">San Luis Potosí</option>
        <option value="Sinaloa" class="bg-surface text-white">Sinaloa</option>
        <option value="Sonora" class="bg-surface text-white">Sonora</option>
        <option value="Tabasco" class="bg-surface text-white">Tabasco</option>
        <option value="Tamaulipas" class="bg-surface text-white">Tamaulipas</option>
        <option value="Tlaxcala" class="bg-surface text-white">Tlaxcala</option>
        <option value="Veracruz" class="bg-surface text-white">Veracruz</option>
        <option value="Yucatán" class="bg-surface text-white">Yucatán</option>
        <option value="Zacatecas" class="bg-surface text-white">Zacatecas</option>
       </select>
      </div>
      
      <!-- Contacto Principal -->
      <div class="md:col-span-3 mt-4 border-t border-white/5 pt-6">
       <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-4">Contacto Principal</h3>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">Persona de contacto <span class="text-red-500">*</span></label>
       <input name="persona_contacto" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="Nombre de quien gestiona" type="text" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">Teléfono (Principal) <span class="text-red-500">*</span></label>
       <input name="telefono" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="10 dígitos" type="tel" required>
      </div>
      <div>
       <label class="block text-xs font-semibold text-slate-300 mb-2">Teléfono Celular <span class="text-red-500">*</span></label>
       <input name="telefono_celular" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="10 dígitos" type="tel" required>
      </div>
      <div class="md:col-span-3">
       <label class="block text-xs font-semibold text-slate-300 mb-2">Correo Electrónico de Contacto (Será tu usuario) <span class="text-red-500">*</span></label>
       <input name="email" class="w-full px-4 py-3 bg-background/50 border border-white/10 rounded-xl focus:ring-2 focus:ring-tertiary/20 focus:border-tertiary text-white text-sm transition-all placeholder:text-slate-500 outline-none" placeholder="ejemplo@empresa.com" type="email" required>
      </div>
     </div>
    </section>

    <!-- Sección 3: Documentación -->
    <section>
     <h2 class="flex items-center gap-2 text-sm font-bold text-tertiary-light uppercase tracking-widest mb-6 border-b border-white/5 pb-3">
      <span class="material-symbols-outlined">folder</span> 3. Documentación Requerida
     </h2>
     <p class="text-xs text-slate-400 mb-6">Sube los documentos escaneados. Formatos permitidos: PDF, JPG, PNG. Tamaño máximo por archivo: 15MB.</p>

     <div class="space-y-4">
      <?php
      $documentos = [
       ['id' => 'licencia_sanitaria', 'titulo' => 'Aviso de funcionamiento o Licencia Sanitaria'],
       ['id' => 'comprobante_domicilio', 'titulo' => 'Comprobante de Domicilio'],
       ['id' => 'alta_hacienda', 'titulo' => 'Alta de Hacienda'],
       ['id' => 'identificacion_oficial', 'titulo' => 'Identificación oficial del representante legal o propietario']
      ];
      foreach($documentos as $doc): ?>
      <div class="file-upload-wrapper bg-background/40 border border-white/10 border-dashed rounded-xl p-5 transition-all duration-300 relative group hover:border-tertiary/50 hover:bg-tertiary/5">
       <input type="file" name="<?= $doc['id'] ?>" id="<?= $doc['id'] ?>" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png" required>
       <div class="flex items-center justify-between pointer-events-none">
        <div class="flex items-center gap-4">
         <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-slate-400 transition-colors icon-container group-hover:bg-tertiary/10 group-hover:text-tertiary">
          <span class="material-symbols-outlined file-icon">upload_file</span>
         </div>
         <div>
          <p class="text-sm font-bold text-white"><?= $doc['titulo'] ?> <span class="text-red-500">*</span></p>
          <p class="text-xs text-slate-400 file-name-display">Arrastra y suelta tu archivo o haz clic para explorar</p>
         </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-white/5 group-hover:bg-tertiary group-hover:text-white border border-white/10 group-hover:border-tertiary flex items-center justify-center text-slate-300 transition-all duration-300">
         <span class="material-symbols-outlined text-lg">upload</span>
        </div>
       </div>
      </div>
      <?php endforeach; ?>

      <!-- Acta Constitutiva (Condicional) -->
      <div id="wrapper_acta_constitutiva" class="file-upload-wrapper bg-background/40 border border-white/10 border-dashed rounded-xl p-5 transition-all duration-300 relative group hover:border-tertiary/50 hover:bg-tertiary/5 hidden">
       <input type="file" name="acta_constitutiva" id="acta_constitutiva" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png">
       <div class="flex items-center justify-between pointer-events-none">
        <div class="flex items-center gap-4">
         <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-slate-400 transition-colors icon-container group-hover:bg-tertiary/10 group-hover:text-tertiary">
          <span class="material-symbols-outlined file-icon">upload_file</span>
         </div>
         <div>
          <p class="text-sm font-bold text-white">Copia del alta constitutiva <span class="text-red-500">*</span></p>
          <p class="text-xs text-slate-400 file-name-display">Arrastra y suelta tu archivo o haz clic para explorar</p>
         </div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-white/5 group-hover:bg-tertiary group-hover:text-white border border-white/10 group-hover:border-tertiary flex items-center justify-center text-slate-300 transition-all duration-300">
         <span class="material-symbols-outlined text-lg">upload</span>
        </div>
       </div>
      </div>
     </div>
    </section>

    <!-- Final Section: Privacy and Submit -->
    <section class="pt-8 border-t border-white/5">
     <div class="space-y-8">
      <div class="bg-background/40 p-6 rounded-xl border border-white/5 flex justify-center items-center">
       <label class="flex items-center gap-4 cursor-pointer group">
        <input class="w-5 h-5 rounded border-white/10 bg-background/50 text-tertiary focus:ring-tertiary/20 transition-all" type="checkbox" required>
        <span class="text-sm text-slate-300 group-hover:text-white transition-colors">Confirmo que los datos proporcionados son verídicos.</span>
       </label>
      </div>

      <div class="pt-4">
       <button class="w-full py-4 bg-tertiary text-white font-bold rounded-xl hover:bg-tertiary/90 hover:-translate-y-0.5 active:scale-[0.98] transition-all flex items-center justify-center gap-2 tracking-wide shadow-lg shadow-tertiary/10" type="submit">
        <span class="material-symbols-outlined text-xl">send</span> Enviar solicitud de registro
       </button>
       <p class="text-center text-xs text-slate-400 mt-4 font-semibold uppercase tracking-widest opacity-80">Proceso 100% Seguro</p>
      </div>
     </div>
    </section>
   </form>
  </div>
 </div>
</main>

<script>
 // Logic for Persona Moral selection
 const regimenSelect = document.getElementById('regimen_fiscal');
 const actaWrapper = document.getElementById('wrapper_acta_constitutiva');
 const actaInput = document.getElementById('acta_constitutiva');

 regimenSelect.addEventListener('change', (e) => {
  if(e.target.value === 'moral') {
   actaWrapper.classList.remove('hidden');
   actaInput.setAttribute('required', 'required');
  } else {
   actaWrapper.classList.add('hidden');
   actaInput.removeAttribute('required');
  }
 });

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
   displayElement.classList.remove('text-green-400', 'font-medium');
   iconElement.textContent = "upload_file";
   iconContainer.classList.remove('bg-green-500/10', 'text-green-400');
   iconContainer.classList.add('bg-white/5', 'text-slate-400');
   return;
  }

  displayElement.textContent = file.name;
  displayElement.classList.add('text-green-400', 'font-medium');
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
<?php require_once '../INCLUDES/footer.php'; ?>
