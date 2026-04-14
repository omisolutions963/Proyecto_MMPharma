<?php
$pageTitle = "MMPharma Portal - Gestión de Inventario";
$activePage = "inventario";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 pt-24 px-8 pb-12">
<!-- Action Required Alert Banner -->
<div class="mb-8 flex items-center gap-4 bg-error-container/40 p-4 rounded-xl border-l-4 border-error">
<span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">warning</span>
<div class="flex-1">
<h3 class="text-on-error-container font-bold text-sm">Acción Requerida: Umbral Crítico Detectado</h3>
<p class="text-on-error-container/80 text-xs mt-1">12 productos han cruzado el umbral mínimo de existencias. Se recomienda reposición inmediata para evitar interrupciones.</p>
</div>
<button class="bg-error text-white text-xs px-4 py-2 rounded-lg font-bold hover:bg-error/90 transition-colors">Revisar Alertas</button>
</div>
<!-- KPI Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
<div class="bg-surface-container-lowest clinical-shadow p-6 rounded-2xl flex items-center justify-between">
<div>
<p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Total de productos</p>
<h2 class="text-3xl font-extrabold tracking-tight text-on-surface">1,740</h2>
<div class="flex items-center gap-1 mt-2 text-tertiary text-xs font-medium">
<span class="material-symbols-outlined text-sm">trending_up</span>
<span>+4.2% este mes</span>
</div>
</div>
<div class="w-12 h-12 bg-primary-fixed-dim/30 rounded-xl flex items-center justify-center text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory</span>
</div>
</div>
<div class="bg-surface-container-lowest clinical-shadow p-6 rounded-2xl flex items-center justify-between">
<div>
<p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Unidades en Red Fría</p>
<h2 class="text-3xl font-extrabold tracking-tight text-on-surface">320</h2>
<div class="flex items-center gap-1 mt-2 text-secondary text-xs font-medium">
<span class="material-symbols-outlined text-sm">thermostat</span>
<span>Temperatura Estable: 4.2°C</span>
</div>
</div>
<div class="w-12 h-12 bg-tertiary-fixed/20 rounded-xl flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">ac_unit</span>
</div>
</div>
<div class="bg-surface-container-lowest clinical-shadow p-6 rounded-2xl flex items-center justify-between border-b-4 border-error/20">
<div>
<p class="text-on-surface-variant text-xs font-semibold uppercase tracking-wider mb-1">Alertas de Stock Crítico</p>
<h2 class="text-3xl font-extrabold tracking-tight text-error">12</h2>
<div class="flex items-center gap-1 mt-2 text-error text-xs font-medium">
<span class="material-symbols-outlined text-sm">notification_important</span>
<span>Requiere atención</span>
</div>
</div>
<div class="w-12 h-12 bg-error-container rounded-xl flex items-center justify-center text-error">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">report_problem</span>
</div>
</div>
</div>
<!-- Filters Section -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-6 bg-surface-container-low p-4 rounded-xl">
<div class="flex items-center gap-4 flex-1">
<div class="flex flex-col gap-1">
<label class="text-[10px] font-bold text-on-surface-variant uppercase ml-1">Almacén</label>
<select class="bg-surface-container-lowest border-none rounded-lg text-sm px-4 py-2 focus:ring-1 focus:ring-primary w-48">
<option>Central Ciudad de México</option>
<option>Norte - Monterrey</option>
<option>Occidente - Guadalajara</option>
</select>
</div>
<div class="flex flex-col gap-1">
<label class="text-[10px] font-bold text-on-surface-variant uppercase ml-1">Categoría</label>
<select class="bg-surface-container-lowest border-none rounded-lg text-sm px-4 py-2 focus:ring-1 focus:ring-primary w-48">
<option>Todos los fármacos</option>
<option>Antibióticos</option>
<option>Vacunas (Red Fría)</option>
<option>Analgésicos</option>
</select>
</div>
<div class="flex flex-col gap-1">
<label class="text-[10px] font-bold text-on-surface-variant uppercase ml-1">Fecha</label>
<div class="flex items-center bg-surface-container-lowest rounded-lg px-3 py-2 gap-2 text-sm">
<span class="material-symbols-outlined text-sm text-on-surface-variant">calendar_month</span>
<span>Últimos 30 días</span>
<span class="material-symbols-outlined text-sm text-on-surface-variant">expand_more</span>
</div>
</div>
</div>
<div class="flex items-center gap-2 self-end">
<button class="p-2 bg-surface-container-lowest text-on-surface-variant rounded-lg hover:bg-surface-container-highest transition-colors">
<span class="material-symbols-outlined">filter_list</span>
</button>
<button class="bg-primary text-white px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-symbols-outlined text-sm">download</span>
                    Exportar Reporte
                </button>
