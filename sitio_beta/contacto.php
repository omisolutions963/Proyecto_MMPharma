<?php
$titulo = 'Contacto | MMPharma';
$pagina_actual = 'contacto';
require_once 'INCLUDES/header.php';
?>
<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden bg-primary">
  <div class="absolute inset-0 z-0 overflow-hidden">
    <img src="IMG/60.webp" class="w-full h-full object-cover opacity-30 parallax-bg scale-125 origin-top" data-speed="0.2">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/60 to-primary/90"></div>
  </div>
  <div class="relative z-10 max-w-[1600px] mx-auto px-8 py-20 w-full text-center md:text-left" data-aos="fade-up">
    <h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4">Contáctanos</h1>
    <p class="text-lg text-blue-100/90 max-w-xl mx-auto md:mx-0 leading-relaxed font-medium">
      Nuestro equipo está listo para atender los requerimientos de tu institución, farmacia o distribuidora con la eficiencia que nos caracteriza.
    </p>
  </div>
</section>

<!-- ── CONTENIDO PRINCIPAL ── -->
<main class="bg-[#f0f7ff] py-32">
<section class="max-w-[1600px] mx-auto px-8">
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">

    <!-- ─ Columna Central: datos de contacto ─ -->
    <div class="lg:col-span-8 lg:col-start-3 space-y-16 text-center" data-aos="fade-up">
      <div>
        <h2 class="text-4xl md:text-5xl font-black text-primary mb-6">Canales directos</h2>
        <p class="text-slate-900 text-lg leading-relaxed max-w-2xl mx-auto font-medium">
          Estamos a tu disposición de lunes a viernes de <span class="text-primary font-bold">9:00 AM a 6:00 PM</span>. Contáctanos por cualquiera de nuestros medios oficiales.
        </p>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        <!-- Cobertura Nacional -->
        <div class="flex flex-col items-center gap-6 p-12 bg-white rounded-[2.5rem] transition-all hover:-translate-y-2 group shadow-sm">
          <div class="w-20 h-20 bg-[#f0f7ff] rounded-2xl flex items-center justify-center flex-shrink-0 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined text-4xl">public</span>
          </div>
          <div>
            <h3 class="font-bold text-primary text-xl mb-3">Presencia nacional</h3>
            <p class="text-slate-900 text-sm leading-relaxed font-medium">Cobertura estratégica en toda la República Mexicana.</p>
          </div>
        </div>

        <!-- Teléfonos -->
        <a href="tel:3322207506"
           class="flex flex-col items-center gap-6 p-12 bg-white rounded-[2.5rem] transition-all hover:-translate-y-2 group shadow-sm">
          <div class="w-20 h-20 bg-[#f0f7ff] rounded-2xl flex items-center justify-center flex-shrink-0 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined text-4xl">call</span>
          </div>
          <div>
            <h3 class="font-bold text-primary text-xl mb-3">Líneas telefónicas</h3>
            <p class="text-slate-900 text-sm font-medium tracking-tight">
                33 2220 7506<br>
                33 4348 0581<br>
                33 4348 0582
            </p>
          </div>
        </a>

        <!-- Correo -->
        <a href="mailto:atencionclientes@mmpharma.mx"
           class="flex flex-col items-center gap-6 p-12 bg-white rounded-[2.5rem] transition-all hover:-translate-y-2 group shadow-sm">
          <div class="w-20 h-20 bg-[#f0f7ff] rounded-2xl flex items-center justify-center flex-shrink-0 text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300">
            <span class="material-symbols-outlined text-4xl">mail</span>
          </div>
          <div>
            <h3 class="font-bold text-primary text-xl mb-3">Correo electrónico</h3>
            <p class="text-slate-900 text-sm font-medium">atencionclientes@mmpharma.mx</p>
          </div>
        </a>
      </div>
    </div>

  </div>
</section>
</main>
<?php require_once 'INCLUDES/footer.php'; ?>
