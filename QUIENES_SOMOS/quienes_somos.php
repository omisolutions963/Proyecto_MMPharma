<?php
$titulo = '¿Quiénes somos? | MMPharma';
$pagina_actual = 'nosotros';
$base = '../';
require_once '../includes/header.php';
?>

<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden bg-primary">
  <div class="absolute inset-0 z-0 overflow-hidden">
    <img src="../IMG/40.webp" class="w-full h-full object-cover opacity-30 parallax-bg scale-125 origin-top" data-speed="0.2">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/60 to-primary/90"></div>
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

<main class="bg-[#f0f7ff]">

<!-- ── NUESTRA HISTORIA ── -->
<section class="py-24 bg-[#f0f7ff]">
  <div class="max-w-[1600px] mx-auto px-8">
    <div class="grid md:grid-cols-2 gap-16 items-center">

      <!-- Imagen -->
      <div class="relative" data-aos="fade-right">
        <div class="aspect-square rounded-2xl overflow-hidden">
          <img class="w-full h-full object-cover"
               src="../IMG/59.webp"
               alt="Almacén MMPharma">
        </div>
        <div class="absolute -bottom-6 -right-6 bg-white p-8 rounded-xl hidden md:block">
          <div class="text-4xl font-black text-primary">+10</div>
          <div class="text-sm font-semibold text-on-surface-variant tracking-wide">años de trayectoria</div>
        </div>
      </div>

      <!-- Texto -->
      <div class="text-center md:text-left" data-aos="fade-left">
        <h2 class="text-3xl font-bold tracking-tight text-primary mb-6">Nuestra historia</h2>
        <div class="space-y-5 text-slate-900 text-lg leading-relaxed font-medium">
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
</section>

<!-- ── MISIÓN Y VISIÓN ── -->
<section class="py-24 bg-primary text-white overflow-hidden">
  <div class="max-w-[1600px] mx-auto px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <h2 class="text-3xl font-bold tracking-tight text-white mb-4">Lo que nos mueve</h2>
      <p class="text-blue-100/70 max-w-2xl mx-auto">La base que sustenta cada una de nuestras operaciones.</p>
    </div>
    <div class="grid md:grid-cols-2 gap-8">
      <div class="bg-white/5 backdrop-blur-xl p-12 rounded-2xl text-center md:text-left" data-aos="fade-up" data-aos-delay="100">

        <h3 class="text-2xl font-bold text-white mb-4">Misión</h3>
        <p class="text-blue-100/70 leading-relaxed">
          Facilitar el acceso a insumos médicos y farmacéuticos con los más altos estándares de calidad, asegurando que los productos lleguen íntegros, a tiempo y al precio correcto para cada tipo de cliente.
        </p>
      </div>
      <div class="bg-white/5 backdrop-blur-xl p-12 rounded-2xl text-center md:text-left" data-aos="fade-up" data-aos-delay="200">

        <h3 class="text-2xl font-bold text-white mb-4">Visión</h3>
        <p class="text-blue-100/70 leading-relaxed">
          Ser la distribuidora de referencia en México, reconocida por nuestra tecnología, transparencia en precios y compromiso inquebrantable con el sector salud.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ── VALORES ── -->
