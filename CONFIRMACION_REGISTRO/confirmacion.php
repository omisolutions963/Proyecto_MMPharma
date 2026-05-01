<?php
$titulo = 'Solicitud Enviada | MMPharma';
$pagina_actual = 'confirmacion';
$base = '../';
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $titulo ?></title>
    <!-- Include fonts and tailwind -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
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
                    "success": "#16a34a",
                    "success-light": "#dcfce7",
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
            font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 48;
        }

        /* Success Animation */
        .circle-ripple {
            position: relative;
            width: 96px;
            height: 96px;
            margin: 0 auto;
        }
        .circle-ripple .circle {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50%;
            border: 4px solid #16a34a;
            opacity: 0;
        }
        .circle-ripple .circle:nth-child(1) {
            animation: ripple 2s infinite;
        }
        .circle-ripple .circle:nth-child(2) {
            animation: ripple 2s infinite;
            animation-delay: 0.6s;
        }
        .circle-ripple .circle:nth-child(3) {
            animation: ripple 2s infinite;
            animation-delay: 1.2s;
        }
        .circle-ripple .icon-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background-color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 10;
            box-shadow: 0 10px 25px -5px rgba(22, 163, 74, 0.4);
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes ripple {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }
        @keyframes popIn {
            0% { transform: translate(-50%, -50%) scale(0); }
            100% { transform: translate(-50%, -50%) scale(1); }
        }
    </style>
