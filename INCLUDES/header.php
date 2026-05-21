<?php
if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $titulo ?? 'MMPharma | Distribuidora farmacéutica' ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="<?= $base ?? '' ?>logos/MMPharma-Isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<!-- SweetAlert2 para alertas animadas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
 "background": "#ffffff",
 "surface": "#f8fafc",
 "surface-container-low": "#f1f5f9",
 "surface-container": "#e2e8f0",
 "surface-container-high": "#cbd5e1",
 },
 fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] },
 borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
 },
 },
 }
</script>
<style>
 body { font-family: 'Inter', sans-serif; }
 .material-symbols-outlined {
 font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
 vertical-align: middle;
 }
 .clinical- { box-: 0 10px 40px -10px rgba(0, 62, 121, 0.1); }

 /* Mobile Menu Transition */
 #mobile-menu {
 transition: transform 0.3s ease-in-out;
 transform: translateY(-100%);
 }
 #mobile-menu.active {
 transform: translateY(0);
 }
 .profile-menu-anim {
 opacity: 0;
 transform: translateY(8px) scale(0.95);
 pointer-events: none;
 transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
 display: block !important;
 }
 .profile-menu-anim.show {
 opacity: 1;
 transform: translateY(0) scale(1);
 pointer-events: auto;
 }
 .parallax-bg {
 transform: scale(1.25);
 will-change: transform;
 }

 .parallax-bg {
 transform: scale(1.25);
 will-change: transform;
 }
</style>
</head>
<style>
 body { 
 opacity: 0; 
 transition: opacity 0.8s ease-in-out; 
 }
 body.ready { 
 opacity: 1; 
 }
</style>
<script>
 window.addEventListener('DOMContentLoaded', () => {
 document.body.classList.add('ready');
 });
</script>
<body class="bg-white font-body text-slate-800 antialiased">

  <!-- Top Bar with Contact Info -->
  <div class="bg-primary text-white text-xs py-2 shadow-inner">
    <div class="max-w-[1369px] mx-auto px-4 md:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
      <div class="flex items-center gap-4 flex-wrap justify-center sm:justify-start">
        <a href="tel:3322207506" class="flex items-center gap-1.5 hover:text-tertiary-light transition-colors font-medium">
          <span class="material-symbols-outlined text-[16px]">call</span>
          <span>33 2220 7506</span>
        </a>
        <span class="hidden sm:inline text-white/30 text-xs">|</span>
        <a href="tel:3343480581" class="flex items-center gap-1.5 hover:text-tertiary-light transition-colors font-medium">
          <span class="material-symbols-outlined text-[16px]">call</span>
          <span>33 4348 0581</span>
        </a>
        <span class="hidden sm:inline text-white/30 text-xs">|</span>
        <a href="tel:3343480582" class="flex items-center gap-1.5 hover:text-tertiary-light transition-colors font-medium">
          <span class="material-symbols-outlined text-[16px]">call</span>
          <span>33 4348 0582</span>
        </a>
      </div>
      <div class="flex items-center gap-4">
        <a href="mailto:atencionclientes@mmpharma.mx" class="flex items-center gap-1.5 hover:text-tertiary-light transition-colors font-medium">
          <span class="material-symbols-outlined text-[16px]">mail</span>
          <span>atencionclientes@mmpharma.mx</span>
        </a>
      </div>
    </div>
  </div>