</div>
</div>
<div class="grid grid-cols-12 gap-8">
<!-- Inventory Table Section -->
<div class="col-span-12 xl:col-span-8">
<div class="bg-surface-container-lowest rounded-2xl clinical-shadow overflow-hidden">
<div class="px-6 py-4 flex items-center justify-between border-b border-surface-container">
<h3 class="text-lg font-bold text-on-surface">Inventario Detallado</h3>
<span class="text-xs text-on-surface-variant font-medium">Mostrando 124 productos</span>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-on-surface-variant">
<th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider">Producto</th>
<th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider">SKU</th>
<th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider">Categoría</th>
<th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-center">Stock Actual</th>
<th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider">Estado</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container">
<!-- Row 1 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high overflow-hidden">
<img alt="Insulina Glargina" class="w-full h-full object-cover" data-alt="close up of pharmaceutical medicine bottle on a white lab surface with clean medical lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAL3NJUVpHwakrpvD9ypzkQfI7mHBo0keAhUBbFlttEx1dqavx13lOoq1e5nuYb3P6Wgu8NTBDdmD7Mfyyvc0e7YxNNvTsLK1W5BTBq04XIFbiLMc5YO7H0yeEjEm409IvsmAofr0vrUsiQMULl_bi96hCRre2YP7jF3impdmg20wpNDWAg7zET_3BXtVxjqR9rNTuQ9JSD3nkZxnbrdjdAhX4SckhukNKtt6IRQmXmIH2cgCgW4KT_K-cscNGa4HIn38oDXRhLaY"/>
</div>
<div>
<p class="text-sm font-bold text-on-surface">Insulina Glargina</p>
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-[10px] text-tertiary-container">ac_unit</span>
<span class="text-[9px] font-bold text-tertiary-container uppercase tracking-tight">Red Fría</span>
</div>
</div>
</div>
</td>
<td class="px-6 py-4 text-xs font-mono text-on-surface-variant">IGL-2024-042</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-surface-container text-on-primary-container rounded text-[10px] font-bold">Endocrinología</span>
</td>
<td class="px-6 py-4">
<div class="flex flex-col items-center gap-1">
<span class="text-sm font-bold text-on-surface">45 unidades</span>
<div class="w-24 h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-error w-[15%]"></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5 text-error">
<div class="w-2 h-2 rounded-full bg-error animate-pulse"></div>
<span class="text-[10px] font-bold uppercase">Crítico</span>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high overflow-hidden">
<img alt="Paracetamol 500mg" class="w-full h-full object-cover" data-alt="white medical pills scattered on a sterile white background with soft shadows" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDPO5nUjV7YKs9l-CSha4f6CYkPtg0Qy5Wi7WSMhK7arf_U-gnXuUQTDOMsUEaLJuKA_JYfMf0RO-fJPZj-gkd3lx5mE0y8vOeaa3vVQHADJTxKcPHudMB2n5BM9zP4yDCSp-86BvXbPlw1y_TRX4TsLkylBeIWkJCNuytkBQx0VkPPLCEjlH_wB0WgHoAQPfyjcj59FcABN5d6BcW-WQhaLLfDaoO-oRq5_erdCBX8BpcVlGdewLdo7hziZpL3p9S5nLAFpbHaMkM"/>
</div>
<div>
<p class="text-sm font-bold text-on-surface">Paracetamol 500mg</p>
<p class="text-[9px] font-bold text-on-surface-variant uppercase tracking-tight">Genérico</p>
</div>
</div>
</td>
<td class="px-6 py-4 text-xs font-mono text-on-surface-variant">PAR-500-GEN</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-surface-container text-on-primary-container rounded text-[10px] font-bold">Analgésicos</span>
</td>
<td class="px-6 py-4">
<div class="flex flex-col items-center gap-1">
<span class="text-sm font-bold text-on-surface">1,200 unidades</span>
<div class="w-24 h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-tertiary-fixed-dim w-[85%]"></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5 text-tertiary">
<div class="w-2 h-2 rounded-full bg-tertiary"></div>
<span class="text-[10px] font-bold uppercase">Óptimo</span>
</div>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high overflow-hidden">
<img alt="Amoxicilina Suspensión" class="w-full h-full object-cover" data-alt="pharmacist hands preparing medicine bottle in a bright modern pharmacy laboratory" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQ7MelhpaFUm3MVTv8bYApB8uCsEOT33oB6Fy7s54DlTqV7QfO12zA2S9MAycd90ToyLUrd9IvN1NSQa5M59PAW8giG30boSKUeNSedf3s733a-uQRtohYl410W3FKorop4Fd3d4r1LqnlqRGcXMFaP5P3DMEljq-Qjk6HFyqA5OEfBTDJ3O0fpz49Ew7RRHzb5zDo3AgOCcFTOGHzLYC2Wizo2CjJZrW59vJztkEVsZm0pDQU_WbxyZ_7q_4hKMOgx3KsiRgfxOw"/>
</div>
<div>
<p class="text-sm font-bold text-on-surface">Amoxicilina Suspensión</p>
<p class="text-[9px] font-bold text-on-surface-variant uppercase tracking-tight">Antibiótico</p>
</div>
</div>
</td>
<td class="px-6 py-4 text-xs font-mono text-on-surface-variant">AMX-SUS-250</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-surface-container text-on-primary-container rounded text-[10px] font-bold">Pediatría</span>
</td>
<td class="px-6 py-4">
<div class="flex flex-col items-center gap-1">
<span class="text-sm font-bold text-on-surface">0 unidades</span>
<div class="w-24 h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-error/20 w-0"></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5 text-on-surface-variant/40">
<div class="w-2 h-2 rounded-full bg-on-surface-variant/40"></div>
<span class="text-[10px] font-bold uppercase">Agotado</span>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-surface-container-high overflow-hidden">
<img alt="Lidocaína 2%" class="w-full h-full object-cover" data-alt="hospital supplies medical ampoules on a metal tray in surgery room setting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCvX3HE0jpLZzqd9AiRj_ZP_U-ILlc3dla-CNDnT7Fjf6HVl2RqjxZtDpxHuopjUgykb4B08jGJmKA3rPr66kKqoKtFGhPIFa49rO4evJl9JJHU7rAzyIrJR1ey-BXRg4vP_vuAN5m2Cp7vbTD1kzikTg7wE2Dcwh8VRvJAv09bbGt8Muyt43px7wtPAK6utn9uxZuuD4eHr09r1-IqvuRnBbB8Ax3CdBmP45UNHdt4q1pva-n90X1siTSGvvWDzO0YUrberMLMdO0"/>
</div>
<div>
<p class="text-sm font-bold text-on-surface">Lidocaína 2%</p>
<p class="text-[9px] font-bold text-on-surface-variant uppercase tracking-tight">Anestésico</p>
</div>
</div>
</td>
<td class="px-6 py-4 text-xs font-mono text-on-surface-variant">LID-SOL-002</td>
<td class="px-6 py-4">
<span class="px-2 py-1 bg-surface-container text-on-primary-container rounded text-[10px] font-bold">Anestesiología</span>
</td>
<td class="px-6 py-4">
<div class="flex flex-col items-center gap-1">
<span class="text-sm font-bold text-on-surface">85 unidades</span>
<div class="w-24 h-1.5 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-secondary-container w-[40%]"></div>
</div>
</div>
</td>
<td class="px-6 py-4">
<div class="flex items-center gap-1.5 text-secondary">
<div class="w-2 h-2 rounded-full bg-secondary"></div>
<span class="text-[10px] font-bold uppercase">Precaución</span>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-surface-container-low/30 flex items-center justify-between">
<button class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
<span class="material-symbols-outlined text-sm">chevron_left</span> Anterior
                        </button>
