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
                    "background": "#0a192f",
                    "surface": "#112240",
                    "surface-container-low": "#1a365d",
                    "surface-container": "#2a4365",
                    "surface-container-high": "#2c5282",
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
    .clinical-shadow { box-shadow: 0 10px 40px -10px rgba(0, 62, 121, 0.1); }

    /* Mobile Menu Transition */
    #mobile-menu {
        transition: transform 0.3s ease-in-out;
        transform: translateX(100%);
    }
    #mobile-menu.active {
        transform: translateX(0);
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
<body class="bg-background font-body text-slate-300 antialiased">

<header class="sticky top-0 z-50 bg-surface">
<nav class="flex justify-between items-center w-full px-12 py-4 max-w-[1600px] mx-auto font-['Inter'] font-medium text-base antialiased">
  <div class="flex-1 flex items-center">
    <a href="<?= $base ?? '' ?>INDEX/index.php">
      <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-8 w-auto">
    </a>
  </div>

  <div class="hidden md:flex gap-12 text-base">
    <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary-light font-bold border-b-2 border-tertiary pb-1' : 'text-slate-300 hover:text-primary-light transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>INDEX/index.php">Inicio</a>
    <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary-light font-bold border-b-2 border-tertiary pb-1' : 'text-slate-300 hover:text-primary-light transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php">¿Quiénes somos?</a>
    <a class="<?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary-light font-bold border-b-2 border-tertiary pb-1' : 'text-slate-300 hover:text-primary-light transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>CATALOGO/catalogo.php">Catálogo</a>
    <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary-light font-bold border-b-2 border-tertiary pb-1' : 'text-slate-300 hover:text-primary-light transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>CONTACTO/contacto.php">Contacto</a>
  </div>

  <div class="flex-1 flex items-center justify-end gap-4">
    <?php if (isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true): ?>
        
        <!-- Lógica de colores por rol -->
        <?php
        $tipo_cliente = $_SESSION['cliente_tipo'] ?? '';
        $role_ring = 'ring-slate-300';
        $role_bg = 'bg-slate-500';
        
        $role_text = 'text-primary-light';
        $role_hover_text = 'hover:text-primary-light';
        $role_hover_bg = 'hover:bg-primary/20';
        $role_icon_bg = 'group-hover/item:bg-primary';
        
        if ($tipo_cliente === 'EMPRESA') {
            $role_ring = 'ring-secondary';
            $role_bg = 'bg-secondary';
            $role_text = 'text-secondary-light';
            $role_hover_text = 'hover:text-secondary-light';
            $role_hover_bg = 'hover:bg-secondary/20';
            $role_icon_bg = 'group-hover/item:bg-secondary';
        } elseif ($tipo_cliente === 'DISTRIBUIDORA') {
            $role_ring = 'ring-tertiary';
            $role_bg = 'bg-tertiary';
            $role_text = 'text-tertiary-light';
            $role_hover_text = 'hover:text-tertiary-light';
            $role_hover_bg = 'hover:bg-tertiary/20';
            $role_icon_bg = 'group-hover/item:bg-tertiary';
        } elseif ($tipo_cliente === 'FARMACIA') {
            $role_ring = 'ring-primary';
            $role_bg = 'bg-primary';
            $role_text = 'text-primary-light';
            $role_hover_text = 'hover:text-primary-light';
            $role_hover_bg = 'hover:bg-primary/20';
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
        <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Dashboard.php" class="hidden md:flex items-center justify-center gap-2 px-6 py-2.5 <?= $role_bg ?> text-white font-bold rounded-xl hover:shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:-translate-y-1 transition-all duration-300 ml-4 group relative overflow-hidden border border-white/10">
            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out"></div>
            <span class="relative z-10 tracking-wide">Ingresar al Portal</span>
            <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:translate-x-1 transition-transform">arrow_forward</span>
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
          <div id="profile-menu" class="profile-menu-anim absolute right-0 mt-4 w-72 bg-surface rounded-2xl overflow-hidden z-[100] origin-top-right border border-white/5">
            <div class="px-6 py-5 bg-background">
              <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Cuenta activa</p>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-surface rounded-xl flex items-center justify-center <?= $role_text ?> overflow-hidden flex-shrink-0">
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
              <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Dashboard.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-300 <?= $role_hover_text ?> <?= $role_hover_bg ?> rounded-xl transition-all group/item">
                <div class="w-9 h-9 bg-background/50 rounded-lg flex items-center justify-center <?= $role_icon_bg ?> group-hover/item:text-white transition-colors border border-white/5">
                  <span class="material-symbols-outlined text-lg">dashboard</span>
                </div>
                <span>Panel de control</span>
              </a>
              <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Perfil.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-300 <?= $role_hover_text ?> <?= $role_hover_bg ?> rounded-xl transition-all group/item">
                <div class="w-8 h-8 bg-background rounded-lg flex items-center justify-center <?= $role_icon_bg ?> group-hover/item:text-white transition-colors">
                  <span class="material-symbols-outlined text-lg">manage_accounts</span>
                </div>
                <span>Mi perfil</span>
              </a>
            </div>
            
            <div class="p-2 bg-background">
              <a href="<?= $base ?? '' ?>LOGIN/logout.php" class="flex items-center gap-3 p-3 text-sm font-bold text-red-400 hover:bg-red-500/10 rounded-xl transition-all group/item">
                <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center group-hover/item:bg-red-500 group-hover/item:text-white transition-colors">
                  <span class="material-symbols-outlined text-lg">logout</span>
                </div>
                <span>Cerrar sesión</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Icono de carrito (Solo para clientes logueados) -->
        <button id="cart-icon-btn" onclick="toggleCartDrawer()" class="relative w-10 h-10 flex items-center justify-center text-slate-600 hover:text-primary hover:bg-slate-100 rounded-xl transition-all ml-2" aria-label="Carrito de compras">
          <span class="material-symbols-outlined text-2xl">shopping_cart</span>
          <span id="cart-badge" class="hidden absolute -top-1 -right-1 bg-secondary text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full">0</span>
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
        <a href="<?= $base ?? '' ?>DASHBOARD_ADMIN/dashboard/dashboard.php" class="hidden md:flex items-center justify-center gap-2 px-6 py-2.5 bg-[#4ade80] text-[#005132] font-black rounded-xl hover:shadow-[0_0_20px_rgba(74,222,128,0.4)] hover:-translate-y-1 transition-all duration-300 ml-4 group relative overflow-hidden border border-[#4ade80]/50">
            <div class="absolute inset-0 bg-white/40 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out"></div>
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
          <div id="profile-menu-admin" class="profile-menu-anim absolute right-0 mt-4 w-72 bg-surface rounded-2xl overflow-hidden z-[100] origin-top-right border border-white/5">
            <div class="px-6 py-5 bg-background">
              <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Cuenta activa</p>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-surface rounded-xl flex items-center justify-center text-[#4ade80] overflow-hidden flex-shrink-0">
                  <?php if ($foto_admin && $foto_admin !== 'PENDIENTE'): ?>
                    <img src="<?= htmlspecialchars($foto_admin) ?>" alt="Perfil" class="w-full h-full object-cover">
                  <?php else: ?>
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">admin_panel_settings</span>
                  <?php endif; ?>
                </div>
                <div class="flex-1 overflow-hidden flex flex-col justify-center gap-1">
                  <p class="text-sm font-black text-[#4ade80] leading-tight break-words whitespace-normal"><?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?></p>
                  <p class="text-[9px] font-bold text-slate-400 uppercase leading-tight">ADMINISTRADOR</p>
                </div>
              </div>
            </div>
            
            <div class="p-2 space-y-1">
              <a href="<?= $base ?? '' ?>DASHBOARD_ADMIN/dashboard/dashboard.php" class="flex items-center gap-3 p-3 text-sm font-bold text-slate-300 hover:text-[#4ade80] hover:bg-[#4ade80]/10 rounded-xl transition-all group/item">
                <div class="w-9 h-9 bg-background/50 rounded-lg flex items-center justify-center group-hover/item:bg-[#4ade80] group-hover/item:text-surface transition-colors border border-white/5">
                  <span class="material-symbols-outlined text-lg">dashboard</span>
                </div>
                <span>Panel de control</span>
              </a>
            </div>
            
            <div class="p-2 bg-background">
              <a href="<?= $base ?? '' ?>LOGIN/logout.php" class="flex items-center gap-3 p-3 text-sm font-bold text-red-400 hover:bg-red-500/10 rounded-xl transition-all group/item">
                <div class="w-8 h-8 bg-red-500/10 rounded-lg flex items-center justify-center group-hover/item:bg-red-500 group-hover/item:text-white transition-colors">
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
        <a href="<?= $base ?? '' ?>LOGIN/login.php">
          <button class="px-4 py-2 text-primary-light font-bold hover:bg-primary/10 rounded-xl transition-all">Iniciar sesión</button>
        </a>
        <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php">
          <button class="px-6 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-light hover:text-surface hover:-translate-y-0.5 transition-all">Solicitar acceso</button>
        </a>
    <?php endif; ?>

    <!-- Mobile Toggle -->
    <button id="menu-toggle" class="md:hidden text-primary p-2 ml-2">
      <span class="material-symbols-outlined text-3xl">menu</span>
    </button>
  </div>
</nav>

<!-- Mobile Menu Sidebar -->
<div id="mobile-menu" class="fixed inset-0 z-[100] bg-background md:hidden">
    <div class="flex flex-col h-full">
        <div class="flex justify-between items-center px-6 py-4">
            <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-7 w-auto">
            <button id="menu-close" class="text-primary-light p-2">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>
        <div class="flex flex-col items-center p-8 gap-10 overflow-y-auto">
            <a href="<?= $base ?? '' ?>INDEX/index.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary-light border-b-4 border-tertiary pb-1' : 'text-slate-300' ?>">Inicio</a>
            <a href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary-light border-b-4 border-tertiary pb-1' : 'text-slate-300' ?>">¿Quiénes somos?</a>
            <a href="<?= $base ?? '' ?>CATALOGO/catalogo.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary-light border-b-4 border-tertiary pb-1' : 'text-slate-300' ?>">Catálogo</a>
            <a href="<?= $base ?? '' ?>CONTACTO/contacto.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary-light border-b-4 border-tertiary pb-1' : 'text-slate-300' ?>">Contacto</a>
            
            <hr class="border-transparent w-full">
            
            <?php if (!isset($_SESSION['cliente_logged_in']) && !isset($_SESSION['admin_logged_in'])): ?>
                <a href="<?= $base ?? '' ?>LOGIN/login.php" class="text-xl font-bold text-primary-light">Iniciar sesión</a>
                <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php" class="text-xl font-bold text-secondary-light">Solicitar acceso</a>
            <?php else: ?>
                <p class="text-sm font-black text-slate-500 uppercase tracking-widest">Mi cuenta</p>
                <?php if (isset($_SESSION['cliente_logged_in'])): ?>
                    <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Dashboard.php" class="text-xl font-bold text-primary-light">Panel de control</a>
                <?php else: ?>
                    <a href="<?= $base ?? '' ?>DASHBOARD_ADMIN/dashboard/dashboard.php" class="text-xl font-bold text-[#4ade80]">Panel admin</a>
                <?php endif; ?>
                <a href="<?= $base ?? '' ?>LOGIN/logout.php" class="text-xl font-bold text-red-400">Cerrar sesión</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</header>

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
<div id="cart-drawer" class="fixed top-0 right-0 h-full w-full sm:w-[420px] bg-background/95 backdrop-blur-xl z-[70] translate-x-full transition-transform duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] flex flex-col">
  
  <!-- Header -->
  <div class="px-8 py-6 flex items-center justify-between bg-surface relative overflow-hidden">
    <div class="absolute top-0 left-0 w-1 h-full bg-primary-light"></div>
    <div class="flex items-center gap-3 text-primary-light">
      <span class="material-symbols-outlined text-[28px] animate-bounce-slow">shopping_bag</span>
      <h2 class="text-xl font-black tracking-tight text-primary-light">Mi Carrito</h2>
    </div>
    <button onclick="toggleCartDrawer()" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-primary-light hover:bg-white/5 rounded-full transition-all bg-surface">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>

  <!-- Items -->
  <div id="cart-items-container" class="flex-1 overflow-y-auto p-6 space-y-4">
    <!-- El contenido se genera con JS -->
  </div>

  <!-- Footer -->
  <div class="p-6 bg-surface/80 backdrop-blur-md relative">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-full w-full h-8 pointer-events-none"></div>
    <div class="flex justify-between items-end mb-6">
      <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Subtotal</p>
      <p id="cart-subtotal" class="text-3xl font-black text-primary-light">$0.00</p>
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
        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Dirección de Envío</label>
        <?php if(!empty($direcciones_cart)): ?>
        <div class="relative">
            <select id="cart-direccion" class="w-full px-4 py-3 bg-white rounded-xl focus:ring-2 focus:ring-primary outline-none appearance-none text-sm font-medium text-slate-700">
                <?php foreach($direcciones_cart as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['alias']) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
        </div>
        <?php else: ?>
        <p class="text-xs text-amber-600 bg-amber-50 p-3 rounded-lg">No tienes direcciones registradas. <a href="<?= $base ?? '' ?>DASHBOARD_CLIENTE/Direcciones.php" class="font-bold underline hover:text-amber-700">Agregar una</a>.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <p class="text-xs text-slate-500 mb-6 leading-relaxed">
      *Los precios mostrados son de lista. Si eres distribuidor o empresa, el precio final se ajustará al generar la cotización formal.
    </p>
    <div class="flex flex-col gap-3">
        <button onclick="confirmarPedido()" id="btn-confirmar-pedido" class="w-full h-14 bg-green-500 text-white font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-sm flex items-center justify-center gap-2 relative overflow-hidden group">
          <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
          <span class="material-symbols-outlined text-[20px] relative z-10">send</span>
          <span class="relative z-10 text-base tracking-wide">Confirmar pedido</span>
        </button>
        <button onclick="generarCotizacion()" class="w-full h-12 bg-red-500 text-white font-bold rounded-xl hover:-translate-y-1 active:scale-[0.98] transition-all duration-300 text-sm flex items-center justify-center gap-2 relative overflow-hidden group">
          <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
          <span class="material-symbols-outlined text-[20px] relative z-10 group-hover:scale-110 transition-transform">picture_as_pdf</span>
          <span class="relative z-10 tracking-wide">Descargar Cotización</span>
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
          <div class="h-full flex flex-col items-center justify-center text-center text-slate-400 opacity-80 px-6 animate-in fade-in zoom-in duration-500">
            <div class="w-24 h-24 bg-primary/5 rounded-full flex items-center justify-center mb-6">
              <span class="material-symbols-outlined text-[64px] text-primary/40">shopping_bag</span>
            </div>
            <p class="text-lg font-black text-slate-600 mb-2">Tu carrito está vacío</p>
            <p class="text-sm font-medium text-slate-400">¡Explora nuestro catálogo y descubre los mejores productos!</p>
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
        <div class="flex gap-4 p-4 bg-surface rounded-2xl relative group hover:-translate-y-1 transition-all duration-300 animate-in slide-in-from-right-4 fade-in duration-300" style="animation-delay: ${index * 50}ms; animation-fill-mode: both;">
          <div class="w-20 h-20 bg-slate-50/80 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
            ${imagenHtml}
          </div>
          <div class="flex-1 min-w-0 py-1">
            <h4 class="text-sm font-bold text-slate-700 leading-tight mb-1 truncate pr-8 transition-colors group-hover:text-primary" title="${item.nombre}">${item.nombre}</h4>
            <p class="text-base font-black text-secondary mb-3">${formatCurrency(item.precio)}</p>
            
            <div class="flex items-center bg-slate-50/80 rounded-xl w-fit p-1">
              <button onclick="cambiarCantidad(${index}, -1)" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-primary-light hover:bg-white/5 rounded-lg transition-all"><span class="material-symbols-outlined text-[18px]">remove</span></button>
              <span class="w-8 text-center text-sm font-black text-slate-700">${item.cantidad}</span>
              <button onclick="cambiarCantidad(${index}, 1)" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-primary-light hover:bg-white/5 rounded-lg transition-all"><span class="material-symbols-outlined text-[18px]">add</span></button>
            </div>
          </div>
          <button onclick="eliminarDelCarrito(${index})" class="absolute top-3 right-3 text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all w-8 h-8 flex items-center justify-center rounded-lg opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0">
            <span class="material-symbols-outlined text-[20px]">delete</span>
          </button>
        </div>
        `;
    });

    container.innerHTML = html;
    subtotalEl.textContent = formatCurrency(subtotal);
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
});

function generarCotizacion() {
    if (carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Añade productos antes de generar la cotización', 'warning');
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