<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm transition-all duration-300">
<nav class="flex justify-between items-center w-full px-4 md:px-8 py-3 md:py-4 max-w-[1369px] mx-auto font-['Inter'] font-medium text-base antialiased gap-2">
 <div class="flex-1 flex items-center">
 <a href="<?= $base ?? '' ?>INDEX/index.php">
 <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-6 md:h-8 w-auto">
 </a>
 </div>

 <div class="hidden lg:flex gap-8 xl:gap-12 text-sm xl:text-base font-semibold">
 <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>INDEX/index.php">Inicio</a>
 <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php">Nosotros</a>
 <a class="<?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>CATALOGO/catalogo.php">Productos</a>
 <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>CONTACTO/contacto.php">Contacto</a>
 </div>

 <div class="flex flex-1 items-center justify-end gap-2 md:gap-4">
 <?php if (isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true): ?>
 
 <!-- Lógica de colores por rol -->
 <?php
 $tipo_cliente = $_SESSION['cliente_tipo'] ?? '';
 $role_ring = 'ring-slate-300';
 $role_bg = 'bg-slate-500';
 
 $role_text = 'text-primary';
 $role_hover_text = 'hover:text-primary';
 $role_hover_bg = 'hover:bg-primary/10';
 $role_icon_bg = 'group-hover/item:bg-primary';
 
 if ($tipo_cliente === 'EMPRESA') {
 $role_ring = 'ring-secondary';
 $role_bg = 'bg-secondary';
 $role_text = 'text-secondary';
 $role_hover_text = 'hover:text-secondary';
 $role_hover_bg = 'hover:bg-secondary/10';
 $role_icon_bg = 'group-hover/item:bg-secondary';
 } elseif ($tipo_cliente === 'DISTRIBUIDORA') {
 $role_ring = 'ring-tertiary';
 $role_bg = 'bg-tertiary';
 $role_text = 'text-tertiary';
 $role_hover_text = 'hover:text-tertiary';
 $role_hover_bg = 'hover:bg-tertiary/10';
 $role_icon_bg = 'group-hover/item:bg-tertiary';
 } elseif ($tipo_cliente === 'FARMACIA') {
 $role_ring = 'ring-primary';
 $role_bg = 'bg-primary';
 $role_text = 'text-primary';
 $role_hover_text = 'hover:text-primary';
 $role_hover_bg = 'hover:bg-primary/10';
 $role_icon_bg = 'group-hover/item:bg-primary';
 }
 ?>

 <?php 
 $foto_cliente_raw = $_SESSION['cliente_foto'] ?? ''; 
 $foto_cliente = '';
 if ($foto_cliente_raw && $foto_cliente_raw !== 'PENDIENTE') {
 $foto_cliente = $base . 'DASHBOARD_CLIENTE/' . ltrim(str_replace('../', '', $foto_cliente_raw), '/');
 }
 ?>
 <!-- Botón Ingresar al Portal -->
 <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Dashboard.php" class="hidden lg:flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-full hover:bg-primary/95 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 ml-4 group shadow-sm">
 <span class="tracking-wide">Ingresar al Portal</span>
 <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>

 <!-- Perfil de cliente con Dropdown -->
 <div class="relative" id="profile-dropdown">
 <button onclick="toggleProfileDropdown()" class="w-10 h-10 <?= $foto_cliente ? 'bg-white' : $role_bg ?> text-white rounded-full flex items-center justify-center group hover:scale-105 transition-all ring-2 ring-offset-2 ring-offset-background <?= $role_ring ?> focus:outline-none ml-2 overflow-hidden">
 <?php if ($foto_cliente && $foto_cliente !== 'PENDIENTE'): ?>
 <img src="<?= htmlspecialchars($foto_cliente) ?>" alt="Perfil" class="w-full h-full object-cover">
 <?php else: ?>
 <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1">person</span>
 <?php endif; ?>
 </button>

 <!-- Dropdown Menu -->
 <div id="profile-menu" class="profile-menu-anim absolute right-0 mt-4 w-72 bg-white rounded-2xl overflow-hidden z-[100] origin-top-right border border-slate-100">
 <div class="px-6 py-5 bg-slate-50">
 <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Cuenta activa</p>
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center <?= $role_text ?> overflow-hidden flex-shrink-0 ">
 <?php if ($foto_cliente && $foto_cliente !== 'PENDIENTE'): ?>
 <img src="<?= htmlspecialchars($foto_cliente) ?>" alt="Perfil" class="w-full h-full object-cover">
 <?php else: ?>
 <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">business</span>
 <?php endif; ?>
 </div>
 <div class="flex-1 overflow-hidden flex flex-col justify-center gap-1">
 <p class="text-sm font-black <?= $role_text ?> leading-tight break-words whitespace-normal"><?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente') ?></p>
 <p class="text-[9px] font-bold text-slate-400 uppercase leading-tight"><?= htmlspecialchars($_SESSION['cliente_tipo'] ?? 'FARMACIA') ?></p>
 </div>
 </div>
 </div>
 
 <div class="p-2 space-y-1">
 <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Dashboard.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-600 <?= $role_hover_text ?> <?= $role_hover_bg ?> rounded-xl transition-all group/item">
 <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center <?= $role_text ?> group-hover/item:text-white transition-colors <?= $role_icon_bg ?>">
 <span class="material-symbols-outlined text-lg">dashboard</span>
 </div>
 <span>Panel de control</span>
 </a>
 <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Perfil.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-600 <?= $role_hover_text ?> <?= $role_hover_bg ?> rounded-xl transition-all group/item">
 <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center <?= $role_text ?> group-hover/item:text-white transition-colors <?= $role_icon_bg ?>">
 <span class="material-symbols-outlined text-lg">manage_accounts</span>
 </div>
 <span>Mi perfil</span>
 </a>
 </div>
 
 <div class="p-2 bg-slate-50 border-t border-slate-100">
 <a href="<?= $base ?? '' ?>LOGIN/logout.php" class="flex items-center gap-3 p-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all group/item">
 <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-500 group-hover/item:bg-red-500 group-hover/item:text-white transition-colors">
 <span class="material-symbols-outlined text-lg">logout</span>
 </div>
 <span>Cerrar sesión</span>
 </a>
 </div>
 </div>
 </div>

 <!-- Icono de carrito (Solo para clientes logueados) -->
 <button id="cart-icon-btn" onclick="toggleCartDrawer()" class="relative w-10 h-10 flex items-center justify-center text-slate-800 hover:bg-slate-50 rounded-xl transition-all ml-1" aria-label="Carrito de compras">
 <span class="material-symbols-outlined text-2xl">shopping_cart</span>
 <span id="cart-badge" class="hidden absolute -top-1 -right-1 bg-tertiary text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full">0</span>
 </button>

 <script>
 function toggleProfileDropdown() {
 const menu = document.getElementById('profile-menu');
 menu.classList.toggle('show');
 }

 document.addEventListener('click', function(event) {
 const dropdown = document.getElementById('profile-dropdown');
 const menu = document.getElementById('profile-menu');
 if (dropdown && !dropdown.contains(event.target)) {
 menu.classList.remove('show');
 }
 });
 </script>

 <?php elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
 <?php 
 $foto_admin_raw = $_SESSION['admin_foto'] ?? ''; 
 $foto_admin = '';
 if ($foto_admin_raw && $foto_admin_raw !== 'PENDIENTE') {
 $foto_admin = $base . 'DASHBOARD_ADMIN/' . ltrim(str_replace('../', '', $foto_admin_raw), '/');
 }
 ?>
 <!-- Botón Ingresar al Portal para Admin -->
 <a href="<?= $base ?? '' ?>DASHBOARD_ADMIN/dashboard/dashboard.php" class="hidden lg:flex items-center justify-center gap-2 px-6 py-2.5 bg-[#4ade80] text-white font-bold rounded-full hover: hover:-translate-y-0.5 transition-all duration-300 ml-4 group relative overflow-hidden">
 <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out"></div>
 <span class="relative z-10 tracking-wide">Ingresar al Portal</span>
 <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>

 <!-- Perfil de Admin con Dropdown -->
 <div class="relative" id="profile-dropdown-admin">
 <button onclick="toggleProfileDropdownAdmin()" class="w-10 h-10 <?= $foto_admin ? 'bg-white' : 'bg-[#005132]' ?> text-white rounded-full flex items-center justify-center group hover:scale-105 transition-all ring-2 ring-offset-2 ring-offset-background ring-[#4ade80] focus:outline-none ml-2 overflow-hidden">
 <?php if ($foto_admin && $foto_admin !== 'PENDIENTE'): ?>
 <img src="<?= htmlspecialchars($foto_admin) ?>" alt="Perfil" class="w-full h-full object-cover">
 <?php else: ?>
 <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1">shield_person</span>
 <?php endif; ?>
 </button>

 <!-- Dropdown Menu -->
 <div id="profile-menu-admin" class="profile-menu-anim absolute right-0 mt-4 w-72 bg-white rounded-2xl overflow-hidden z-[100] origin-top-right border border-slate-100">
 <div class="px-6 py-5 bg-slate-50">
 <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Cuenta activa</p>
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#4ade80] overflow-hidden flex-shrink-0">
 <?php if ($foto_admin && $foto_admin !== 'PENDIENTE'): ?>
 <img src="<?= htmlspecialchars($foto_admin) ?>" alt="Perfil" class="w-full h-full object-cover">
 <?php else: ?>
 <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">admin_panel_settings</span>
 <?php endif; ?>
 </div>
 <div class="flex-1 overflow-hidden flex flex-col justify-center gap-1">
 <p class="text-sm font-black text-slate-800 leading-tight break-words whitespace-normal"><?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?></p>
 <p class="text-[9px] font-bold text-slate-400 uppercase leading-tight">ADMINISTRADOR</p>
 </div>
 </div>
 </div>
 
 <div class="p-2 space-y-1">
 <a href="<?= $base ?? '' ?>DASHBOARD_ADMIN/dashboard/dashboard.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-600 hover:text-[#4ade80] hover:bg-green-50 rounded-xl transition-all group/item">
 <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center group-hover/item:bg-[#4ade80] group-hover/item:text-white transition-colors text-slate-500">
 <span class="material-symbols-outlined text-lg">dashboard</span>
 </div>
 <span>Panel de control</span>
 </a>
 </div>
 
 <div class="p-2 bg-slate-50 border-t border-slate-100">
 <a href="<?= $base ?? '' ?>LOGIN/logout.php" class="flex items-center gap-3 p-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all group/item">
 <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center group-hover/item:bg-red-500 text-red-500 group-hover/item:text-white transition-colors">
 <span class="material-symbols-outlined text-lg">logout</span>
 </div>
 <span>Cerrar sesión</span>
 </a>
 </div>
 </div>
 </div>

 <script>
 function toggleProfileDropdownAdmin() {
 const menu = document.getElementById('profile-menu-admin');
 menu.classList.toggle('show');
 }

 document.addEventListener('click', function(event) {
 const dropdown = document.getElementById('profile-dropdown-admin');
 const menu = document.getElementById('profile-menu-admin');
 if (dropdown && !dropdown.contains(event.target)) {
 menu.classList.remove('show');
 }
 });
 </script>

 <?php else: ?>
 <div class="hidden lg:flex items-center gap-3">
 <a href="<?= $base ?? '' ?>LOGIN/login.php"
 class="px-5 py-2.5 border border-slate-200 text-slate-800 font-bold text-sm rounded-full hover:bg-slate-50 hover:-translate-y-0.5 active:scale-95 transition-all duration-300">
 Iniciar sesi&oacute;n
 </a>
 <a href="<?= $base ?? '' ?>INDEX/SELECCI&Oacute;N_REGISTRO/selecci&oacute;n_registro.php"
 class="px-5 py-2.5 bg-tertiary text-white font-bold text-sm rounded-full hover:bg-tertiary/90 hover:-translate-y-0.5 active:scale-95 hover: transition-all duration-300">
 Crear cuenta
 </a>
 </div>
 <?php endif; ?>

 <!-- Mobile Toggle -->
 <button id="menu-toggle" class="lg:hidden text-slate-800 hover:bg-slate-50 rounded-xl p-1 md:p-2 ml-1">
    <span class="material-symbols-outlined text-3xl">menu</span>
  </button>
  </div>
</nav>
</header>

<!-- Mobile Menu — fuera del header para posicionamiento correcto -->
<div id="mobile-menu" class="fixed inset-0 z-[100] bg-white flex flex-col">

  <!-- Top bar -->
  <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100 flex-shrink-0">
    <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
    <button id="menu-close" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary rounded-full hover:bg-slate-100 transition-all">
      <span class="material-symbols-outlined text-3xl">close</span>
    </button>
  </div>

  <!-- Nav links — centrados verticalmente -->
  <div class="flex-1 flex flex-col items-center justify-center gap-8 px-8 text-center">
    <a href="<?= $base ?? '' ?>INDEX/index.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Inicio</a>
    <a href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Nosotros</a>
    <a href="<?= $base ?? '' ?>CATALOGO/catalogo.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Productos</a>
    <a href="<?= $base ?? '' ?>CONTACTO/contacto.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Contacto</a>
  </div>

  <!-- Bottom session links -->
  <div class="flex-shrink-0 flex flex-col items-center gap-4 pb-10 pt-6 border-t border-slate-100">
    <?php if (!isset($_SESSION['cliente_logged_in']) && !isset($_SESSION['admin_logged_in'])): ?>
    <a href="<?= $base ?? '' ?>LOGIN/login.php" class="text-base font-bold text-primary">Iniciar sesión</a>
    <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php" class="px-6 py-2.5 bg-primary text-white font-bold text-sm rounded-full">Solicitar acceso</a>
    <?php else: ?>
    <?php if (isset($_SESSION['cliente_logged_in'])): ?>
    <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Dashboard.php" class="text-base font-bold text-primary">Panel de control</a>
    <?php else: ?>
    <a href="<?= $base ?? '' ?>DASHBOARD_ADMIN/dashboard/dashboard.php" class="text-base font-bold text-[#005132]">Panel admin</a>
    <?php endif; ?>
    <a href="<?= $base ?? '' ?>LOGIN/logout.php" class="text-base font-bold text-red-500">Cerrar sesión</a>
    <?php endif; ?>
  </div>

</div>


<script>
// Mobile Menu Logic
const menuToggle = document.getElementById('menu-toggle');
const menuClose = document.getElementById('menu-close');
const mobileMenu = document.getElementById('mobile-menu');

if (menuToggle && mobileMenu) {
 menuToggle.addEventListener('click', () => {
 mobileMenu.classList.add('active');
 document.body.style.overflow = 'hidden';
 });
}

if (menuClose && mobileMenu) {
 menuClose.addEventListener('click', () => {
 mobileMenu.classList.remove('active');
 document.body.style.overflow = '';
 });
}
</script>

<!-- ═══ CART DRAWER ═══ -->
<div id="cart-overlay" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[60] opacity-0 pointer-events-none transition-all duration-300" onclick="toggleCartDrawer()"></div>
<div id="cart-drawer" class="fixed top-0 right-0 h-full w-full sm:w-[420px] bg-primary z-[70] translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] flex flex-col border-l border-white/10 ">
 
 <!-- Header -->
 <div class="px-8 py-6 flex items-center justify-between border-b border-white/10">
 <div class="flex items-center gap-3 text-white">
 <span class="material-symbols-outlined text-[28px] animate-bounce-slow">shopping_bag</span>
 <h2 class="text-xl font-black tracking-tight text-white">Mi carrito</h2>
 </div>
 <button onclick="toggleCartDrawer()" class="w-10 h-10 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 rounded-full transition-all">
 <span class="material-symbols-outlined">close</span>
 </button>
 </div>

 <!-- Items -->
 <div id="cart-items-container" class="flex-1 overflow-y-auto p-6 space-y-4">
 <!-- El contenido se genera con JS -->
 </div>

 <!-- Footer -->
 <div class="p-6 border-t border-white/10">
 <div class="flex justify-between items-end mb-2">
 <p class="text-xs font-black text-white/50 uppercase tracking-widest">Subtotal</p>
 <p id="cart-subtotal" class="text-lg font-black text-white">$0.00</p>
 </div>
 <div class="flex justify-between items-end mb-2">
 <p class="text-xs font-black text-white/50 uppercase tracking-widest">Envío</p>
 <p id="cart-envio" class="text-sm font-bold text-tertiary">Selecciona dirección</p>
 </div>
 <div class="flex justify-between items-end mb-6 pt-2 border-t border-white/10">
 <p class="text-sm font-black text-white uppercase tracking-widest">Total</p>
 <p id="cart-total" class="text-3xl font-black text-white">$0.00</p>
 </div>
 
 <?php if(isset($_SESSION['cliente_id'])): 
 try {
 if(!isset($pdo)) {
 require_once __DIR__ . '/db.php';
 $pdo = getDB();
 }
 $stmt_dir = $pdo->prepare("SELECT id, alias FROM clientes_direcciones WHERE cliente_id = ? ORDER BY predeterminada DESC, alias ASC");
 $stmt_dir->execute([$_SESSION['cliente_id']]);
 $direcciones_cart = $stmt_dir->fetchAll(PDO::FETCH_ASSOC);
 } catch(Exception $e) {
 $direcciones_cart = [];
 }
 ?>
 <div class="mb-4">
 <label class="block text-xs font-bold text-white/50 uppercase tracking-widest mb-2">Dirección de envío</label>
 <?php if(!empty($direcciones_cart)): ?>
 <div class="relative">
 <select id="cart-direccion" class="w-full px-4 py-3 bg-white rounded-xl focus:ring-4 focus:ring-white/20 outline-none appearance-none text-sm font-bold text-primary">
 <?php foreach($direcciones_cart as $d): ?>
 <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['alias']) ?></option>
 <?php endforeach; ?>
 </select>
 <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-primary pointer-events-none">expand_more</span>
 </div>
 <?php else: ?>
 <p class="text-xs text-white bg-white/10 p-3 rounded-xl border border-white/5">No tienes direcciones registradas. <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Direcciones.php" class="font-bold underline hover:text-tertiary">Agregar una</a>.</p>
 <?php endif; ?>
 </div>
 <?php endif; ?>

 <p class="text-xs text-white/50 mb-6 leading-relaxed">
 *Los precios mostrados son de lista. Si eres distribuidor o empresa, el precio final se ajustará al generar la cotización formal.
 </p>
 <div class="flex flex-col gap-3">
 <button onclick="confirmarPedido()" id="btn-confirmar-pedido" class="w-full h-14 bg-tertiary text-white font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-sm flex items-center justify-center gap-2 relative overflow-hidden group">
 <span class="relative z-10 text-base tracking-wide">Confirmar pedido</span>
 <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:translate-x-1 transition-transform">send</span>
 </button>
 <button onclick="generarCotizacion()" class="w-full h-14 bg-white text-primary font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-sm flex items-center justify-center gap-2 relative overflow-hidden group">
 <span class="relative z-10 tracking-wide">Descargar cotización</span>
 <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:-translate-y-1 transition-transform">picture_as_pdf</span>
 </button>
 </div>
 </div>
