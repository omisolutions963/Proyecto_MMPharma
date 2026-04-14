<?php
$pageTitle = "MMPharma Portal - Gestión de Pedidos";
$activePage = "pedidos";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-[calc(100vh-4rem)]">
<!-- Header & Action Row -->
<section class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
<div class="space-y-1">
<h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Gestión de Pedidos</h2>
<p class="text-on-surface-variant text-sm">Control centralizado de transacciones y estados de envío de la red clínica.</p>
</div>
<button class="flex items-center gap-2 bg-gradient-to-br from-primary to-primary-container text-white px-6 py-2.5 rounded-lg font-semibold shadow-lg shadow-primary/10 hover:shadow-primary/20 transition-all">
<span class="material-symbols-outlined text-xl">download</span>
<span>Descargar Reporte de Ventas (Excel)</span>
</button>
</section>
<!-- Filters Section -->
<section class="bg-surface-container-low rounded-xl p-6 mb-8 flex flex-wrap items-end gap-6">
<div class="flex flex-col gap-2 flex-1 min-w-[200px]">
<label class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant px-1">Rango de Fechas</label>
<div class="flex items-center gap-2 bg-surface-container-lowest p-2 rounded-lg border border-outline-variant/20 shadow-sm">
<input class="bg-transparent border-none focus:ring-0 text-sm text-on-surface w-full" type="date"/>
<span class="text-outline-variant">/</span>
<input class="bg-transparent border-none focus:ring-0 text-sm text-on-surface w-full" type="date"/>
</div>
</div>
<div class="flex flex-col gap-2 flex-1 min-w-[200px]">
<label class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant px-1">Estado del Pedido</label>
<select class="bg-surface-container-lowest border-none rounded-lg p-2.5 text-sm text-on-surface focus:ring-2 focus:ring-primary-fixed shadow-sm">
<option value="">Todos los estados</option>
<option value="pendiente">Pendiente</option>
<option value="procesando">Procesando</option>
<option value="enviado">Enviado</option>
<option value="entregado">Entregado</option>
</select>
</div>
<div class="flex flex-col gap-2 flex-1 min-w-[200px]">
<label class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant px-1">Tipo de Cliente</label>
<div class="flex gap-2">
<button class="flex-1 py-2 text-sm font-medium bg-surface-container-highest/40 text-on-secondary-container rounded-lg border border-transparent hover:border-primary-fixed">Farmacia</button>
<button class="flex-1 py-2 text-sm font-medium bg-surface-container-highest/40 text-on-secondary-container rounded-lg border border-transparent hover:border-primary-fixed">Empresa</button>
</div>
</div>
<button class="bg-surface-container-highest text-on-secondary-container p-2.5 rounded-lg hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined">filter_list</span>
</button>
</section>
<!-- Orders Table Section -->
<div class="bg-surface-container-lowest rounded-xl shadow-xl shadow-primary/5 overflow-hidden border border-outline-variant/10">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-[11px] uppercase tracking-widest font-bold text-on-surface-variant">
<th class="px-6 py-4">ID Pedido</th>
<th class="px-6 py-4">Cliente</th>
<th class="px-6 py-4">Fecha</th>
<th class="px-6 py-4">Monto Total</th>
<th class="px-6 py-4">Método Pago</th>
<th class="px-6 py-4">Estado Envío</th>
<th class="px-6 py-4 text-right">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/10 text-sm">
<!-- Row 1 -->
<tr class="hover:bg-surface-bright transition-colors group">
<td class="px-6 py-4 font-mono font-bold text-primary">#ORD-2024-8841</td>
<td class="px-6 py-4">
<div class="flex flex-col gap-1">
<span class="font-semibold text-on-surface">Farmacias del Valle Central</span>
<span class="inline-flex w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-surface-container-highest text-on-secondary-fixed-variant">Farmacia</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">24 Oct 2023</td>
<td class="px-6 py-4 font-bold text-on-surface">$12,450.00</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-on-surface-variant text-lg">credit_card</span>
<span class="text-on-surface-variant">Transferencia</span>
</div>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-tertiary-container/10 text-on-tertiary-container border border-tertiary-container/20">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span>
                                    Entregado
                                </span>
</td>
<td class="px-6 py-4 text-right">
<button class="text-primary font-semibold hover:underline text-xs bg-primary-fixed/30 px-3 py-1.5 rounded-md">Ver Detalle</button>
</td>
</tr>
<!-- Row 2 -->
<tr class="bg-surface/50 hover:bg-surface-bright transition-colors group">
<td class="px-6 py-4 font-mono font-bold text-primary">#ORD-2024-8902</td>
<td class="px-6 py-4">
<div class="flex flex-col gap-1">
<span class="font-semibold text-on-surface">BioLab S.A. de C.V.</span>
<span class="inline-flex w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-surface-container text-on-primary-fixed-variant">Empresa</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">25 Oct 2023</td>
<td class="px-6 py-4 font-bold text-on-surface">$8,290.50</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-on-surface-variant text-lg">payments</span>
<span class="text-on-surface-variant">Crédito 30D</span>
</div>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-secondary-container/10 text-on-secondary-container border border-secondary-container/20">
<span class="w-1.5 h-1.5 rounded-full bg-secondary-container animate-pulse"></span>
                                    Enviado
                                </span>
