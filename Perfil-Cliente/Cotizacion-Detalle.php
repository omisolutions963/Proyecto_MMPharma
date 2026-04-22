<?php
$pageTitle  = 'MMPharma Portal - Detalle de Cotización';
$activePage = 'cotizaciones';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Top Nav -->
    <a href="Cotizaciones.php" class="inline-flex items-center gap-2 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors mb-6">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver a Mis Cotizaciones
    </a>

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-reveal">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Cotización #COT-2024-0892</h1>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-secondary/10 hover:bg-secondary/20 text-secondary text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> PDF
            </button>
            <button class="px-4 py-2 bg-tertiary/10 hover:bg-tertiary/20 text-tertiary text-sm font-bold rounded-xl transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">table_chart</span> Excel
            </button>
            <button class="px-5 py-2 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-bold rounded-xl transition-colors flex items-center gap-2 shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-[18px]">content_copy</span> Duplicar
            </button>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 animate-reveal delay-100">
        
        <!-- Document Area (Left 3 cols) -->
        <div class="xl:col-span-3 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 lg:p-12 shadow-sm">
            
            <!-- Doc Header -->
            <div class="flex flex-col md:flex-row justify-between items-start mb-12 border-b border-outline-variant/30 pb-8">
                <div class="flex items-center gap-4 mb-6 md:mb-0">
                    <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center text-white font-black text-2xl tracking-tighter">
                        MM
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-white">MMPharma S.A. de C.V.</h2>
                        <p class="text-xs text-on-surface-variant mb-1">RFC: <span class="text-white">DMM1784204T6</span></p>
                        <p class="text-[10px] text-on-surface-variant max-w-xs leading-relaxed">Av. Insurgentes Sur 1200, Col. Extremadura Insurgentes, CP 03740, CDMX, México.</p>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <div class="inline-block bg-surface-container-high text-white text-sm font-black px-4 py-1.5 rounded-lg uppercase tracking-widest mb-3">Cotización</div>
                    <p class="text-xs text-on-surface-variant mb-1">No. <span class="text-white font-semibold">2024-0892</span></p>
                    <p class="text-xs text-on-surface-variant mb-1">Fecha: <span class="text-white font-semibold">14 Oct, 2023</span></p>
                    <p class="text-xs text-on-surface-variant">Vence: <span class="text-white font-semibold">21 Oct, 2023</span></p>
                </div>
            </div>

            <!-- Doc Info (Client & Delivery) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-surface-container-low/50 rounded-xl p-6 border border-outline-variant/20">
                    <h3 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Datos del Cliente</h3>
                    <p class="text-base font-bold text-white mb-1">Farmacias del Centro S.A.</p>
                    <p class="text-xs text-on-surface-variant mb-3">RFC: <span class="text-white">FCE920101XYZ</span></p>
                    <p class="text-xs text-on-surface-variant leading-relaxed mb-4">Calle Reforma #45, Int 203, Centro Histórico, Querétaro, QRO, 76000.</p>
                    <p class="text-xs text-primary font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">call</span> +52 (442) 345-0987
                    </p>
                </div>
                <div class="bg-surface-container-low/50 rounded-xl p-6 border border-outline-variant/20">
                    <h3 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Lugar de Entrega</h3>
                    <p class="text-base font-bold text-white mb-2">Almacén Regional Norte</p>
                    <p class="text-xs text-on-surface-variant leading-relaxed mb-5">Libramiento Norte 1029, Parque Industrial, Querétaro, QRO, 76120.</p>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 bg-tertiary/10 border border-tertiary/20 text-tertiary text-[9px] font-black rounded-md uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">ac_unit</span> Red Fría
                        </span>
                        <span class="px-2 py-1 bg-primary/10 border border-primary/20 text-primary text-[9px] font-black rounded-md uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">local_shipping</span> Envío Express
                        </span>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-12 overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap">
                    <thead class="bg-surface-container border-b border-outline-variant/30 text-[10px] font-black text-white uppercase tracking-widest">
                        <tr>
                            <th class="py-3 px-4 rounded-tl-xl">CVE/SKU</th>
                            <th class="py-3 px-4">Descripción del Producto</th>
                            <th class="py-3 px-4 text-center">Cant.</th>
                            <th class="py-3 px-4 text-right">Unitario</th>
                            <th class="py-3 px-4 text-right rounded-tr-xl">Importe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        <tr>
                            <td class="py-5 px-4 text-xs font-bold text-white">INS-8921-X</td>
                            <td class="py-5 px-4">
                                <p class="text-sm font-bold text-white mb-0.5">Insulina Glargina 100 UI/ml</p>
                                <p class="text-[10px] text-on-surface-variant">Solución Inyectable - Caja c/5 Plumas Pre-llenadas</p>
                            </td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-center">50</td>
                            <td class="py-5 px-4 text-sm text-on-surface-variant text-right">$842.00</td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-right">$42,100.00</td>
                        </tr>
                        <tr>
                            <td class="py-5 px-4 text-xs font-bold text-white">MET-4418-L</td>
                            <td class="py-5 px-4">
                                <p class="text-sm font-bold text-white mb-0.5">Metformina 850mg LP</p>
                                <p class="text-[10px] text-on-surface-variant">Tabletas de liberación prolongada - Caja c/30</p>
                            </td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-center">200</td>
                            <td class="py-5 px-4 text-sm text-on-surface-variant text-right">$125.50</td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-right">$25,100.00</td>
                        </tr>
                        <tr>
                            <td class="py-5 px-4 text-xs font-bold text-white">AMO-1022-S</td>
                            <td class="py-5 px-4">
                                <p class="text-sm font-bold text-white mb-0.5">Amoxicilina/Ác. Clavulánico 875/125mg</p>
                                <p class="text-[10px] text-on-surface-variant">Tabletas Recubiertas - Caja c/14</p>
                            </td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-center">120</td>
                            <td class="py-5 px-4 text-sm text-on-surface-variant text-right">$312.00</td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-right">$37,440.00</td>
                        </tr>
                        <tr>
                            <td class="py-5 px-4 text-xs font-bold text-white">KET-7781-Z</td>
                            <td class="py-5 px-4">
                                <p class="text-sm font-bold text-white mb-0.5">Ketorolaco Trometamina 10mg</p>
                                <p class="text-[10px] text-on-surface-variant">Tabletas - Caja c/10</p>
                            </td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-center">350</td>
                            <td class="py-5 px-4 text-sm text-on-surface-variant text-right">$48.90</td>
                            <td class="py-5 px-4 text-sm font-bold text-white text-right">$17,115.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="flex flex-col items-end border-t border-outline-variant/30 pt-6">
                <div class="w-full max-w-sm">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-on-surface-variant">Subtotal:</span>
                        <span class="text-sm font-bold text-white">$121,755.00</span>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-sm text-on-surface-variant">IVA (16%):</span>
                        <span class="text-sm font-bold text-white">$19,480.80</span>
                    </div>
                    <div class="flex justify-between items-center bg-surface-container-high rounded-xl p-4">
                        <span class="text-lg font-black text-white uppercase tracking-widest">Total:</span>
                        <span class="text-2xl font-extrabold text-primary">$141,235.80</span>
                    </div>
                    <p class="text-[9px] text-on-surface-variant/50 text-right mt-4 italic">Precios sujetos a cambio sin previo aviso. Moneda: MXN.</p>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-12 pt-4 border-t border-outline-variant/10 text-[9px] text-on-surface-variant">
                <span>Página 1 de 1</span>
                <span>Generado vía MMPharma Digital Portal 2.0</span>
            </div>

        </div>

        <!-- Sidebar Estatus (Right Col) -->
        <div class="space-y-6">
            
            <!-- Status Timeline -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm">
                <h3 class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">history</span> Estatus de Pedido
                </h3>
                
                <div class="relative pl-3">
                    <!-- Line -->
                    <div class="absolute left-[19px] top-4 bottom-8 w-0.5 bg-outline-variant/30"></div>

                    <!-- Step 1 -->
                    <div class="relative flex items-start gap-4 mb-6">
                        <div class="w-4 h-4 rounded-full bg-tertiary border-2 border-surface-container-lowest relative z-10 flex items-center justify-center text-white mt-0.5">
                            <span class="material-symbols-outlined text-[10px] font-bold">check</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white mb-0.5">Generated</p>
                            <p class="text-[10px] text-on-surface-variant">14 Oct, 2023 - 09:12 AM</p>
                            <p class="text-[10px] text-tertiary mt-1 font-semibold">Listo para procesar</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative flex items-start gap-4 mb-6">
                        <div class="w-4 h-4 rounded-full bg-tertiary border-2 border-surface-container-lowest relative z-10 flex items-center justify-center text-white mt-0.5">
                            <span class="material-symbols-outlined text-[10px] font-bold">check</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white mb-0.5">Received</p>
                            <p class="text-[10px] text-on-surface-variant">14 Oct, 2023 - 10:45 AM</p>
                        </div>
                    </div>

                    <!-- Step 3 (Current) -->
                    <div class="relative flex items-start gap-4 mb-6">
                        <div class="w-6 h-6 -ml-1 rounded-full bg-primary/20 border-2 border-primary relative z-10 flex items-center justify-center text-primary animate-pulse">
                            <span class="material-symbols-outlined text-[12px]">sync</span>
                        </div>
                        <div>
                            <p class="text-sm font-extrabold text-primary mb-0.5">In Review</p>
                            <p class="text-xs text-white">En proceso de validación</p>
                            <span class="inline-block px-2 py-0.5 bg-primary/10 border border-primary/20 text-primary text-[9px] font-bold rounded mt-2">Validando Stock</span>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="relative flex items-start gap-4 mb-6 opacity-40">
                        <div class="w-4 h-4 rounded-full bg-surface-container-high border-2 border-surface-container-lowest relative z-10 mt-0.5"></div>
                        <div>
                            <p class="text-sm font-bold text-white mb-0.5">Approved</p>
                            <p class="text-[10px] text-on-surface-variant">Pendiente de autorización</p>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="relative flex items-start gap-4 mb-6 opacity-40">
                        <div class="w-4 h-4 rounded-full bg-surface-container-high border-2 border-surface-container-lowest relative z-10 mt-0.5"></div>
                        <div>
                            <p class="text-sm font-bold text-white mb-0.5">Shipped</p>
                            <p class="text-[10px] text-on-surface-variant">Pendiente</p>
                        </div>
                    </div>

                    <!-- Step 6 -->
                    <div class="relative flex items-start gap-4 opacity-40">
                        <div class="w-4 h-4 rounded-full bg-surface-container-high border-2 border-surface-container-lowest relative z-10 mt-0.5"></div>
                        <div>
                            <p class="text-sm font-bold text-white mb-0.5">Delivered</p>
                            <p class="text-[10px] text-on-surface-variant">Pendiente</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Support Box -->
            <div class="bg-primary-container border border-primary/20 rounded-2xl p-6 relative overflow-hidden shadow-lg">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-primary/30 rounded-full blur-xl"></div>
                <h3 class="text-sm font-bold text-white mb-2">Información de Soporte</h3>
                <p class="text-[10px] text-on-primary-container leading-relaxed mb-6">¿Tiene dudas sobre esta cotización? Contacte a su ejecutivo asignado.</p>
                
                <div class="flex items-center gap-3 bg-surface-container-lowest/40 rounded-xl p-3 border border-outline-variant/30 backdrop-blur-sm relative z-10">
                    <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[20px]">support_agent</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-white">Lic. Elena Rivas</p>
                        <p class="text-[9px] text-on-primary-container uppercase tracking-wider">Ejecutivo de Cuenta</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</main>
<?php include('Includes/footer.php'); ?>