</div>

<script>
let carrito = [];
try {
 const parsed = JSON.parse(localStorage.getItem('mm_carrito'));
 carrito = Array.isArray(parsed) ? parsed : [];
} catch (e) {
 carrito = [];
 localStorage.removeItem('mm_carrito');
}

function guardarCarrito() {
 localStorage.setItem('mm_carrito', JSON.stringify(carrito));
 actualizarBadge();
}

function actualizarBadge() {
 const badge = document.getElementById('cart-badge');
 const icon = document.getElementById('cart-icon-btn'); // Necesitamos darle un id al boton
 if (!badge) return;
 const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
 
 if (totalItems > 0) {
 badge.textContent = totalItems;
 badge.classList.remove('hidden');
 // Animacion
 badge.classList.remove('animate-bounce');
 void badge.offsetWidth; // trigger reflow
 badge.classList.add('animate-bounce');
 setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
 
 if(icon) {
 icon.classList.add('scale-110', 'text-primary');
 setTimeout(() => icon.classList.remove('scale-110', 'text-primary'), 300);
 }
 } else {
 badge.classList.add('hidden');
 }
}

function formatCurrency(amount) {
 return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function renderCartItems() {
 const container = document.getElementById('cart-items-container');
 const subtotalEl = document.getElementById('cart-subtotal');
 if (!container) return;
 
 if (carrito.length === 0) {
 container.innerHTML = `
 <div class="h-full flex flex-col items-center justify-center text-center px-6 animate-in fade-in zoom-in duration-500">
 <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mb-6 border border-white/10">
 <span class="material-symbols-outlined text-[64px] text-white/50">shopping_bag</span>
 </div>
 <p class="text-lg font-black text-white mb-2">Tu carrito está vacío</p>
 <p class="text-sm font-medium text-white/50">¡Explora nuestro catálogo y descubre los mejores productos!</p>
 </div>
 `;
 subtotalEl.textContent = '$0.00';
 return;
 }

 let html = '';
 let subtotal = 0;

 carrito.forEach((item, index) => {
 const totalLinea = item.precio * item.cantidad;
 subtotal += totalLinea;
 
 let imagenHtml = '';
 if (item.imagen && item.imagen !== 'PENDIENTE' && item.imagen !== '') {
 imagenHtml = `<img src="<?= $base ?? '' ?>CATALOGO/imagenes/productos/${item.imagen}" class="w-full h-full object-contain mix-blend-multiply p-2 transition-transform duration-300 group-hover:scale-110">`;
 } else {
 imagenHtml = `<span class="material-symbols-outlined text-slate-300 text-3xl transition-transform duration-300 group-hover:scale-110">medication</span>`;
 }

 html += `
 <div class="flex gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl relative group hover:-translate-y-1 transition-all duration-300 animate-in slide-in-from-right-4 fade-in duration-300" style="animation-delay: ${index * 50}ms; animation-fill-mode: both;">
 <div class="w-20 h-20 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden p-1">
 ${imagenHtml}
 </div>
 <div class="flex-1 min-w-0 py-1">
 <h4 class="text-sm font-bold text-white leading-tight mb-1 truncate pr-8 transition-colors group-hover:text-tertiary" title="${item.nombre}">${item.nombre}</h4>
 <p class="text-base font-black text-tertiary mb-3">${formatCurrency(item.precio)}</p>
 
 <div class="flex items-center bg-white/10 rounded-xl w-fit p-1 border border-white/5">
 <button onclick="cambiarCantidad(${index}, -1)" class="w-7 h-7 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all"><span class="material-symbols-outlined text-[18px]">remove</span></button>
 <span class="w-8 text-center text-sm font-black text-white">${item.cantidad}</span>
 <button onclick="cambiarCantidad(${index}, 1)" class="w-7 h-7 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all"><span class="material-symbols-outlined text-[18px]">add</span></button>
 </div>
 </div>
 <button onclick="eliminarDelCarrito(${index})" class="absolute top-3 right-3 text-white/30 hover:text-white hover:bg-red-500/80 transition-all w-8 h-8 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0">
 <span class="material-symbols-outlined text-[20px]">delete</span>
 </button>
 </div>
 `;
 });

 container.innerHTML = html;
 subtotalEl.textContent = formatCurrency(subtotal);
 calcularEnvioDynamic(subtotal);
}

function calcularEnvioDynamic(subtotal) {
  const dirSelect = document.getElementById('cart-direccion');
  const envioEl = document.getElementById('cart-envio');
  const totalEl = document.getElementById('cart-total');

  if (!dirSelect || !envioEl || !totalEl) {
      if(totalEl) totalEl.textContent = formatCurrency(subtotal);
      return;
  }

  const direccion_id = dirSelect.value;
  if (!direccion_id) {
     envioEl.textContent = 'Selecciona dirección';
     totalEl.textContent = formatCurrency(subtotal);
     return;
  }

  fetch('<?= $base ?? '' ?>CATALOGO/api_calcular_envio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ direccion_id: direccion_id, subtotal: subtotal })
  })
  .then(res => res.json())
  .then(data => {
      if (data.success) {
          const calc = data.calculo;
          if (calc.costo > 0) {
              envioEl.textContent = formatCurrency(calc.costo);
          } else {
              envioEl.textContent = calc.mensaje;
          }
          const total = subtotal + calc.costo;
          totalEl.textContent = formatCurrency(total);
      } else {
          envioEl.textContent = 'Error al calcular';
          totalEl.textContent = formatCurrency(subtotal);
      }
  })
  .catch(err => {
      envioEl.textContent = 'Error';
      totalEl.textContent = formatCurrency(subtotal);
  });
}

