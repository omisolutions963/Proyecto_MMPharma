<?php
$pageTitle = "MMPharma Portal - Configuración y Usuarios";
$activePage = "usuarios";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-[calc(100vh-4rem)]">
<!-- Header & Breadcrumbs -->
<div class="mb-8">
<div class="flex items-center gap-2 text-xs font-medium text-on-surface-variant mb-2">
<span>Portal</span>
<span class="material-symbols-outlined text-[12px]" data-icon="chevron_right">chevron_right</span>
<span class="text-primary font-bold">Configuración y Usuarios</span>
</div>
<h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Administración del Sistema</h2>
<p class="text-on-surface-variant text-sm mt-1">Gestione los parámetros globales y el acceso del personal clínico.</p>
</div>
<!-- Bento Layout Tabs -->
<div class="flex gap-1 p-1 bg-surface-container-low rounded-xl w-fit mb-8">
<button class="px-6 py-2.5 rounded-lg text-sm font-bold bg-surface-container-lowest text-primary shadow-sm transition-all">General</button>
<button class="px-6 py-2.5 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-high transition-all">Usuarios Admin</button>
<button class="px-6 py-2.5 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-high transition-all">Roles y Permisos</button>
</div>
<div class="grid grid-cols-12 gap-8">
<!-- Left Column: Configuration Form -->
<div class="col-span-12 lg:col-span-7 space-y-8">
<section class="bg-surface-container-lowest p-8 rounded-xl shadow-sm">
<div class="flex items-center justify-between mb-8">
<div>
<h3 class="text-xl font-bold text-on-surface tracking-tight">Configuración General</h3>
<p class="text-xs text-on-surface-variant">Información de identidad corporativa y fiscal.</p>
</div>
<span class="material-symbols-outlined text-primary-container/40 text-4xl" data-icon="domain">domain</span>
</div>
<form class="space-y-6">
<div class="grid grid-cols-2 gap-6">
<div class="col-span-2">
<label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre del Negocio</label>
<input class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed" type="text" value="MMPharma Distribution S.A. de C.V."/>
</div>
<div>
<label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">RFC</label>
<input class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed" type="text" value="MMP120514PH1"/>
</div>
<div>
<label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Sede Principal</label>
<select class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed">
<option>Ciudad de México</option>
<option>Monterrey</option>
<option>Guadalajara</option>
</select>
</div>
<div class="col-span-2">
<label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Dirección Fiscal</label>
<input class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed" type="text" value="Av. Insurgentes Sur 1450, Col. Actipan, CDMX"/>
</div>
<div>
<label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Teléfono de Contacto</label>
<input class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed" type="tel" value="+52 55 1234 5678"/>
</div>
<div>
<label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Correo del Sistema</label>
<input class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed" type="email" value="contacto@mmpharma.com"/>
</div>
</div>
<div class="pt-4 flex justify-end">
<button class="px-8 py-3 bg-gradient-to-br from-primary to-primary-container text-white rounded-lg text-sm font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                                Guardar Cambios
                            </button>
</div>
</form>
</section>
</div>
<!-- Right Column: Quick Metrics & Stats -->
<div class="col-span-12 lg:col-span-5 space-y-6">
<div class="bg-surface-container-low p-6 rounded-xl border-l-4 border-primary">
<div class="flex items-center gap-4">
<div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-white">
<span class="material-symbols-outlined" data-icon="shield">shield</span>
</div>
<div>
<h4 class="font-bold text-on-surface">Estado de Seguridad</h4>
<p class="text-xs text-on-surface-variant">Protección de Datos Nivel 3 Activa</p>
</div>
</div>
</div>
<!-- Bento Card: Usage Stats -->
<div class="grid grid-cols-2 gap-4">
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm">
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1">Adm. Activos</p>
<h3 class="text-3xl font-extrabold text-primary">12</h3>
</div>
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm">
<p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1">Roles Def.</p>
<h3 class="text-3xl font-extrabold text-secondary">05</h3>
</div>
</div>
<div class="bg-primary text-white p-8 rounded-xl relative overflow-hidden">
<div class="relative z-10">
<h4 class="text-lg font-bold mb-2">Certificación Sanitaria</h4>
<p class="text-emerald-100/70 text-sm mb-4 leading-relaxed">Su licencia de operación clínica vence en 45 días. Asegúrese de actualizar su documentación.</p>
<button class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 hover:gap-3 transition-all">
                            Renovar Ahora <span class="material-symbols-outlined text-sm" data-icon="arrow_forward">arrow_forward</span>
