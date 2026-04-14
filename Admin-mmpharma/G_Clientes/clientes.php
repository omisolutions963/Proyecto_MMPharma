<?php
$pageTitle = 'MMPharma Portal - Gestión de Clientes';
$activePage = 'clientes';
include('../includes/header.php');
include('../includes/sidebar.php');
?>

<!-- Main Content -->
<main class="ml-64 p-8 min-h-[calc(100vh-4rem)]">

    <!-- Title & Breadcrumb -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <nav class="flex items-center gap-2 text-xs font-medium text-on-surface-variant mb-2">
                <span>Management</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">Clients</span>
            </nav>
            <h2 class="text-3xl font-extrabold tracking-tight text-on-surface">Gestión de Clientes</h2>
        </div>
        <button class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-lg shadow-primary/10 flex items-center gap-2 hover:scale-[0.98] transition-transform">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            Nuevo Cliente
        </button>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border-l-4 border-primary group hover:bg-surface-container transition-colors">
            <p class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant mb-1">Total Clientes</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-on-surface">187</span>
                <span class="text-tertiary font-bold text-xs">+3.2%</span>
            </div>
            <div class="mt-4 w-full h-1 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-full bg-primary w-[75%]"></div>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border-l-4 border-tertiary-container group hover:bg-surface-container transition-colors">
            <p class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant mb-1">Activos</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-on-surface">175</span>
                <span class="material-symbols-outlined text-on-tertiary-container text-sm">check_circle</span>
            </div>
            <p class="text-[10px] text-on-tertiary-container mt-2 font-medium">93.5% del ecosistema</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border-l-4 border-secondary group hover:bg-surface-container transition-colors">
            <p class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant mb-1">Docs Pendientes</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-on-surface">8</span>
                <span class="material-symbols-outlined text-secondary text-sm">description</span>
            </div>
            <p class="text-[10px] text-on-secondary-container mt-2 font-medium">Requiere revisión</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border-l-4 border-error group hover:bg-surface-container transition-colors">
            <p class="text-[10px] uppercase font-bold tracking-widest text-on-surface-variant mb-1">Inactivos</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-on-surface">4</span>
                <span class="material-symbols-outlined text-error text-sm">person_off</span>
            </div>
            <p class="text-[10px] text-on-error-container mt-2 font-medium">Cuenta desregistrada</p>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-surface-container-low rounded-2xl overflow-hidden shadow-sm">
        <!-- Filters Bar -->
        <div class="px-8 py-6 flex flex-wrap gap-4 items-center justify-between bg-white/50 backdrop-blur-sm">
            <div class="flex gap-4 items-center">
                <div class="flex bg-surface-container-high rounded-lg p-1">
                    <button class="px-4 py-1.5 text-xs font-bold rounded-md bg-white shadow-sm text-primary">Todos</button>
                    <button class="px-4 py-1.5 text-xs font-bold text-on-surface-variant hover:text-primary transition-colors">Farmacia</button>
                    <button class="px-4 py-1.5 text-xs font-bold text-on-surface-variant hover:text-primary transition-colors">Distribuidora</button>
                </div>
            </div>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input class="pl-9 pr-4 py-2 bg-white border border-outline-variant/30 rounded-lg text-sm focus:ring-2 focus:ring-primary-fixed" placeholder="Buscar cliente..." type="text"/>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container text-on-surface-variant text-[10px] uppercase tracking-widest font-bold border-b border-outline-variant/20">
                    <tr>
                        <th class="px-8 py-4">Cliente / RFC</th>
                        <th class="px-6 py-4">Tipo</th>
                        <th class="px-6 py-4">Contacto</th>
                        <th class="px-6 py-4">Agente</th>
                        <th class="px-6 py-4">Estatus</th>
                        <th class="px-8 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <!-- Row 1 -->
                    <tr class="group hover:bg-surface-container-lowest transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">FC</div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface leading-tight">Farmacias del Centro</p>
                                    <p class="text-[11px] font-mono text-on-surface-variant uppercase">FDC930215ABC</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-surface-container-highest text-primary-container">FARMACIA</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Dr. Carlos Mendez</span>
                                <span class="text-[11px] text-on-surface-variant">c.mendez@farmcentro.mx</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-[10px] font-bold text-primary">JL</div>
                                <span class="text-xs font-medium text-on-surface">Jorge L.</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-on-tertiary-container"></span>
                                <span class="text-[11px] font-black text-on-tertiary-container uppercase tracking-wider">Activo</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="group hover:bg-surface-container-lowest transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary font-bold shrink-0">DM</div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface leading-tight">Dist. Médica Norte</p>
                                    <p class="text-[11px] font-mono text-on-surface-variant uppercase">DMN010101XYZ</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-surface-container-highest text-primary-container">DISTRIBUIDORA</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Lic. Elena Gomez</span>
                                <span class="text-[11px] text-on-surface-variant">e.gomez@distmedica.mx</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-secondary/20 flex items-center justify-center text-[10px] font-bold text-secondary">SL</div>
                                <span class="text-xs font-medium text-on-surface">Sofia L.</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-on-tertiary-container"></span>
                                <span class="text-[11px] font-black text-on-tertiary-container uppercase tracking-wider">Activo</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 3 Inactive -->
                    <tr class="group hover:bg-surface-container-lowest transition-colors opacity-80 hover:opacity-100">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-bold shrink-0">EM</div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface leading-tight">Enlace Medico del Norte</p>
                                    <p class="text-[11px] font-mono text-on-surface-variant uppercase">EMN080505RTY</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-surface-container-high text-on-surface-variant">EMPRESA</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Dr. Samuel Torres</span>
                                <span class="text-[11px] text-on-surface-variant">s.torres@enlacemedico.mx</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">CG</div>
                                <span class="text-xs font-medium text-on-surface">Carlos G.</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-error"></span>
                                <span class="text-[11px] font-black text-error uppercase tracking-wider">Inactivo</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 4 Pending -->
                    <tr class="group hover:bg-surface-container-lowest transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700 font-bold shrink-0">PF</div>
                                <div>
                                    <p class="text-sm font-bold text-on-surface leading-tight">ProFarma Center</p>
                                    <p class="text-[11px] font-mono text-on-surface-variant uppercase">PFC201111QWE</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-surface-container-highest text-primary-container">FARMACIA</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-on-surface">Dra. Monica Ruiz</span>
                                <span class="text-[11px] text-on-surface-variant">m.ruiz@profarma.mx</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-secondary/20 flex items-center justify-center text-[10px] font-bold text-secondary">SL</div>
                                <span class="text-xs font-medium text-on-surface">Sofia L.</span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                <span class="text-[11px] font-black text-secondary uppercase tracking-wider">Docs Pendientes</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                                <button class="w-8 h-8 flex items-center justify-center rounded-md bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Stats -->
        <div class="bg-surface-container-high/30 px-8 py-4 flex justify-between items-center">
            <p class="text-[10px] font-bold text-on-surface-variant">ÚLTIMA ACTUALIZACIÓN: HOY, <?= date('d/m/Y - h:i A') ?></p>
            <div class="flex gap-4">
                <button class="text-[10px] font-black text-primary uppercase hover:underline">Exportar CSV</button>
                <button class="text-[10px] font-black text-primary uppercase hover:underline">Imprimir Reporte</button>
            </div>
        </div>
    </div>

</main>

<?php include('../includes/footer.php'); ?>
