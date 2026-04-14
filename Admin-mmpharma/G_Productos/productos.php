<?php
$pageTitle = "MMPharma Portal - Gestión de Productos";
$activePage = "productos";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>
<main class="ml-64 p-8 min-h-[calc(100vh-4rem)]">
<div class="p-8 space-y-8">

    <!-- Header Section -->
    <section class="flex items-end justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight leading-none mb-2">Catálogo de Productos</h2>
            <p class="text-on-surface-variant">Gestión centralizada de inventario farmacéutico y suministros clínicos.</p>
        </div>
        <button onclick="abrirModal()" class="bg-gradient-to-br from-primary to-primary-container text-white px-6 py-3 rounded-md flex items-center gap-2 font-medium shadow-lg shadow-primary/10 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-lg">add</span>
            Nuevo Producto
        </button>
    </section>

    <!-- Filters Bar -->
    <section class="bg-surface-container-low p-5 rounded-xl flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[240px] relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">filter_list</span>
            <input class="w-full bg-surface-container-lowest border-none rounded-lg py-2.5 pl-10 pr-4 focus:ring-2 focus:ring-primary-fixed text-sm text-on-surface" placeholder="Filtrar por nombre o SKU..." type="text"/>
        </div>
        <div class="w-48">
            <select class="w-full bg-surface-container-lowest border-none rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-primary-fixed text-sm text-on-surface">
                <option value="">Todas las Categorías</option>
                <option value="antibioticos">Antibióticos</option>
                <option value="red_fria">Red Fría</option>
                <option value="analgesicos">Analgésicos</option>
                <option value="oncologia">Oncología</option>
            </select>
        </div>
        <div class="w-40">
            <select class="w-full bg-surface-container-lowest border-none rounded-lg py-2.5 px-4 focus:ring-2 focus:ring-primary-fixed text-sm text-on-surface">
                <option value="">Cualquier Estado</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
        </div>
        <button class="px-4 py-2.5 text-on-surface-variant font-medium text-sm hover:bg-surface-container-high rounded-lg transition-colors">
            Limpiar Filtros
        </button>
    </section>

    <!-- Products Table -->
    <section class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-xs font-bold uppercase tracking-widest">
                    <th class="px-6 py-4">Producto</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4">Categoría</th>
                    <th class="px-6 py-4">Precio</th>
                    <th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <tr class="hover:bg-surface transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden flex-shrink-0">
                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLXgphxQYPWa_xdPfKGL4qkKL905T1BJs1naHW4HsC9ka3AStRMx5MDwdNGknp5JuVFnrGbbFOxwl_YdfAP04SRnde8An5n_zGEsmffc86HYMhq-B69ilu6iNA_nmkAsmE7KBsQKXmqJhITgaMQb0OhL7vsKpHHJdjy2Mn9LPzGTZUFZBiPRAQsDkwDPnHRt88Xx6fMZZoHW8g8_CS65y7oAKd5I7_cB163er5zMJ8WtzwTbd93AtbvF_dFzuaY2IGF0EtcfRvjGA"/>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-on-surface">Insulina Humana NPH</div>
                                <div class="text-xs text-on-surface-variant">100 UI/ml Suspensión</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-outline">PH-7821-XP</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-tertiary-container/10 text-on-tertiary-container text-[10px] font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[12px]">ac_unit</span> RED FRÍA
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-on-surface">$1,420.00</td>
                    <td class="px-6 py-4"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-tertiary"></div><span class="text-sm text-on-surface">452 Unidades</span></div></td>
                    <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-md bg-tertiary/10 text-tertiary text-[11px] font-bold uppercase tracking-tighter">Activo</span></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-2 hover:bg-primary-container/10 text-primary rounded-lg transition-colors"><span class="material-symbols-outlined text-xl">edit</span></button>
                            <button class="p-2 hover:bg-error-container/20 text-error rounded-lg transition-colors"><span class="material-symbols-outlined text-xl">delete</span></button>
                        </div>
                    </td>
                </tr>
                <tr class="bg-surface/30 hover:bg-surface transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden flex-shrink-0">
                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCRjZ1y5FKXJ2E6q3AXx_vQS_iQVL_9F7UOsHFz-bk6KBTuJcQuRY5EYHzRq6paLkTIYqUtlkF7F-rvHY7t9wgSWdgHWO_ynLTAeW7gfRKZ9yW7PzBWKtU1ZEo9QBSPtL65Cj00_Ot6iO3qv0J2OGxzIX9Qw6ZO3guCAV1gKr-gaI2XJoBHZLypb1Lk-7f3LBpafjpH76dho5WaYr4G0O-pFi4cphlsqnihnnj7EkBTr2XPxqr_QGFQhmvl2TZJyonDmpd8LHT9r8M"/>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-on-surface">Amoxicilina 500mg</div>
                                <div class="text-xs text-on-surface-variant">Cápsulas Caja c/20</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-outline">PH-1044-AM</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">Antibióticos</td>
                    <td class="px-6 py-4 text-sm font-semibold text-on-surface">$285.50</td>
                    <td class="px-6 py-4"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-error"></div><span class="text-sm text-error font-medium">12 Unidades</span></div></td>
                    <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-md bg-tertiary/10 text-tertiary text-[11px] font-bold uppercase tracking-tighter">Activo</span></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-2 hover:bg-primary-container/10 text-primary rounded-lg transition-colors"><span class="material-symbols-outlined text-xl">edit</span></button>
                            <button class="p-2 hover:bg-error-container/20 text-error rounded-lg transition-colors"><span class="material-symbols-outlined text-xl">delete</span></button>
                        </div>
                    </td>
                </tr>
                <tr class="hover:bg-surface transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden flex-shrink-0">
                                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDCjtjsgOIBomp_08BCVezhrWKNNkVMPtyTFJHapSxBzYI3CfaJphSOkbbJTZJe8dus3u-xZ7-rvWaPStczg-D2BPWNYQngDnU9uoXVaK3C6Gc79MPrNmV275OK8qxHY0DhUUGVfm7tOZd3hIo1CQKpXDF26j_kdzgLKJRv8HutyNeFe9NgGkLFy99B7ICEIyDYG8tGyxFfKGbGdLZ1ZNh0y2OgWM0Fykw9rrE9zeZ84iryTtOoYUiP8AatiD2ywvG3frFnP4XbdYU"/>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-on-surface">Vincristina 1mg/ml</div>
                                <div class="text-xs text-on-surface-variant">Solución Inyectable</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-outline">PH-3319-ON</td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">Oncología</td>
                    <td class="px-6 py-4 text-sm font-semibold text-on-surface">$5,890.00</td>
                    <td class="px-6 py-4"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-secondary"></div><span class="text-sm text-on-surface">84 Unidades</span></div></td>
                    <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-md bg-outline-variant/30 text-on-surface-variant text-[11px] font-bold uppercase tracking-tighter">Inactivo</span></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-2 hover:bg-primary-container/10 text-primary rounded-lg transition-colors"><span class="material-symbols-outlined text-xl">edit</span></button>
                            <button class="p-2 hover:bg-error-container/20 text-error rounded-lg transition-colors"><span class="material-symbols-outlined text-xl">delete</span></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="px-6 py-5 bg-surface-container-low flex items-center justify-between border-t border-outline-variant/10">
            <div class="text-xs text-on-surface-variant font-medium">Mostrando <span class="text-on-surface">1-3</span> de <span class="text-on-surface">124</span> productos</div>
            <div class="flex items-center gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-outline hover:bg-surface-container-lowest transition-colors disabled:opacity-30" disabled>
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </button>
                <div class="flex gap-1">
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-xs">1</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container-lowest font-bold text-xs">2</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg text-on-surface-variant hover:bg-surface-container-lowest font-bold text-xs">3</button>
                </div>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant text-outline hover:bg-surface-container-lowest transition-colors">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Bento Cards -->
    <section class="grid grid-cols-12 gap-6">
        <div class="col-span-8 bg-gradient-to-br from-surface-container-lowest to-surface-container-low p-6 rounded-2xl shadow-sm border border-outline-variant/10">
            <h4 class="text-lg font-bold text-on-surface mb-4">Métricas de Categoría</h4>
            <div class="grid grid-cols-3 gap-6">
                <div class="space-y-1">
                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Antibióticos</p>
                    <p class="text-2xl font-black text-primary tracking-tight">42% <span class="text-xs font-normal text-outline">del total</span></p>
                    <div class="w-full h-1 bg-surface-container-highest rounded-full overflow-hidden"><div class="h-full bg-primary w-[42%]"></div></div>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Red Fría</p>
                    <p class="text-2xl font-black text-tertiary tracking-tight">18% <span class="text-xs font-normal text-outline">del total</span></p>
                    <div class="w-full h-1 bg-surface-container-highest rounded-full overflow-hidden"><div class="h-full bg-tertiary w-[18%]"></div></div>
                </div>
                <div class="space-y-1">
                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Analgesia</p>
                    <p class="text-2xl font-black text-secondary tracking-tight">30% <span class="text-xs font-normal text-outline">del total</span></p>
                    <div class="w-full h-1 bg-surface-container-highest rounded-full overflow-hidden"><div class="h-full bg-secondary w-[30%]"></div></div>
                </div>
            </div>
        </div>
        <div class="col-span-4 bg-primary text-white p-6 rounded-2xl flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-lg font-bold mb-1">Estado de Stock</h4>
                <p class="text-primary-fixed-dim text-sm">3 productos requieren atención inmediata por bajo inventario.</p>
            </div>
            <button class="mt-4 w-fit px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium backdrop-blur-md transition-colors relative z-10">Ver Reporte de Faltantes</button>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
        </div>
    </section>
