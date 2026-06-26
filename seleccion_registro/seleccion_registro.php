<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Registro MMPharma - Selección de Negocio</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="../logos/mmpharma-isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script id="tailwind-config">
 tailwind.config = {
 darkMode: "class",
 theme: {
 extend: {
 colors: {
 "primary": "#003e79",
 "secondary": "#1e60aa",
 "tertiary": "#2ca1b5",
 "primary-light": "#60a5fa",
 "secondary-light": "#93c5fd",
 "tertiary-light": "#67e8f9",
 "background": "#0a192f",
 "surface": "#112240",
 "surface-container-low": "#1a365d",
 "surface-container": "#2a4365",
 "surface-container-high": "#2c5282",
 },
 fontFamily: {
 "headline": ["Inter"],
 "body": ["Inter"],
 "label": ["Inter"]
 },
 borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
 },
 },
 }
</script>
<style>
 .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
 .glass-card { background: rgba(17, 34, 64, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
</style>
</head>
<body class="bg-background font-body text-slate-300 min-h-screen flex flex-col relative overflow-x-hidden">

<!-- Decorative Background -->
<div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
 <img src="../img/5.webp" class="w-full h-full object-cover object-top opacity-5 transform scale-125">
</div>

<!-- TopAppBar -->
<header class="relative z-50 flex justify-between items-center w-full px-4 sm:px-8 py-4 sm:py-6 bg-background/80 backdrop-blur-md border-b border-white/5">
 <a href="../index/index.php" class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-2xl transition-all group">
  <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
 </a>

  <a href="../index/index.php" class="absolute left-1/2 -translate-x-1/2">
  <img src="../logos/mmpharma-logotipo-horizontal-blanco.png" alt="MMPharma" class="h-6 sm:h-8 w-auto hover:scale-105 transition-transform duration-300">
  </a>

 <div class="flex items-center">
  <button onclick="document.getElementById('popupAyuda').classList.remove('hidden')"
  class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-2xl transition-all group">
   <span class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">help</span>
  </button>
 </div>
</header>

<!-- Popup Ayuda -->
<div id="popupAyuda" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-background/95">
 <div class="bg-background rounded-3xl p-8 max-w-md w-full mx-4 border border-white/20" data-aos="zoom-in" data-aos-duration="300">
 <div class="flex justify-between items-center mb-8">
 <h3 class="text-xl font-black text-white">¿Cómo elegir tu tipo de cuenta?</h3>
 <button onclick="document.getElementById('popupAyuda').classList.add('hidden')" 
 class="w-8 h-8 flex items-center justify-center rounded-lg text-white hover:bg-white/10 transition-colors">
 <span class="material-symbols-outlined">close</span>
 </button>
 </div>
 <div class="space-y-4">
 <div class="p-5 bg-surface rounded-2xl border border-white/5">
 <p class="font-black text-white mb-1 text-lg flex items-center gap-2"><span class="material-symbols-outlined text-white">storefront</span> Farmacia</p>
 <p class="text-sm text-white">Venta directa al público. Farmacias independientes o sucursales.</p>
 </div>
 <div class="p-5 bg-primary rounded-2xl border border-white/5">
 <p class="font-black text-white mb-1 text-lg flex items-center gap-2"><span class="material-symbols-outlined text-white">local_shipping</span> Distribuidora</p>
 <p class="text-sm text-white">Mayoristas con volumen alto y logística propia.</p>
 </div>
 <div class="p-5 bg-tertiary rounded-2xl">
 <p class="font-black text-white mb-1 text-lg flex items-center gap-2"><span class="material-symbols-outlined text-white">domain</span> Empresa</p>
 <p class="text-sm text-white">Clínicas, hospitales o corporativos que compran para uso interno.</p>
 </div>
 </div>
 <p class="text-xs text-white mt-8 text-center font-medium">¿Sigues con dudas? Escríbenos a <a href="mailto:ventas@mmpharma.com" class="text-tertiary hover:text-white transition-colors font-bold">ventas@mmpharma.com</a></p>
 </div>
</div>

<!-- Main Content Canvas -->
<main class="relative z-10 flex-grow flex flex-col items-center justify-center px-6 pt-16 pb-24 max-w-7xl mx-auto w-full">
<!-- Hero Header -->
<div class="text-center mb-[36px] max-w-3xl" data-aos="fade-up">
 <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
 Solicitar acceso al portal
 </h1>
 <p class="text-lg text-slate-300 leading-relaxed font-medium">
 Selecciona el tipo de negocio para el cual deseas solicitar una cuenta. Prepararemos tu entorno clínico personalizado.
 </p>
</div>

<!-- Selection Grid: Asymmetric Bento Influence -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full relative">
<!-- Line Connector Background (Desktop) -->
<div class="hidden md:block absolute top-1/2 left-0 w-full h-px border-t-2 border-dashed border-white/5 z-0 -translate-y-1/2"></div>

<!-- TARJETA 1: FARMACIA -->
<div class="relative z-10 flex flex-col bg-surface p-8 rounded-3xl" data-aos="fade-up" data-aos-delay="100">
 <div class="mb-6 w-16 h-16 rounded-2xl bg-primary/20 flex items-center justify-center text-white">
  <span class="material-symbols-outlined text-3xl">storefront</span>
 </div>
 <h3 class="text-2xl font-black text-white mb-3">Farmacia</h3>
 <p class="text-white text-sm mb-8 leading-relaxed">
 Para farmacias independientes o sucursales que requieren medicamentos de patente y genéricos.
 </p>
 <div class="flex-grow">
  <h4 class="text-xs font-black uppercase tracking-widest text-white mb-4">Requisitos básicos:</h4>
  <ul class="space-y-3 mb-10">
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Licencia Sanitaria</span>
   </li>
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Comprobante de domicilio</span>
   </li>
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Constancia de Situación Fiscal</span>
   </li>
  </ul>
 </div>
   <button onclick="location.href='../registro_farmacia/registro_farmacia.php'" class="w-full h-14 bg-tertiary text-white font-bold rounded-2xl hover:bg-tertiary/90 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 group/btn">
  <span>Solicitar acceso</span>
  <span class="material-symbols-outlined text-xl">arrow_forward</span>
 </button>
</div>

<!-- TARJETA 2: DISTRIBUIDORA -->
<div class="relative z-10 flex flex-col bg-primary text-white p-8 rounded-3xl" data-aos="fade-up" data-aos-delay="200">
 <div class="mb-6 w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-white">
  <span class="material-symbols-outlined text-3xl">local_shipping</span>
 </div>
 <h3 class="text-2xl font-black mb-3 text-white">Distribuidora</h3>
 <p class="text-white text-sm mb-8 leading-relaxed">
 Para mayoristas y distribuidores regionales con volumen de compra masivo y logística propia.
 </p>
 <div class="flex-grow">
  <h4 class="text-xs font-black uppercase tracking-widest text-white mb-4">Requisitos especializados:</h4>
  <ul class="space-y-3 mb-10">
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Licencia (Venta al por mayor)</span>
   </li>
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Acta constitutiva</span>
   </li>
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Identificación oficial representante</span>
   </li>
  </ul>
 </div>
   <button onclick="location.href='../registro_distribuidora/registro_distribuidora.php'" class="w-full h-14 bg-tertiary text-white font-bold rounded-2xl hover:bg-tertiary/90 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 group/btn">
  <span>Solicitar acceso</span>
  <span class="material-symbols-outlined text-xl">arrow_forward</span>
 </button>
</div>

<!-- TARJETA 3: EMPRESA -->
<div class="relative z-10 flex flex-col bg-tertiary text-white p-8 rounded-3xl" data-aos="fade-up" data-aos-delay="300">
 <div class="mb-6 w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-white">
  <span class="material-symbols-outlined text-3xl">domain</span>
 </div>
 <h3 class="text-2xl font-black text-white mb-3">Empresa</h3>
 <p class="text-white text-sm mb-8 leading-relaxed">
 Para clínicas, hospitales o corporativos que requieren insumos médicos para uso interno.
 </p>
 <div class="flex-grow">
  <h4 class="text-xs font-black uppercase tracking-widest text-white mb-4">Requisitos corporativos:</h4>
  <ul class="space-y-3 mb-10">
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Constancia de Situación Fiscal</span>
   </li>
   <li class="flex items-center gap-3 text-sm text-white font-medium">
    <span class="material-symbols-outlined text-white text-lg">check_circle</span>
    <span>Formato de alta empresarial</span>
   </li>
  </ul>
 </div>
   <button onclick="location.href='../registro_empresa/registro_empresa.php'" class="w-full h-14 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 group/btn">
  <span>Solicitar acceso</span>
  <span class="material-symbols-outlined text-xl">arrow_forward</span>
 </button>
</div>

</div>

<!-- Support Section -->
<div class="mt-[36px] flex flex-col items-center gap-4 relative z-10">
 <div class="bg-surface/50 backdrop-blur-md border border-white/5 rounded-full px-6 py-3 flex items-center gap-3">
 <span class="material-symbols-outlined text-tertiary text-xl">mail</span>
 <p class="text-sm font-medium text-slate-300">¿Necesitas ayuda personalizada? Contacta a <a class="text-tertiary hover:text-white transition-colors font-bold" href="mailto:ventas@mmpharma.com">ventas@mmpharma.com</a></p>
 </div>
</div>
</main>



<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
 AOS.init({
 duration: 800,
 once: true,
 });
</script>
</body>
</html>
