<?php
$pageTitle  = 'MMPharma Portal - Mi Perfil';
$activePage = 'perfil';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Header -->
    <div class="mb-6 animate-reveal">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Mi Perfil</h1>
    </div>

    <!-- Hero Profile Card -->
    <div class="bg-gradient-to-r from-surface-container-highest to-surface-container rounded-3xl p-8 mb-8 relative overflow-hidden shadow-xl shadow-black/20 animate-reveal delay-100 border border-outline-variant/30">
        <!-- Abstract Decoration -->
        <div class="absolute right-0 top-0 w-1/2 h-full bg-gradient-to-l from-primary/10 to-transparent pointer-events-none"></div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 relative z-10">
            <!-- Avatar -->
            <div class="relative group">
                <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-surface shadow-lg">
                    <img src="https://picsum.photos/seed/doctor/200/200" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <button class="absolute -bottom-3 -right-3 w-10 h-10 bg-surface text-primary rounded-xl flex items-center justify-center border border-outline-variant shadow-lg hover:text-white hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                </button>
            </div>
            
            <!-- Info -->
            <div class="flex-1 text-center md:text-left mt-2 md:mt-0">
                <h2 class="text-3xl font-bold text-white mb-1">Dr. Alejandro Moreno</h2>
                <p class="text-on-surface-variant text-sm flex items-center justify-center md:justify-start gap-2 mb-4">
                    <span class="material-symbols-outlined text-primary text-[16px]">verified_user</span>
                    Administrador Titular • Farmacias del Centro S.A.
                </p>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <span class="px-3 py-1.5 bg-primary/20 border border-primary/30 text-primary text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">star</span> Cliente VIP
                    </span>
                    <span class="px-3 py-1.5 bg-tertiary/20 border border-tertiary/30 text-tertiary text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">ac_unit</span> Red Fría Activa
                    </span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3 mt-4 md:mt-auto self-stretch md:self-end">
                <button class="px-6 py-2 bg-transparent border border-outline hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors">Ver Contratos</button>
                <button class="px-6 py-2 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20">Editar Perfil</button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-8 border-b border-outline-variant/30 mb-8 overflow-x-auto animate-reveal delay-200">
        <button class="pb-4 text-sm font-bold text-primary border-b-2 border-primary whitespace-nowrap">Información personal</button>
        <button class="pb-4 text-sm font-bold text-on-surface-variant hover:text-white transition-colors whitespace-nowrap">Facturación</button>
        <button class="pb-4 text-sm font-bold text-on-surface-variant hover:text-white transition-colors whitespace-nowrap">Seguridad</button>
        <button class="pb-4 text-sm font-bold text-on-surface-variant hover:text-white transition-colors whitespace-nowrap">Notificaciones</button>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-reveal delay-300">
        
        <!-- Left Column (General Data & Billing) -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Datos Generales -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span> Datos Generales
                    </h3>
                    <button class="text-[10px] font-black text-primary uppercase tracking-widest hover:text-primary-fixed-dim transition-colors">Actualizar datos</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                        <input type="text" value="Alejandro Moreno Ruiz" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">RFC</label>
                        <input type="text" value="MOR850412HB9" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Email Profesional</label>
                        <input type="email" value="amoreno@farmaciascentro.mx" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Teléfono Directo</label>
                        <input type="text" value="+52 55 4123 9088" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Régimen Fiscal</label>
                        <div class="relative">
                            <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all">
                                <option>612 - Personas Físicas con Actividades Empresariales y Profesionales</option>
                                <option>601 - General de Ley Personas Morales</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferencias de Facturación -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 shadow-sm">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-1">Preferencias de Facturación</h3>
                    <p class="text-xs text-on-surface-variant">Configura tus documentos fiscales por defecto para agilizar tus pedidos.</p>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-3 ml-1">Tipo de Documento</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer bg-primary/10 border border-primary/30 px-4 py-2.5 rounded-xl">
                                <input type="radio" name="tipo_doc" checked class="text-primary focus:ring-primary bg-surface-container border-outline-variant accent-primary">
                                <span class="text-sm font-semibold text-white">Factura (CFDI 4.0)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer bg-surface-container-low border border-outline-variant/50 px-4 py-2.5 rounded-xl hover:border-outline-variant">
                                <input type="radio" name="tipo_doc" class="text-primary focus:ring-primary bg-surface-container border-outline-variant accent-primary">
                                <span class="text-sm font-semibold text-on-surface-variant">Nota de Crédito</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Método de Pago</label>
                            <div class="relative">
                                <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all">
                                    <option>PPD - Pago en parcialidades o diferido</option>
                                    <option>PUE - Pago en una sola exhibición</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Uso de CFDI</label>
                            <div class="relative">
                                <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all">
                                    <option>G01 - Adquisición de mercancías</option>
                                    <option>G03 - Gastos en general</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="bg-error-container/10 border border-error/20 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-error/20 text-error flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">delete</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white mb-0.5">Solicitar baja de cuenta</h4>
                        <p class="text-xs text-on-surface-variant">Este proceso es irreversible y requiere validación legal.</p>
                    </div>
                </div>
                <div class="flex gap-3 w-full md:w-auto mt-2 md:mt-0">
                    <button class="px-5 py-2.5 bg-surface-container hover:bg-surface-container-high text-white text-sm font-semibold rounded-xl transition-all w-full md:w-auto">Descartar</button>
                    <button class="px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 w-full md:w-auto">Guardar Cambios</button>
                </div>
            </div>

        </div>

        <!-- Right Column (Security & Alerts) -->
        <div class="space-y-8">
            
            <!-- Seguridad de la Cuenta -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">shield</span> Seguridad de la Cuenta
                </h3>
                
                <div class="bg-surface-container-low border border-primary/30 rounded-xl p-4 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">laptop_mac</span>
                        <div>
                            <p class="text-sm font-bold text-white">MacBook Pro 14"</p>
                            <p class="text-[10px] text-on-surface-variant">CDMX, MX • Sesión activa</p>
                        </div>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-tertiary shadow-[0_0_8px_rgba(52,196,122,0.6)]"></span>
                </div>

                <div class="space-y-3">
                    <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border border-outline-variant hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors">
                        <span class="material-symbols-outlined text-[18px]">key</span> Cambiar Contraseña
                    </button>
                    <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border border-outline-variant hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors">
                        <span class="material-symbols-outlined text-[18px]">security</span> Autenticación 2FA
                    </button>
                </div>
            </div>

            <!-- Alertas y Avisos -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[20px]">notifications_active</span> Alertas y Avisos
                </h3>
                
                <div class="space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-white">Estado de Pedidos</p>
                            <p class="text-[10px] text-on-surface-variant">Alertas de surtido y envío</p>
                        </div>
                        <!-- Custom Toggle Checked -->
                        <div class="w-10 h-6 bg-primary rounded-full relative cursor-pointer">
                            <div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-white">Stock & Disponibilidad</p>
                            <p class="text-[10px] text-on-surface-variant">Aviso de productos agotados</p>
                        </div>
                        <!-- Custom Toggle Checked -->
                        <div class="w-10 h-6 bg-primary rounded-full relative cursor-pointer">
                            <div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-white">Promociones MMPharma</p>
                            <p class="text-[10px] text-on-surface-variant">Descuentos exclusivos B2B</p>
                        </div>
                        <!-- Custom Toggle Unchecked -->
                        <div class="w-10 h-6 bg-surface-container-high rounded-full relative cursor-pointer border border-outline-variant">
                            <div class="w-4 h-4 bg-on-surface-variant rounded-full absolute left-1 top-0.5"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Soporte Técnico -->
            <div class="bg-gradient-to-br from-tertiary-container/80 to-surface-container border border-tertiary/20 rounded-2xl p-6 relative overflow-hidden">
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[100px] text-tertiary/10 rotate-[-15deg] pointer-events-none">support_agent</span>
                <div class="relative z-10">
                    <h3 class="text-sm font-bold text-white mb-1">¿Necesitas ayuda técnica?</h3>
                    <p class="text-[10px] text-on-surface-variant mb-4">Tu gestor de cuenta asignado está disponible.</p>
                    <button class="w-full px-4 py-2.5 bg-white text-tertiary-container text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors shadow-sm">
                        Contactar Soporte
                    </button>
                </div>
            </div>

        </div>
    </div>

</main>
<?php include('Includes/footer.php'); ?>