</div>

<footer class="mt-auto py-6 px-8 border-t border-outline-variant/10 text-[11px] text-outline flex justify-between items-center bg-surface">
    <p>© 2024 MMPharma Clinical Systems. Todos los derechos reservados.</p>
    <div class="flex gap-4">
        <a class="hover:text-primary transition-colors" href="#">Política de Privacidad</a>
        <a class="hover:text-primary transition-colors" href="#">Términos de Servicio</a>
    </div>
</footer>
</main>

<!-- ===== MODAL NUEVO PRODUCTO (Panel lateral deslizante) ===== -->
<div id="modalProducto" class="fixed inset-0 z-[100] hidden">
    <div onclick="cerrarModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-white shadow-2xl flex flex-col duration-300 transition-transform translate-x-full" id="modalPanel">

        <!-- Header del modal -->
        <div class="flex items-center justify-between px-8 py-5 border-b border-white/10 bg-[#1A5632]">
            <div>
                <h3 class="text-xl font-bold text-white tracking-tight">Nuevo Producto</h3>
                <p class="text-emerald-100/60 text-xs mt-0.5">Completa los campos para registrar el producto</p>
            </div>
            <button onclick="cerrarModal()" class="w-9 h-9 flex items-center justify-center rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Cuerpo del modal -->
        <div class="flex-1 overflow-y-auto px-8 py-6 space-y-6">

            <!-- Foto -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">Foto del Producto</label>
                <div id="dropZone" onclick="document.getElementById('fotoInput').click()"
                     class="border-2 border-dashed border-outline-variant rounded-xl p-8 flex flex-col items-center justify-center gap-3 cursor-pointer hover:border-primary hover:bg-primary/5 transition-all group">
                    <div id="previewContainer" class="hidden w-full flex flex-col items-center gap-3">
                        <img id="fotoPreview" class="w-32 h-32 object-cover rounded-xl shadow-md"/>
                        <p class="text-xs text-on-surface-variant" id="fotoNombre"></p>
                        <button onclick="event.stopPropagation(); limpiarFoto()" class="text-xs text-error hover:underline">Cambiar foto</button>
                    </div>
                    <div id="uploadPlaceholder" class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-full bg-surface-container-low flex items-center justify-center group-hover:bg-primary/10 transition-colors">
                            <span class="material-symbols-outlined text-outline group-hover:text-primary transition-colors text-3xl">add_photo_alternate</span>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-on-surface">Haz clic para subir una foto</p>
                            <p class="text-xs text-on-surface-variant mt-1">PNG, JPG o WEBP · Máx. 5MB</p>
                        </div>
                    </div>
                    <input type="file" id="fotoInput" accept="image/*" class="hidden" onchange="previsualizarFoto(event)"/>
                </div>
            </div>

            <!-- Nombre y SKU -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre del Producto *</label>
                    <input type="text" id="nombre" placeholder="Ej. Amoxicilina 500mg"
                           class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">SKU *</label>
                    <input type="text" id="sku" placeholder="Ej. PH-1044-AM"
                           class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none font-mono"/>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Descripción</label>
                <textarea id="descripcion" rows="3" placeholder="Presentación, dosis, indicaciones, etc."
                          class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none resize-none"></textarea>
            </div>

            <!-- Categoría y Tipo Cadena -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Categoría *</label>
                    <select id="categoria" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none">
                        <option value="">Seleccionar...</option>
                        <option value="antibioticos">Antibióticos</option>
                        <option value="analgesicos">Analgésicos</option>
                        <option value="oncologia">Oncología</option>
                        <option value="antiinflamatorios">Antiinflamatorios</option>
                        <option value="antihipertensivos">Antihipertensivos</option>
                        <option value="vitaminas">Vitaminas y Suplementos</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Tipo de Cadena *</label>
                    <div class="flex gap-3 mt-1">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="cadena" value="seca" class="peer hidden" checked/>
                            <div class="peer-checked:bg-primary peer-checked:text-white bg-surface-container-low text-on-surface-variant rounded-lg px-3 py-3 text-sm font-semibold text-center flex items-center justify-center gap-1.5 transition-all border-2 border-transparent peer-checked:border-primary">
                                <span class="material-symbols-outlined text-base">light_mode</span> Seca
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="cadena" value="fria" class="peer hidden"/>
                            <div class="peer-checked:bg-primary peer-checked:text-white bg-surface-container-low text-on-surface-variant rounded-lg px-3 py-3 text-sm font-semibold text-center flex items-center justify-center gap-1.5 transition-all border-2 border-transparent peer-checked:border-primary">
                                <span class="material-symbols-outlined text-base">ac_unit</span> Red Fría
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Precio y Stock -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Precio Unitario *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline font-bold text-sm">$</span>
                        <input type="number" id="precio" placeholder="0.00" min="0" step="0.01"
                               class="w-full bg-surface-container-low border-none rounded-lg pl-8 pr-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Stock Inicial *</label>
                    <input type="number" id="stock" placeholder="0" min="0"
                           class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none"/>
                </div>
            </div>

            <!-- Stock mínimo y Proveedor -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Stock Mínimo (alerta)</label>
                    <input type="number" id="stock_minimo" placeholder="Ej. 20" min="0"
                           class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Proveedor</label>
                    <input type="text" id="proveedor" placeholder="Nombre del laboratorio"
                           class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed outline-none"/>
                </div>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">Estado</label>
                <div class="flex gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="estado" value="activo" class="peer hidden" checked/>
                        <div class="peer-checked:bg-tertiary/10 peer-checked:text-tertiary peer-checked:border-tertiary bg-surface-container-low text-on-surface-variant rounded-lg px-6 py-2.5 text-sm font-semibold transition-all border-2 border-transparent">
                            ✓ Activo
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="estado" value="inactivo" class="peer hidden"/>
                        <div class="peer-checked:bg-outline-variant/30 peer-checked:text-on-surface peer-checked:border-outline bg-surface-container-low text-on-surface-variant rounded-lg px-6 py-2.5 text-sm font-semibold transition-all border-2 border-transparent">
                            ✕ Inactivo
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Footer del modal -->
        <div class="px-8 py-5 border-t border-outline-variant/20 bg-surface-container-low flex items-center justify-between">
            <button onclick="cerrarModal()" class="px-6 py-2.5 text-on-surface-variant font-semibold text-sm hover:bg-surface-container-high rounded-lg transition-colors">
                Cancelar
            </button>
            <button onclick="guardarProducto()" class="px-8 py-2.5 bg-gradient-to-br from-primary to-primary-container text-white font-bold text-sm rounded-lg shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">save</span>
                Guardar Producto
            </button>
        </div>
    </div>
