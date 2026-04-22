<?php
$pageTitle  = 'MMPharma Portal - Cotizaciones';
$activePage = 'cotizaciones';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 animate-reveal">
        <div>
            <nav class="flex text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-3">
                <a href="Dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="mx-2">›</span>
                <span class="text-primary">Mis Cotizaciones</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Historial de Cotizaciones</h1>
            <p class="text-on-surface-variant mt-1 text-sm">Gestiona y consulta el estatus de tus solicitudes de inventario.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="Cotizaciones.php?action=new" class="px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Nueva Cotización
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-100">
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">calendar_month</span>
            </div>
            <div>
                <p class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Este mes</p>
                <h3 class="text-3xl font-extrabold text-white leading-none">8</h3>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <p class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Monto total</p>
                <h3 class="text-3xl font-extrabold text-white leading-none">$89,340</h3>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <p class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Aprobadas</p>
                <h3 class="text-3xl font-extrabold text-white leading-none">6</h3>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-4 mb-6 animate-reveal delay-200">
        <div class="relative flex-1 min-w-[250px]">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" placeholder="Buscar por folio o agente..." class="w-full bg-surface-container-lowest border border-outline-variant/50 text-white rounded-xl pl-12 pr-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all">
        </div>
        <div class="relative w-48">
            <select class="w-full bg-surface-container-lowest border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none">
                <option>Todos los estados</option>
                <option>Aprobada</option>
                <option>Pendiente</option>
                <option>Rechazada</option>
            </select>
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
        </div>
        <div class="relative w-64">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">date_range</span>
            <input type="text" value="01/10/2023 - 31/10/2023" readonly class="w-full bg-surface-container-lowest border border-outline-variant/50 text-white rounded-xl pl-12 pr-4 py-3 text-sm focus:border-primary outline-none cursor-pointer">
        </div>
        <button class="px-5 py-3 bg-surface-container border border-outline-variant/50 hover:bg-surface-container-high text-primary text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">filter_list</span> Filtros avanzados
        </button>
    </div>

    <!-- Table -->
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-sm overflow-hidden animate-reveal delay-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead class="bg-surface-container-low text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
                    <tr>
                        <th class="py-4 px-6">Folio</th>
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6 text-center">Cant.</th>
                        <th class="py-4 px-6 text-right">Subtotal</th>
                        <th class="py-4 px-6 text-right">IVA</th>
                        <th class="py-4 px-6 text-right">Total</th>
                        <th class="py-4 px-6 text-center">Envío</th>
                        <th class="py-4 px-6 text-center">Estado</th>
                        <th class="py-4 px-6">Agente</th>
                        <th class="py-4 px-6"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-primary">CT-8942</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">28 Oct 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">12</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$12,450.00</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$1,992.00</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$14,442.00</td>
                        <td class="py-4 px-6 text-center"><span class="material-symbols-outlined text-tertiary text-[18px]">local_shipping</span></td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-3 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider">Aprobada</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">Lic. Mendoza</td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-primary">CT-8941</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">27 Oct 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">5</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$8,200.00</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$1,312.00</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$9,512.00</td>
                        <td class="py-4 px-6 text-center"><span class="material-symbols-outlined text-on-surface-variant text-[18px]">local_shipping</span></td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-3 py-1 bg-secondary/20 text-secondary text-[10px] font-bold rounded-md uppercase tracking-wider">Pendiente</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">Lic. Mendoza</td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-primary">CT-8938</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">25 Oct 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">45</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$45,000.00</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$7,200.00</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$52,200.00</td>
                        <td class="py-4 px-6 text-center"><span class="material-symbols-outlined text-tertiary text-[18px]">local_shipping</span></td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-3 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider">Aprobada</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">Ing. García</td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-primary">CT-8935</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">22 Oct 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">8</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$3,400.00</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$544.00</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$3,944.00</td>
                        <td class="py-4 px-6 text-center"><span class="material-symbols-outlined text-on-surface-variant text-[18px]">local_shipping</span></td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-3 py-1 bg-error/20 text-error text-[10px] font-bold rounded-md uppercase tracking-wider">Rechazada</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">Lic. Mendoza</td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface-container/30 transition-colors group">
                        <td class="py-4 px-6 text-sm font-bold text-primary">CT-8930</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">20 Oct 2023</td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant text-center">15</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$5,600.00</td>
                        <td class="py-4 px-6 text-sm text-on-surface text-right">$896.00</td>
                        <td class="py-4 px-6 text-sm font-bold text-white text-right">$6,496.00</td>
                        <td class="py-4 px-6 text-center"><span class="material-symbols-outlined text-tertiary text-[18px]">local_shipping</span></td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-3 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider">Aprobada</span>
                        </td>
                        <td class="py-4 px-6 text-xs text-on-surface-variant">Ing. García</td>
                        <td class="py-4 px-6 text-center">
                            <button class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined text-[18px]">more_vert</span></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-between gap-4 bg-surface-container-low/50">
            <span class="text-xs text-on-surface-variant">Mostrando 5 de 42 cotizaciones</span>
            <div class="flex items-center gap-1">
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container"><span class="material-symbols-outlined text-sm">keyboard_double_arrow_left</span></button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-primary text-white text-xs font-bold shadow-lg shadow-primary/20">1</button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface hover:bg-surface-container text-xs font-bold">2</button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface hover:bg-surface-container text-xs font-bold">3</button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-on-surface-variant hover:bg-surface-container"><span class="material-symbols-outlined text-sm">keyboard_double_arrow_right</span></button>
            </div>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="flex justify-end gap-3 mt-6 animate-reveal delay-400">
        <button class="px-5 py-2.5 bg-secondary/10 hover:bg-secondary/20 text-secondary text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">table_chart</span> Exportar Excel
        </button>
        <button class="px-5 py-2.5 bg-error/10 hover:bg-error/20 text-error text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Descargar Reporte PDF
        </button>
    </div>

</main>
<?php include('Includes/footer.php'); ?>