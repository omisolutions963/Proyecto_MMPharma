<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $titulo ?? 'MMPharma | Distribuidora farmacéutica' ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="logos/MMPharma-Isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script id="tailwind-config">
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    "primary": "#003e79",
                    "secondary": "#1e60aa",
                    "tertiary": "#2ca1b5",
                    "background": "#f0f7ff",
                    "surface": "#ffffff",
                },
                fontFamily: { "body": ["Inter"] },
            },
        },
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
    .parallax-bg { will-change: transform; transition: transform 0.1s cubic-bezier(0,0,0.1,1); }
    
    /* Mobile Menu Transition */
    #mobile-menu {
        transition: transform 0.3s ease-in-out;
        transform: translateX(100%);
    }
    #mobile-menu.active {
        transform: translateX(0);
    }
</style>
</head>
<body class="bg-[#f0f7ff] font-body text-on-surface antialiased">

<header class="sticky top-0 z-50 bg-white shadow-[0_4px_30px_rgba(0,0,0,0.03)]">
<nav class="flex justify-between items-center w-full px-6 md:px-12 py-4 max-w-[1600px] mx-auto font-['Inter'] font-medium text-base antialiased">
  <div class="flex items-center">
    <a href="index.php">
      <img src="logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-7 md:h-8 w-auto">
    </a>
  </div>

  <!-- Desktop Nav -->
  <div class="hidden md:flex items-center gap-12 text-base">
    <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary font-bold border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="index.php">Inicio</a>
    <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary font-bold border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="quienes_somos.php">¿Quiénes somos?</a>
    <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary font-bold border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="contacto.php">Contacto</a>
  </div>

  <!-- Mobile Toggle -->
  <button id="menu-toggle" class="md:hidden text-primary p-2">
    <span class="material-symbols-outlined text-3xl">menu</span>
  </button>
</nav>

<!-- Mobile Menu Sidebar -->
<div id="mobile-menu" class="fixed inset-0 z-[60] bg-white md:hidden">
    <div class="flex flex-col h-full">
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
            <img src="logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-7 w-auto">
            <button id="menu-close" class="text-primary p-2">
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
        </div>
        <div class="flex flex-col items-center p-8 gap-10">
            <a href="index.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary border-b-4 border-tertiary pb-1' : 'text-slate-600' ?>">Inicio</a>
            <a href="quienes_somos.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary border-b-4 border-tertiary pb-1' : 'text-slate-600' ?>">¿Quiénes somos?</a>
            <a href="contacto.php" class="text-2xl font-bold w-fit text-center <?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary border-b-4 border-tertiary pb-1' : 'text-slate-600' ?>">Contacto</a>
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

// Parallax Logic
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax-bg');
    
    parallaxElements.forEach(el => {
        const speed = el.getAttribute('data-speed') || 0.5;
        const offset = scrolled * speed;
        el.style.transform = `scale(1.25) translateY(${offset * 0.1}px)`;
    });
});
</script>
