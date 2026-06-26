<?php
$titulo = 'MMPharma | Distribuidora Farmacéutica';
$pagina_actual = 'inicio';
$base = '../';
require_once '../includes/header.php';
require_once '../includes/db.php';

// Obtener total de productos dinámico según el tipo de cliente
$is_cliente_idx = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$is_admin_idx = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$cliente_tipo_idx = $is_cliente_idx ? $_SESSION['cliente_tipo'] : 'FARMACIA';

$where_idx = ["codigo NOT IN ('99999999999', 'DESCUENTO')"];
if ($cliente_tipo_idx === 'EMPRESA') {
 $where_idx[] = "(solo_empresa = 'SI' OR nombre LIKE '%ASPIRINA%' OR sustancia LIKE '%ASPIRINA%' OR nombre LIKE '%LORATADINA%' OR sustancia LIKE '%LORATADINA%' OR nombre LIKE '%LORATIDINA%' OR sustancia LIKE '%LORATIDINA%' OR nombre LIKE '%BUSCAPINA%' OR nombre LIKE '%BUTILHIOSCINA%' OR sustancia LIKE '%BUTILHIOSCINA%')";
} else {
 $where_idx[] = "solo_empresa = 'NO'";
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

// Helper function for the stats
function roundStatIndex($num) {
 if ($num >= 1000) {
 return '+' . floor($num / 1000) . ',' . str_pad(($num % 1000), 3, '0', STR_PAD_LEFT);
 }
 return '+' . $num;
}
?>

<!-- Hero Section -->
<section class="relative pt-20 pb-16 bg-white overflow-hidden">
 <div class="max-w-[1369px] mx-auto px-8 w-full">
 <div class="flex flex-col lg:flex-row items-center gap-12">
 <!-- Left Content -->
 <div class="w-full lg:w-1/2 z-10" data-aos="fade-right">
 <h1 class="text-5xl md:text-6xl lg:text-[72px] font-black tracking-tight leading-[1.1] mb-6 text-primary">
 Cuidamos<br>
 la salud de<br>
 <span class="text-tertiary">tu negocio</span>
 </h1>
 <p class="text-lg md:text-xl text-slate-900 mb-10 max-w-lg leading-relaxed font-medium">
 Distribuimos medicamentos con eficiencia, confianza y atención personalizada.
 </p>

 <div class="flex flex-wrap gap-4 mb-12">
 <a href="<?= $base ?? '' ?>catalogo/catalogo.php" class="px-8 py-3.5 bg-primary text-white font-bold rounded-full hover:bg-primary/90 hover:-translate-y-0.5 transition-all flex items-center gap-2 group">
 Conoce nuestro catálogo <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>
 <a href="<?= $base ?? '' ?>contacto/contacto.php" class="px-8 py-3.5 text-white font-bold rounded-full bg-tertiary hover:bg-tertiary/90 hover:-translate-y-0.5 transition-all flex items-center gap-2 group">
 Habla con un asesor <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">chat</span>
 </a>
 </div>
 </div>

 <!-- Right Image area -->
 <div class="w-full lg:w-1/2 relative" data-aos="fade-left">
 <!-- Blob shape / Mask for image (Isolated Group for Hover) -->
 <div class="relative w-full aspect-[4/3] max-w-[800px] mx-auto overflow-hidden rounded-[40px] md:rounded-[80px] lg:rounded-bl-[120px] group">
 <img src="../img/33.webp" alt="Equipo MMPharma" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
 </div>

 <!-- Floating Badge -->
 <div class="absolute -bottom-8 right-4 md:right-12 bg-primary text-white p-6 rounded-2xl flex items-center gap-4 max-w-[320px] animate-bounce-slow group/badge">
 <p class="font-bold text-sm leading-snug">Comprometidos con tu bienestar y el de tus clientes</p>
 <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover/badge:bg-white transition-all duration-300">
 <span class="material-symbols-outlined text-white group-hover/badge:text-primary text-2xl transition-colors duration-300">check</span>
 </div>
 </div>
 </div>
 </div>
 </div>
</section>

<!-- Features Grid -->
<section class="py-[69px] bg-white relative z-20">
 <div class="max-w-[1369px] mx-auto px-8">
 <div class="grid grid-cols-2 md:grid-cols-4 gap-8">

 <div class="flex flex-col items-center text-center gap-4 group cursor-default" data-aos="fade-up" data-aos-delay="100">
 <span class="material-symbols-outlined text-primary text-5xl group-hover:scale-110 transition-all duration-300">person</span>
 <p class="font-black text-tertiary text-base">Atención personalizada</p>
 </div>

 <div class="flex flex-col items-center text-center gap-4 group cursor-default" data-aos="fade-up" data-aos-delay="200">
 <span class="material-symbols-outlined text-primary text-5xl group-hover:scale-110 transition-all duration-300">medication</span>
 <p class="font-black text-tertiary text-base">Catálogo especializado</p>
 <p class="text-xs text-slate-500 font-medium">Más de <?= number_format($total_productos) ?> productos farmacéuticos</p>
 </div>

 <div class="flex flex-col items-center text-center gap-4 group cursor-default" data-aos="fade-up" data-aos-delay="300">
 <span class="material-symbols-outlined text-primary text-5xl group-hover:scale-110 transition-all duration-300">location_on</span>
 <p class="font-black text-tertiary text-base">Cobertura nacional</p>
 </div>

 <div class="flex flex-col items-center text-center gap-4 group cursor-default" data-aos="fade-up" data-aos-delay="400">
 <span class="material-symbols-outlined text-primary text-5xl group-hover:scale-110 transition-all duration-300">verified</span>
 <p class="font-black text-tertiary text-base">Calidad garantizada</p>
 </div>

 </div>
 </div>
</section>

<!-- Nuestras Soluciones Section -->
<section class="py-[69px] bg-white">
 <div class="max-w-[1369px] mx-auto px-8">
 <h2 class="text-4xl md:text-5xl font-black text-primary mb-12 text-center tracking-tight" data-aos="fade-up">
 Nuestras <span class="text-tertiary">soluciones</span>
 </h2>
 
 <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
 <!-- Card 1 -->
 <div class="bg-primary p-8 rounded-3xl border border-primary/20 transition-all group flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="100">
 <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-white transition-all text-white group-hover:text-primary">
 <span class="material-symbols-outlined text-3xl">inventory_2</span>
 </div>
 <h3 class="font-black text-white text-lg mb-3">Distribución mayorista</h3>
 <p class="text-white/80 text-sm leading-relaxed">Amplio catálogo de medicamentos de patente, genéricos y material de curación.</p>
 </div>
 
 <!-- Card 2 -->
 <div class="bg-primary p-8 rounded-3xl border border-primary/20 transition-all group flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="200">
 <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-white transition-all text-white group-hover:text-primary">
 <span class="material-symbols-outlined text-3xl">local_shipping</span>
 </div>
 <h3 class="font-black text-white text-lg mb-3">Logística eficiente</h3>
 <p class="text-white/80 text-sm leading-relaxed">Procesos optimizados para entregas rápidas y seguras.</p>
 </div>
 
 <!-- Card 4 (Now Card 3) -->
 <div class="bg-primary p-8 rounded-3xl border border-primary/20 transition-all group flex flex-col items-center text-center" data-aos="fade-up" data-aos-delay="300">
 <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-white transition-all text-white group-hover:text-primary">
 <span class="material-symbols-outlined text-3xl">sentiment_satisfied</span>
 </div>
 <h3 class="font-black text-white text-lg mb-3">Atención personalizada</h3>
 <p class="text-white/80 text-sm leading-relaxed">Un equipo experto para acompañarte siempre que lo necesites.</p>
 </div>
 </div>
 </div>
</section>

<!-- Banner Soluciones -->
<section class="pt-[69px] pb-[136px] bg-white">
 <div class="max-w-[1369px] mx-auto px-8">
 <div class="bg-tertiary rounded-[48px] py-24 px-10 md:px-20 relative overflow-hidden flex items-center justify-center text-center" data-aos="fade-up">
 <!-- Background Image Overlay -->
 <div class="absolute inset-0 pointer-events-none">
 <img src="../img/15.webp" alt="" class="w-full h-full object-cover opacity-60 mix-blend-overlay">
 <div class="absolute inset-0 bg-tertiary/70"></div>
 </div>

 <!-- Content -->
 <div class="relative z-10 max-w-4xl mx-auto">
 <h2 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight tracking-tight">
 Tu aliado en distribución farmacéutica
 </h2>
 <p class="text-white text-lg mb-10 max-w-xl mx-auto font-medium leading-relaxed">
 Explora nuestro catálogo completo de medicamentos, material de curación y soluciones especializadas con entrega inmediata en todo el país.
 </p>
 <div class="flex justify-center">
 <a href="<?= $base ?? '' ?>catalogo/catalogo.php" class="inline-flex items-center justify-center px-10 py-4 bg-primary text-white font-semibold rounded-2xl hover:bg-primary/90 hover:-translate-y-1 active:scale-95 transition-all group text-base">
 Explorar catálogo <span class="material-symbols-outlined ml-2 group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>
 </div>
 </div>
 </div>
 </div>
</section>

<?php require_once '../includes/footer.php'; ?>

