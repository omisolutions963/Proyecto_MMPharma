<?php
$pageTitle = "MMPharma Portal - Solicitudes de Registro";
$activePage = "solicitudes";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 p-8 space-y-8">
<!-- Header Section -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div>
<nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant mb-2">
<span>Portal</span>
<span class="material-symbols-outlined text-[10px]">chevron_right</span>
<span>Management</span>
<span class="material-symbols-outlined text-[10px]">chevron_right</span>
</nav>
<h2 class="text-3xl font-extrabold tracking-tight text-on-surface">Solicitudes de Registro</h2>
</div>
<div class="flex gap-3">
<button class="px-4 py-2 bg-surface-container-highest text-on-secondary-container rounded-xl text-xs font-bold hover:bg-surface-variant transition-colors">
                        Export Report
                    </button>
<button onclick="mockAction('Registro Manual', 'Abriendo formulario para ingreso manual de solicitud.', 'info')" class="px-6 py-2 bg-gradient-to-br from-primary to-primary-container text-white rounded-xl text-xs font-bold shadow-lg shadow-primary/10 hover:brightness-110 transition-all">
                        Manual Registration
                    </button>
</div>
</div>
<!-- KPI Grid: Bento Style -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
<div class="bg-surface-container-low p-6 rounded-xl relative overflow-hidden group">
<div class="relative z-10">
<p class="text-xs font-bold text-on-surface-variant tracking-wider uppercase mb-1">Pending Requests</p>
<h3 class="text-4xl font-black text-primary">4</h3>
<p class="text-[10px] text-tertiary font-bold mt-2 flex items-center gap-1">
<span class="material-symbols-outlined text-xs">trending_up</span>
                            +2 Since yesterday
                        </p>
</div>
<span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl text-primary/5 group-hover:scale-110 transition-transform">pending</span>
</div>
<div class="bg-surface-container-low p-6 rounded-xl relative overflow-hidden group">
<div class="relative z-10">
<p class="text-xs font-bold text-on-surface-variant tracking-wider uppercase mb-1">In Review</p>
<h3 class="text-4xl font-black text-secondary">2</h3>
<p class="text-[10px] text-on-surface-variant mt-2">Active compliance audits</p>
</div>
<span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl text-secondary/5 group-hover:scale-110 transition-transform">fact_check</span>
</div>
<div class="bg-surface-container-low p-6 rounded-xl relative overflow-hidden group border-l-4 border-error/20">
<div class="relative z-10">
<p class="text-xs font-bold text-on-surface-variant tracking-wider uppercase mb-1">Rejected this month</p>
<h3 class="text-4xl font-black text-error">1</h3>
<p class="text-[10px] text-on-surface-variant mt-2">Reason: Insufficient RFC data</p>
</div>
<span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl text-error/5 group-hover:scale-110 transition-transform">block</span>
</div>
</div>
<!-- Table Section: Clinical Tonal Layering -->
<div class="bg-surface-container-lowest rounded-2xl shadow-xl shadow-primary/5 overflow-hidden">
<div class="p-6 flex items-center justify-between bg-surface-container-low/50">
<h4 class="text-sm font-bold text-on-surface tracking-tight">Recent Applications</h4>
<div class="flex gap-2">
<button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined text-sm">filter_list</span>
</button>
<button class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined text-sm">sort</span>
</button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low">
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Applicant</th>
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Business Type</th>
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Email</th>
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Request Date</th>
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest text-center">Docs Status</th>
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Decision Status</th>
<th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest"></th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container">
<!-- Row 1 -->
<tr class="hover:bg-surface-container-low/30 transition-colors group">
<td class="px-6 py-5">
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">Farmacias Similares del Centro</span>
<span class="text-[10px] font-medium text-on-surface-variant opacity-60">FSC890120-X12</span>
</div>
</td>
<td class="px-6 py-5">
<span class="px-3 py-1 bg-tertiary-container/10 text-on-tertiary-container text-[10px] font-bold rounded-full uppercase">Farmacia</span>
</td>
<td class="px-6 py-5 text-xs text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="88faedefe1fbfcfae7fbc8fbe1e5e1ebede6fcfae7a6e5f0">[email&#160;protected]</a></td>
<td class="px-6 py-5 text-xs text-on-surface-variant">Oct 24, 2023</td>
<td class="px-6 py-5 text-center">
<span class="inline-flex items-center gap-1 text-xs font-bold text-tertiary">
                                        5/5 <span class="material-symbols-outlined text-[14px]">check_circle</span>
</span>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-secondary"></span>
<span class="text-xs font-semibold text-secondary">En Revisión</span>
</div>
</td>
<td class="px-6 py-5 text-right">
<button onclick="mockAction('Revisar Solicitud', 'Abriendo panel de revisión de documentos y aprobación...', 'info')" class="text-xs font-bold text-primary group-hover:underline underline-offset-4 flex items-center justify-end gap-1">
                                        Revisar <span class="material-symbols-outlined text-sm">arrow_forward</span>
