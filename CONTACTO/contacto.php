<?php
$titulo = 'Contacto | MMPharma';
$pagina_actual = 'contacto';
$base = '../';

$enviado = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $nombre = trim($_POST['nombre'] ?? '');
 $empresa = trim($_POST['empresa'] ?? '');
 $correo = trim($_POST['correo'] ?? '');
 $telefono = trim($_POST['telefono'] ?? '');
 $mensaje = trim($_POST['mensaje'] ?? '');

 if ($nombre && $correo && filter_var($correo, FILTER_VALIDATE_EMAIL) && $mensaje) {
 // 1. Guardar en BD
 try {
 require_once '../INCLUDES/db.php';
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

require_once '../includes/header.php';
?>
<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden bg-slate-900">
 <div class="absolute inset-0 z-0 overflow-hidden">
 <img src="../IMG/60.webp" class="w-full h-full object-cover opacity-50 parallax-bg scale-125 origin-top" data-speed="0.2">
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
 Atendemos de lunes a viernes de <span class="text-primary font-bold">9:00 AM a 6:00 PM</span>. Selecciona el medio que más te convenga.
 </p>
 </div>

 <div class="space-y-4 mb-8">
 <!-- Cobertura Nacional -->
 <div class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-primary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-primary transition-all">
 <span class="material-symbols-outlined text-3xl">public</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Presencia nacional</h3>
 <p class="text-white text-sm leading-relaxed font-medium">
 Cobertura estratégica en toda la República Mexicana.
 </p>
 </div>
 </div>

 <!-- Teléfonos -->
 <a href="tel:3322207506"
 class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-primary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-primary transition-all">
 <span class="material-symbols-outlined text-3xl">call</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Líneas telefónicas</h3>
 <p class="text-white text-sm font-medium tracking-tight">
 33 2220 7506 <span class="mx-2 text-white/40">|</span> 33 4348 0581 <span class="mx-2 text-white/40">|</span> 33 4348 0582
 </p>
 </div>
 </a>

 <!-- Correo -->
 <a href="mailto:atencionclientes@mmpharma.mx"
 class="flex flex-col sm:flex-row items-center text-center sm:text-left gap-5 p-6 bg-primary rounded-2xl transition-all hover:-translate-y-1 group border border-white/5">
 <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 text-white group-hover:bg-white group-hover:text-primary transition-all">
 <span class="material-symbols-outlined text-3xl">mail</span>
 </div>
 <div>
 <h3 class="font-bold text-white text-lg mb-0.5">Correo electrónico</h3>
 <p class="text-white text-sm font-medium">atencionclientes@mmpharma.mx</p>
 </div>
 </a>
 </div>

 <!-- Estatus Red Fría Rediseñado -->
 <div class="flex-1 flex flex-col">
 <div class="p-5 bg-tertiary rounded-[2rem] relative overflow-hidden group transition-all flex flex-col justify-between hover:-translate-y-1 h-full">
 <!-- Icono decorativo de fondo -->
 <div class="absolute -right-6 -bottom-6 opacity-[0.08] group-hover:opacity-[0.15] transition-all">
 <span class="material-symbols-outlined text-[120px] text-white" style="font-variation-settings: 'FILL' 1">ac_unit</span>
 </div>
 
 <div class="relative z-10 flex flex-col gap-3">
 <div class="flex items-center justify-between">
 <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white transition-all group-hover:bg-white group-hover:text-tertiary">
 <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1">ac_unit</span>
 </div>
 <div class="flex items-center gap-2 px-3 py-1 bg-green-100 rounded-full border border-green-200">
 <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse 0_0_8px_rgba(34,197,94,0.5)]"></div>
 <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Monitoreo Activo 24/7</span>
 </div>
 </div>
 
 <div>
 <h4 class="text-white text-2xl font-black mb-2 tracking-tight">Estatus Red Fría</h4>
 <p class="text-white/80 text-sm leading-relaxed font-medium">
 Infraestructura certificada de monitoreo térmico constante para garantizar la integridad biológica de cada insumo especializado.
 </p>
 </div>

 <div class="flex items-center gap-2 text-white font-bold text-xs">
 <span class="material-symbols-outlined text-sm">verified</span>
 Certificación COFEPRIS
 </div>
 </div>
 </div>
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
 <a href="contacto.php" class="mt-10 inline-flex items-center gap-2 px-10 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 hover:-translate-y-1 transition-all ">
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
 class="w-full bg-white rounded-xl p-5 text-primary font-medium focus:ring-4 focus:ring-white/20 outline-none border border-transparent focus:border-tertiary transition-all placeholder:text-slate-400 resize-none"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
 </div>

 <div class="md:col-span-2 pt-4">
 <button type="submit" class="w-full py-4 bg-white text-primary font-semibold rounded-xl hover:bg-slate-100 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">
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
