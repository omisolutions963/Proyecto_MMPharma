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
<title><?= $titulo ?? 'MMPharma | Distribuidora Farmacéutica' ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="<?= $base ?? '' ?>logos/MMPharma-Isotipo.png">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "on-tertiary-container": "#39bb6c",
                    "surface-bright": "#f7f9ff",
                    "secondary-container": "#71c0fe",
                    "surface-container-lowest": "#ffffff",
                    "secondary-fixed-dim": "#92ccff",
                    "tertiary-fixed": "#7efba4",
                    "outline-variant": "#c4c6d0",
                    "primary-container": "#1a3a6b",
                    "inverse-primary": "#abc7ff",
                    "surface-container-low": "#edf4ff",
                    "on-secondary-fixed-variant": "#004b73",
                    "surface-dim": "#c6dcf6",
                    "surface-container-highest": "#cfe5ff",
                    "secondary-fixed": "#cce5ff",
                    "secondary": "#006397",
                    "tertiary-container": "#004520",
                    "primary-fixed-dim": "#abc7ff",
                    "on-background": "#051d30",
                    "surface-container": "#e3efff",
                    "on-surface": "#051d30",
                    "on-primary-fixed": "#001b3f",
                    "error-container": "#ffdad6",
                    "surface": "#f7f9ff",
                    "on-secondary": "#ffffff",
                    "on-primary-container": "#89a5dd",
                    "on-secondary-fixed": "#001d31",
                    "surface-container-high": "#d9eaff",
                    "on-secondary-container": "#004d77",
                    "inverse-surface": "#1d3246",
                    "surface-tint": "#415e91",
                    "on-primary-fixed-variant": "#284678",
                    "error": "#ba1a1a",
                    "tertiary": "#002c13",
                    "on-tertiary-fixed": "#00210c",
                    "on-error-container": "#93000a",
                    "primary-fixed": "#d7e2ff",
                    "on-error": "#ffffff",
                    "on-tertiary-fixed-variant": "#005228",
                    "on-tertiary": "#ffffff",
                    "surface-variant": "#cfe5ff",
                    "inverse-on-surface": "#e8f2ff",
                    "primary": "#002451",
                    "tertiary-fixed-dim": "#61de8a",
                    "background": "#f7f9ff",
                    "outline": "#747780",
                    "on-surface-variant": "#43474f",
                    "on-primary": "#ffffff"
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
    .clinical-shadow { box-shadow: 0 10px 40px -10px rgba(0, 36, 81, 0.08); }
</style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<header class="sticky top-0 z-50 bg-[#f7f9ff] shadow-sm">
<nav class="flex justify-between items-center w-full px-8 py-4 max-w-7xl mx-auto font-['Inter'] font-medium text-sm antialiased">
  <div class="flex items-center gap-12">
    <a href="<?= $base ?? '' ?>INDEX/index.php">
      <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
    </a>
    <div class="hidden md:flex gap-8">
      <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-[#002451] font-semibold border-b-2 border-[#002451] pb-1' : 'text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200' ?>" href="<?= $base ?? '' ?>INDEX/index.php">Inicio</a>
      <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-[#002451] font-semibold border-b-2 border-[#002451] pb-1' : 'text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200' ?>" href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php">¿Quiénes somos?</a>
      <a class="<?= ($pagina_actual ?? '') === 'catalogo' ? 'text-[#002451] font-semibold border-b-2 border-[#002451] pb-1' : 'text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200' ?>" href="<?= $base ?? '' ?>CATALOGO/catalogo.php">Catálogo</a>
      <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-[#002451] font-semibold border-b-2 border-[#002451] pb-1' : 'text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200' ?>" href="<?= $base ?? '' ?>CONTACTO/contacto.php">Contacto</a>
    </div>
  </div>
  <div class="flex items-center gap-4">
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <a href="<?= $base ?? '' ?>Admin-mmpharma/dashboard/dashboard.php">
          <button class="px-6 py-2 bg-[#004520] text-white font-semibold rounded-lg shadow-sm hover:opacity-90 active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">dashboard</span> Panel de Administrador
          </button>
        </a>
        <a href="<?= $base ?? '' ?>LOGIN/logout.php">
          <button class="px-4 py-2 text-[#ba1a1a] font-medium hover:bg-[#ffdad6] rounded-lg transition-all">Cerrar Sesión</button>
        </a>
    <?php else: ?>
        <a href="<?= $base ?? '' ?>LOGIN/login.php">
          <button class="px-4 py-2 text-[#1A3A6B] font-medium hover:bg-[#edf4ff] rounded-lg transition-all">Iniciar sesión</button>
        </a>
        <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php">
          <button class="px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow-sm hover:opacity-90 active:scale-95 transition-all">Solicitar acceso</button>
        </a>
    <?php endif; ?>
  </div>
</nav>
</header>