<section class="py-32 bg-[#f0f7ff]">
  <div class="max-w-[1600px] mx-auto px-8">
    <div class="text-center mb-16" data-aos="fade-up">
      <h2 class="text-3xl font-bold tracking-tight text-primary mb-4">Valores fundamentales</h2>
      <p class="text-slate-900 font-medium max-w-2xl mx-auto">Los principios que guían cada decisión dentro de MMPharma.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="group p-8 rounded-xl bg-white transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="100">
        <div class="w-16 h-16 bg-[#f0f7ff] rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
          <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white transition-colors duration-300" style="font-variation-settings: 'FILL' 1">verified_user</span>
        </div>
        <h4 class="text-xl font-bold text-primary mb-3">Integridad</h4>
        <p class="text-sm text-slate-900 leading-relaxed font-medium">Transparencia absoluta en precios, inventarios y condiciones de entrega.</p>
      </div>
      <div class="group p-8 rounded-xl bg-white transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="200">
        <div class="w-16 h-16 bg-[#f0f7ff] rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
          <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white transition-colors duration-300" style="font-variation-settings: 'FILL' 1">biotech</span>
        </div>
        <h4 class="text-xl font-bold text-primary mb-3">Precisión</h4>
        <p class="text-sm text-slate-900 leading-relaxed font-medium">Manejo cuidadoso de cadena de frío y entrega exacta de inventarios especializados.</p>
      </div>
      <div class="group p-8 rounded-xl bg-white transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="300">
        <div class="w-16 h-16 bg-[#f0f7ff] rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
          <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white transition-colors duration-300" style="font-variation-settings: 'FILL' 1">handshake</span>
        </div>
        <h4 class="text-xl font-bold text-primary mb-3">Compromiso</h4>
        <p class="text-sm text-slate-900 leading-relaxed font-medium">Atención personalizada y lealtad total hacia nuestros socios comerciales.</p>
      </div>
      <div class="group p-8 rounded-xl bg-white transition-all duration-300 flex flex-col items-center text-center md:items-start md:text-left" data-aos="fade-up" data-aos-delay="400">
        <div class="w-16 h-16 bg-[#f0f7ff] rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
          <span class="material-symbols-outlined text-4xl text-primary group-hover:text-white transition-colors duration-300" style="font-variation-settings: 'FILL' 1">memory</span>
        </div>
        <h4 class="text-xl font-bold text-primary mb-3">Innovación</h4>
        <p class="text-sm text-slate-900 leading-relaxed font-medium">Portal B2B propio con inventario en tiempo real y cotizaciones automatizadas.</p>
      </div>
    </div>
  </div>
</section>



<!-- ── CTA FINAL ── -->
<section class="pb-32 bg-[#f0f7ff] relative overflow-hidden pt-0">
  <!-- Círculos decorativos de fondo -->
  <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
  <div class="absolute bottom-0 left-0 w-96 h-96 bg-tertiary/5 rounded-full -ml-48 -mb-48 blur-3xl"></div>

  <div class="max-w-[1600px] mx-auto px-12 relative z-10">
    <div class="bg-white rounded-[2rem] p-12 md:p-20 text-center border border-blue-50 relative overflow-hidden group" data-aos="zoom-in">
      
      <div class="inline-flex items-center justify-center w-20 h-20 bg-[#f0f7ff] rounded-3xl mb-8 group-hover:rotate-[360deg] transition-transform duration-700">
        <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings: 'FILL' 1">handshake</span>
      </div>

      <h2 class="text-4xl md:text-5xl font-black text-primary tracking-tight mb-6">¿Listo para trabajar juntos?</h2>
      <p class="text-slate-900 text-lg mb-12 max-w-2xl mx-auto font-medium leading-relaxed">
        Únete a la red de distribución farmacéutica más confiable de México. Solicita tu acceso al portal B2B y comienza a optimizar tu abastecimiento hoy mismo.
      </p>
      <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
        <a href="../SELECCIÓN_REGISTRO/selección_registro.php"
           class="px-8 md:px-12 py-5 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(0,62,121,0.2)] active:scale-95 transition-all text-base md:text-lg flex items-center gap-2 whitespace-nowrap">
          Solicitar acceso
        </a>
        <a href="../CONTACTO/contacto.php"
           class="px-8 md:px-12 py-5 border-2 border-primary/10 text-primary font-bold rounded-xl hover:bg-primary/5 hover:-translate-y-1 transition-all text-base md:text-lg whitespace-nowrap">
          Contactar ahora
        </a>
      </div>
    </div>
  </div>
</section>

</main>

<?php require_once '../includes/footer.php'; ?>
