<?php
$titulo = 'Contacto | MMPharma';
$pagina_actual = 'contacto';
$base = '../';

$enviado = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $honeypot = trim($_POST['contacto_website'] ?? '');
 if ($honeypot !== '') {
  // Es un bot de spam. Fingimos éxito pero no guardamos ni enviamos nada.
  $enviado = true;
 } else {
  $nombre = trim($_POST['nombre'] ?? '');
  $empresa = trim($_POST['empresa'] ?? '');
  $correo = trim($_POST['correo'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $mensaje = trim($_POST['mensaje'] ?? '');

  if ($nombre && $correo && filter_var($correo, FILTER_VALIDATE_EMAIL) && $mensaje) {
  // 1. Guardar en BD
  try {
  require_once '../includes/db.php';
  $pdo = getDB();
  $pdo->prepare(
  "INSERT INTO clientes_contacto_mensajes (nombre, email, telefono, empresa, mensaje, ip_origen)
  VALUES (?, ?, ?, ?, ?, ?)"
  )->execute([$nombre, $correo, $telefono, $empresa, $mensaje, $_SERVER['REMOTE_ADDR'] ?? null]);
  $enviado = true;
  } catch (Exception $e) {
  $enviado = false;
  $error = true;
  }

  // 2. Intentar enviar email (opcional, no bloquea si falla)
  if ($enviado) {
  $para = 'ventas@mmpharma.com';
  $asunto = "Nuevo mensaje de contacto — $nombre ($empresa)";
  $cuerpo = "Nombre: $nombre\nEmpresa: $empresa\nCorreo: $correo\nTeléfono: $telefono\n\nMensaje:\n$mensaje";
  $headers = "From: $correo\r\nReply-To: $correo\r\nContent-Type: text/plain; charset=UTF-8";
  @mail($para, $asunto, $cuerpo, $headers);
  }
  } else {
  $error = true;
  }
 }
}

require_once '../includes/header.php';
?>
<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden bg-slate-900">
 <div class="absolute inset-0 z-0 overflow-hidden">
 <img src="../img/60.webp" class="w-full h-full object-cover opacity-50 parallax-bg scale-125 origin-top" data-speed="0.2">
 <div class="absolute inset-0 bg-primary/70"></div>
 </div>
 <div class="relative z-10 max-w-[1369px] mx-auto px-8 py-20 w-full text-center md:text-left" data-aos="fade-up">
 <h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4">Contáctanos</h1>
 <p class="text-lg text-white max-w-xl mx-auto md:mx-0 leading-relaxed font-medium">
 Nuestro equipo está listo para atender los requerimientos de tu institución, farmacia o distribuidora con la eficiencia que nos caracteriza.
 </p>
 </div>
</section>

<!-- ── CONTENIDO PRINCIPAL ── -->
<main class="bg-white py-24">
<section class="max-w-[1369px] mx-auto px-8">
 <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-stretch">

 <!-- ─ Columna izquierda: datos de contacto ─ -->
 <div class="lg:col-span-5 flex flex-col h-full" data-aos="fade-right">

 <div class="text-center lg:text-left mb-8">
 <h2 class="text-3xl font-black text-primary mb-3">Canales <span class="text-tertiary">directos</span></h2>
 <p class="text-slate-900 text-base leading-relaxed">
 Atendemos de lunes a viernes de <span class="text-primary font-bold">9:00 AM a 6:00 PM (Hora del Centro)</span>. Selecciona el medio que más te convenga.
 </p>
 </div>

 <div class="flex flex-col gap-4 lg:flex-1 lg:justify-between lg:gap-0 mb-8 lg:mb-0">
 <!-- Cobertura Nacional -->
 <div class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-tertiary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-tertiary transition-all">
 <span class="material-symbols-outlined text-3xl">public</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Presencia nacional</h3>
 <p class="text-white text-sm leading-relaxed font-medium">
 Cobertura estratégica en toda la República Mexicana.
 </p>
 </div>
 </div>

  <!-- Teléfono 1 (WhatsApp) -->
  <a href="https://wa.me/523322207506" target="_blank" rel="noopener noreferrer"
  class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-tertiary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
  <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-tertiary transition-all">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="currentColor"/>
    </svg>
  </div>
  <div>
  <h3 class="font-bold text-white text-lg mb-0.5">WhatsApp</h3>
  <p class="text-white text-sm font-medium tracking-tight">
  33 2220 7506
  </p>
  </div>
  </a>

 <!-- Teléfono 2 -->
 <a href="tel:3343480581"
 class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-tertiary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-tertiary transition-all">
 <span class="material-symbols-outlined text-3xl">call</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Línea telefónica 2</h3>
 <p class="text-white text-sm font-medium tracking-tight">
 33 4348 0581
 </p>
 </div>
 </a>

 <!-- Teléfono 3 -->
 <a href="tel:3343480582"
 class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-tertiary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-tertiary transition-all">
 <span class="material-symbols-outlined text-3xl">call</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Línea telefónica 3</h3>
 <p class="text-white text-sm font-medium tracking-tight">
 33 4348 0582
 </p>
 </div>
 </a>

 <!-- Correo -->
 <a href="mailto:atencionclientes@mmpharma.mx"
 class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-tertiary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-tertiary transition-all">
 <span class="material-symbols-outlined text-3xl">mail</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Correo electrónico</h3>
 <p class="text-white text-sm font-medium">atencionclientes@mmpharma.mx</p>
 </div>
 </a>
 </div>

 </div>

 <!-- ─ Columna derecha: formulario ─ -->
 <div class="lg:col-span-7" data-aos="fade-left">
 <div class="bg-primary p-10 md:p-14 rounded-3xl">

 <?php if ($enviado): ?>
 <div class="text-center py-16">
 <div class="w-24 h-24 bg-[#4ade80]/10 text-[#4ade80] rounded-3xl flex items-center justify-center mx-auto mb-8 animate-bounce">
 <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1">verified</span>
 </div>
 <h2 class="text-3xl font-black text-primary mb-4">¡Mensaje recibido!</h2>
 <p class="text-slate-900 text-lg max-w-sm mx-auto leading-relaxed">
 Hemos registrado tu solicitud correctamente. Un asesor se pondrá en contacto contigo muy pronto.
 </p>
 <a href="contacto.php" class="mt-10 inline-flex items-center gap-2 px-10 py-4 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 hover:-translate-y-1 transition-all ">
 Cerrar y volver
 </a>
 </div>

 <?php else: ?>

 <div class="mb-10">
 <h2 class="text-4xl font-black text-white mb-2">Envíanos un mensaje</h2>
 <p class="text-white font-medium">Atención personalizada para tu empresa o institución.</p>
 </div>

 <?php if ($error): ?>
 <div class="mb-8 flex items-center gap-4 bg-red-500/10 p-5 rounded-2xl text-red-400">
 <span class="material-symbols-outlined">report_problem</span>
 <p class="text-sm font-bold">Por favor, verifica que todos los campos requeridos estén llenos correctamente.</p>
 </div>
 <?php endif; ?>

 <form method="POST" action="contacto.php" class="grid grid-cols-1 md:grid-cols-2 gap-6">

 <!-- Campo Honeypot invisible para humanos pero atractivo para bots -->
 <div class="hidden" style="display: none !important;">
   <input type="text" name="contacto_website" id="contacto_website" autocomplete="off" tabindex="-1">
 </div>

 <div class="space-y-2">
 <label class="text-sm font-bold text-white uppercase tracking-wider ml-1">Nombre completo *</label>
 <input type="text" name="nombre" required
 value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
 placeholder="Ej. Juan Pérez"
 class="w-full h-14 bg-white rounded-xl px-5 text-primary font-medium focus:ring-4 focus:ring-white/20 outline-none border border-transparent focus:border-tertiary transition-all placeholder:text-slate-400">
 </div>

 <div class="space-y-2">
 <label class="text-sm font-bold text-white uppercase tracking-wider ml-1">Empresa / Institución</label>
 <input type="text" name="empresa"
 value="<?= htmlspecialchars($_POST['empresa'] ?? '') ?>"
 placeholder="Razón social"
 class="w-full h-14 bg-white rounded-xl px-5 text-primary font-medium focus:ring-4 focus:ring-white/20 outline-none border border-transparent focus:border-tertiary transition-all placeholder:text-slate-400">
 </div>

 <div class="space-y-2">
 <label class="text-sm font-bold text-white uppercase tracking-wider ml-1">Correo electrónico *</label>
 <input type="email" name="correo" required
 value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
 placeholder="contacto@empresa.com"
 class="w-full h-14 bg-white rounded-xl px-5 text-primary font-medium focus:ring-4 focus:ring-white/20 outline-none border border-transparent focus:border-tertiary transition-all placeholder:text-slate-400">
 </div>

 <div class="space-y-2">
 <label class="text-sm font-bold text-white uppercase tracking-wider ml-1">Teléfono</label>
 <input type="tel" name="telefono"
 value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
 placeholder="10 dígitos"
 class="w-full h-14 bg-white rounded-xl px-5 text-primary font-medium focus:ring-4 focus:ring-white/20 outline-none border border-transparent focus:border-tertiary transition-all placeholder:text-slate-400">
 </div>

 <div class="md:col-span-2 space-y-2">
 <label class="text-sm font-bold text-white uppercase tracking-wider ml-1">Mensaje *</label>
 <textarea name="mensaje" required rows="4"
 placeholder="Describe tu requerimiento o dudas..."
 class="w-full bg-white rounded-2xl p-5 text-primary font-medium focus:ring-4 focus:ring-white/20 outline-none border border-transparent focus:border-tertiary transition-all placeholder:text-slate-400 resize-none"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
 </div>

 <div class="md:col-span-2 pt-4">
 <button type="submit" class="w-full py-4 bg-tertiary text-white font-semibold rounded-2xl hover:bg-tertiary/90 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">
 Enviar mensaje
 <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">send</span>
 </button>
 <p class="text-xs text-white/50 font-bold text-center mt-4 uppercase tracking-[0.2em]">* Los campos marcados son obligatorios</p>
 </div>

 </form>
 <?php endif; ?>

 </div>
 </div>

 </div>
</section>

</main>

<?php require_once '../includes/footer.php'; ?>