function agregarAlCarrito(id, nombre, precio, imagen) {
 console.log("agregarAlCarrito llamado con:", {id, nombre, precio, imagen});
 const itemIndex = carrito.findIndex(item => item.id == id);
 
 if (itemIndex > -1) {
 carrito[itemIndex].cantidad += 1;
 } else {
 carrito.push({
 id: id,
 nombre: nombre,
 precio: parseFloat(precio),
 imagen: imagen,
 cantidad: 1
 });
 }
 
 guardarCarrito();
 renderCartItems();
 
 // Mostramos una alerta visual (toast) en su lugar para indicar éxito sin interrumpir
 Swal.fire({
 toast: true,
 position: 'bottom-end',
 icon: 'success',
 title: 'Producto añadido',
 showConfirmButton: false,
 timer: 1500,
 timerProgressBar: true
 });
}

function cambiarCantidad(index, delta) {
 if (carrito[index]) {
 carrito[index].cantidad += delta;
 if (carrito[index].cantidad <= 0) {
 eliminarDelCarrito(index);
 } else {
 guardarCarrito();
 renderCartItems();
 }
 }
}

function eliminarDelCarrito(index) {
 carrito.splice(index, 1);
 guardarCarrito();
 renderCartItems();
}

function toggleCartDrawer() {
 const drawer = document.getElementById('cart-drawer');
 const overlay = document.getElementById('cart-overlay');
 if (!drawer || !overlay) return;
 
 if (drawer.classList.contains('translate-x-full')) {
 // Abrir
 drawer.classList.remove('translate-x-full');
 overlay.classList.remove('opacity-0', 'pointer-events-none');
 renderCartItems(); // Renderizar al abrir para estar seguros
 } else {
 // Cerrar
 drawer.classList.add('translate-x-full');
 overlay.classList.add('opacity-0', 'pointer-events-none');
 }
}

