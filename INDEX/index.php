<?php
$titulo = 'MMPharma | Distribuidora Farmacéutica';
$pagina_actual = 'inicio';
$base = '../';
require_once '../includes/header.php';
?>
<!-- Hero Section -->
<section class="relative min-h-[580px] text-white flex items-center overflow-hidden">
  <!-- Imagen de fondo -->
  <img src="../img/33.webp" class="absolute inset-0 w-full h-full object-cover" alt="MMPharma bodega">
  <!-- Overlay azul -->
  <div class="absolute inset-0 bg-[#002451] opacity-80"></div>
<div class="max-w-7xl mx-auto px-8 py-20 w-full grid md:grid-cols-2 gap-12 items-center">
<div class="z-10">
<h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight mb-6">
                    Tu distribuidora farmacéutica de confianza
                </h1>
<p class="text-lg text-blue-100/90 mb-8 max-w-lg leading-relaxed">
                    Accede a más de 2,000 productos farmacéuticos con los estándares más altos de calidad y precios competitivos para tu sector.
                </p>
<div class="flex flex-wrap gap-4 mb-12">
<a href="SELECCIÓN_REGISTRO/selección_registro.php"><button class="px-8 py-4 bg-white text-[#1A3A6B] font-bold rounded-lg shadow-xl hover:bg-blue-50 transition-all">
                        Solicitar acceso
                    </button></a>
<button class="px-8 py-4 border-2 border-white/30 hover:border-white/60 text-white font-bold rounded-lg backdrop-blur-sm transition-all">
                        Ver catálogo de ejemplo
                    </button>
