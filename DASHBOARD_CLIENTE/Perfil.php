<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    header("Location: ../LOGIN/login.php");
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

$foto_perfil = $cliente['foto_perfil'] ?? null;
$nombre = htmlspecialchars($cliente['razon_social'] ?? 'Cliente');
$rfc = htmlspecialchars($cliente['rfc'] ?? '');
$email = htmlspecialchars($cliente['email'] ?? '');
$telefono = htmlspecialchars($cliente['telefono_celular'] ?? '');
$regimen = htmlspecialchars($cliente['regimen_fiscal'] ?? '');
$tipo_doc = $cliente['documento_tipo'] ?? 'FACTURA';
$metodo_pago = $cliente['metodo_pago'] ?? 'TRANSFERENCIA';
$uso_cfdi = htmlspecialchars($cliente['uso_cfdi'] ?? '');
$estatus = $cliente['estatus'] ?? 'ACTIVO';
$tipo_cliente = htmlspecialchars($cliente['tipo'] ?? 'FARMACIA');

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
                <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-surface shadow-lg flex items-center justify-center bg-surface-container-high">
                    <?php if ($foto_perfil): ?>
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-4xl font-black text-primary"><?= strtoupper(substr($nombre, 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <button class="absolute -bottom-3 -right-3 w-10 h-10 bg-surface text-primary rounded-xl flex items-center justify-center border border-outline-variant shadow-lg hover:text-white hover:bg-primary transition-colors" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Subida de fotos en la siguiente actualización.', background:'#071628', color:'#fff'})">
                    <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                </button>
            </div>
            
            <!-- Info -->
            <div class="flex-1 text-center md:text-left mt-2 md:mt-0">
                <h2 class="text-3xl font-bold text-white mb-1"><?= $nombre ?></h2>
                <p class="text-on-surface-variant text-sm flex items-center justify-center md:justify-start gap-2 mb-4">
                    <span class="material-symbols-outlined text-primary text-[16px]">storefront</span>
                    <?= $tipo_cliente ?>
                </p>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <?php if ($estatus === 'ACTIVO'): ?>
                    <span class="px-3 py-1.5 bg-tertiary/20 border border-tertiary/30 text-tertiary text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">verified</span> Cuenta Activa
                    </span>
                    <?php else: ?>
                    <span class="px-3 py-1.5 bg-error/20 border border-error/30 text-error text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">warning</span> Docs Pendientes
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3 mt-4 md:mt-auto self-stretch md:self-end">
                <a href="Documentos.php" class="px-6 py-2 bg-transparent border border-outline hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors text-center">Ver Documentos</a>
                <button class="px-6 py-2 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Actualización de perfil en desarrollo.', background:'#071628', color:'#fff'})">Editar Perfil</button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-8 border-b border-outline-variant/30 mb-8 overflow-x-auto animate-reveal delay-200">
        <button class="pb-4 text-sm font-bold text-primary border-b-2 border-primary whitespace-nowrap">Información personal</button>
        <button class="pb-4 text-sm font-bold text-on-surface-variant hover:text-white transition-colors whitespace-nowrap" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">Facturación</button>
        <button class="pb-4 text-sm font-bold text-on-surface-variant hover:text-white transition-colors whitespace-nowrap" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">Seguridad</button>
        <button class="pb-4 text-sm font-bold text-on-surface-variant hover:text-white transition-colors whitespace-nowrap" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">Notificaciones</button>
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
                    <button class="text-[10px] font-black text-primary uppercase tracking-widest hover:text-primary-fixed-dim transition-colors" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">Actualizar datos</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Razón Social</label>
                        <input type="text" value="<?= $nombre ?>" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" readonly>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">RFC</label>
                        <input type="text" value="<?= $rfc ?>" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" readonly>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Email Profesional</label>
                        <input type="email" value="<?= $email ?>" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" readonly>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Teléfono</label>
                        <input type="text" value="<?= $telefono ?>" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" readonly>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Régimen Fiscal</label>
                        <div class="relative">
                            <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all" disabled>
                                <?php if ($regimen): ?>
                                <option><?= $regimen ?></option>
                                <?php else: ?>
                                <option>No especificado</option>
                                <?php endif; ?>
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
                            <label class="flex items-center gap-2 cursor-pointer <?= $tipo_doc === 'FACTURA' ? 'bg-primary/10 border-primary/30' : 'bg-surface-container-low border-outline-variant/50' ?> px-4 py-2.5 rounded-xl border">
                                <input type="radio" name="tipo_doc" value="FACTURA" <?= $tipo_doc === 'FACTURA' ? 'checked' : '' ?> class="text-primary focus:ring-primary bg-surface-container border-outline-variant accent-primary" disabled>
                                <span class="text-sm font-semibold <?= $tipo_doc === 'FACTURA' ? 'text-white' : 'text-on-surface-variant' ?>">Factura (CFDI 4.0)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer <?= $tipo_doc === 'NOTA' ? 'bg-primary/10 border-primary/30' : 'bg-surface-container-low border-outline-variant/50' ?> px-4 py-2.5 rounded-xl border hover:border-outline-variant">
                                <input type="radio" name="tipo_doc" value="NOTA" <?= $tipo_doc === 'NOTA' ? 'checked' : '' ?> class="text-primary focus:ring-primary bg-surface-container border-outline-variant accent-primary" disabled>
                                <span class="text-sm font-semibold <?= $tipo_doc === 'NOTA' ? 'text-white' : 'text-on-surface-variant' ?>">Nota de Crédito</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Método de Pago</label>
                            <div class="relative">
                                <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all" disabled>
                                    <option><?= htmlspecialchars($metodo_pago) ?></option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Uso de CFDI</label>
                            <div class="relative">
                                <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all" disabled>
                                    <?php if ($uso_cfdi): ?>
                                    <option><?= $uso_cfdi ?></option>
                                    <?php else: ?>
                                    <option>No especificado</option>
                                    <?php endif; ?>
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
                    <button class="px-5 py-2.5 bg-surface-container hover:bg-surface-container-high text-white text-sm font-semibold rounded-xl transition-all w-full md:w-auto" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">Solicitar</button>
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
                            <p class="text-sm font-bold text-white"><?= $email ?></p>
                            <p class="text-[10px] text-on-surface-variant">Sesión activa</p>
                        </div>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-tertiary shadow-[0_0_8px_rgba(52,196,122,0.6)]"></span>
                </div>

                <div class="space-y-3">
                    <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border border-outline-variant hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">
                        <span class="material-symbols-outlined text-[18px]">key</span> Cambiar Contraseña
                    </button>
                    <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border border-outline-variant hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors" onclick="Swal.fire({icon:'info', title:'Próximamente', background:'#071628', color:'#fff'})">
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
                        <div class="w-10 h-6 bg-primary rounded-full relative cursor-pointer">
                            <div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-white">Stock & Disponibilidad</p>
                            <p class="text-[10px] text-on-surface-variant">Aviso de productos agotados</p>
                        </div>
                        <div class="w-10 h-6 bg-primary rounded-full relative cursor-pointer">
                            <div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-white">Promociones MMPharma</p>
                            <p class="text-[10px] text-on-surface-variant">Descuentos exclusivos B2B</p>
                        </div>
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
                    <a href="Contacto.php" class="block w-full px-4 py-2.5 bg-white text-tertiary-container text-center text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors shadow-sm">
                        Contactar Soporte
                    </a>
                </div>
            </div>

        </div>
    </div>

</main>
<?php include('Includes/footer.php'); ?>
