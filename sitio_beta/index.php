<?php
$titulo = 'MMPharma | Distribuidora Farmacéutica';
$pagina_actual = 'inicio';
require_once 'INCLUDES/header.php';
?>
<!-- Hero Section -->
<section class="relative min-h-screen text-white flex items-center overflow-hidden bg-primary">
  <div class="absolute inset-0 z-0 overflow-hidden">
    <img src="IMG/33.webp" class="w-full h-full object-cover object-top opacity-30 parallax-bg scale-125 relative top-[-25%]" data-speed="0.2">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/60 to-primary/90"></div>
  </div>
<div class="max-w-6xl mx-auto px-8 py-24 w-full flex flex-col items-center text-center relative z-10" data-aos="fade-up">
    <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-tight mb-6 text-white">
        Tu distribuidora farmacéutica de confianza
    </h1>
    <p class="text-xl text-blue-100/90 mb-10 max-w-2xl leading-relaxed">
        Accede a más de 700 productos farmacéuticos con los estándares más altos de calidad y precios competitivos para tu sector.
    </p>

    <!-- Botones ocultos para versión beta -->
</div>
</section>

<!-- ¿Cómo funciona? Section -->
<section class="py-24 bg-[#f0f7ff]">
  <div class="max-w-[1600px] mx-auto px-8">
<div class="text-center mb-20" data-aos="fade-up">
<h2 class="text-3xl font-black text-primary tracking-tight mb-4">¿Cómo funciona?</h2>
<p class="text-lg text-slate-900 font-medium max-w-2xl mx-auto">Nuestro proceso de alta está diseñado para garantizar la seguridad y profesionalismo en la distribución de insumos médicos.</p>
</div>
<div class="relative grid md:grid-cols-4 gap-8">
<div class="hidden md:block absolute top-12 left-0 w-full h-px border-t-2 border-dashed border-primary/20 z-0"></div>
<!-- Step 1 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="100">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">01</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Solicita tu acceso</h3>
<p class="text-base text-slate-900 px-4 font-medium">Completa el formulario con los datos de tu empresa o farmacia.</p>
</div>
<!-- Step 2 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="200">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">02</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Aprobación en 24 hrs</h3>
<p class="text-base text-slate-900 px-4 font-medium">Validamos tu documentación para asignarte un nivel de cliente.</p>
</div>
<!-- Step 3 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="300">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">03</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Busca y cotiza</h3>
<p class="text-base text-slate-900 px-4 font-medium">Explora el catálogo y genera cotizaciones en tiempo real.</p>
</div>
<!-- Step 4 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="400">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">04</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Recibe tu pedido</h3>
<p class="text-base text-slate-900 px-4 font-medium">Confirmamos tu orden y entregamos en tu domicilio.</p>
</div>
</div>
</div>
</section>

<?php require_once 'INCLUDES/footer.php'; ?>