</div>
<div class="grid grid-cols-3 gap-6 pt-8 border-t border-white/10">
<div>
<p class="text-3xl font-black">+700</p>
<p class="text-xs uppercase tracking-wider text-blue-200">Productos</p>
</div>
<div>
<p class="text-3xl font-black">4</p>
<p class="text-xs uppercase tracking-wider text-blue-200">Niveles de precio</p>
</div>
<div>
<p class="text-3xl font-black">24-48h</p>
<p class="text-xs uppercase tracking-wider text-blue-200">Entrega rápida</p>
</div>
</div>
</div>
<div class="relative hidden md:block">
<div class="absolute -top-20 -right-20 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
<!-- Quote Mockup Card -->
<div class="relative bg-white text-on-surface p-8 rounded-2xl shadow-2xl rotate-[3deg] border border-white/20 transform hover:rotate-0 transition-transform duration-500">
<div class="flex justify-between items-start mb-6">
<div>
<p class="text-[10px] uppercase tracking-widest font-bold text-outline">Cotización #7724</p>
<h3 class="font-bold text-lg">Resumen de pedido</h3>
</div>
<span class="material-symbols-outlined text-primary" data-icon="description">description</span>
</div>
<div class="space-y-4 mb-8">
<div class="flex justify-between items-center py-2 border-b border-surface-container">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-surface-container-low rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-primary text-sm" data-icon="medication">medication</span>
</div>
<div>
<p class="text-xs font-bold">Aspirina 500mg c/40 Tab</p>
<p class="text-[10px] text-on-surface-variant">10 Piezas</p>
</div>
</div>
<p class="text-xs font-bold">$511.50</p>
</div>
<div class="flex justify-between items-center py-2 border-b border-surface-container">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-surface-container-low rounded-lg flex items-center justify-center text-tertiary-container">
<span class="material-symbols-outlined text-sm" data-icon="ac_unit">ac_unit</span>
</div>
<div>
<p class="text-xs font-bold">Alquifos 1g Sol. Iny. ❄</p>
<p class="text-[10px] text-on-surface-variant">3 Piezas — Red Fría</p>
</div>
</div>
<p class="text-xs font-bold">$1,890.00</p>
</div>
<div class="flex justify-between items-center py-2">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-surface-container-low rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-primary text-sm" data-icon="healing">healing</span>
</div>
<div>
<p class="text-xs font-bold">Gasa Estéril 10x10cm c/100</p>
<p class="text-[10px] text-on-surface-variant">5 Exhibidores</p>
</div>
</div>
<p class="text-xs font-bold">$526.75</p>
</div>
</div>
<div class="bg-surface-container-low p-4 rounded-xl space-y-2">
<div class="flex justify-between text-xs">
<span class="text-on-surface-variant">Subtotal</span>
<span class="font-medium">$2,928.25</span>
</div>
<div class="flex justify-between text-xs">
<span class="text-on-surface-variant">IVA (16%)</span>
<span class="font-medium">$81.84</span>
</div>
<div class="flex justify-between text-sm pt-2 border-t border-outline-variant/30">
<span class="font-bold">Total</span>
<span class="font-black text-primary">$3,010.09</span>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- ¿Cómo funciona? Section -->
<section class="py-24 max-w-7xl mx-auto px-8">
<div class="text-center mb-20">
<h2 class="text-3xl font-black text-on-surface tracking-tight mb-4">¿Cómo funciona?</h2>
<p class="text-on-surface-variant max-w-2xl mx-auto">Nuestro proceso de alta está diseñado para garantizar la seguridad y profesionalismo en la distribución de insumos médicos.</p>
</div>
<div class="relative grid md:grid-cols-4 gap-8">
<!-- Connector Line (Desktop) -->
<div class="hidden md:block absolute top-12 left-0 w-full h-px border-t-2 border-dashed border-outline-variant z-0"></div>
<!-- Step 1 -->
<div class="relative z-10 flex flex-col items-center text-center group">
<div class="w-24 h-24 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-lg border border-outline-variant/10 mb-6 group-hover:bg-primary transition-colors duration-300">
<span class="text-2xl font-black text-primary group-hover:text-white">01</span>
</div>
<h3 class="font-bold text-lg mb-2">Solicita tu acceso</h3>
<p class="text-sm text-on-surface-variant px-4">Completa el formulario con los datos de tu empresa o farmacia.</p>
</div>
<!-- Step 2 -->
<div class="relative z-10 flex flex-col items-center text-center group">
<div class="w-24 h-24 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-lg border border-outline-variant/10 mb-6 group-hover:bg-primary transition-colors duration-300">
<span class="text-2xl font-black text-primary group-hover:text-white">02</span>
</div>
<h3 class="font-bold text-lg mb-2">Aprobación en 24 hrs</h3>
<p class="text-sm text-on-surface-variant px-4">Validamos tu documentación para asignarte un nivel de cliente.</p>
</div>
<!-- Step 3 -->
<div class="relative z-10 flex flex-col items-center text-center group">
<div class="w-24 h-24 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-lg border border-outline-variant/10 mb-6 group-hover:bg-primary transition-colors duration-300">
<span class="text-2xl font-black text-primary group-hover:text-white">03</span>
</div>
<h3 class="font-bold text-lg mb-2">Busca y cotiza</h3>
<p class="text-sm text-on-surface-variant px-4">Explora el catálogo y genera cotizaciones en tiempo real.</p>
</div>
<!-- Step 4 -->
<div class="relative z-10 flex flex-col items-center text-center group">
<div class="w-24 h-24 bg-surface-container-lowest rounded-full flex items-center justify-center shadow-lg border border-outline-variant/10 mb-6 group-hover:bg-primary transition-colors duration-300">
<span class="text-2xl font-black text-primary group-hover:text-white">04</span>
</div>
<h3 class="font-bold text-lg mb-2">Recibe tu pedido</h3>
<p class="text-sm text-on-surface-variant px-4">Confirmamos tu orden y entregamos en tu domicilio.</p>
</div>
</div>
</section>
<!-- Buscador rápido de productos -->
<section class="py-24 relative overflow-hidden">
  <img src="img/23 (1).webp" class="absolute inset-0 w-full h-full object-cover" alt="MMPharma bodega">
  <div class="absolute inset-0 bg-gradient-to-br from-[#002451]/90 to-[#006397]/80"></div>
  <div class="max-w-7xl mx-auto px-8 relative z-10">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
      <div>
        <h2 class="text-white/60 font-bold tracking-widest uppercase text-sm mb-2">769 Productos Disponibles</h2>
        <h3 class="text-4xl font-black tracking-tight text-white">Encuentra lo que necesitas</h3>
      </div>
      <a class="flex items-center gap-2 font-bold text-white hover:underline" href="CATALOGO/catalogo.php">
        Ver catálogo completo
        <span class="material-symbols-outlined">arrow_forward</span>
      </a>
    </div>

    <!-- Buscador rápido -->
    <form action="CATALOGO/catalogo.php" method="GET" class="mb-12">
      <div class="flex gap-3 max-w-2xl">
        <div class="relative flex-1">
          <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline">search</span>
          <input type="text" name="q" placeholder="Buscar por nombre o sustancia activa..."
            class="w-full bg-white/95 rounded-xl pl-11 pr-4 py-4 text-on-surface text-sm focus:ring-2 focus:ring-white outline-none">
        </div>
        <button type="submit"
          class="px-6 py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 transition-all text-sm whitespace-nowrap">
          Buscar
        </button>
      </div>
    </form>

    <!-- Accesos rápidos por tipo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <a href="CATALOGO/catalogo.php?tipo=seco&q=aspirina" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all p-6 rounded-2xl border border-white/20 group">
        <span class="material-symbols-outlined text-3xl text-white mb-3 block">medication</span>
        <h4 class="text-white font-bold mb-1">Medicamentos</h4>
        <p class="text-white/60 text-xs">Patente, genérico y especialidades</p>
      </a>
      <a href="CATALOGO/catalogo.php?tipo=seco&q=gasa" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all p-6 rounded-2xl border border-white/20 group">
        <span class="material-symbols-outlined text-3xl text-white mb-3 block">healing</span>
        <h4 class="text-white font-bold mb-1">Material de curación</h4>
        <p class="text-white/60 text-xs">Gasas, vendajes, jeringas y más</p>
      </a>
      <a href="CATALOGO/catalogo.php?tipo=seco&q=solucion" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 transition-all p-6 rounded-2xl border border-white/20 group">
        <span class="material-symbols-outlined text-3xl text-white mb-3 block">vaccines</span>
        <h4 class="text-white font-bold mb-1">Soluciones e inyectables</h4>
        <p class="text-white/60 text-xs">Sueros, ampolletas y soluciones</p>
      </a>
      <a href="CATALOGO/catalogo.php?tipo=red_fria" class="bg-tertiary-container/80 backdrop-blur-sm hover:bg-tertiary-container transition-all p-6 rounded-2xl border border-tertiary-fixed/30 group">
        <span class="material-symbols-outlined text-3xl text-tertiary-fixed mb-3 block">ac_unit</span>
        <h4 class="text-white font-bold mb-1">Red Fría</h4>
        <p class="text-tertiary-fixed/70 text-xs">27 productos con cadena de frío</p>
      </a>
    </div>
  </div>