</td>
<td class="px-6 py-4 text-right">
<button class="text-primary font-semibold hover:underline text-xs bg-primary-fixed/30 px-3 py-1.5 rounded-md">Ver Detalle</button>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-surface-bright transition-colors group">
<td class="px-6 py-4 font-mono font-bold text-primary">#ORD-2024-9011</td>
<td class="px-6 py-4">
<div class="flex flex-col gap-1">
<span class="font-semibold text-on-surface">Botica Médica Universal</span>
<span class="inline-flex w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-surface-container-highest text-on-secondary-fixed-variant">Farmacia</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">26 Oct 2023</td>
<td class="px-6 py-4 font-bold text-on-surface">$42,000.00</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-on-surface-variant text-lg">account_balance</span>
<span class="text-on-surface-variant">Transferencia</span>
</div>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary-fixed/20 text-on-primary-fixed-variant border border-primary-fixed/30">
<span class="w-1.5 h-1.5 rounded-full bg-on-primary-fixed-variant"></span>
                                    Procesando
                                </span>
</td>
<td class="px-6 py-4 text-right">
<button class="text-primary font-semibold hover:underline text-xs bg-primary-fixed/30 px-3 py-1.5 rounded-md">Ver Detalle</button>
</td>
</tr>
<!-- Row 4 -->
<tr class="bg-surface/50 hover:bg-surface-bright transition-colors group">
<td class="px-6 py-4 font-mono font-bold text-primary">#ORD-2024-9104</td>
<td class="px-6 py-4">
<div class="flex flex-col gap-1">
<span class="font-semibold text-on-surface">Red Hospitalaria Norte</span>
<span class="inline-flex w-fit px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-surface-container text-on-primary-fixed-variant">Empresa</span>
</div>
</td>
<td class="px-6 py-4 text-on-surface-variant">27 Oct 2023</td>
<td class="px-6 py-4 font-bold text-on-surface">$156,720.00</td>
<td class="px-6 py-4">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-on-surface-variant text-lg">payments</span>
<span class="text-on-surface-variant">Crédito 60D</span>
</div>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-error-container/20 text-on-error-container border border-error-container/40">
<span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                    Pendiente
                                </span>
</td>
<td class="px-6 py-4 text-right">
<button class="text-primary font-semibold hover:underline text-xs bg-primary-fixed/30 px-3 py-1.5 rounded-md">Ver Detalle</button>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination-like footer -->
<div class="px-6 py-4 bg-surface-container-low flex items-center justify-between">
<span class="text-xs text-on-surface-variant">Mostrando 10 de 1,240 pedidos</span>
<div class="flex gap-2">
<button class="p-1 rounded hover:bg-white transition-colors text-on-surface-variant">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="px-3 py-1 rounded bg-primary text-white text-xs font-bold shadow-sm">1</button>
<button class="px-3 py-1 rounded bg-white text-on-surface text-xs hover:bg-primary-fixed/20 transition-colors">2</button>
<button class="px-3 py-1 rounded bg-white text-on-surface text-xs hover:bg-primary-fixed/20 transition-colors">3</button>
<button class="p-1 rounded hover:bg-white transition-colors text-on-surface-variant">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
</div>
<!-- Cold Chain (Red Fría) Alert Section - Asymmetric Bento Component -->
<section class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
<div class="lg:col-span-2 relative overflow-hidden bg-primary-container rounded-2xl p-8 text-white shadow-2xl">
<div class="relative z-10">
<div class="flex items-center gap-2 mb-4">
<span class="material-symbols-outlined text-tertiary-fixed">ac_unit</span>
<span class="text-[10px] uppercase tracking-[0.2em] font-bold text-tertiary-fixed">Logística Crítica</span>
</div>
<h3 class="text-2xl font-bold mb-2">Monitoreo de "Red Fría"</h3>
<p class="text-on-primary-container text-sm max-w-md mb-6 leading-relaxed">3 pedidos actuales contienen productos termolábiles. El sistema está monitoreando las rutas activas para asegurar la integridad clínica.</p>
<div class="flex gap-4">
<div class="glass-panel rounded-lg p-4 flex flex-col gap-1 border border-white/10 bg-white/5">
<span class="text-xs text-white/60">Temp. Promedio</span>
<span class="text-xl font-bold">4.2°C</span>
</div>
<div class="glass-panel rounded-lg p-4 flex flex-col gap-1 border border-white/10 bg-white/5">
<span class="text-xs text-white/60">Integridad</span>
<span class="text-xl font-bold text-tertiary-fixed">100%</span>
</div>
</div>
</div>
<!-- Abstract decorative element -->
<div class="absolute -right-20 -bottom-20 w-80 h-80 bg-tertiary opacity-20 blur-3xl rounded-full"></div>
<div class="absolute right-10 top-10 w-20 h-20 bg-secondary opacity-10 blur-xl rounded-full"></div>
</div>
<div class="bg-surface-container-high rounded-2xl p-8 flex flex-col justify-center border border-outline-variant/10">
<h4 class="text-sm font-bold text-on-surface uppercase tracking-widest mb-6">Eficiencia de Entrega</h4>
<div class="relative h-4 w-full bg-surface-container rounded-full overflow-hidden mb-4">
<div class="absolute top-0 left-0 h-full w-[94%] bg-gradient-to-r from-secondary to-primary-container shadow-[0_0_10px_rgba(0,99,151,0.3)]"></div>
</div>
<div class="flex justify-between items-end">
<span class="text-3xl font-black text-primary">94%</span>
<span class="text-xs text-on-surface-variant font-medium">+2.4% vs Mes Anterior</span>
</div>
<p class="mt-4 text-xs text-on-surface-variant leading-tight">Optimización basada en la gestión automatizada de inventarios MMPharma.</p>
</div>
</section>
</main>
<?php include("../Includes/footer.php"); ?>