<div class="flex items-center gap-2">
<button class="w-6 h-6 rounded bg-primary text-white text-[10px] font-bold">1</button>
<button class="w-6 h-6 rounded hover:bg-surface-container text-on-surface text-[10px] font-bold">2</button>
<button class="w-6 h-6 rounded hover:bg-surface-container text-on-surface text-[10px] font-bold">3</button>
</div>
<button class="text-xs font-bold text-primary flex items-center gap-1 hover:underline">
                            Siguiente <span class="material-symbols-outlined text-sm">chevron_right</span>
</button>
</div>
</div>
</div>
<!-- Recent Movements Section -->
<div class="col-span-12 xl:col-span-4">
<div class="bg-surface-container-lowest rounded-2xl clinical-shadow overflow-hidden flex flex-col h-full">
<div class="px-6 py-5 border-b border-surface-container">
<h3 class="text-lg font-bold text-on-surface">Movimientos Recientes</h3>
<p class="text-xs text-on-surface-variant">Últimas 24 horas</p>
</div>
<div class="p-6 space-y-6 overflow-y-auto max-h-[600px]">
<!-- Movement 1 -->
<div class="flex gap-4 relative">
<div class="absolute left-4 top-10 bottom-[-24px] w-0.5 bg-surface-container"></div>
<div class="z-10 w-9 h-9 rounded-full bg-tertiary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-tertiary text-lg">login</span>
</div>
<div class="flex-1">
<div class="flex items-center justify-between mb-1">
<span class="text-[10px] font-bold uppercase text-tertiary bg-tertiary/5 px-2 py-0.5 rounded">Entrada</span>
<span class="text-[10px] text-on-surface-variant font-medium">Hace 15 min</span>
</div>
<p class="text-sm font-bold text-on-surface">Vacuna Influenza Quad</p>
<p class="text-xs text-on-surface-variant">+500 unidades - Lote V23-01</p>
<div class="flex items-center gap-2 mt-2">
<div class="w-5 h-5 rounded-full bg-surface-container-highest flex items-center justify-center text-[8px] font-bold">RV</div>
<span class="text-[10px] text-on-surface-variant">Responsable: Ricardo V.</span>
</div>
</div>
</div>
<!-- Movement 2 -->
<div class="flex gap-4 relative">
<div class="absolute left-4 top-10 bottom-[-24px] w-0.5 bg-surface-container"></div>
<div class="z-10 w-9 h-9 rounded-full bg-error/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-error text-lg">logout</span>
</div>
<div class="flex-1">
<div class="flex items-center justify-between mb-1">
<span class="text-[10px] font-bold uppercase text-error bg-error/5 px-2 py-0.5 rounded">Salida</span>
<span class="text-[10px] text-on-surface-variant font-medium">Hace 2 horas</span>
</div>
<p class="text-sm font-bold text-on-surface">Omeprazol 20mg</p>
<p class="text-xs text-on-surface-variant">-240 unidades - Pedido #8821</p>
<div class="flex items-center gap-2 mt-2">
<div class="w-5 h-5 rounded-full bg-surface-container-highest flex items-center justify-center text-[8px] font-bold">ML</div>
<span class="text-[10px] text-on-surface-variant">Responsable: María L.</span>
</div>
</div>
</div>
<!-- Movement 3 -->
<div class="flex gap-4 relative">
<div class="absolute left-4 top-10 bottom-[-24px] w-0.5 bg-surface-container"></div>
<div class="z-10 w-9 h-9 rounded-full bg-secondary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-secondary text-lg">edit_note</span>
</div>
<div class="flex-1">
<div class="flex items-center justify-between mb-1">
<span class="text-[10px] font-bold uppercase text-secondary bg-secondary/5 px-2 py-0.5 rounded">Ajuste</span>
<span class="text-[10px] text-on-surface-variant font-medium">Hace 5 horas</span>
</div>
<p class="text-sm font-bold text-on-surface">Alcohol Etílico 70%</p>
<p class="text-xs text-on-surface-variant">-15 unidades - Merma por daño</p>
<div class="flex items-center gap-2 mt-2">
<div class="w-5 h-5 rounded-full bg-surface-container-highest flex items-center justify-center text-[8px] font-bold">JS</div>
<span class="text-[10px] text-on-surface-variant">Responsable: Jorge S.</span>
</div>
</div>
</div>
<!-- Movement 4 -->
<div class="flex gap-4 relative">
<div class="z-10 w-9 h-9 rounded-full bg-tertiary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-tertiary text-lg">login</span>
</div>
<div class="flex-1">
<div class="flex items-center justify-between mb-1">
<span class="text-[10px] font-bold uppercase text-tertiary bg-tertiary/5 px-2 py-0.5 rounded">Entrada</span>
<span class="text-[10px] text-on-surface-variant font-medium">Hace 8 horas</span>
</div>
<p class="text-sm font-bold text-on-surface">Ketorolaco 10mg</p>
<p class="text-xs text-on-surface-variant">+1,000 unidades - Reabastecimiento</p>
<div class="flex items-center gap-2 mt-2">
<div class="w-5 h-5 rounded-full bg-surface-container-highest flex items-center justify-center text-[8px] font-bold">RV</div>
<span class="text-[10px] text-on-surface-variant">Responsable: Ricardo V.</span>
</div>
</div>
</div>
</div>
<div class="mt-auto p-4 border-t border-surface-container">
<button class="w-full text-center py-2 text-xs font-bold text-primary hover:bg-primary-fixed/20 rounded-lg transition-colors">
                            Ver historial completo
                        </button>
