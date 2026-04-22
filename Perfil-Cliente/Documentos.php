<?php
$pageTitle  = 'MMPharma Portal - Mis Documentos';
$activePage = 'documentos';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Header -->
    <div class="mb-8 animate-reveal">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Mis Documentos</h1>
    </div>

    <!-- Alert Banner -->
    <div class="bg-tertiary-container/20 border border-tertiary/40 rounded-2xl p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm animate-reveal delay-100">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-tertiary/20 text-tertiary flex items-center justify-center shrink-0 shadow-[0_0_15px_rgba(52,196,122,0.3)]">
                <span class="material-symbols-outlined text-[24px]">check_circle</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white mb-0.5">Todos tus documentos están vigentes</h3>
                <p class="text-sm text-tertiary/80">Tu cuenta se encuentra al día y autorizada para compras controladas.</p>
            </div>
        </div>
        <button class="px-5 py-2.5 bg-tertiary hover:bg-tertiary-fixed-dim text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-tertiary/20 flex items-center gap-2 w-full md:w-auto justify-center">
            <span class="material-symbols-outlined text-[18px]">download</span> Descargar Expediente
        </button>
    </div>

    <!-- Documents Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-200">
        
        <!-- Doc 1 -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex flex-col shadow-sm relative group hover:border-outline-variant transition-colors">
            <div class="absolute top-6 right-6">
                <span class="px-2.5 py-1 bg-tertiary/10 border border-tertiary/30 text-tertiary text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Vigente
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[20px]">medical_services</span>
            </div>
            <h3 class="text-base font-bold text-white mb-2">Licencia Sanitaria</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-6 flex-1">Permiso federal para la comercialización de insumos de salud y psicotrópicos.</p>
            
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider mb-5">
                <div>
                    <span class="block text-on-surface-variant/70 mb-1">Última Carga</span>
                    <span class="text-white">12 Oct 2023</span>
                </div>
                <div class="text-right">
                    <span class="block text-on-surface-variant/70 mb-1">Vence</span>
                    <span class="text-white">12 Oct 2025</span>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high text-primary text-sm font-bold rounded-xl transition-colors">Ver Documento</button>
                <button class="w-11 py-2.5 bg-surface-container hover:bg-surface-container-high text-on-surface-variant text-sm font-bold rounded-xl transition-colors flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">sync</span></button>
            </div>
        </div>

        <!-- Doc 2 -->
        <div class="bg-surface-container-lowest border border-secondary/30 rounded-2xl p-6 flex flex-col shadow-sm relative group hover:border-secondary/50 transition-colors">
            <div class="absolute top-6 right-6">
                <span class="px-2.5 py-1 bg-secondary/10 border border-secondary/30 text-secondary text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> Por Vencer
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            </div>
            <h3 class="text-base font-bold text-white mb-2">Constancia Fiscal</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-6 flex-1">Situación fiscal actualizada (SAT) para facturación y validación de RFC.</p>
            
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider mb-5">
                <div>
                    <span class="block text-on-surface-variant/70 mb-1">Última Carga</span>
                    <span class="text-white">15 Ene 2024</span>
                </div>
                <div class="text-right">
                    <span class="block text-secondary mb-1">Vence en 15 días</span>
                    <span class="text-white">28 Mar 2024</span>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high text-primary text-sm font-bold rounded-xl transition-colors">Ver Documento</button>
                <button class="flex-1 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-bold rounded-xl transition-colors shadow-lg shadow-primary/20">Actualizar</button>
            </div>
        </div>

        <!-- Doc 3 -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex flex-col shadow-sm relative group hover:border-outline-variant transition-colors">
            <div class="absolute top-6 right-6">
                <span class="px-2.5 py-1 bg-tertiary/10 border border-tertiary/30 text-tertiary text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Vigente
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[20px]">storefront</span>
            </div>
            <h3 class="text-base font-bold text-white mb-2">Aviso de Funcionamiento</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed mb-6 flex-1">Documentación de alta ante COFEPRIS para la operación del establecimiento.</p>
            
            <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider mb-5">
                <div>
                    <span class="block text-on-surface-variant/70 mb-1">Última Carga</span>
                    <span class="text-white">22 Nov 2023</span>
                </div>
                <div class="text-right">
                    <span class="block text-on-surface-variant/70 mb-1">Vence</span>
                    <span class="text-white">N/A</span>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high text-primary text-sm font-bold rounded-xl transition-colors">Ver Documento</button>
                <button class="w-11 py-2.5 bg-surface-container hover:bg-surface-container-high text-on-surface-variant text-sm font-bold rounded-xl transition-colors flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">sync</span></button>
            </div>
        </div>
    </div>

    <!-- Upload Area -->
    <div class="border-2 border-dashed border-outline-variant/50 bg-surface-container/20 rounded-3xl p-12 flex flex-col items-center justify-center text-center transition-colors hover:bg-surface-container/40 animate-reveal delay-300">
        <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center text-primary mb-6 shadow-md">
            <span class="material-symbols-outlined text-[32px]">upload_file</span>
        </div>
        <h3 class="text-xl font-bold text-white mb-2">Cargar Nuevos Documentos</h3>
        <p class="text-on-surface-variant text-sm mb-8 max-w-md">Arrastra y suelta tus archivos PDF aquí o haz clic para explorar. Formatos permitidos: PDF, JPG (Máx. 10MB).</p>
        <button class="px-8 py-3 bg-surface-container-highest hover:bg-surface-dim border border-outline-variant text-white text-sm font-bold rounded-xl transition-colors flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-[18px]">add_circle</span> Seleccionar Archivos
        </button>
    </div>

</main>
<?php include('Includes/footer.php'); ?>