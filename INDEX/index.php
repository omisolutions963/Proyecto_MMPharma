<?php
$titulo = 'MMPharma | Distribuidora Farmacéutica';
$pagina_actual = 'inicio';
$base = '../';
require_once '../includes/header.php';
require_once '../INCLUDES/db.php';

// Obtener total de productos dinámico según el tipo de cliente
$is_cliente_idx = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$is_admin_idx   = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$cliente_tipo_idx = $is_cliente_idx ? $_SESSION['cliente_tipo'] : 'FARMACIA';

$where_idx = [];
if ($cliente_tipo_idx === 'EMPRESA') {
    $where_idx[] = "solo_empresa = 'SI'";
}

$where_sql_idx = $where_idx ? 'WHERE ' . implode(' AND ', $where_idx) : '';

try {
    $pdo_idx = getDB();
    $total_stmt = $pdo_idx->prepare("SELECT COUNT(*) FROM catalogo_productos $where_sql_idx");
    $total_stmt->execute();
    $total_productos = $total_stmt->fetchColumn();
} catch (Exception $e) {
    $total_productos = 788; // Fallback
}
?>
<!-- Hero Section -->
<section class="relative min-h-screen text-white flex items-center overflow-hidden bg-primary">
  <div class="absolute inset-0 z-0 overflow-hidden">
    <img src="../IMG/33.webp" class="w-full h-full object-cover object-top opacity-30 parallax-bg scale-125 relative top-[-25%]" data-speed="0.2">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/60 to-primary/90"></div>
  </div>
<div class="max-w-6xl mx-auto px-8 py-24 w-full flex flex-col items-center text-center relative z-10" data-aos="fade-up">
    <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-tight mb-6 text-white">
        Tu distribuidora farmacéutica de confianza
    </h1>
    <p class="text-xl text-blue-100/90 mb-10 max-w-2xl leading-relaxed">
        Accede a más de <?= ltrim(roundStat($total_productos), '+') ?> productos farmacéuticos con los estándares más altos de calidad y precios competitivos para tu sector.
    </p>

    
    <div class="flex flex-wrap justify-center gap-4 mb-16">
        <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php" class="px-10 py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 hover:-translate-y-1 transition-all hover:scale-105 flex items-center justify-center">
            Solicitar acceso
        </a>
        <a href="<?= $base ?? '' ?>CATALOGO/catalogo.php" class="px-10 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white/10 hover:-translate-y-1 transition-all flex items-center justify-center">
            Ver catálogo
        </a>
    </div>
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
<!-- Connector Line (Desktop) -->
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
</section>
<section class="py-24 bg-primary text-white overflow-hidden relative">
  <!-- Background Image Overlay -->
  <div class="absolute inset-0 z-0 overflow-hidden">
    <img src="../IMG/5.webp" class="w-full h-full object-cover object-top opacity-20 parallax-bg scale-125 relative top-[-20%]" data-speed="0.1">
    <div class="absolute inset-0 bg-primary/80"></div>
  </div>
  <div class="max-w-[1600px] mx-auto px-8 relative z-10">
    <div class="grid lg:grid-cols-2 gap-20 items-center">
      
      <!-- Lado Izquierdo: Contenido y Buscador -->
      <div class="text-center lg:text-left" data-aos="fade-right">
        <h2 class="text-white/60 font-bold tracking-widest uppercase text-sm mb-4"><?= number_format($total_productos) ?> Productos disponibles</h2>
        <h3 class="text-4xl md:text-5xl font-black tracking-tight mb-6">Encuentra lo que necesitas</h3>
        <p class="text-blue-100/90 text-lg mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0">
          Explora nuestro catálogo completo de medicamentos, material de curación y soluciones especializadas con entrega inmediata en todo el país.
        </p>

        <!-- Buscador integrado -->
        <form action="CATALOGO/catalogo.php" method="GET" class="mb-8">
          <div class="relative group">
            <span class="absolute left-5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input type="text" name="q" placeholder="¿Qué estás buscando?..."
              class="w-full bg-white border-none rounded-xl pl-14 pr-6 py-5 text-on-surface text-base focus:ring-4 focus:ring-white/20 outline-none transition-all">
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
      <div class="grid grid-cols-2 gap-3 md:gap-6" data-aos="fade-left">
        <a href="CATALOGO/catalogo.php?tipo=seco&q=aspirina" class="bg-white/15 backdrop-blur-xl hover:bg-white/25 transition-all p-4 md:p-8 rounded-2xl md:rounded-3xl border border-white/20 group text-center">
          <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-2xl md:text-3xl text-white">medication</span>
          </div>
          <h4 class="text-white font-black text-sm md:text-lg leading-tight">Medicamentos</h4>
        </a>
        <a href="CATALOGO/catalogo.php?tipo=seco&q=gasa" class="bg-white/15 backdrop-blur-xl hover:bg-white/25 transition-all p-4 md:p-8 rounded-2xl md:rounded-3xl border border-white/20 group text-center">
          <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-2xl md:text-3xl text-white">healing</span>
          </div>
          <h4 class="text-white font-black text-sm md:text-lg leading-tight">Curación</h4>
        </a>
        <a href="CATALOGO/catalogo.php?tipo=seco&q=solucion" class="bg-white/15 backdrop-blur-xl hover:bg-white/25 transition-all p-4 md:p-8 rounded-2xl md:rounded-3xl border border-white/20 group text-center">
          <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-2xl md:text-3xl text-white">vaccines</span>
          </div>
          <h4 class="text-white font-black text-sm md:text-lg leading-tight">Soluciones</h4>
        </a>
        <a href="CATALOGO/catalogo.php?tipo=red_fria" class="bg-tertiary hover:brightness-110 transition-all p-4 md:p-8 rounded-2xl md:rounded-3xl border border-white/20 group text-center">
          <div class="w-12 h-12 md:w-16 md:h-16 bg-white/20 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-2xl md:text-3xl text-white">ac_unit</span>
          </div>
          <h4 class="text-white font-black text-sm md:text-lg leading-tight">Red Fría</h4>
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
<div class="bg-primary p-12 rounded-3xl hover:-translate-y-2 transition-transform text-white shadow-xl flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="100">
  <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
    <span class="material-symbols-outlined text-white text-4xl" data-icon="storefront">storefront</span>
  </div>
  <h4 class="font-black text-white text-3xl leading-tight mb-2">Farmacia</h4>
  <p class="text-xs uppercase font-black tracking-[0.2em] text-white/70 mb-10">Puntos de venta</p>
  
  <a href="REGISTRO_DISTRIBUIDORA_FARMACIA/REGISTRO_DISTRIBUIDORA_FARMACIA.php" class="w-full py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 hover:-translate-y-1 transition-all inline-block text-center">Solicitar este acceso</a>
