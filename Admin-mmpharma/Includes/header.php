<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'MMPharma Portal' ?></title>
<link rel="icon" type="image/png" href="../../logos/MMPharma-Isotipo.png">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "surface-container-lowest": "#ffffff",
                    "error-container": "#ffdad6",
                    "outline-variant": "#c4c6d0",
                    "surface-bright": "#f7f9ff",
                    "surface": "#f7f9ff",
                    "on-tertiary-container": "#39bb6c",
                    "background": "#f7f9ff",
                    "surface-container-low": "#edf4ff",
                    "on-tertiary-fixed": "#00210c",
                    "on-secondary-fixed-variant": "#004b73",
                    "on-secondary-container": "#004d77",
                    "primary-fixed-dim": "#abc7ff",
                    "on-primary": "#ffffff",
                    "surface-container-highest": "#cfe5ff",
                    "tertiary-container": "#004520",
                    "secondary-fixed": "#cce5ff",
                    "surface-container": "#e3efff",
                    "on-error-container": "#93000a",
                    "primary-fixed": "#d7e2ff",
                    "outline": "#747780",
                    "on-secondary": "#ffffff",
                    "on-tertiary-fixed-variant": "#005228",
                    "on-secondary-fixed": "#001d31",
                    "secondary-fixed-dim": "#92ccff",
                    "on-surface-variant": "#43474f",
                    "primary": "#002451",
                    "on-surface": "#051d30",
                    "tertiary-fixed-dim": "#61de8a",
                    "tertiary": "#002c13",
                    "surface-dim": "#c6dcf6",
                    "secondary-container": "#71c0fe",
                    "primary-container": "#1a3a6b",
                    "on-tertiary": "#ffffff",
                    "error": "#ba1a1a",
                    "surface-tint": "#415e91",
                    "surface-variant": "#cfe5ff",
                    "secondary": "#006397",
                    "inverse-surface": "#1d3246",
                    "inverse-primary": "#abc7ff",
                    "on-primary-container": "#89a5dd",
                    "on-error": "#ffffff",
                    "surface-container-high": "#d9eaff",
                    "on-background": "#051d30",
                    "inverse-on-surface": "#e8f2ff",
                    "on-primary-fixed-variant": "#284678",
                    "tertiary-fixed": "#7efba4",
                    "on-primary-fixed": "#001b3f"
                },
                "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
                },
                "fontFamily": {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"]
                }
            },
        },
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Funciones globales para mockear acciones
    function mockAction(title, text = 'Acción procesada correctamente', icon = 'success') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonColor: '#002451',
            confirmButtonText: 'Aceptar'
        });
    }

    function confirmAction(title, text, confirmText, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#747780',
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }
</script>
</head>
<body class="bg-background text-on-background">

<!-- TopNavBar -->
<header class="h-16 sticky top-0 z-40 bg-[#f7f9ff] dark:bg-[#051d30] backdrop-blur-md flex items-center justify-between px-8 ml-64 w-[calc(100%-16rem)]">
    <div class="flex items-center gap-4 flex-1">
        <div class="relative w-96">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input class="w-full pl-10 pr-4 py-2 bg-surface-container-lowest rounded-full border-none focus:ring-2 focus:ring-primary-fixed text-sm" placeholder="Buscar pedidos, lotes o productos..." type="text"/>
        </div>
    </div>
    <div class="flex items-center gap-6">
        <button class="text-on-surface-variant hover:bg-[#edf4ff] p-2 rounded-full transition-colors relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full"></span>
        </button>
        <button class="text-on-surface-variant hover:bg-[#edf4ff] p-2 rounded-full transition-colors">
            <span class="material-symbols-outlined">help_outline</span>
        </button>
        <div class="h-8 w-[1px] bg-outline-variant/30"></div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-[#002451] font-inter">MMPharma Portal</span>
        </div>
    </div>
</header>
