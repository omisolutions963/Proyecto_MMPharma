<?php
$titulo = '¿Quiénes somos? | MMPharma';
$pagina_actual = 'nosotros';
$base = '../';
require_once '../includes/header.php';
?>

<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden bg-background">
  <div class="absolute inset-0 z-0 overflow-hidden">
    <img src="../IMG/5.webp" class="w-full h-full object-cover object-center parallax-bg scale-125 origin-top" data-speed="0.2">
    <div class="absolute inset-0 bg-background/80"></div>
  </div>
  <div class="relative z-10 max-w-[1600px] mx-auto px-8 py-24 w-full text-center md:text-left" data-aos="fade-up">
    <h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4">
      Quiénes somos
    </h1>
    <p class="text-lg text-blue-100/90 max-w-2xl mx-auto md:mx-0 leading-relaxed">
      Más de 10 años conectando a la industria farmacéutica con soluciones de distribución de alta precisión a nivel nacional.
    </p>
  </div>
</section>

<main class="bg-surface py-24">

<!-- ── NUESTRA HISTORIA ── -->
<section class="pb-24">
  <div class="max-w-[1600px] mx-auto px-8">
    <div class="grid md:grid-cols-2 gap-16 items-center">

      <!-- Imagen y Badge -->
      <div class="relative group" data-aos="fade-left">
        <!-- Contenedor con overflow-hidden solo para la imagen -->
        <div class="relative rounded-3xl overflow-hidden">
          <img src="../IMG/7.webp" alt="Nuestra Historia" class="w-full object-cover aspect-[4/3] rounded-3xl group-hover:scale-105 transition-transform duration-700">
          <div class="absolute inset-0 bg-primary mix-blend-overlay opacity-10"></div>
        </div>
        
        <!-- Dato destacado sobre la imagen flotando fuera -->
        <div class="absolute -bottom-6 -right-6 md:-bottom-8 md:-right-8 bg-tertiary p-6 md:p-8 rounded-[2rem] hidden md:flex flex-col items-center text-center z-10 min-w-[200px]">
          <p class="text-5xl font-black text-white mb-2">10+</p>
          <p class="text-xs font-black text-white/90 uppercase tracking-[0.2em] leading-tight">Años de<br>experiencia</p>
        </div>
      </div>

      <!-- Texto -->
      <div class="text-center md:text-left" data-aos="fade-left">
        <h2 class="text-3xl font-bold tracking-tight text-primary-light mb-6">Nuestra historia</h2>
        <div class="space-y-5 text-slate-300 text-lg leading-relaxed font-medium">
          <p>
            MMPharma nació de una necesidad real en el mercado mexicano: una distribución farmacéutica que entendiera que cada medicamento no es solo una unidad de inventario, sino una vida que depende de su integridad y puntualidad.
          </p>
          <p>
            Hemos construido una red de distribución B2B que atiende a farmacias, distribuidoras y empresas del sector salud en todo México con precios diferenciados, atención personalizada y manejo especializado de cadena de frío.
          </p>
          <p>
            Nuestro catálogo de más de 769 productos cubre desde medicamentos de patente hasta insumos médicos y soluciones de red fría, todo respaldado por un equipo comprometido con la precisión en cada entrega.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── MISIÓN Y VISIÓN ── -->
<section class="py-24 bg-background overflow-hidden">
  <div class="max-w-[1600px] mx-auto px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <h2 class="text-3xl font-bold tracking-tight text-white mb-4">Lo que nos mueve</h2>
      <p class="text-blue-100/70 max-w-2xl mx-auto">La base que sustenta cada una de nuestras operaciones.</p>
    </div>
    <div class="grid md:grid-cols-2 gap-8 relative z-10">
      <!-- Misión -->
      <div class="bg-secondary p-12 rounded-2xl text-center md:text-left" data-aos="fade-up" data-aos-delay="100">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
          <span class="material-symbols-outlined text-white text-3xl">flag</span>
        </div>
        <h3 class="text-2xl font-bold text-white mb-4">Misión</h3>
        <p class="text-white/90 leading-relaxed">
          Facilitar el acceso a insumos médicos y farmacéuticos con los más altos estándares de calidad, asegurando que los productos lleguen íntegros, a tiempo y al precio correcto para cada tipo de cliente.
        </p>
      </div>
      <!-- Visión -->
      <div class="bg-secondary p-12 rounded-2xl text-center md:text-left" data-aos="fade-up" data-aos-delay="200">
        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
          <span class="material-symbols-outlined text-white text-3xl">visibility</span>
        </div>
        <h3 class="text-2xl font-bold text-white mb-4">Visión</h3>
        <p class="text-white/90 leading-relaxed">
          Ser la distribuidora de referencia en México, reconocida por nuestra tecnología, transparencia en precios y compromiso inquebrantable con el sector salud.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ── VALORES ── -->