// Inicializar el badge al cargar la página
document.addEventListener('DOMContentLoaded', () => {
 actualizarBadge();
 const dirSelect = document.getElementById('cart-direccion');
 if (dirSelect) {
     dirSelect.addEventListener('change', () => {
         const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
         calcularEnvioDynamic(subtotal);
     });
 }
});

function generarCotizacion() {
 if (carrito.length === 0) {
 Swal.fire('Carrito vacío', 'Añade productos antes de generar la cotización', 'warning');
 return;
 }

 const dirSelect = document.getElementById('cart-direccion');
 const direccion_id = dirSelect ? dirSelect.value : null;

 if (dirSelect && !direccion_id) {
    Swal.fire('Atención', 'Debes seleccionar una dirección para cotizar.', 'warning');
    return;
 }
 
 // Create a hidden form to submit the cart data as POST and trigger download
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = '<?= $base ?? '' ?>CATALOGO/generar_cotizacion_pdf.php';
 form.target = '_blank';
 
 const input = document.createElement('input');
 input.type = 'hidden';
 input.name = 'carrito_data';
 input.value = JSON.stringify(carrito);
 
 form.appendChild(input);

 if (typeof direccion_id !== 'undefined' && direccion_id) {
     const inputDir = document.createElement('input');
     inputDir.type = 'hidden';
     inputDir.name = 'direccion_id';
     inputDir.value = direccion_id;
     form.appendChild(inputDir);
 }
 document.body.appendChild(form);
 form.submit();
 document.body.removeChild(form);
}

