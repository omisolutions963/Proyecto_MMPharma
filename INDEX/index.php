<?php
$titulo = 'MMPharma | Distribuidora Farmacéutica';
$pagina_actual = 'inicio';
$base = '../';
require_once '../includes/header.php';
?>
<!-- Hero Section -->
<section class="relative min-h-screen text-white flex items-center overflow-hidden bg-gradient-to-br from-[#003e79] to-[#1e60aa]">
<div class="max-w-6xl mx-auto px-8 py-24 w-full flex flex-col items-center text-center relative z-10" data-aos="fade-up">
    <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-tight mb-6 text-white">
        Tu distribuidora farmacéutica de confianza
    </h1>
    <p class="text-xl text-blue-100/90 mb-10 max-w-2xl leading-relaxed">
        Accede a más de 2,000 productos farmacéuticos con los estándares más altos de calidad y precios competitivos para tu sector.
    </p>
    
    <div class="flex flex-wrap justify-center gap-4 mb-16">
        <a href="SELECCIÓN_REGISTRO/selección_registro.php" class="px-10 py-4 bg-white text-primary font-bold rounded-xl shadow-2xl hover:bg-blue-50 hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(0,0,0,0.15)] transition-all hover:scale-105 flex items-center justify-center">
            Solicitar acceso
        </a>
        <a href="CATALOGO/catalogo.php" class="px-10 py-4 bg-[#003e79] text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(0,0,0,0.15)] transition-all flex items-center justify-center">
            Ver catálogo
        </a>
    </div>

    <div class="grid grid-cols-3 gap-12 md:gap-24 pt-12 border-t border-white/10 w-full max-w-3xl">
        <div>
            <p class="text-4xl font-black text-white mb-1">+700</p>
            <p class="text-xs uppercase tracking-widest text-blue-300 font-bold">Productos</p>
        </div>
        <div>
            <p class="text-4xl font-black text-white mb-1">4</p>
            <p class="text-xs uppercase tracking-widest text-blue-300 font-bold">Niveles de precio</p>
        </div>
        <div>
            <p class="text-4xl font-black text-white mb-1">24-48h</p>
            <p class="text-xs uppercase tracking-widest text-blue-300 font-bold">Entrega rápida</p>
        </div>
    </div>
</div>
</section>
<!-- ¿Cómo funciona? Section -->
<section class="py-24 bg-white">
  <div class="max-w-[1600px] mx-auto px-8">