<section class="pt-16 pb-8 bg-surface">
  <div class="max-w-[1600px] mx-auto px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <h2 class="text-3xl font-bold tracking-tight text-primary-light mb-4">Valores fundamentales</h2>
      <p class="text-slate-300 font-medium max-w-2xl mx-auto">Los principios que guían cada decisión dentro de MMPharma.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Integridad -->
      <div class="group p-8 rounded-xl bg-tertiary transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="100">
        <div class="w-14 h-14 rounded-2xl bg-white/10 text-white flex items-center justify-center mb-5 group-hover:bg-white group-hover:text-tertiary transition-colors">
          <span class="material-symbols-outlined text-3xl">verified_user</span>
        </div>
        <h4 class="text-xl font-bold text-white mb-3">Integridad</h4>
        <p class="text-sm text-white/80 leading-relaxed font-medium">Transparencia absoluta en precios, inventarios y condiciones de entrega.</p>
      </div>
      <!-- Calidad -->
      <div class="group p-8 rounded-xl bg-tertiary transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="200">
        <div class="w-14 h-14 rounded-2xl bg-white/10 text-white flex items-center justify-center mb-5 group-hover:bg-white group-hover:text-tertiary transition-colors">
          <span class="material-symbols-outlined text-3xl">high_quality</span>
        </div>
        <h4 class="text-xl font-bold text-white mb-3">Precisión</h4>
        <p class="text-sm text-white/80 leading-relaxed font-medium">Manejo cuidadoso de cadena de frío y entrega exacta de inventarios especializados.</p>
      </div>
      <!-- Innovación -->
      <div class="group p-8 rounded-xl bg-tertiary transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="300">
        <div class="w-14 h-14 rounded-2xl bg-white/10 text-white flex items-center justify-center mb-5 group-hover:bg-white group-hover:text-tertiary transition-colors">
          <span class="material-symbols-outlined text-3xl">lightbulb</span>
        </div>
        <h4 class="text-xl font-bold text-white mb-3">Compromiso</h4>
        <p class="text-sm text-white/80 leading-relaxed font-medium">Atención personalizada y lealtad total hacia nuestros socios comerciales.</p>
      </div>
      <!-- Compromiso -->
      <div class="group p-8 rounded-xl bg-tertiary transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="400">
        <div class="w-14 h-14 rounded-2xl bg-white/10 text-white flex items-center justify-center mb-5 group-hover:bg-white group-hover:text-tertiary transition-colors">
          <span class="material-symbols-outlined text-3xl">handshake</span>
        </div>
        <h4 class="text-xl font-bold text-white mb-3">Innovación</h4>
        <p class="text-sm text-white/80 leading-relaxed font-medium">Portal B2B propio con inventario en tiempo real y cotizaciones automatizadas.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA FINAL ── -->
<section class="pt-12 pb-2 bg-surface">
  <div class="max-w-[1200px] mx-auto px-8">
    <div class="bg-secondary rounded-[2rem] p-12 md:p-16 text-center" data-aos="zoom-in">
      
      <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-3xl mb-8">
        <span class="material-symbols-outlined text-4xl text-white" style="font-variation-settings: 'FILL' 1">handshake</span>
      </div>

      <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-6">¿Listo para trabajar juntos?</h2>
      <p class="text-white/90 text-lg mb-12 max-w-2xl mx-auto font-medium leading-relaxed">
        Únete a la red de distribución farmacéutica más confiable de México. Solicita tu acceso al portal B2B y comienza a optimizar tu abastecimiento hoy mismo.
      </p>
      <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
        <a href="../SELECCIÓN_REGISTRO/selección_registro.php"
           class="px-8 md:px-12 py-5 bg-white text-secondary font-bold rounded-xl hover:bg-slate-100 hover:-translate-y-1 transition-all text-base md:text-lg flex items-center gap-2 whitespace-nowrap">
          Solicitar acceso
          <span class="material-symbols-outlined text-xl">arrow_forward</span>
        </a>
        <a href="../CONTACTO/contacto.php"
           class="px-8 md:px-12 py-5 bg-white/10 text-white font-bold rounded-xl hover:bg-white/20 hover:-translate-y-1 transition-all text-base md:text-lg whitespace-nowrap">
          Contactar ahora
        </a>
      </div>
    </div>
  </div>
</section>

</main>

<?php require_once '../includes/footer.php'; ?>