</div>
</div>
</div>
</div>
<!-- Asymmetric Detail Cards (Contextual) -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
<div class="lg:col-span-2 bg-gradient-to-br from-primary to-primary-container p-6 rounded-2xl text-white relative overflow-hidden">
<div class="relative z-10">
<h4 class="text-xl font-bold mb-2">Optimización de Stock IA</h4>
<p class="text-sm text-white/80 mb-6 max-w-sm">Basado en el historial de demanda, se sugiere aumentar el stock de Analgésicos un 15% para el próximo trimestre.</p>
<button class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-6 py-2 rounded-xl text-sm font-bold transition-all">Ver Proyecciones</button>
</div>
<span class="material-symbols-outlined absolute right-[-20px] bottom-[-20px] text-[160px] text-white/5 pointer-events-none">auto_awesome</span>
</div>
<div class="bg-surface-container-lowest clinical-shadow p-6 rounded-2xl border-l-4 border-secondary">
<span class="material-symbols-outlined text-secondary mb-4" style="font-variation-settings: 'FILL' 1;">thermostat_auto</span>
<h4 class="text-sm font-bold text-on-surface">Red Fría: OK</h4>
<p class="text-xs text-on-surface-variant mt-2">Sensores de almacén central reportan 3.8°C - 4.5°C constante en las últimas 72h.</p>
</div>
<div class="bg-surface-container-lowest clinical-shadow p-6 rounded-2xl border-l-4 border-tertiary">
<span class="material-symbols-outlined text-tertiary mb-4" style="font-variation-settings: 'FILL' 1;">verified</span>
<h4 class="text-sm font-bold text-on-surface">Certificación COFEPRIS</h4>
<p class="text-xs text-on-surface-variant mt-2">Próxima auditoría programada: <br/><span class="font-bold">14 de Octubre, 2024</span>.</p>
</div>
</div>
</main>
<!-- FAB for quick action -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-primary text-white rounded-full clinical-shadow flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-50">
<span class="material-symbols-outlined" style="font-size: 28px;">add_box</span>
</button>
<?php include("../Includes/footer.php"); ?>