</head>
<body class="bg-background text-on-surface antialiased min-h-screen flex flex-col items-center justify-center p-6">
    
    <div class="max-w-5xl w-full bg-surface rounded-3xl shadow-2xl p-8 md:p-12 relative overflow-hidden">
        <!-- Accent top border -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-success to-tertiary"></div>

        <!-- Success Animation & Title -->
        <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12 mb-12">
            <div class="shrink-0 pt-4">
                <div class="circle-ripple">
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="circle"></div>
                    <div class="icon-container">
                        <span class="material-symbols-outlined text-[48px]">check</span>
                    </div>
                </div>
            </div>
            <div class="text-center lg:text-left">
                <h1 class="text-3xl md:text-4xl font-black text-on-surface mb-4 tracking-tight">¡Solicitud enviada exitosamente!</h1>
                <p class="text-on-surface-variant text-lg font-medium">Nuestro equipo revisará tu información y validará los documentos proporcionados en las próximas <strong class="text-primary font-bold">24 a 48 horas hábiles</strong>.</p>
            </div>
        </div>

        <!-- Timeline -->
        <div class="mb-12">
            <!-- Desktop Timeline (hidden on mobile) -->
            <div class="hidden md:flex items-start justify-between relative">
                <div class="absolute top-8 left-16 right-16 h-1 bg-slate-100 -z-10 rounded-full"></div>
                
                <!-- Step 1 -->
                <div class="flex flex-col items-center flex-1 text-center relative">
                    <div class="w-16 h-16 rounded-full bg-success flex items-center justify-center shadow-lg border-4 border-white mb-4">
                        <span class="material-symbols-outlined text-white text-[28px]">task_alt</span>
                    </div>
                    <h3 class="text-lg font-bold text-success">Solicitud enviada</h3>
                    <p class="text-sm text-slate-500 font-medium px-4">Información y documentos recibidos</p>
                </div>
                <!-- Step 2 -->
                <div class="flex flex-col items-center flex-1 text-center relative">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center border-4 border-white mb-4">
                        <span class="material-symbols-outlined text-slate-400 text-[28px] font-variation-settings-'FILL'-0">manage_search</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Revisión de documentos</h3>
                    <p class="text-sm text-slate-500 font-medium px-4">Validación legal y fiscal</p>
                </div>
                <!-- Step 3 -->
                <div class="flex flex-col items-center flex-1 text-center relative">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center border-4 border-white mb-4">
                        <span class="material-symbols-outlined text-slate-400 text-[28px] font-variation-settings-'FILL'-0">key</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Activación de cuenta</h3>
                    <p class="text-sm text-slate-500 font-medium px-4">Envío de accesos al portal</p>
                </div>
            </div>

            <!-- Mobile Timeline (hidden on desktop) -->
            <div class="md:hidden relative">
                <div class="absolute left-8 top-0 bottom-0 w-1 bg-slate-100 rounded-full"></div>
                <div class="space-y-8 relative">
                    <!-- Step 1 Mobile -->
                    <div class="flex items-start gap-6">
                        <div class="w-16 h-16 rounded-full bg-success flex items-center justify-center shadow-lg border-4 border-white relative z-10 shrink-0">
                            <span class="material-symbols-outlined text-white text-[28px]">task_alt</span>
                        </div>
                        <div class="pt-4">
                            <h3 class="text-lg font-bold text-success">Solicitud enviada</h3>
                            <p class="text-sm text-slate-500 font-medium">Información y documentos recibidos</p>
                        </div>
                    </div>
                    <!-- Step 2 Mobile -->
                    <div class="flex items-start gap-6">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center border-4 border-white relative z-10 shrink-0">
                            <span class="material-symbols-outlined text-slate-400 text-[28px] font-variation-settings-'FILL'-0">manage_search</span>
                        </div>
                        <div class="pt-4">
                            <h3 class="text-lg font-bold text-slate-700">Revisión de documentos</h3>
                            <p class="text-sm text-slate-500 font-medium">Validación legal y fiscal</p>
                        </div>
                    </div>
                    <!-- Step 3 Mobile -->
                    <div class="flex items-start gap-6">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center border-4 border-white relative z-10 shrink-0">
                            <span class="material-symbols-outlined text-slate-400 text-[28px] font-variation-settings-'FILL'-0">key</span>
                        </div>
                        <div class="pt-4">
                            <h3 class="text-lg font-bold text-slate-700">Activación de cuenta</h3>
                            <p class="text-sm text-slate-500 font-medium">Envío de accesos al portal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact & Buttons -->
        <div class="flex flex-col lg:flex-row gap-8 lg:items-center pt-10 border-t border-slate-100">
            <div class="flex-1 space-y-6">
                <!-- Info Box -->
                <div class="bg-primary/5 border border-primary/10 rounded-2xl p-5 flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-primary shrink-0 shadow-sm mt-1">
                        <span class="material-symbols-outlined text-[24px]">schedule</span>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-primary mb-1">Tiempo estimado de respuesta</h4>
                        <p class="text-sm text-on-surface-variant font-medium leading-relaxed">De <strong class="font-bold">24 a 48 horas hábiles</strong>.<br>Lunes a Viernes de 8:00 AM a 6:00 PM.</p>
                    </div>
                </div>
                
                <!-- Contact -->
                <div class="text-center sm:text-left">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">¿Dudas sobre tu trámite?</p>
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-8 justify-center sm:justify-start">
                        <a href="tel:3312345678" class="flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-primary transition-colors group justify-center">
                            <span class="material-symbols-outlined text-lg text-primary group-hover:scale-110 transition-transform">call</span> (33) 1234 5678
                        </a>
                        <a href="mailto:ventas@mmpharma.com" class="flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-primary transition-colors group justify-center">
                            <span class="material-symbols-outlined text-lg text-primary group-hover:scale-110 transition-transform">mail</span> ventas@mmpharma.com
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="w-full lg:w-[320px] shrink-0 flex flex-col gap-4">
                <a href="../INDEX/index.php" class="w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-secondary hover:shadow-xl hover:-translate-y-0.5 active:scale-[0.98] transition-all text-center flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">home</span> Volver al inicio
                </a>
                <a href="mailto:ventas@mmpharma.com" class="w-full py-4 bg-white border-2 border-primary text-primary font-bold rounded-xl hover:bg-primary/5 active:scale-[0.98] transition-all text-center flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">support_agent</span> Contactar soporte
                </a>
            </div>
        </div>
    </div>
</body>
</html>