</button>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-surface-container-low/30 transition-colors group">
<td class="px-6 py-5">
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">MedLogistics México S.A.</span>
<span class="text-[10px] font-medium text-on-surface-variant opacity-60">MLM150612-QA5</span>
</div>
</td>
<td class="px-6 py-5">
<span class="px-3 py-1 bg-secondary-container/10 text-on-secondary-container text-[10px] font-bold rounded-full uppercase">Distribuidora</span>
</td>
<td class="px-6 py-5 text-xs text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="583b37362c393b2c3718353d3c34373f763520">[email&#160;protected]</a></td>
<td class="px-6 py-5 text-xs text-on-surface-variant">Oct 23, 2023</td>
<td class="px-6 py-5 text-center">
<span class="inline-flex items-center gap-1 text-xs font-bold text-tertiary">
                                        5/5 <span class="material-symbols-outlined text-[14px]">check_circle</span>
</span>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-primary-container"></span>
<span class="text-xs font-semibold text-primary">Pendiente</span>
</div>
</td>
<td class="px-6 py-5 text-right">
<button class="text-xs font-bold text-primary group-hover:underline underline-offset-4 flex items-center justify-end gap-1">
                                        Revisar <span class="material-symbols-outlined text-sm">arrow_forward</span>
</button>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-surface-container-low/30 transition-colors group">
<td class="px-6 py-5">
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">PharmaCorp Innovations</span>
<span class="text-[10px] font-medium text-on-surface-variant opacity-60">PCI121201-B33</span>
</div>
</td>
<td class="px-6 py-5">
<span class="px-3 py-1 bg-primary/5 text-primary text-[10px] font-bold rounded-full uppercase">Empresa</span>
</td>
<td class="px-6 py-5 text-xs text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="2140454c484f61514940534c40424e53510f424e4c">[email&#160;protected]</a></td>
<td class="px-6 py-5 text-xs text-on-surface-variant">Oct 22, 2023</td>
<td class="px-6 py-5 text-center">
<span class="inline-flex items-center gap-1 text-xs font-bold text-on-surface-variant">
                                        3/5 <span class="material-symbols-outlined text-[14px]">pending</span>
</span>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-primary-container"></span>
<span class="text-xs font-semibold text-primary">Pendiente</span>
</div>
</td>
<td class="px-6 py-5 text-right">
<button class="text-xs font-bold text-primary group-hover:underline underline-offset-4 flex items-center justify-end gap-1">
                                        Revisar <span class="material-symbols-outlined text-sm">arrow_forward</span>
</button>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-surface-container-low/30 transition-colors group">
<td class="px-6 py-5">
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">Droguería San Juan</span>
<span class="text-[10px] font-medium text-on-surface-variant opacity-60">DSJ050510-LK9</span>
</div>
</td>
<td class="px-6 py-5">
<span class="px-3 py-1 bg-tertiary-container/10 text-on-tertiary-container text-[10px] font-bold rounded-full uppercase">Farmacia</span>
</td>
<td class="px-6 py-5 text-xs text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="0a607f6b644a6e78656d7f6f78636b7960246772">[email&#160;protected]</a></td>
<td class="px-6 py-5 text-xs text-on-surface-variant">Oct 20, 2023</td>
<td class="px-6 py-5 text-center">
<span class="inline-flex items-center gap-1 text-xs font-bold text-tertiary">
                                        5/5 <span class="material-symbols-outlined text-[14px]">check_circle</span>
</span>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-2">
<span class="w-2 h-2 rounded-full bg-secondary"></span>
<span class="text-xs font-semibold text-secondary">En Revisión</span>
</div>
</td>
<td class="px-6 py-5 text-right">
<button class="text-xs font-bold text-primary group-hover:underline underline-offset-4 flex items-center justify-end gap-1">
                                        Revisar <span class="material-symbols-outlined text-sm">arrow_forward</span>
</button>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-6 bg-surface-container-low/50 flex items-center justify-between text-xs text-on-surface-variant font-medium">
<p>Showing 4 of 7 pending requests</p>
<div class="flex gap-1">
<button class="w-8 h-8 flex items-center justify-center rounded bg-white text-on-surface shadow-sm border border-outline-variant/10">1</button>
<button class="w-8 h-8 flex items-center justify-center rounded hover:bg-white transition-all">2</button>
<button class="w-8 h-8 flex items-center justify-center rounded hover:bg-white transition-all"><span class="material-symbols-outlined text-xs">chevron_right</span></button>
</div>
</div>
</div>
<!-- "Red Fría" / Compliance Glassmorphic Alert (Asymmetric Overlay Style) -->
<div class="relative bg-tertiary-container text-white p-8 rounded-2xl overflow-hidden shadow-2xl shadow-tertiary-container/20">
<div class="absolute top-0 right-0 w-64 h-64 bg-tertiary/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
<div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
<div class="flex items-start gap-4">
<div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xl flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-tertiary-fixed">shield</span>
</div>
<div>
<h5 class="text-lg font-bold">Cold Chain Compliance Update</h5>
<p class="text-sm opacity-80 max-w-md leading-relaxed mt-1">New registration requirements for distributors specializing in temperature-sensitive pharmaceuticals are now active. Ensure all "Red Fría" certifications are up to date.</p>
</div>
</div>
<button onclick="mockAction('Requisitos Red Fría', 'Mostrando checklist de cumplimiento para cadena de frío.', 'info')" class="px-6 py-3 bg-white text-tertiary font-black text-xs rounded-xl uppercase tracking-widest hover:scale-105 transition-transform">
    Ver Requisitos
</button>
</div>
</div>
</div>
</main>
<?php include("../Includes/footer.php"); ?>
