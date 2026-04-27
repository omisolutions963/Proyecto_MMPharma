<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Fetch Notifications if database connection is available
$notificaciones = [];
$unread_count = 0;
if (isset($pdo) && isset($_SESSION['cliente_id'])) {
    $stmt_notif = $pdo->prepare("SELECT * FROM admin_alertas_notificaciones WHERE cliente_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt_notif->execute([$_SESSION['cliente_id']]);
    $notificaciones = $stmt_notif->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM admin_alertas_notificaciones WHERE cliente_id = ? AND leida = 0");
    $stmt_unread->execute([$_SESSION['cliente_id']]);
    $unread_count = $stmt_unread->fetchColumn();
}
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
    /* ══ ANTI-FOUC: Colores hardcoded antes de que Tailwind CDN procese ══ */
    html, body, main, aside { background-color: #071628 !important; }
    body { color: #e8f0ff; }
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
        background: rgba(74,144,217,0.3) !important;
        border-radius: 10px !important;
        border: 2px solid transparent !important;
        background-clip: content-box !important;
    }
    *::-webkit-scrollbar-thumb:hover {
        background: rgba(74,144,217,0.6) !important;
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
    .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); }
    .card-glow { box-shadow: 0 0 30px rgba(74,144,217,0.08); }

    /* ══ ESTANDARIZACIÓN DE SOMBRAS GLOBALES ══ */
    .shadow-sm   { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important; }
    .shadow      { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important; }
    .shadow-md   { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important; }
    .shadow-lg   { box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25) !important; }
    .shadow-xl   { box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important; }
    .shadow-2xl  { box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4) !important; }

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
    function toggleNotificaciones() {
        const dropdown = document.getElementById('notificaciones-dropdown');
        dropdown.classList.toggle('opacity-0');
        dropdown.classList.toggle('invisible');
        dropdown.classList.toggle('translate-y-2');
    }
    // Close dropdowns when clicking outside
    window.onclick = function(event) {
        if (!event.target.closest('#notif-btn') && !event.target.closest('#notificaciones-dropdown')) {
            const dropdown = document.getElementById('notificaciones-dropdown');
            if (dropdown && !dropdown.classList.contains('invisible')) {
                dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
            }
        }
    }
</script>
</head>
<body class="bg-background text-on-surface">

<!-- TopNavBar -->
<header style="background:rgba(7,22,40,0.97);border-bottom:1px solid rgba(74,144,217,0.15)"
        class="h-16 fixed top-0 z-40 backdrop-blur-xl flex items-center justify-between px-8 ml-64 w-[calc(100%-16rem)]">
    
    <div class="flex items-center gap-5 flex-1">
        <!-- Portal Label -->
        <div class="flex items-center gap-3">
            <div class="flex flex-col leading-none">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/70 mb-1">MMPharma</span>
                <span class="text-xl font-extrabold text-white tracking-tight uppercase">Portal cliente</span>
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

        <!-- Notifications -->
        <div class="relative">
            <button id="notif-btn" onclick="toggleNotificaciones()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low/60 border border-outline-variant/30 text-on-surface-variant hover:text-primary hover:border-primary/50 transition-all relative group">
                <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">notifications</span>
                <?php if ($unread_count > 0): ?>
                <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border border-background"></span>
                <?php endif; ?>
            </button>

            <!-- Notifications Dropdown -->
            <div id="notificaciones-dropdown" class="absolute right-0 top-[calc(100%+0.75rem)] w-80 bg-surface-container-low border border-outline-variant/50 rounded-2xl shadow-2xl opacity-0 invisible translate-y-2 transition-all duration-200 z-50 overflow-hidden">
                <div class="p-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container/50">
                    <h3 class="text-sm font-bold text-white">Notificaciones</h3>
                    <span class="text-[10px] font-black text-primary uppercase tracking-widest"><?= $unread_count ?> sin leer</span>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <?php if (empty($notificaciones)): ?>
                    <div class="p-8 text-center">
                        <span class="material-symbols-outlined text-outline text-[40px] mb-2">notifications_off</span>
                        <p class="text-xs text-on-surface-variant">No tienes notificaciones por ahora.</p>
                    </div>
                    <?php else: ?>
                        <?php foreach($notificaciones as $n): ?>
                        <div class="p-4 border-b border-outline-variant/10 hover:bg-white/5 transition-colors cursor-pointer relative">
                            <?php if(!$n['leida']): ?>
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                            <?php endif; ?>
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 
                                    <?= $n['tipo'] === 'SUCCESS' ? 'bg-tertiary/10 text-tertiary' : 
                                       ($n['tipo'] === 'WARNING' ? 'bg-error/10 text-error' : 'bg-primary/10 text-primary') ?>">
                                    <span class="material-symbols-outlined text-[18px]">
                                        <?= $n['tipo'] === 'SUCCESS' ? 'check_circle' : 
                                           ($n['tipo'] === 'WARNING' ? 'warning' : 'info') ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white mb-0.5"><?= htmlspecialchars($n['mensaje']) ?></p>
                                    <p class="text-[10px] text-on-surface-variant"><?= date('d M, H:i', strtotime($n['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <a href="#" class="block p-3 text-center text-[11px] font-bold text-primary hover:bg-primary/5 transition-colors">
                    Ver todas las notificaciones
                </a>
            </div>
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
                 class="w-8 h-8 rounded-lg object-cover border-2 border-primary/40"
                 alt="Perfil">
            <?php else: ?>
            <div id="headerProfileImg"
                 class="w-8 h-8 rounded-lg flex items-center justify-center text-primary font-bold text-sm border-2 border-primary/30 group-hover:border-primary/60 transition-colors"
                 style="background:rgba(74,144,217,0.2)">
                <?= strtoupper(substr($nombre, 0, 1)) ?>
            </div>
            <?php endif; ?>
            <div class="hidden lg:flex flex-col items-start leading-none">
                <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($nombre) ?></span>
                <span class="text-[10px] text-on-surface-variant">Mi cuenta</span>
            </div>
            <span class="material-symbols-outlined text-outline text-[16px] hidden lg:block group-hover:text-primary transition-colors">expand_more</span>
        </a>
    </div>
</header>