</div>

<script>
function abrirModal() {
    const modal = document.getElementById('modalProducto');
    const panel = document.getElementById('modalPanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    const panel = document.getElementById('modalPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        document.getElementById('modalProducto').classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function previsualizarFoto(event) {
    const archivo = event.target.files[0];
    if (!archivo) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('fotoPreview').src = e.target.result;
        document.getElementById('fotoNombre').textContent = archivo.name;
        document.getElementById('previewContainer').classList.remove('hidden');
        document.getElementById('uploadPlaceholder').classList.add('hidden');
    };
    reader.readAsDataURL(archivo);
}

function limpiarFoto() {
    document.getElementById('fotoInput').value = '';
    document.getElementById('previewContainer').classList.add('hidden');
    document.getElementById('uploadPlaceholder').classList.remove('hidden');
}

function guardarProducto() {
    const nombre    = document.getElementById('nombre').value.trim();
    const sku       = document.getElementById('sku').value.trim();
    const categoria = document.getElementById('categoria').value;
    const precio    = document.getElementById('precio').value;
    const stock     = document.getElementById('stock').value;

    if (!nombre || !sku || !categoria || !precio || !stock) {
        alert('Por favor completa todos los campos obligatorios (*)');
        return;
    }

    // Aquí conectaremos con la base de datos en el siguiente paso
    alert('✅ Producto "' + nombre + '" registrado.\n\n(Próximo paso: guardar en base de datos)');
    cerrarModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });
</script>

<?php include("../Includes/footer.php"); ?>
