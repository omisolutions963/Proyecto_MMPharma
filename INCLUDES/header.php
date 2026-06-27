<?php
if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
if (isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true) {
    if (isset($_SESSION['debe_cambiar_password']) && $_SESSION['debe_cambiar_password'] == 1) {
        $current_script = basename($_SERVER['PHP_SELF']);
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        if ($current_script !== 'cambiar_password_obligatorio.php' && $current_script !== 'logout.php') {
            $redirect_url = ($current_dir === 'login') ? 'cambiar_password_obligatorio.php' : '../login/cambiar_password_obligatorio.php';
            header("Location: " . $redirect_url);
            exit;
        }
    }
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
<link rel="icon" type="image/png" href="<?= $base ?? '' ?>logos/mmpharma-isotipo.png">
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

 .marquee-container {
 overflow: hidden;
 white-space: nowrap;
 position: relative;
 width: 100%;
 }
 .marquee-content {
 display: inline-flex;
 animation: marquee 30s linear infinite;
 align-items: center;
 gap: 4rem;
 }
 .marquee-content:hover {
 animation-play-state: paused;
 }
 @keyframes marquee {
 0% { transform: translateX(-50%); }
 100% { transform: translateX(0); }
 }
 .marquee-content img {
 height: 120px;
 max-width: 300px;
 object-fit: contain;
 filter: grayscale(100%) opacity(0.7);
 transition: all 0.3s ease;
 }
 .marquee-content img:hover {
 filter: grayscale(0%) opacity(1);
 transform: scale(1.1);
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
        <a href="https://wa.me/523322207506" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 hover:text-tertiary-light transition-colors font-medium">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="currentColor"/>
          </svg>
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
        <a href="mailto:atencionclientes@mmpharma.mx" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 hover:text-tertiary-light transition-colors font-medium">
          <span class="material-symbols-outlined text-[16px]">mail</span>
          <span>atencionclientes@mmpharma.mx</span>
        </a>
      </div>
    </div>
  </div>

<header class="sticky top-0 z-50 bg-white border-b border-slate-100 shadow-sm transition-all duration-300">
<nav class="flex justify-between items-center w-full px-4 md:px-8 py-3 md:py-4 max-w-[1369px] mx-auto font-['Inter'] font-medium text-base antialiased gap-2">
 <div class="flex-1 flex items-center">
 <a href="<?= $base ?? '' ?>index/index.php">
 <img src="<?= $base ?? '' ?>logos/mmpharma-logotipo-horizontal.png" alt="MMPharma" class="h-6 md:h-8 w-auto">
 </a>
 </div>

 <div class="hidden lg:flex gap-8 xl:gap-12 text-sm xl:text-base font-semibold">
 <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>index/index.php">Inicio</a>
 <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>quienes_somos/quienes_somos.php">Nosotros</a>
 <a class="<?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>catalogo/catalogo.php">Productos</a>
 <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200' ?>" href="<?= $base ?? '' ?>contacto/contacto.php">Contacto</a>
 </div>

  <?php
  $is_logged_in = (isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true) || (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true);
  $justify_class = $is_logged_in ? 'justify-end' : 'justify-between lg:justify-end';
  ?>
  <div class="flex flex-1 items-center <?= $justify_class ?> gap-2 md:gap-4">
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
 $foto_cliente = $base . 'dashboard_cliente/' . ltrim(str_replace('../', '', $foto_cliente_raw), '/');
 }
 ?>
 <!-- Botón Ingresar al Portal -->
 <a href="<?= $base ?? '' ?>dashboard_cliente/dashboard.php" class="hidden lg:flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-full hover:bg-primary/95 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 ml-4 group shadow-sm">
 <span class="tracking-wide">Ingresar al portal</span>
 <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>
 <div class="flex items-center gap-1.5 sm:gap-3">
 <!-- Perfil de cliente con Dropdown -->
 <div class="lg:relative" id="profile-dropdown">
 <button onclick="toggleProfileDropdown()" class="w-10 h-10 <?= $foto_cliente ? 'bg-white' : $role_bg ?> text-white rounded-full flex items-center justify-center group hover:scale-105 transition-all ring-2 ring-offset-2 ring-offset-background <?= $role_ring ?> focus:outline-none ml-2 overflow-hidden">
 <?php if ($foto_cliente && $foto_cliente !== 'PENDIENTE'): ?>
 <img src="<?= htmlspecialchars($foto_cliente) ?>" alt="Perfil" class="w-full h-full object-cover">
 <?php else: ?>
 <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1">person</span>
 <?php endif; ?>
 </button>

 <!-- Dropdown Menu -->
 <div id="profile-menu" class="profile-menu-anim absolute left-4 right-4 lg:left-auto lg:right-0 mt-4 lg:w-72 bg-white rounded-2xl overflow-hidden z-[100] origin-top-right border border-slate-100">
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
 <a href="<?= $base ?? '' ?>dashboard_cliente/dashboard.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-600 <?= $role_hover_text ?> <?= $role_hover_bg ?> rounded-xl transition-all group/item">
 <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center <?= $role_text ?> group-hover/item:text-white transition-colors <?= $role_icon_bg ?>">
 <span class="material-symbols-outlined text-lg">dashboard</span>
 </div>
 <span>Panel de control</span>
 </a>
 <a href="<?= $base ?? '' ?>dashboard_cliente/perfil.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-600 <?= $role_hover_text ?> <?= $role_hover_bg ?> rounded-xl transition-all group/item">
 <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center <?= $role_text ?> group-hover/item:text-white transition-colors <?= $role_icon_bg ?>">
 <span class="material-symbols-outlined text-lg">manage_accounts</span>
 </div>
 <span>Mi perfil</span>
 </a>
 </div>
 
 <div class="p-2 bg-slate-50 border-t border-slate-100">
 <a href="<?= $base ?? '' ?>login/logout.php" class="flex items-center gap-3 p-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all group/item">
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
 </div>

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
 $foto_admin = $base . 'dashboard_admin/' . ltrim(str_replace('../', '', $foto_admin_raw), '/');
 }
 ?>
 <!-- Botón Ingresar al Portal para Admin -->
 <a href="<?= $base ?? '' ?>dashboard_admin/dashboard/dashboard.php" class="hidden lg:flex items-center justify-center gap-2 px-5 py-2.5 bg-[#008151] text-white font-bold text-sm rounded-full hover:bg-[#008151]/95 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 ml-4 group shadow-sm">
 <span class="tracking-wide">Ingresar al portal</span>
 <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
 </a>

 <!-- Perfil de Admin con Dropdown -->
 <div class="lg:relative" id="profile-dropdown-admin">
 <button onclick="toggleProfileDropdownAdmin()" class="w-10 h-10 <?= $foto_admin ? 'bg-white' : 'bg-[#005132]' ?> text-white rounded-full flex items-center justify-center group hover:scale-105 transition-all ring-2 ring-offset-2 ring-offset-background ring-[#008151] focus:outline-none ml-2 overflow-hidden">
 <?php if ($foto_admin && $foto_admin !== 'PENDIENTE'): ?>
 <img src="<?= htmlspecialchars($foto_admin) ?>" alt="Perfil" class="w-full h-full object-cover">
 <?php else: ?>
 <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1">shield_person</span>
 <?php endif; ?>
 </button>

 <!-- Dropdown Menu -->
 <div id="profile-menu-admin" class="profile-menu-anim absolute left-4 right-4 lg:left-auto lg:right-0 mt-4 lg:w-72 bg-white rounded-2xl overflow-hidden z-[100] origin-top-right border border-slate-100">
 <div class="px-6 py-5 bg-slate-50">
 <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Cuenta activa</p>
 <div class="flex items-center gap-3">
 <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-[#008151] overflow-hidden flex-shrink-0">
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
 <a href="<?= $base ?? '' ?>dashboard_admin/dashboard/dashboard.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-600 hover:text-[#008151] hover:bg-emerald-50 rounded-xl transition-all group/item">
 <div class="w-9 h-9 bg-slate-100 rounded-lg flex items-center justify-center group-hover/item:bg-[#008151] group-hover/item:text-white transition-colors text-slate-500 font-medium">
 <span class="material-symbols-outlined text-lg">dashboard</span>
 </div>
 <span>Panel de control</span>
 </a>
 </div>
 
 <div class="p-2 bg-slate-50 border-t border-slate-100">
 <a href="<?= $base ?? '' ?>login/logout.php" class="flex items-center gap-3 p-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all group/item">
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
  <div class="flex items-center gap-1.5 sm:gap-3">
  <a href="<?= $base ?? '' ?>login/login.php"
  class="px-2.5 py-1.5 sm:px-5 sm:py-2.5 bg-primary text-white font-bold text-[11px] sm:text-sm rounded-full hover:bg-primary/90 hover:-translate-y-0.5 active:scale-95 transition-all duration-300 whitespace-nowrap">
  Iniciar sesi&oacute;n
  </a>
  <a href="<?= $base ?? '' ?>seleccion_registro/seleccion_registro.php"
  class="px-2.5 py-1.5 sm:px-5 sm:py-2.5 bg-tertiary text-white font-bold text-[11px] sm:text-sm rounded-full hover:bg-tertiary/90 hover:-translate-y-0.5 active:scale-95 hover: transition-all duration-300 whitespace-nowrap">
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
    <img src="<?= $base ?? '' ?>logos/mmpharma-logotipo-horizontal.png" alt="MMPharma" class="h-8 w-auto">
    <button id="menu-close" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary rounded-full hover:bg-slate-100 transition-all">
      <span class="material-symbols-outlined text-3xl">close</span>
    </button>
  </div>

  <!-- Nav links — centrados verticalmente con my-auto -->
  <div class="my-auto flex flex-col items-center gap-8 px-8 text-center">
    <a href="<?= $base ?? '' ?>index/index.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Inicio</a>
    <a href="<?= $base ?? '' ?>quienes_somos/quienes_somos.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Nosotros</a>
    <a href="<?= $base ?? '' ?>catalogo/catalogo.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Productos</a>
    <a href="<?= $base ?? '' ?>contacto/contacto.php" class="text-3xl font-semibold <?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary border-b-2 border-tertiary pb-1' : 'text-slate-500 hover:text-primary transition-all duration-200' ?>">Contacto</a>
  </div>

  <!-- Bottom session links -->
  <?php if (isset($_SESSION['cliente_logged_in']) || isset($_SESSION['admin_logged_in'])): ?>
  <div class="flex-shrink-0 flex flex-col items-center gap-4 pb-10 pt-6 border-t border-slate-100">
    <?php if (isset($_SESSION['cliente_logged_in'])): ?>
    <a href="<?= $base ?? '' ?>dashboard_cliente/dashboard.php" class="text-base font-bold text-primary">Panel de control</a>
    <?php else: ?>
    <a href="<?= $base ?? '' ?>dashboard_admin/dashboard/dashboard.php" class="text-base font-bold text-[#005132]">Panel admin</a>
    <?php endif; ?>
    <a href="<?= $base ?? '' ?>login/logout.php" class="text-base font-bold text-red-500">Cerrar sesión</a>
  </div>
  <?php endif; ?>

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
 
 <div id="opcion-recoger-sucursal" class="hidden flex items-center justify-between bg-white/10 p-3 rounded-xl mb-4 border border-white/5 transition-all duration-300">
 <div>
 <p class="text-sm font-bold text-white">¿Recoger en sucursal?</p>
 <p class="text-[10px] text-white/50">El envío será gratis</p>
 </div>
 <label class="relative inline-flex items-center cursor-pointer">
 <input type="checkbox" id="checkbox-recoger-sucursal" class="sr-only peer" onchange="toggleRecogerSucursal()">
 <div class="w-11 h-6 bg-white/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary"></div>
 </label>
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
 <p class="text-xs text-white bg-white/10 p-3 rounded-xl border border-white/5">No tienes direcciones registradas. <a href="<?= $base ?? '' ?>dashboard_cliente/direcciones.php" class="font-bold underline hover:text-tertiary">Agregar una</a>.</p>
 <?php endif; ?>
 </div>
 <?php endif; ?>


 <div class="flex flex-col gap-3">
 <button onclick="confirmarPedido()" id="btn-confirmar-pedido" class="w-full h-14 bg-tertiary text-white font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-sm flex items-center justify-center gap-2 relative overflow-hidden group">
 <span class="relative z-10 text-base tracking-wide">Confirmar pedido</span>
 <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:translate-x-1 transition-transform">send</span>
 </button>
 <div class="flex gap-2">
 <button onclick="generarCotizacion()" class="flex-1 h-12 bg-white text-primary font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-xs flex items-center justify-center gap-1.5 group" title="Descargar cotización en PDF">
 <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
 <span>PDF</span>
 </button>
 <button onclick="generarCotizacionExcel()" class="flex-1 h-12 bg-[#1a7f4b] text-white font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-xs flex items-center justify-center gap-1.5 group" title="Descargar cotización en Excel/CSV">
 <span class="material-symbols-outlined text-[18px]">table_view</span>
 <span>Excel</span>
 </button>
 </div>
 </div>
 </div>
</div>

<script>
const mm_cart_key = 'mm_carrito_' + (<?= isset($_SESSION['cliente_id']) ? json_encode($_SESSION['cliente_id']) : 'null' ?> || 'guest');
let carrito = [];
try {
 const parsed = JSON.parse(localStorage.getItem(mm_cart_key));
 carrito = Array.isArray(parsed) ? parsed : [];
} catch (e) {
 carrito = [];
 localStorage.removeItem(mm_cart_key);
}

function guardarCarrito() {
 localStorage.setItem(mm_cart_key, JSON.stringify(carrito));
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
 let totalIva = 0;
 let totalNormalConIva = 0;
 let tieneRedFria = false;

 carrito.forEach((item, index) => {
  const totalLinea = item.precio * item.cantidad;
  subtotal += totalLinea;
  
  const tasa = item.tasa_iva !== undefined ? parseFloat(item.tasa_iva) : 0.16;
  const tipo = item.tipo !== undefined ? item.tipo.toUpperCase() : 'SECO';
  const itemSinIva = totalLinea;
  const itemIva = totalLinea * tasa;
  totalIva += itemIva;
  
  if (tipo === 'RED FRIA') {
      tieneRedFria = true;
  } else {
      totalNormalConIva += (totalLinea + itemIva);
  }
  
  let imagenHtml = '';
  if (item.imagen && item.imagen !== 'PENDIENTE' && item.imagen !== '') {
  imagenHtml = `<img src="<?= $base ?? '' ?>includes/image_cache.php?img=${encodeURIComponent(item.imagen)}&w=150" class="w-full h-full object-contain p-1.5 transition-transform duration-300 group-hover:scale-[1.03]">`;
  } else {
  imagenHtml = `<span class="material-symbols-outlined text-slate-400 text-3xl transition-transform duration-300 group-hover:scale-[1.03]">medication</span>`;
  }

  html += `
  <div class="flex gap-4 p-4 bg-white/5 border border-white/10 rounded-2xl relative group hover:-translate-y-0.5 transition-all duration-300 animate-in slide-in-from-right-4 fade-in duration-300" style="animation-delay: ${index * 50}ms; animation-fill-mode: both;">
  <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden p-1">
  ${imagenHtml}
  </div>
  <div class="flex-1 min-w-0 py-1">
  <h4 class="text-sm font-bold text-white leading-tight mb-1 truncate pr-8" title="${item.nombre}">${item.nombre}</h4>
  <p class="text-base font-black text-tertiary mb-1">${formatCurrency(item.precio)}</p>
  <p class="text-[10px] text-white/50 mb-3">Precios más IVA | IVA: ${(tasa * 100).toFixed(0)}% (+${formatCurrency(itemIva)})</p>
  
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
 calcularEnvioDynamic(subtotal, totalIva, totalNormalConIva, tieneRedFria);
}

let currentShippingCost = 0;
let currentSubtotal = 0;
let currentTotalIva = 0;
let currentTotalNormalConIva = 0;
let currentTieneRedFria = false;
let currentShippingType = 'GRATIS';

function calcularEnvioDynamic(subtotal, totalIva = 0, totalNormalConIva = 0, tieneRedFria = false) {
  currentSubtotal = subtotal;
  currentTotalIva = totalIva;
  currentTotalNormalConIva = totalNormalConIva;
  currentTieneRedFria = tieneRedFria;
  const dirSelect = document.getElementById('cart-direccion');
  const envioEl = document.getElementById('cart-envio');
  const totalEl = document.getElementById('cart-total');

  if (!dirSelect || !envioEl || !totalEl) {
      if(totalEl) totalEl.textContent = formatCurrency(subtotal + currentTotalIva);
      return;
  }

  const direccion_id = dirSelect.value;
  if (!direccion_id) {
     envioEl.textContent = 'Selecciona dirección';
     totalEl.textContent = formatCurrency(subtotal + currentTotalIva);
     document.getElementById('opcion-recoger-sucursal').classList.add('hidden');
     document.getElementById('checkbox-recoger-sucursal').checked = false;
     return;
  }

  fetch('<?= $base ?? '' ?>catalogo/api_calcular_envio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ direccion_id: direccion_id, subtotal_normal_con_iva: totalNormalConIva, tiene_red_fria: tieneRedFria })
  })
  .then(res => res.json())
  .then(data => {
      if (data.success) {
          const calc = data.calculo;
          currentShippingType = calc.tipo;
          
          if (calc.costo > 0) {
              currentShippingCost = calc.costo;
              document.getElementById('opcion-recoger-sucursal').classList.remove('hidden');
              actualizarTotalConEnvio();
          } else {
              currentShippingCost = 0;
              document.getElementById('opcion-recoger-sucursal').classList.add('hidden');
              document.getElementById('checkbox-recoger-sucursal').checked = false;
              if (calc.tipo === 'SUCURSAL') {
                  envioEl.innerHTML = `<div class="text-right"><span class="text-white font-bold block">Recoger en almacén</span><span class="text-[10px] text-white/50 block leading-tight mt-0.5 max-w-[200px] ml-auto">Su pedido estará listo para que pase a recolectarlo</span></div>`;
              } else {
                  envioEl.textContent = calc.mensaje;
              }
              totalEl.textContent = formatCurrency(subtotal + currentTotalIva);
          }
      } else {
          envioEl.textContent = 'Error al calcular';
          totalEl.textContent = formatCurrency(subtotal + currentTotalIva);
      }
  })
  .catch(err => {
      envioEl.textContent = 'Error';
      totalEl.textContent = formatCurrency(subtotal + currentTotalIva);
  });
}

function toggleRecogerSucursal() {
    actualizarTotalConEnvio();
}

function actualizarTotalConEnvio() {
    const envioEl = document.getElementById('cart-envio');
    const totalEl = document.getElementById('cart-total');
    const isRecoger = document.getElementById('checkbox-recoger-sucursal') ? document.getElementById('checkbox-recoger-sucursal').checked : false;
    
    if (currentShippingType === 'SUCURSAL') {
        envioEl.innerHTML = `<div class="text-right"><span class="text-white font-bold block">Recoger en almacén</span><span class="text-[10px] text-white/50 block leading-tight mt-0.5 max-w-[200px] ml-auto">Su pedido estará listo para que pase a recolectarlo</span></div>`;
        totalEl.textContent = formatCurrency(currentSubtotal + currentTotalIva);
    } else if (isRecoger) {
        envioEl.innerHTML = `<span class="line-through text-white/50 text-xs mr-2">${formatCurrency(currentShippingCost)}</span><span class="text-white">Recoger en sucursal</span>`;
        totalEl.textContent = formatCurrency(currentSubtotal + currentTotalIva);
    } else {
        envioEl.textContent = formatCurrency(currentShippingCost);
        totalEl.textContent = formatCurrency(currentSubtotal + currentTotalIva + currentShippingCost);
    }
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
         renderCartItems(); // Esto recalcula todo y llama a calcularEnvioDynamic
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

  if (!direccion_id) {
     Swal.fire('Atención', 'Debes registrar y seleccionar una dirección para cotizar.', 'warning');
     return;
  }
 
 const isRecoger = document.getElementById('checkbox-recoger-sucursal') ? document.getElementById('checkbox-recoger-sucursal').checked : false;

 // Create a hidden form to submit the cart data as POST and trigger download
 const form = document.createElement('form');
 form.method = 'POST';
 form.action = '<?= $base ?? '' ?>catalogo/generar_cotizacion_pdf.php';
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
     
     const inputRecoger = document.createElement('input');
     inputRecoger.type = 'hidden';
     inputRecoger.name = 'recoger_sucursal';
     inputRecoger.value = isRecoger ? '1' : '0';
     form.appendChild(inputRecoger);
 }
 document.body.appendChild(form);
 form.submit();
 document.body.removeChild(form);
}

function generarCotizacionExcel() {
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

 const isRecoger = document.getElementById('checkbox-recoger-sucursal')
   ? document.getElementById('checkbox-recoger-sucursal').checked : false;

 const form = document.createElement('form');
 form.method = 'POST';
 form.action = '<?= $base ?? '' ?>catalogo/generar_cotizacion_excel.php';
 form.target = '_blank';

 const input = document.createElement('input');
 input.type = 'hidden';
 input.name = 'carrito_data';
 input.value = JSON.stringify(carrito);
 form.appendChild(input);

 if (direccion_id) {
   const inputDir = document.createElement('input');
   inputDir.type = 'hidden';
   inputDir.name = 'direccion_id';
   inputDir.value = direccion_id;
   form.appendChild(inputDir);

   const inputRecoger = document.createElement('input');
   inputRecoger.type = 'hidden';
   inputRecoger.name = 'recoger_sucursal';
   inputRecoger.value = isRecoger ? '1' : '0';
   form.appendChild(inputRecoger);
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

  if (!direccion_id) {
  Swal.fire('Atención', 'Debes seleccionar una dirección de envío o registrar una nueva.', 'warning');
  btn.innerHTML = originalText;
  btn.disabled = false;
  return;
  }
 
 const isRecoger = document.getElementById('checkbox-recoger-sucursal') ? document.getElementById('checkbox-recoger-sucursal').checked : false;

 fetch('<?= $base ?? '' ?>catalogo/procesar_pedido.php', {
 method: 'POST',
 headers: { 'Content-Type': 'application/json' },
 body: JSON.stringify({ carrito: carrito, direccion_id: direccion_id, recoger_sucursal: isRecoger })
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
 <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-2">¡Pedido enviado!</h2>
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
 window.location.href = '<?= $base ?? '' ?>catalogo/catalogo.php';
 });
 } else if (data.red_fria) {
    Swal.fire({
      icon: 'warning',
      title: '❄️ Producto de red fría',
      text: data.message,
      confirmButtonText: 'Entendido',
      confirmButtonColor: '#002451',
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
