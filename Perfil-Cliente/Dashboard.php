<?php
$pageTitle  = 'MMPharma Portal - Dashboard';
$activePage = 'dashboard';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-reveal">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                Dashboard Principal <span class="text-2xl">👋</span>
            </h1>
            <p class="text-on-surface-variant mt-1 text-sm">Aquí tienes un resumen de tu actividad reciente, <?php echo htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Farmacia San Jorge'); ?>.</p>
        </div>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <a href="Cotizaciones.php" class="px-5 py-2.5 bg-surface-container border border-outline-variant/50 hover:bg-surface-container-high text-white text-sm font-semibold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">download</span> Estado de Cuenta
            </a>
            <a href="Cotizaciones.php?action=new" class="px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Nueva Cotización
            </a>
        </div>
    </div>

    <!-- ROW 1: CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8 animate-reveal delay-100">
        
        <!-- Alerta Documentos (Col-span-2) -->
        <div class="md:col-span-2 bg-[#422c10] border border-[#a66a1d] rounded-2xl p-6 flex flex-col justify-center relative overflow-hidden">
            <div class="absolute -right-4 -top-4 text-yellow-500/10 rotate-12">
                <span class="material-symbols-outlined" style="font-size: 120px; font-variation-settings: 'FILL' 1;">warning</span>
            </div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-[#eab308]/20 text-[#eab308] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">warning</span>
                </div>
                <div>
                    <h3 class="text-[#fde047] font-bold text-base mb-1">Documentos por vencer</h3>
                    <p class="text-[#fef08a]/80 text-xs mb-3 leading-relaxed">Tu aviso de funcionamiento y licencia sanitaria requieren actualización en los próximos 15 días.</p>
                    <a href="Documentos.php" class="text-[#facc15] text-xs font-bold hover:underline">Actualizar ahora</a>
                </div>
            </div>
        </div>

        <!-- Metric 1: Cotizaciones -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Cotizaciones este mes</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-extrabold text-white leading-none">8</span>
                <span class="text-xs font-bold text-tertiary flex items-center mb-1"><span class="material-symbols-outlined text-[14px] mr-0.5">trending_up</span> +12%</span>
            </div>
        </div>

        <!-- Metric 2: Última Cotización -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Última Cotización</span>
            <div>
                <span class="text-2xl font-extrabold text-white block mb-1">$12,450.00</span>
                <span class="text-[10px] text-on-surface-variant italic">Hace 2 días</span>
            </div>
        </div>

        <!-- Metric 3: Productos Favoritos -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Productos Favoritos</span>
            <div class="flex items-center gap-2">
                <span class="text-3xl font-extrabold text-white leading-none">34</span>
                <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1">favorite</span>
            </div>
        </div>

        <!-- Metric 4: Estado de Cuenta -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Estado de Cuenta</span>
            <div>
                <span class="px-2 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider mb-2 inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> ACTIVO</span>
                <span class="block text-[10px] text-on-surface-variant leading-tight">Sin adeudos<br>pendientes</span>
            </div>
        </div>
    </div>

    <!-- ROW 2: Buscador + Acciones -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-200">
        <!-- Búsqueda Banner -->
        <div class="md:col-span-2 bg-surface-container border border-primary/20 rounded-2xl p-8 flex flex-col items-center justify-center text-center relative overflow-hidden shadow-lg shadow-primary/5">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent"></div>
            <div class="relative z-10 w-full max-w-lg mx-auto">
                <h2 class="text-xl font-bold text-white mb-6">Busca rápidamente en nuestro catálogo de +5,000 productos</h2>
                <div class="flex relative w-full mb-4">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                    <input type="text" placeholder="¿Qué medicamento o insumo buscas hoy?" class="w-full bg-white text-gray-900 rounded-xl pl-12 pr-32 py-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary shadow-inner">
                    <button class="absolute right-2 top-2 bottom-2 bg-surface-container-highest hover:bg-surface-dim text-white px-6 rounded-lg text-sm font-bold transition-colors">Buscar</button>
                </div>
                <div class="flex items-center justify-center gap-3 text-[10px] font-semibold text-on-surface-variant">
                    <span>POPULARES:</span>
                    <span class="flex items-center gap-1 bg-white/5 px-2 py-1 rounded-md cursor-pointer hover:bg-white/10"><span class="material-symbols-outlined text-[12px]">medication</span> Antibióticos</span>
                    <span class="flex items-center gap-1 bg-white/5 px-2 py-1 rounded-md cursor-pointer hover:bg-white/10"><span class="material-symbols-outlined text-[12px]">healing</span> Material de Curación</span>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm">
            <h3 class="text-sm font-bold text-on-surface mb-4">Acciones Rápidas</h3>
            <div class="space-y-3">
                <a href="Cotizaciones.php?action=new" class="flex items-center justify-between p-4 bg-surface-container-low hover:bg-surface-container rounded-xl border border-transparent hover:border-primary/30 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        </div>
                        <span class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">Nueva cotización</span>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant text-[16px]">chevron_right</span>
                </a>
                <a href="Documentos.php" class="flex items-center justify-between p-4 bg-surface-container-low hover:bg-surface-container rounded-xl border border-transparent hover:border-primary/30 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-tertiary/10 text-tertiary flex items-center justify-center group-hover:bg-tertiary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[18px]">folder_open</span>
                        </div>
                        <span class="text-sm font-semibold text-on-surface group-hover:text-tertiary transition-colors">Mis documentos</span>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant text-[16px]">chevron_right</span>
                </a>
                <a href="Cotizaciones.php" class="flex items-center justify-between p-4 bg-surface-container-low hover:bg-surface-container rounded-xl border border-transparent hover:border-primary/30 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center group-hover:bg-secondary group-hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                        </div>
                        <span class="text-sm font-semibold text-on-surface group-hover:text-secondary transition-colors">Mis cotizaciones</span>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant text-[16px]">chevron_right</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ROW 3: Tabla Cotizaciones Recientes -->
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-sm overflow-hidden animate-reveal delay-300">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/20">
            <h3 class="text-base font-bold text-on-surface">Cotizaciones recientes</h3>
            <a href="Cotizaciones.php" class="text-xs font-bold text-primary hover:text-primary-fixed-dim transition-colors">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
                    <tr>
                        <th class="py-3 px-6">Folio</th>
                        <th class="py-3 px-6">Fecha</th>
                        <th class="py-3 px-6 text-center">Productos</th>
                        <th class="py-3 px-6 text-right">Total</th>
                        <th class="py-3 px-6 text-center">Estado</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-white">#QT-2024-001</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">24 Oct, 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">12 items</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$4,250.00</td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-1 bg-primary/20 text-primary text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">edit</span>Borrador</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white transition-colors"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-white">#QT-2023-892</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">22 Oct, 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">5 items</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$12,450.00</td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-1 bg-yellow-500/20 text-yellow-500 text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">schedule</span>Pendiente</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white transition-colors"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-white">#QT-2023-875</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">18 Oct, 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">28 items</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$34,120.00</td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">check_circle</span>Aprobada</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white transition-colors"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-white">#QT-2023-860</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">15 Oct, 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">3 items</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$1,200.00</td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-1 bg-error/20 text-error text-[10px] font-bold rounded-md uppercase tracking-wider"><span class="material-symbols-outlined text-[10px] mr-1 align-middle">cancel</span>Vencida</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white transition-colors"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</main>
<?php include('Includes/footer.php'); ?>
<script>
    console.log("MMPharma Dashboard Loaded");
</script>