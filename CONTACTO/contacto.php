<?php
$titulo = 'Contacto | MMPharma';
$pagina_actual = 'contacto';
$base = '../';

// ── Manejo del formulario ──
$enviado = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $empresa  = trim($_POST['empresa'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $mensaje  = trim($_POST['mensaje'] ?? '');

    if ($nombre && $correo && filter_var($correo, FILTER_VALIDATE_EMAIL) && $mensaje) {
        $para    = 'ventas@mmpharma.com';
        $asunto  = "Nuevo mensaje de contacto — $nombre ($empresa)";
        $cuerpo  = "Nombre: $nombre\nEmpresa: $empresa\nCorreo: $correo\nTeléfono: $telefono\n\nMensaje:\n$mensaje";
        $headers = "From: $correo\r\nReply-To: $correo\r\nContent-Type: text/plain; charset=UTF-8";

        if (mail($para, $asunto, $cuerpo, $headers)) {
            $enviado = true;
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}

require_once '../includes/header.php';
?>

<!-- ── HERO── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden">
  <img src="../IMG/9.webp" class="absolute inset-0 w-full h-full object-cover" alt="MMPharma instalaciones">
  <div class="absolute inset-0 bg-[#002451] opacity-80"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-8 py-20 w-full">
    <h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4">Contáctanos</h1>
    <p class="text-lg text-blue-100/90 max-w-xl leading-relaxed">
      Nuestro equipo está listo para atender los requerimientos de tu institución, farmacia o distribuidora.
    </p>
  </div>
</section>

<!-- ── CONTENIDO PRINCIPAL ── -->
<main>
<section class="max-w-7xl mx-auto px-8 py-20">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

    <!-- ─ Columna izquierda: datos de contacto ─ -->
    <div class="lg:col-span-5 space-y-6">

      <div>
        <h2 class="text-2xl font-bold text-on-surface mb-2">Canales directos</h2>
        <p class="text-on-surface-variant text-sm leading-relaxed">Comunícate con nosotros por el canal que prefieras. Atendemos de lunes a viernes de 9:00 AM a 6:00 PM.</p>
      </div>

      <!-- Dirección → Google Maps -->
      <a href="https://maps.google.com/?q=Av+Obsidiana+3644+Residencial+Loma+Bonita+Zapopan+Jalisco" target="_blank"
         class="flex items-start gap-4 p-6 bg-surface-container-low rounded-xl hover:bg-surface-container transition-all group clinical-shadow">
        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm group-hover:bg-primary transition-colors">
          <span class="material-symbols-outlined text-primary group-hover:text-white transition-colors">location_on</span>
        </div>
        <div>
          <h3 class="font-bold text-on-surface mb-1">Dirección</h3>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            Av. Obsidiana 3644,<br>
            Residencial Loma Bonita,<br>
            Zapopan, Jalisco, CP 45088
          </p>
          <span class="text-xs font-bold text-secondary mt-2 inline-flex items-center gap-1 group-hover:underline">
            Abrir en Google Maps
            <span class="material-symbols-outlined text-xs">open_in_new</span>
          </span>
        </div>
      </a>

      <!-- Teléfonos → tel: -->
      <a href="tel:3343480581"
         class="flex items-start gap-4 p-6 bg-surface-container-low rounded-xl hover:bg-surface-container transition-all group clinical-shadow">
        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm group-hover:bg-primary transition-colors">
          <span class="material-symbols-outlined text-primary group-hover:text-white transition-colors">call</span>
        </div>
        <div>
          <h3 class="font-bold text-on-surface mb-1">Teléfono</h3>
          <p class="text-on-surface-variant text-sm">33 4348 0581</p>
          <p class="text-on-surface-variant text-sm">33 4348 0582</p>
          <p class="text-xs text-outline mt-1 italic">Lun - Vie: 9:00 AM - 6:00 PM</p>
        </div>
      </a>

      <!-- Correo → mailto: -->
      <a href="mailto:ventas@mmpharma.com"
         class="flex items-start gap-4 p-6 bg-surface-container-low rounded-xl hover:bg-surface-container transition-all group clinical-shadow">
        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm group-hover:bg-primary transition-colors">
          <span class="material-symbols-outlined text-primary group-hover:text-white transition-colors">mail</span>
        </div>
        <div>
          <h3 class="font-bold text-on-surface mb-1">Correo electrónico</h3>
          <p class="text-on-surface-variant text-sm">ventas@mmpharma.com</p>
          <span class="text-xs font-bold text-secondary mt-1 inline-block group-hover:underline">Enviar correo</span>
        </div>
      </a>

      <!-- Badge Red Fría -->
      <div class="p-6 bg-tertiary-container/5 rounded-2xl border border-tertiary-container/15 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-tertiary-container flex items-center justify-center flex-shrink-0">
          <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1">ac_unit</span>
        </div>
        <div>
          <span class="text-[10px] uppercase tracking-widest font-bold text-tertiary-container block mb-1">Estatus Red Fría</span>
          <p class="text-on-surface font-medium text-sm">Instalaciones monitoreadas 24/7</p>
        </div>
      </div>

    </div>

    <!-- ─ Columna derecha: formulario ─ -->
    <div class="lg:col-span-7">
      <div class="bg-surface-container-lowest p-8 md:p-12 rounded-2xl clinical-shadow ring-1 ring-outline-variant/10">

        <?php if ($enviado): ?>
        <!-- Estado enviado exitosamente -->
        <div class="text-center py-12">
          <div class="w-20 h-20 bg-tertiary-container/10 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-tertiary-container text-4xl" style="font-variation-settings: 'FILL' 1">check_circle</span>
          </div>
          <h2 class="text-2xl font-bold text-on-surface mb-3">¡Mensaje enviado!</h2>
          <p class="text-on-surface-variant text-sm max-w-sm mx-auto leading-relaxed">
            Gracias por contactarnos. Nuestro equipo te responderá a la brevedad en el correo que proporcionaste.
          </p>
          <a href="contacto.php" class="mt-8 inline-block px-8 py-3 bg-primary text-white font-bold rounded-xl hover:opacity-90 transition-all text-sm">
            Enviar otro mensaje
          </a>
        </div>

        <?php else: ?>

        <div class="mb-8">
          <h2 class="text-2xl font-bold text-on-surface mb-1">Envíanos un mensaje</h2>
          <p class="text-on-surface-variant text-sm">Completa el formulario y te respondemos a la brevedad.</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-6 flex items-center gap-3 bg-error-container p-4 rounded-xl">
          <span class="material-symbols-outlined text-error">error</span>
          <p class="text-sm text-on-error-container font-medium">Ocurrió un error. Verifica que todos los campos estén correctos e inténtalo de nuevo.</p>
        </div>
        <?php endif; ?>

        <form method="POST" action="contacto.php" class="space-y-5">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nombre completo *</label>
              <input type="text" name="nombre" required
                value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                placeholder="Ej. Juan Pérez"
                class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-on-surface text-sm focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline/50">
            </div>
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Empresa / Institución</label>
              <input type="text" name="empresa"
                value="<?= htmlspecialchars($_POST['empresa'] ?? '') ?>"
                placeholder="Razón social"
                class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-on-surface text-sm focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline/50">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Correo electrónico *</label>
              <input type="email" name="correo" required
                value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                placeholder="contacto@empresa.com"
                class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-on-surface text-sm focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline/50">
            </div>
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Teléfono</label>
              <input type="tel" name="telefono"
                value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                placeholder="10 dígitos"
                class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-on-surface text-sm focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline/50">
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Mensaje *</label>
            <textarea name="mensaje" required rows="5"
              placeholder="Describe tu requerimiento, solicitud de catálogo o cualquier duda..."
              class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-on-surface text-sm focus:ring-2 focus:ring-primary-fixed outline-none transition-all placeholder:text-outline/50 resize-none"><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
          </div>

          <div class="pt-2">
            <button type="submit"
              class="w-full py-4 bg-gradient-to-br from-primary to-primary-container text-white font-bold rounded-xl hover:opacity-90 active:scale-[0.98] transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
              <span class="material-symbols-outlined">send</span>
              Enviar mensaje
            </button>
            <p class="text-xs text-on-surface-variant text-center mt-3">* Campos obligatorios</p>
          </div>

        </form>
        <?php endif; ?>

      </div>
    </div>

  </div>
</section>

<!-- ── MAPA ── -->
<section class="w-full h-[420px] relative overflow-hidden">
  <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3732.0!2d-103.4!3d20.7!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8428ae5e9b3f0001%3A0x1!2sAv.+Obsidiana+3644%2C+Residencial+Loma+Bonita%2C+Zapopan%2C+Jalisco!5e0!3m2!1ses!2smx!4v1"
    width="100%"
    height="100%"
    style="border:0; filter: grayscale(20%) contrast(1.05);"
    allowfullscreen=""
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade">
  </iframe>
  <!-- Card encima del mapa -->
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 pointer-events-none">
    <div class="bg-white/95 backdrop-blur-sm px-8 py-5 rounded-2xl text-center clinical-shadow border border-outline-variant/20 pointer-events-auto">
      <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-1">MM Pharma</p>
      <p class="text-sm font-medium text-on-surface mb-3">Av. Obsidiana 3644, Loma Bonita, Zapopan</p>
      <a href="https://maps.google.com/?q=Av+Obsidiana+3644+Residencial+Loma+Bonita+Zapopan+Jalisco"
         target="_blank"
         class="inline-flex items-center gap-1.5 px-5 py-2 bg-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all">
        <span class="material-symbols-outlined text-sm">map</span>
        Abrir en Google Maps
      </a>
    </div>
  </div>
</section>

</main>

<?php require_once '../includes/footer.php'; ?>