<div class="text-center mb-20" data-aos="fade-up">
<h2 class="text-3xl font-black text-primary tracking-tight mb-4">¿Cómo funciona?</h2>
<p class="text-lg text-slate-900 font-medium max-w-2xl mx-auto">Nuestro proceso de alta está diseñado para garantizar la seguridad y profesionalismo en la distribución de insumos médicos.</p>
</div>
<div class="relative grid md:grid-cols-4 gap-8">
<!-- Connector Line (Desktop) -->
<div class="hidden md:block absolute top-12 left-0 w-full h-px border-t-2 border-dashed border-primary/20 z-0"></div>
<!-- Step 1 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="100">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center shadow-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">01</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Solicita tu acceso</h3>
<p class="text-base text-slate-900 px-4 font-medium">Completa el formulario con los datos de tu empresa o farmacia.</p>
</div>
<!-- Step 2 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="200">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center shadow-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">02</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Aprobación en 24 hrs</h3>
<p class="text-base text-slate-900 px-4 font-medium">Validamos tu documentación para asignarte un nivel de cliente.</p>
</div>
<!-- Step 3 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="300">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center shadow-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">03</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Busca y cotiza</h3>
<p class="text-base text-slate-900 px-4 font-medium">Explora el catálogo y genera cotizaciones en tiempo real.</p>
</div>
<!-- Step 4 -->
<div class="relative z-10 flex flex-col items-center text-center group" data-aos="fade-up" data-aos-delay="400">
<div class="w-24 h-24 bg-primary rounded-full flex items-center justify-center shadow-2xl mb-6 group-hover:scale-110 transition-transform duration-300">
<span class="text-2xl font-black text-white">04</span>
</div>
<h3 class="font-black text-primary text-lg mb-2">Recibe tu pedido</h3>
<p class="text-base text-slate-900 px-4 font-medium">Confirmamos tu orden y entregamos en tu domicilio.</p>
</div>
</div>
</section>
<!-- Buscador rápido de productos (Layout Estilo 'Tu operación') -->
<section class="py-24 bg-gradient-to-br from-[#003e79] to-[#1e60aa] text-white overflow-hidden">
  <div class="max-w-[1600px] mx-auto px-8 relative z-10">
    <div class="grid lg:grid-cols-2 gap-20 items-center">
      
      <!-- Lado Izquierdo: Contenido y Buscador -->
      <div data-aos="fade-right">
        <h2 class="text-white/60 font-bold tracking-widest uppercase text-sm mb-4">769 Productos Disponibles</h2>
        <h3 class="text-4xl md:text-5xl font-black tracking-tight mb-6">Encuentra lo que necesitas</h3>
        <p class="text-blue-100/90 text-lg mb-10 leading-relaxed max-w-xl">
          Explora nuestro catálogo completo de medicamentos, material de curación y soluciones especializadas con entrega inmediata en todo el país.
        </p>

        <!-- Buscador integrado -->
        <form action="CATALOGO/catalogo.php" method="GET" class="mb-8">
          <div class="relative group">
            <span class="absolute left-5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input type="text" name="q" placeholder="¿Qué estás buscando?..."
              class="w-full bg-white border-none rounded-xl pl-14 pr-6 py-5 text-on-surface text-base shadow-[0_0_40px_rgba(0,0,0,0.15)] focus:ring-4 focus:ring-white/20 outline-none transition-all">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 px-6 py-3 bg-primary text-white font-bold rounded-xl hover:opacity-90 transition-all">
              Buscar
            </button>
          </div>
        </form>

        <a class="inline-flex items-center gap-2 font-bold text-white hover:underline transition-all" href="CATALOGO/catalogo.php">
          Ver catálogo completo
          <span class="material-symbols-outlined">arrow_forward</span>
        </a>
      </div>

      <!-- Lado Derecho: Categorías (Cuadrícula 2x2) -->
      <div class="grid grid-cols-2 gap-6" data-aos="fade-left">
        <a href="CATALOGO/catalogo.php?tipo=seco&q=aspirina" class="bg-white/15 backdrop-blur-xl hover:bg-white/25 transition-all p-8 rounded-3xl border border-white/20 group text-center hover:shadow-[0_0_30px_rgba(0,0,0,0.15)]">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-3xl text-white">medication</span>
          </div>
          <h4 class="text-white font-black text-lg">Medicamentos</h4>
        </a>
        <a href="CATALOGO/catalogo.php?tipo=seco&q=gasa" class="bg-white/15 backdrop-blur-xl hover:bg-white/25 transition-all p-8 rounded-3xl border border-white/20 group text-center hover:shadow-[0_0_30px_rgba(0,0,0,0.15)]">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-3xl text-white">healing</span>
          </div>
          <h4 class="text-white font-black text-lg">Curación</h4>
        </a>
        <a href="CATALOGO/catalogo.php?tipo=seco&q=solucion" class="bg-white/15 backdrop-blur-xl hover:bg-white/25 transition-all p-8 rounded-3xl border border-white/20 group text-center hover:shadow-[0_0_30px_rgba(0,0,0,0.15)]">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-3xl text-white">vaccines</span>
          </div>
          <h4 class="text-white font-black text-lg">Soluciones</h4>
        </a>
        <a href="CATALOGO/catalogo.php?tipo=red_fria" class="bg-[#32b4ca] hover:brightness-110 transition-all p-8 rounded-3xl border border-white/20 group text-center shadow-[0_0_40px_rgba(0,0,0,0.1)]">
          <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-3xl text-white">ac_unit</span>
          </div>
          <h4 class="text-white font-black text-lg">Red Fría</h4>
        </a>
      </div>

    </div>
  </div>
</section>
<!-- Niveles de Cliente Section -->
<section class="py-32 bg-[#f0f7ff]">
  <div class="max-w-[1600px] mx-auto px-8">
<div class="text-center mb-16" data-aos="fade-up">
<h2 class="text-3xl font-black text-primary mb-4">Soluciones a tu medida</h2>
<p class="text-lg text-slate-900 font-medium">Niveles de acceso adaptados a la magnitud de tu negocio.</p>
</div>
<div class="grid md:grid-cols-3 gap-8">
<!-- Level 1: Farmacia -->
<div class="bg-white p-10 rounded-2xl shadow-[0_0_40px_rgba(0,0,0,0.06)] hover:-translate-y-2 transition-transform" data-aos="fade-up" data-aos-delay="100">
<div class="flex items-center gap-3 mb-8">
<div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
<span class="material-symbols-outlined text-primary" data-icon="storefront">storefront</span>
</div>
<div>
<h4 class="font-black text-primary text-xl leading-tight">Farmacia</h4>
<p class="text-xs uppercase font-black tracking-widest text-secondary mt-1">Puntos de venta</p>
</div>
</div>
<ul class="space-y-4 mb-10">
<li class="flex items-center gap-3 text-base text-slate-900">
<span class="material-symbols-outlined text-secondary text-sm" data-icon="check_circle">check_circle</span>
                        Precios preferenciales por volumen.
                    </li>
