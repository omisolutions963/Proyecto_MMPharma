<?php
$pageTitle = 'MMPharma Portal - Dashboard';
$activePage = 'dashboard'; // Esto marca el tab activo en el sidebar
include('../includes/header.php');
include('../includes/sidebar.php');
?>

<!-- Main Content -->
<main class="ml-64 p-8 min-h-[calc(100vh-4rem)]">

    <!-- Alerts Section -->
    <div class="mb-8">
        <div class="bg-error-container/40 border border-error/10 rounded-xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center text-error">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <div class="flex-1">
                <h4 class="text-on-error-container font-bold text-sm">Alerta de Inventario Crítico</h4>
                <p class="text-on-error-container/80 text-xs mt-0.5">8 productos se encuentran por debajo del umbral de seguridad. Se requiere reabastecimiento inmediato.</p>
            </div>
            <a href="../G_Inventario/inventario.php" class="px-4 py-1.5 bg-error text-white text-xs font-semibold rounded-md shadow-lg shadow-error/20 block text-center">Gestionar Stock</a>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/5 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-primary/5 rounded-lg text-primary">
                    <span class="material-symbols-outlined">payments</span>
                </div>
                <span class="text-xs font-bold text-tertiary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +14%
                </span>
            </div>
            <p class="text-on-surface-variant text-xs font-medium uppercase tracking-wider">Ventas del día</p>
            <h3 class="text-2xl font-extrabold text-on-surface mt-1 tracking-tight">$142,500</h3>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/5 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-secondary/5 rounded-lg text-secondary">
                    <span class="material-symbols-outlined">person_add</span>
                </div>
                <span class="text-xs font-bold text-tertiary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> +3
                </span>
            </div>
            <p class="text-on-surface-variant text-xs font-medium uppercase tracking-wider">Clientes Nuevos</p>
            <h3 class="text-2xl font-extrabold text-on-surface mt-1 tracking-tight">12</h3>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/5 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-tertiary/5 rounded-lg text-tertiary">
                    <span class="material-symbols-outlined">pending_actions</span>
                </div>
                <span class="px-2 py-0.5 bg-tertiary-container/20 text-on-tertiary-container rounded-full text-[10px] font-bold">ACCION REQUERIDA</span>
            </div>
            <p class="text-on-surface-variant text-xs font-medium uppercase tracking-wider">Solicitudes Pendientes</p>
            <h3 class="text-2xl font-extrabold text-on-surface mt-1 tracking-tight">5</h3>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/5 transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-error/5 rounded-lg text-error">
                    <span class="material-symbols-outlined">inventory</span>
                </div>
                <span class="px-2 py-0.5 bg-error-container text-error rounded-full text-[10px] font-bold">STOCK BAJO</span>
            </div>
            <p class="text-on-surface-variant text-xs font-medium uppercase tracking-wider">Productos Bajo Stock</p>
            <h3 class="text-2xl font-extrabold text-on-surface mt-1 tracking-tight">8</h3>
        </div>
    </div>

    <!-- Dashboard Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Sales Chart -->
            <section class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border border-outline-variant/5">
                <div class="flex justify-between items-end mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Ventas Semanales</h2>
                        <p class="text-sm text-on-surface-variant">Comparativa entre semana actual y anterior</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs font-medium">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-primary"></span><span>Semana Actual</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-secondary-container"></span><span>Semana Anterior</span>
                        </div>
                    </div>
                </div>
                <div class="h-64 flex items-end justify-between gap-4 px-2">
                    <?php
                    $days = ['LUN','MAR','MIE','JUE','VIE','SAB','DOM'];
                    $prev = [40, 60, 50, 70, 45, 30, 20];
                    $curr = [55, 75, 45, 90, 65, 40, 25];
                    foreach ($days as $i => $day): ?>
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <div class="w-full flex justify-center items-end gap-1 h-full group">
                            <div class="w-4 bg-secondary-container rounded-t-sm transition-all group-hover:opacity-80" style="height:<?= $prev[$i] ?>%"></div>
                            <div class="w-4 bg-primary rounded-t-sm transition-all group-hover:brightness-125" style="height:<?= $curr[$i] ?>%"></div>
                        </div>
                        <span class="text-[10px] font-bold text-on-surface-variant"><?= $day ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Orders Table -->
            <section class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/5 overflow-hidden">
                <div class="p-6 border-b border-surface-container-low flex justify-between items-center">
                    <h2 class="text-lg font-bold text-on-surface">Últimos Pedidos</h2>
                    <a href="../G_Pedidos/pedidos.php" class="text-primary text-sm font-semibold hover:underline">Ver todo el historial</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low text-on-surface-variant text-[10px] uppercase tracking-widest font-bold">
                            <tr>
                                <th class="px-6 py-4">Folio</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Monto</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="px-6 py-4 font-bold text-primary">#PH-9402</td>
                                <td class="px-6 py-4 font-medium">Farmacias del Centro S.A.</td>
                                <td class="px-6 py-4">$42,300.00</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-1 bg-tertiary-container/20 text-on-tertiary-container rounded-full text-[10px] font-bold">ENTREGADO</span></td>
                                <td class="px-6 py-4 text-on-surface-variant">Hoy, 10:24 AM</td>
                            </tr>
                            <tr class="bg-surface-container-lowest hover:bg-surface-container-low transition-colors">
                                <td class="px-6 py-4 font-bold text-primary">#PH-9401</td>
                                <td class="px-6 py-4 font-medium">Hospital General Metropolitano</td>
                                <td class="px-6 py-4">$156,000.00</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-1 bg-secondary-container/20 text-on-secondary-container rounded-full text-[10px] font-bold">EN RUTA</span></td>
                                <td class="px-6 py-4 text-on-surface-variant">Hoy, 09:15 AM</td>
                            </tr>
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="px-6 py-4 font-bold text-primary">#PH-9399</td>
                                <td class="px-6 py-4 font-medium">Clínica Santa Fe</td>
                                <td class="px-6 py-4">$12,800.00</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-1 bg-primary-container/20 text-on-primary-container rounded-full text-[10px] font-bold">PROCESANDO</span></td>
                                <td class="px-6 py-4 text-on-surface-variant">Ayer, 06:45 PM</td>
                            </tr>
                            <tr class="bg-surface-container-lowest hover:bg-surface-container-low transition-colors">
                                <td class="px-6 py-4 font-bold text-primary">#PH-9398</td>
                                <td class="px-6 py-4 font-medium">Distribuidora Médica Norte</td>
                                <td class="px-6 py-4">$89,200.00</td>
                                <td class="px-6 py-4"><span class="px-2.5 py-1 bg-tertiary-container/20 text-on-tertiary-container rounded-full text-[10px] font-bold">ENTREGADO</span></td>
                                <td class="px-6 py-4 text-on-surface-variant">Ayer, 04:30 PM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-8">
            <!-- Best Sellers -->
            <section class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/5">
                <h2 class="text-lg font-bold text-on-surface mb-6">Productos más vendidos</h2>
                <div class="space-y-5">
                    <?php
                    $products = [
                        ['name' => 'Amoxicilina 500mg',    'category' => 'Antibiótico',       'units' => 240, 'rank' => 1],
                        ['name' => 'Insulina Glargina',    'category' => 'Red Fría',           'units' => 185, 'rank' => 2],
                        ['name' => 'Paracetamol 1g',       'category' => 'Analgésico',         'units' => 150, 'rank' => 3],
                        ['name' => 'Ibuprofeno Suspensión','category' => 'Antiinflamatorio',   'units' => 98,  'rank' => 4],
                        ['name' => 'Losartán 50mg',        'category' => 'Antihipertensivo',   'units' => 72,  'rank' => 5],
                    ];
                    foreach ($products as $p): ?>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-surface-container-low flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary">medication</span>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-bold text-on-surface truncate"><?= $p['name'] ?></p>
                            <p class="text-xs text-on-surface-variant"><?= $p['category'] ?> · <?= $p['units'] ?> unidades</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-tertiary">#<?= $p['rank'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="export_best_sellers.php" class="block text-center w-full mt-8 py-3 bg-surface-container-low text-primary text-xs font-bold rounded-lg hover:bg-surface-container-high transition-colors">
                    Descargar Reporte Completo
                </a>
            </section>

            <!-- Status Card -->
            <section class="bg-primary-container text-white p-6 rounded-xl shadow-lg shadow-primary/20 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-emerald-400">verified</span>
                        <span class="text-xs font-bold tracking-widest uppercase opacity-80">Estado Operativo</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Sistemas 100%</h3>
                    <p class="text-xs text-blue-100/70 mb-6">Todos los almacenes y la red de transporte se encuentran operando bajo los estándares de MMPharma.</p>
                    <div class="flex items-center gap-2">
                        <div class="flex -space-x-2">
                            <div class="w-6 h-6 rounded-full border-2 border-primary-container bg-emerald-500 flex items-center justify-center text-[10px] font-bold">AL1</div>
                            <div class="w-6 h-6 rounded-full border-2 border-primary-container bg-emerald-500 flex items-center justify-center text-[10px] font-bold">AL2</div>
                            <div class="w-6 h-6 rounded-full border-2 border-primary-container bg-emerald-500 flex items-center justify-center text-[10px] font-bold">CD</div>
                        </div>
                        <span class="text-[10px] font-medium text-blue-100/50">Nodos Activos</span>
                    </div>
                </div>
                <div class="absolute -right-12 -bottom-12 w-32 h-32 bg-white/5 rounded-full blur-3xl"></div>
            </section>
        </div>
    </div>

<?php include('../includes/footer.php'); ?>