function confirmarPedido() {
 if (carrito.length === 0) {
 Swal.fire('Carrito vacío', 'Añade productos antes de confirmar el pedido', 'warning');
 return;
 }
 
 const btn = document.getElementById('btn-confirmar-pedido');
 const originalText = btn.innerHTML;
 btn.innerHTML = '<div class="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div> Procesando...';
 btn.disabled = true;

 const dirSelect = document.getElementById('cart-direccion');
 const direccion_id = dirSelect ? dirSelect.value : null;

 if (dirSelect && !direccion_id) {
 Swal.fire('Atención', 'Debes seleccionar una dirección de envío o registrar una nueva.', 'warning');
 btn.innerHTML = originalText;
 btn.disabled = false;
 return;
 }

 fetch('<?= $base ?? '' ?>CATALOGO/procesar_pedido.php', {
 method: 'POST',
 headers: { 'Content-Type': 'application/json' },
 body: JSON.stringify({ carrito: carrito, direccion_id: direccion_id })
 })
 .then(res => res.json())
 .then(data => {
 if (data.success) {
 carrito = [];
 guardarCarrito();
 renderCartItems();
 toggleCartDrawer();
 
 let folioText = data.folio ? `Folio: ${data.folio}` : '';
 
 Swal.fire({
 html: `
 <div class="flex flex-col items-center justify-center pt-4 pb-2">
 <div class="w-20 h-20 mb-6 bg-green-50 rounded-full flex items-center justify-center relative">
 <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-50"></div>
 <span class="material-symbols-outlined text-green-500 text-4xl relative z-10">check_circle</span>
 </div>
 <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-2">¡Pedido Enviado!</h2>
 ${data.folio ? `<p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4">Folio: ${data.folio}</p>` : ''}
 <p class="text-sm text-slate-500 font-medium text-center">Tu pedido ha sido recibido y está en revisión.</p>
 </div>
 `,
 showConfirmButton: true,
 confirmButtonText: 'Entendido',
 buttonsStyling: false,
 customClass: {
 popup: 'rounded-[2rem] p-4 bg-surface',
 confirmButton: 'w-full py-3 px-6 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 hover:-translate-y-0.5 transition-all mt-2'
 },
 width: '28em',
 allowOutsideClick: false,
 backdrop: `rgba(15, 23, 42, 0.6)`
 }).then(() => {
 window.location.href = '<?= $base ?? '' ?>CATALOGO/catalogo.php';
 });
 } else {
 Swal.fire('Error', 'Hubo un error al procesar tu pedido: ' + (data.message || 'Error desconocido'), 'error');
 }
 })
 .catch(err => {
 console.error(err);
 Swal.fire('Error de conexión', 'No se pudo procesar el pedido. Verifica tu conexión a internet.', 'error');
 })
 .finally(() => {
 btn.innerHTML = originalText;
 btn.disabled = false;
 });
}

// --- Parallax Effect ---
function applyParallax() {
 const scrolled = window.pageYOffset;
 const parallaxElements = document.querySelectorAll('.parallax-bg');
 parallaxElements.forEach(el => {
 const speed = parseFloat(el.dataset.speed) || 0.2;
 el.style.transform = `scale(1.25) translateY(${scrolled * speed}px)`;
 });
}

window.addEventListener('DOMContentLoaded', () => {
 applyParallax(); 
 window.addEventListener('scroll', () => requestAnimationFrame(applyParallax));
});
</script>