</div>
<!-- Level 2: Empresa -->
<div class="bg-secondary p-12 rounded-3xl hover:-translate-y-2 transition-transform text-white shadow-xl flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="200">
  <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
    <span class="material-symbols-outlined text-white text-4xl" data-icon="business">business</span>
  </div>
  <h4 class="font-black text-white text-3xl leading-tight mb-2">Empresa</h4>
  <p class="text-xs uppercase font-black tracking-[0.2em] text-white/70 mb-10">Clínicas y corporativos</p>

  <a href="REGISTRO_EMPRESA/registro_empresa.php" class="w-full py-4 bg-white text-secondary font-bold rounded-xl hover:bg-blue-50 hover:-translate-y-1 transition-all inline-block text-center">Solicitar este acceso</a>
</div>
<!-- Level 3: Distribuidor -->
<div class="bg-tertiary p-12 rounded-3xl hover:-translate-y-2 transition-transform text-white shadow-xl flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="300">
  <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mb-6">
    <span class="material-symbols-outlined text-white text-4xl" data-icon="inventory_2">inventory_2</span>
  </div>
  <h4 class="font-black text-white text-3xl leading-tight mb-2">Distribuidor</h4>
  <p class="text-xs uppercase font-black tracking-[0.2em] text-white/70 mb-10">Mayoreo masivo</p>

  <a href="REGISTRO_DISTRIBUIDORA_FARMACIA/REGISTRO_DISTRIBUIDORA_FARMACIA.php" class="w-full py-4 bg-white text-tertiary font-bold rounded-xl hover:bg-blue-50 hover:-translate-y-1 transition-all inline-block text-center">Solicitar este acceso</a>
</div>
</div>
  </div>
</section>


<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

</body></html>
