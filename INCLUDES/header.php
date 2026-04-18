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
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#003e79",
                    "secondary": "#1e60aa",
                    "tertiary": "#32b4ca",
                    "primary-container": "#e0f2ff",
                    "secondary-container": "#cfe5ff",
                    "tertiary-container": "#d1e4ff",
                    "on-surface": "#001d35",
                    "on-surface-variant": "#003e79",
                    "background": "#f0f7ff",
                    "surface": "#ffffff",
                    "surface-container-low": "#e1f0ff",
                    "surface-container": "#cfe5ff",
                    "surface-container-high": "#abc7ff",
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
</style>
</head>
<body class="bg-[#f0f7ff] font-body text-on-surface antialiased">

<header class="sticky top-0 z-50 bg-white shadow-[0_4px_30px_rgba(0,0,0,0.03)]">
<nav class="flex justify-between items-center w-full px-12 py-4 max-w-[1600px] mx-auto font-['Inter'] font-medium text-base antialiased">
  <div class="flex-1 flex items-center">
    <a href="<?= $base ?? '' ?>INDEX/index.php">
      <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
    </a>
  </div>

  <div class="hidden md:flex gap-12 text-base">
    <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>INDEX/index.php">Inicio</a>
    <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php">¿Quiénes somos?</a>
    <a class="<?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>CATALOGO/catalogo.php">Catálogo</a>
    <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>CONTACTO/contacto.php">Contacto</a>
  </div>

  <div class="flex-1 flex items-center justify-end gap-4">
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <a href="<?= $base ?? '' ?>Admin-mmpharma/dashboard/dashboard.php">
          <button class="px-6 py-2 bg-gradient-to-br from-[#005132] to-[#008151] text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(0,129,81,0.2)] active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">dashboard</span> Panel de Administrador
          </button>
        </a>
        <a href="<?= $base ?? '' ?>LOGIN/logout.php" title="Cerrar Sesión">
          <button class="w-10 h-10 flex items-center justify-center text-[#ba1a1a] hover:bg-[#ffdad6] rounded-xl transition-all group" aria-label="Cerrar Sesión">
            <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">logout</span>
          </button>
        </a>
    <?php else: ?>
        <a href="<?= $base ?? '' ?>LOGIN/login.php">
          <button class="px-4 py-2 text-[#1A3A6B] font-bold hover:bg-[#edf4ff] rounded-xl transition-all">Iniciar sesión</button>
        </a>
        <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php">
          <button class="px-6 py-2 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(0,0,0,0.1)] active:scale-95 transition-all">Solicitar acceso</button>
        </a>
    <?php endif; ?>
  </div>
</nav>
</header>