<li class="flex items-center gap-3 text-base text-slate-900">
<span class="material-symbols-outlined text-secondary text-sm" data-icon="check_circle">check_circle</span>
                        Envíos rápidos a domicilio.
                    </li>
<li class="flex items-center gap-3 text-base text-slate-900">
<span class="material-symbols-outlined text-secondary text-sm" data-icon="check_circle">check_circle</span>
                        Gestión de pedidos por portal.
                    </li>
</ul>
<a href="REGISTRO_DISTRIBUIDORA_FARMACIA/REGISTRO_DISTRIBUIDORA_FARMACIA.php" class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-secondary hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(0,0,0,0.15)] transition-all inline-block text-center">Solicitar este acceso</a>
</div>
<!-- Level 2: Empresa -->
<div class="bg-[#003e79] p-10 rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.15)] scale-105 text-white relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
<div class="absolute top-0 right-0 p-4">
<span class="bg-secondary px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest">Popular</span>
</div>
<div class="flex items-center gap-3 mb-8">
<div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
<span class="material-symbols-outlined text-white" data-icon="business">business</span>
</div>
<div>
<h4 class="font-black text-white text-xl leading-tight">Empresa</h4>
<p class="text-xs uppercase font-black tracking-widest text-white/80 mt-1">Clínicas y Corporativos</p>
</div>
</div>
<ul class="space-y-4 mb-10">
<li class="flex items-center gap-3 text-sm text-white">
<span class="material-symbols-outlined text-white text-sm" data-icon="check_circle">check_circle</span>
                        Línea de crédito disponible.
                    </li>
<li class="flex items-center gap-3 text-sm text-white">
<span class="material-symbols-outlined text-white text-sm" data-icon="check_circle">check_circle</span>
                        Asesoría técnica especializada.
                    </li>
<li class="flex items-center gap-3 text-sm text-white">
<span class="material-symbols-outlined text-white text-sm" data-icon="check_circle">check_circle</span>
                        Acceso prioritario a lanzamientos.
                    </li>
<li class="flex items-center gap-3 text-sm text-white">
<span class="material-symbols-outlined text-white text-sm" data-icon="check_circle">check_circle</span>
                        Consolidación de envíos nacionales.
                    </li>
</ul>
<a href="REGISTRO_EMPRESA/registro_empresa.php" class="w-full py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(0,0,0,0.15)] transition-all inline-block text-center">Solicitar este acceso</a>
</div>
<!-- Level 3: Distribuidor -->
<div class="bg-white p-10 rounded-2xl shadow-[0_0_40px_rgba(0,0,0,0.06)] hover:-translate-y-2 transition-transform" data-aos="fade-up" data-aos-delay="300">
<div class="flex items-center gap-3 mb-8">
<div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
<span class="material-symbols-outlined text-primary" data-icon="inventory_2">inventory_2</span>
</div>
<div>
<h4 class="font-black text-primary text-xl leading-tight">Distribuidor</h4>
<p class="text-xs uppercase font-black tracking-widest text-secondary mt-1">Mayoreo Masivo</p>
</div>
</div>
<ul class="space-y-4 mb-10">
<li class="flex items-center gap-3 text-base text-slate-900">
<span class="material-symbols-outlined text-primary text-sm" data-icon="check_circle">check_circle</span>
                        Precios directos de fabricante.
                    </li>
<li class="flex items-center gap-3 text-base text-slate-900">
<span class="material-symbols-outlined text-primary text-sm" data-icon="check_circle">check_circle</span>
                        Integración vía API para stock.
                    </li>
<li class="flex items-center gap-3 text-base text-slate-900">
<span class="material-symbols-outlined text-primary text-sm" data-icon="check_circle">check_circle</span>
                        Logística de transporte pesada.
                    </li>
</ul>
<a href="REGISTRO_DISTRIBUIDORA_FARMACIA/REGISTRO_DISTRIBUIDORA_FARMACIA.php" class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-secondary hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(0,0,0,0.15)] transition-all inline-block text-center">Solicitar este acceso</a>
</div>
</div>
  </div>
</section>


<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

</body></html>