</button>
</div>
<div class="absolute -right-4 -bottom-4 opacity-10">
<span class="material-symbols-outlined text-[120px]" data-icon="medical_services">medical_services</span>
</div>
</div>
</div>
<!-- Full Width: Users Table -->
<div class="col-span-12">
<section class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
<div class="p-8 border-b border-surface-container-low flex items-center justify-between bg-white">
<div>
<h3 class="text-xl font-bold text-on-surface tracking-tight">Usuarios Admin</h3>
<p class="text-xs text-on-surface-variant">Control de acceso granular para el personal administrativo.</p>
</div>
<button class="flex items-center gap-2 px-6 py-3 bg-surface-container-highest text-on-secondary-container rounded-lg text-sm font-bold hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-sm" data-icon="person_add">person_add</span>
                            + Agregar Usuario Admin
                        </button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low">
<th class="px-8 py-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Nombre</th>
<th class="px-8 py-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Correo</th>
<th class="px-8 py-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Rol</th>
<th class="px-8 py-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Último Acceso</th>
<th class="px-8 py-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest text-right">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-surface-container-low">
<tr class="hover:bg-surface/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">AV</div>
<span class="text-sm font-semibold text-on-surface">Alejandro Villalobos</span>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="dcbdf2aab5b0b0bdb0b3beb3af9cb1b1acb4bdaeb1bdf2bfb3b1">[email&#160;protected]</a></td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-primary-container/10 text-primary-container text-[10px] font-bold uppercase rounded-full">Admin</span>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">Hoy, 09:42 AM</td>
<td class="px-8 py-5 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-on-surface-variant hover:text-primary transition-colors">
<span class="material-symbols-outlined text-xl" data-icon="edit">edit</span>
</button>
<button class="p-2 text-on-surface-variant hover:text-error transition-colors">
<span class="material-symbols-outlined text-xl" data-icon="block">block</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-surface/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-secondary/10 text-secondary flex items-center justify-center font-bold text-xs">ER</div>
<span class="text-sm font-semibold text-on-surface">Elena Ramírez</span>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="36531844575b5f44534c765b5b465e57445b571855595b">[email&#160;protected]</a></td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-secondary-container/20 text-on-secondary-container text-[10px] font-bold uppercase rounded-full">Empleado</span>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">Ayer, 18:15 PM</td>
<td class="px-8 py-5 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-on-surface-variant hover:text-primary transition-colors">
<span class="material-symbols-outlined text-xl" data-icon="edit">edit</span>
</button>
<button class="p-2 text-on-surface-variant hover:text-error transition-colors">
<span class="material-symbols-outlined text-xl" data-icon="block">block</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-surface/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-tertiary-container/10 text-on-tertiary-container flex items-center justify-center font-bold text-xs">JM</div>
<span class="text-sm font-semibold text-on-surface">Julián Martínez</span>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="12783c7f7360667b7c7768527f7f627a73607f733c717d7f">[email&#160;protected]</a></td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-secondary-container/20 text-on-secondary-container text-[10px] font-bold uppercase rounded-full">Empleado</span>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">12 May, 2024</td>
<td class="px-8 py-5 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-on-surface-variant hover:text-primary transition-colors">
<span class="material-symbols-outlined text-xl" data-icon="edit">edit</span>
</button>
<button class="p-2 text-on-surface-variant hover:text-error transition-colors">
<span class="material-symbols-outlined text-xl" data-icon="block">block</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</section>
</div>
</div>
</main>
<?php include("../Includes/footer.php"); ?>