</section>
<!-- Niveles de Cliente Section -->
<section class="py-24 max-w-7xl mx-auto px-8">
<div class="text-center mb-16">
<h2 class="text-3xl font-black mb-4">Soluciones a tu medida</h2>
<p class="text-on-surface-variant">Niveles de acceso adaptados a la magnitud de tu negocio.</p>
</div>
<div class="grid md:grid-cols-3 gap-8">
<!-- Level 1: Farmacia -->
<div class="bg-white p-10 rounded-3xl border border-tertiary-container/10 shadow-lg hover:-translate-y-2 transition-transform">
<div class="flex items-center gap-3 mb-8">
<div class="w-12 h-12 bg-tertiary-container/10 rounded-xl flex items-center justify-center">
<span class="material-symbols-outlined text-tertiary-container" data-icon="storefront">storefront</span>
</div>
<div>
<h4 class="font-black text-tertiary-container text-xl leading-none">Farmacia</h4>
<p class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant">Puntos de venta</p>
</div>
</div>
<ul class="space-y-4 mb-10">
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-tertiary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Precios preferenciales por volumen.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-tertiary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Entrega local en 24 horas.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-tertiary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Gestión de pedidos por portal.
                    </li>
</ul>
<a href="REGISTRO_DISTRIBUIDORA_FARMACIA/REGISTRO_DISTRIBUIDORA_FARMACIA.php" class="w-full py-4 bg-tertiary-container text-white font-bold rounded-xl hover:opacity-90 transition-all inline-block text-center" href="#">Solicitar este acceso</a>
</div>
<!-- Level 2: Empresa -->
<div class="bg-primary p-10 rounded-3xl shadow-2xl scale-105 border border-white/10 text-white relative overflow-hidden">
<div class="absolute top-0 right-0 p-4">
<span class="bg-secondary px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Popular</span>
</div>
<div class="flex items-center gap-3 mb-8">
<div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
<span class="material-symbols-outlined text-white" data-icon="business">business</span>
</div>
<div>
<h4 class="font-black text-white text-xl leading-none">Empresa</h4>
<p class="text-[10px] uppercase font-bold tracking-widest text-blue-200">Clínicas y Corporativos</p>
</div>
</div>
<ul class="space-y-4 mb-10">
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-secondary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Línea de crédito disponible.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-secondary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Asesoría técnica especializada.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-secondary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Acceso prioritario a lanzamientos.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-secondary-container text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Consolidación de envíos nacionales.
                    </li>
