<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'MMPharma Portal' ?></title>
<link rel="icon" type="image/png" href="../../logos/MMPharma-Isotipo.png">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Cropper.js para recorte de foto de perfil -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: { extend: {
            colors: {
                "surface-container-lowest": "#03100a",
                "surface-container-low":    "#072115",
                "surface-container":        "#0b2d1e",
                "surface-container-high":   "#11422c",
                "surface-container-highest":"#185c3e",
                "surface":                  "#051a10",
                "surface-bright":           "#0d3a26",
                "surface-dim":              "#020c07",
                "surface-variant":          "#11422c",
                "background":               "#051a10",
                "on-surface":               "#f1fdf7",
                "on-surface-variant":       "#c4f0d5",
                "on-background":            "#f1fdf7",
                "primary":                  "#008151",
                "primary-container":        "#003822",
                "on-primary":               "#ffffff",
                "on-primary-container":     "#c4f0d5",
                "on-primary-fixed":         "#002111",
                "on-primary-fixed-variant": "#005132",
                "primary-fixed":            "#008151",
                "primary-fixed-dim":        "#00a669",
                "secondary":                "#00a669",
                "secondary-container":      "#00311d",
                "on-secondary":             "#ffffff",
                "on-secondary-container":   "#92f5c0",
                "secondary-fixed":          "#005234",
                "secondary-fixed-dim":      "#00a669",
                "on-secondary-fixed":       "#e1ffe7",
                "on-secondary-fixed-variant":"#006d48",
                "tertiary":                 "#34c47a",
                "tertiary-container":       "#0a3d20",
                "on-tertiary":              "#ffffff",
                "on-tertiary-container":    "#52e098",
                "tertiary-fixed":           "#7efba4",
                "tertiary-fixed-dim":       "#61de8a",
                "on-tertiary-fixed":        "#00210c",
                "on-tertiary-fixed-variant":"#005228",
                "error":                    "#f28b82",
                "error-container":          "#5c1010",
                "on-error":                 "#ffffff",
                "on-error-container":       "#ffb4ab",
                "outline":                  "#284a3c",
                "outline-variant":          "#153d27",
                "inverse-surface":          "#f1fdf7",
                "inverse-on-surface":       "#020d08",
                "inverse-primary":          "#005132",
                "surface-tint":             "#008151",
            },
            fontFamily: { headline: ["Inter"], body: ["Inter"], label: ["Inter"] },
        }}
    }
</script>
<style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #051a10; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    /* ══ SCROLLBAR GLOBAL AGRESIVA ══════════════════════════════════════════ */
    /* Forzamos el estilo en todos los elementos para evitar el comportamiento de Windows 11 */
    *::-webkit-scrollbar {
        width: 10px !important;
        height: 10px !important;
    }
    *::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05) !important;
    }
    *::-webkit-scrollbar-thumb {
        background: rgba(0,129,81,0.3) !important;
        border-radius: 10px !important;
        border: 2px solid transparent !important;
        background-clip: content-box !important;
    }
    *::-webkit-scrollbar-thumb:hover {
        background: rgba(0,129,81,0.6) !important;
        background-clip: content-box !important;
    }

    html { 
        scrollbar-gutter: stable !important; 
        scrollbar-width: auto !important; 
        overflow-y: scroll !important;
    }
    body {
        overflow-x: hidden !important;
    }
    .glass-card { background: rgba(0,129,81,0.05); backdrop-filter: blur(20px); }
    .card-glow { box-shadow: 0 0 30px rgba(0,129,81,0.08); }

    /* --- ANIMACIONES NATIVAS --- */
    @keyframes revealUp {
        0% { opacity: 0; transform: translateY(5px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleIn {
        0% { opacity: 0; transform: scale(0.98); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-reveal, .animate-fade-up { animation: revealUp 0.6s ease-out forwards; opacity: 0; }
    .animate-scale-in { animation: scaleIn 0.6s ease-out forwards; opacity: 0; }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    .delay-500 { animation-delay: 0.5s; }

    /* ══ ESTANDARIZACIÓN DE SOMBRAS GLOBALES ══ */
    .shadow-sm   { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important; }
    .shadow      { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important; }
    .shadow-md   { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3) !important; }
    .shadow-lg   { box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35) !important; }
    .shadow-xl   { box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4) !important; }
    .shadow-2xl  { box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5) !important; }
</style>

<script>
    function mockAction(title, text = 'Acción procesada correctamente', icon = 'success') {
        Swal.fire({ title, text, icon, confirmButtonColor: '#008151', confirmButtonText: 'Aceptar',
            background: '#05160e', color: '#f1fdf7' });
    }
    function confirmAction(title, text, confirmText, callback) {
        Swal.fire({ title, text, icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#f28b82', cancelButtonColor: '#284a3c',
            confirmButtonText: confirmText, cancelButtonText: 'Cancelar',
            background: '#05160e', color: '#f1fdf7'
        }).then(r => { if (r.isConfirmed) callback(); });
    }
</script>
</head>
<body class="bg-background text-on-surface">

<!-- TopNavBar -->
<header style="background:rgba(2,13,8,0.97);border-bottom:1px solid rgba(0,129,81,0.15)"
        class="h-16 sticky top-0 z-40 backdrop-blur-xl flex items-center justify-between px-8 ml-64 w-[calc(100%-16rem)]">
    
    <div class="flex items-center gap-5 flex-1">
        <!-- Portal Label -->
        <div class="flex items-center gap-3">
            <div class="flex flex-col leading-none">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/70 mb-1">MMPharma</span>
                <span class="text-xl font-extrabold text-white tracking-tight uppercase">Portal de administrador</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-5">
        <!-- Search -->
        <div class="relative hidden md:block">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
            <input class="w-72 pl-9 pr-4 py-2 rounded-xl border border-outline-variant/50
                          bg-surface-container-low/60 text-on-surface text-sm placeholder:text-outline
                          focus:ring-1 focus:ring-primary focus:outline-none"
                   placeholder="Buscar en el portal..." type="text"/>
        </div>
        
        <!-- Divider -->
        <div class="h-6 w-px bg-outline-variant/30"></div>

        <!-- User / Perfil Button -->
        <button onclick="abrirPerfil()"
                class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition-all hover:bg-white/5 group">
            <?php
            $foto = $_SESSION['admin_foto'] ?? '';
            $nombre = $_SESSION['admin_nombre'] ?? 'Admin';
            ?>
            <?php if ($foto): ?>
            <img src="<?= htmlspecialchars($foto) ?>" id="headerProfileImg"
                 class="w-8 h-8 rounded-lg object-cover border-2 border-emerald-500/40"
                 alt="Perfil">
            <?php else: ?>
            <div id="headerProfileImg"
                 class="w-8 h-8 rounded-lg flex items-center justify-center text-emerald-500 font-bold text-sm border-2 border-emerald-500/30 group-hover:border-emerald-500/60 transition-colors"
                 style="background:rgba(0,129,81,0.2)">
                <?= strtoupper(substr($nombre, 0, 1)) ?>
            </div>
            <?php endif; ?>
            <div class="hidden lg:flex flex-col items-start leading-none">
                <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($nombre) ?></span>
                <span class="text-[10px] text-on-surface-variant">Mi cuenta</span>
            </div>
            <span class="material-symbols-outlined text-outline text-[16px] hidden lg:block group-hover:text-primary transition-colors">expand_more</span>
        </button>
    </div>
</header>
