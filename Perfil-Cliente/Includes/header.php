<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'MMPharma Portal Cliente' ?></title>
<link rel="icon" type="image/png" href="../logos/MMPharma-Isotipo.png">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: { extend: {
            colors: {
                "surface-container-lowest": "#0d1f3c",
                "surface-container-low":    "#102245",
                "surface-container":        "#152a52",
                "surface-container-high":   "#1a3260",
                "surface-container-highest":"#1e3a6e",
                "surface":                  "#0a1929",
                "surface-bright":           "#112038",
                "surface-dim":              "#061422",
                "surface-variant":          "#1a3260",
                "background":               "#071628",
                "on-surface":               "#e8f0ff",
                "on-surface-variant":       "#8aaad4",
                "on-background":            "#e8f0ff",
                "primary":                  "#4a90d9",
                "primary-container":        "#1a3a6b",
                "on-primary":               "#ffffff",
                "on-primary-container":     "#abc7ff",
                "on-primary-fixed":         "#001b3f",
                "on-primary-fixed-variant": "#284678",
                "primary-fixed":            "#1d3a70",
                "primary-fixed-dim":        "#3a7abf",
                "secondary":                "#5bb8f5",
                "secondary-container":      "#0a3a5c",
                "on-secondary":             "#ffffff",
                "on-secondary-container":   "#9dd4f7",
                "secondary-fixed":          "#1a4a72",
                "secondary-fixed-dim":      "#4a9fd4",
                "on-secondary-fixed":       "#d0eaff",
                "on-secondary-fixed-variant":"#7bbfe0",
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
                "outline":                  "#3a5a8a",
                "outline-variant":          "#1e3a6e",
                "inverse-surface":          "#e8f0ff",
                "inverse-on-surface":       "#071628",
                "inverse-primary":          "#002451",
                "surface-tint":             "#4a90d9",
            },
            fontFamily: { headline: ["Inter"], body: ["Inter"], label: ["Inter"] },
        }}
    }
</script>
<style>
    * { font-family: 'Inter', sans-serif; }
    body { background: #071628; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #0a1929; }
    ::-webkit-scrollbar-thumb { background: #1e3a6e; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #4a90d9; }
    .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); }
    .card-glow { box-shadow: 0 0 30px rgba(74,144,217,0.08); }

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
</style>

<script>
    function mockAction(title, text = 'Acción procesada correctamente', icon = 'success') {
        Swal.fire({ title, text, icon, confirmButtonColor: '#4a90d9', confirmButtonText: 'Aceptar',
            background: '#102245', color: '#e8f0ff' });
    }
    function confirmAction(title, text, confirmText, callback) {
        Swal.fire({ title, text, icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#f28b82', cancelButtonColor: '#3a5a8a',
            confirmButtonText: confirmText, cancelButtonText: 'Cancelar',
            background: '#102245', color: '#e8f0ff'
        }).then(r => { if (r.isConfirmed) callback(); });
    }
</script>
</head>
<body class="bg-background text-on-surface flex">

<!-- TopNavBar -->
<header style="background:rgba(7,22,40,0.97);border-bottom:1px solid rgba(74,144,217,0.15)"
        class="h-16 fixed top-0 z-40 backdrop-blur-xl flex items-center justify-between px-8 ml-64 w-[calc(100%-16rem)]">
    
    <div class="flex items-center gap-5 flex-1">
        <!-- Portal Label -->
        <div class="flex items-center gap-3">
            <div class="flex flex-col leading-none">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/70 mb-1">MMPharma</span>
                <span class="text-xl font-extrabold text-white tracking-tight uppercase">Portal Cliente</span>
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
                   placeholder="Buscar..." type="text"/>
        </div>
        
        <!-- Divider -->
        <div class="h-6 w-px bg-outline-variant/30"></div>

        <!-- User / Perfil Button -->
        <a href="Perfil.php" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition-all hover:bg-white/5 group">
            <?php
            $foto = $_SESSION['cliente_foto'] ?? '';
            $nombre = $_SESSION['cliente_nombre'] ?? 'Cliente';
            ?>
            <?php if ($foto): ?>
            <img src="<?= htmlspecialchars($foto) ?>" id="headerProfileImg"
                 class="w-8 h-8 rounded-full object-cover border-2 border-primary/40"
                 alt="Perfil">
            <?php else: ?>
            <div id="headerProfileImg"
                 class="w-8 h-8 rounded-full flex items-center justify-center text-primary font-bold text-sm border-2 border-primary/30 group-hover:border-primary/60 transition-colors"
                 style="background:rgba(74,144,217,0.2)">
                <?= strtoupper(substr($nombre, 0, 1)) ?>
            </div>
            <?php endif; ?>
            <div class="hidden lg:flex flex-col items-start leading-none">
                <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($nombre) ?></span>
                <span class="text-[10px] text-on-surface-variant">Mi cuenta</span>
            </div>
        </a>
    </div>
</header>