</ul>
<a href="REGISTRO_EMPRESA/registro_empresa.php" class="w-full py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 transition-all inline-block text-center" href="#">Solicitar este acceso</a>
</div>
<!-- Level 3: Distribuidor -->
<div class="bg-surface-container p-10 rounded-3xl border border-outline-variant/30 shadow-lg hover:-translate-y-2 transition-transform">
<div class="flex items-center gap-3 mb-8">
<div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
<span class="material-symbols-outlined text-primary" data-icon="inventory_2">inventory_2</span>
</div>
<div>
<h4 class="font-black text-primary text-xl leading-none">Distribuidor</h4>
<p class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant">Mayoreo Masivo</p>
</div>
</div>
<ul class="space-y-4 mb-10">
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Precios directos de fabricante.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Integración vía API para stock.
                    </li>
<li class="flex items-start gap-3 text-sm">
<span class="material-symbols-outlined text-primary text-sm mt-1" data-icon="check_circle">check_circle</span>
                        Logística de transporte pesada.
                    </li>
</ul>
<a href="REGISTRO_DISTRIBUIDORA_FARMACIA/REGISTRO_DISTRIBUIDORA_FARMACIA.php" class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:opacity-90 transition-all inline-block text-center" href="#">Solicitar este acceso</a>
</div>
</div>
</section>
<!-- Stats Banner -->
<section class="bg-primary text-white py-20">
<div class="max-w-7xl mx-auto px-8 grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
<div>
<p class="text-5xl font-black mb-2">+10</p>
<p class="text-sm uppercase tracking-widest text-blue-200">Años de experiencia</p>
</div>
<div>
<p class="text-5xl font-black mb-2">+300</p>
<p class="text-sm uppercase tracking-widest text-blue-200">Clientes activos</p>
</div>
<div>
<p class="text-5xl font-black mb-2">+700</p>
<p class="text-sm uppercase tracking-widest text-blue-200">Productos en catálogo</p>
</div>
<div>
<p class="text-5xl font-black mb-2">4</p>
<p class="text-sm uppercase tracking-widest text-blue-200">Niveles de precio</p>
</div>
</div>
</section>

<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

</body></html>