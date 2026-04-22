<?php
$pageTitle  = 'MMPharma Portal - Mis Direcciones';
$activePage = 'direcciones';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 animate-reveal">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight mb-1">Mis Direcciones</h1>
            <p class="text-on-surface-variant text-sm">Gestiona las ubicaciones donde recibirás tus pedidos.</p>
        </div>
        <button class="mt-4 md:mt-0 px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span> Agregar nueva dirección
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 animate-reveal delay-100">
        
        <!-- Card 1: Principal -->
        <div class="bg-surface-container-lowest border-2 border-primary/30 rounded-2xl p-6 shadow-[0_0_20px_rgba(74,144,217,0.05)] relative flex flex-col">
            <div class="mb-4">
                <span class="px-2.5 py-1 bg-primary/20 text-primary text-[10px] font-black rounded-lg uppercase tracking-wider inline-block mb-3">Principal</span>
                <h3 class="text-lg font-bold text-white">Farmacia Principal</h3>
            </div>
            
            <div class="space-y-4 mb-8 flex-1">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px] mt-0.5">location_on</span>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Av. Insurgentes Sur 1234, Col. Del Valle, Benito Juárez, 03100, CDMX</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">schedule</span>
                    <p class="text-sm text-on-surface-variant">Lunes a Viernes: 08:00 - 18:00</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">person</span>
                    <p class="text-sm text-on-surface-variant">Dr. Roberto Sánchez</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">call</span>
                    <p class="text-sm text-on-surface-variant">55 1234 5678</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-auto">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-white text-sm font-bold rounded-xl transition-colors">Editar</button>
                <button class="w-11 h-11 bg-surface-container hover:bg-error/20 hover:text-error text-on-surface-variant rounded-xl transition-colors flex items-center justify-center border border-outline-variant/50"><span class="material-symbols-outlined text-[18px]">delete</span></button>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm flex flex-col hover:border-outline-variant transition-colors">
            <div class="mb-4">
                <div class="h-[22px] mb-3"></div> <!-- Spacer to align with the badge of principal -->
                <h3 class="text-lg font-bold text-white">Sucursal Norte</h3>
            </div>
            
            <div class="space-y-4 mb-8 flex-1">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px] mt-0.5">location_on</span>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Blvd. Manuel Ávila Camacho 567, Naucalpan de Juárez, 53500, Estado de México</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">schedule</span>
                    <p class="text-sm text-on-surface-variant">Lunes a Sábado: 09:00 - 20:00</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">person</span>
                    <p class="text-sm text-on-surface-variant">Lic. Ana Gómez</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">call</span>
                    <p class="text-sm text-on-surface-variant">55 9876 5432</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-auto">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-white text-sm font-bold rounded-xl transition-colors">Editar</button>
                <button class="w-11 h-11 bg-surface-container hover:bg-error/20 hover:text-error text-on-surface-variant rounded-xl transition-colors flex items-center justify-center border border-outline-variant/50"><span class="material-symbols-outlined text-[18px]">delete</span></button>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm flex flex-col hover:border-outline-variant transition-colors">
            <div class="mb-4">
                <div class="h-[22px] mb-3"></div> <!-- Spacer -->
                <h3 class="text-lg font-bold text-white">Almacén Central</h3>
            </div>
            
            <div class="space-y-4 mb-8 flex-1">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px] mt-0.5">location_on</span>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Prol. Paseo de la Reforma 890, Santa Fe, Álvaro Obregón, 01210, CDMX</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">schedule</span>
                    <p class="text-sm text-on-surface-variant">24 Horas</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">person</span>
                    <p class="text-sm text-on-surface-variant">Ing. Carlos Mendoza</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">call</span>
                    <p class="text-sm text-on-surface-variant">55 3322 1100</p>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-auto">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-white text-sm font-bold rounded-xl transition-colors">Editar</button>
                <button class="w-11 h-11 bg-surface-container hover:bg-error/20 hover:text-error text-on-surface-variant rounded-xl transition-colors flex items-center justify-center border border-outline-variant/50"><span class="material-symbols-outlined text-[18px]">delete</span></button>
            </div>
        </div>

    </div>

</main>
<?php include('Includes/footer.php'); ?>