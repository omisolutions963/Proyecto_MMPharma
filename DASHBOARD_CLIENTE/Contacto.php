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

// Get company config
$stmt = $pdo->query("SELECT clave, valor FROM admin_configuracion WHERE clave IN ('empresa_telefono', 'empresa_email')");
$configuracion = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $configuracion[$row['clave']] = $row['valor'];
}
$empresa_telefono = $configuracion['empresa_telefono'] ?? '+52 (55) 8922-1000';
$empresa_email = $configuracion['empresa_email'] ?? 'soporte@mmpharma.com';
if (empty($empresa_telefono)) $empresa_telefono = '+52 (55) 8922-1000'; // fallback
if (empty($empresa_email)) $empresa_email = 'soporte@mmpharma.com'; // fallback

// Get assigned agent
$stmt = $pdo->prepare("
    SELECT c.agente_id, a.nombre, a.foto_perfil, a.telefono, a.email 
    FROM clientes_usuarios c 
    LEFT JOIN admin_usuarios a ON c.agente_id = a.id 
    WHERE c.id = ?
");
$stmt->execute([$cliente_id]);
$agente_data = $stmt->fetch(PDO::FETCH_ASSOC);

$tiene_agente = !empty($agente_data['agente_id']);
$agente_nombre = $tiene_agente ? $agente_data['nombre'] : 'No asignado';
$agente_foto = ($tiene_agente && !empty($agente_data['foto_perfil'])) ? $agente_data['foto_perfil'] : 'https://picsum.photos/seed/default/100/100';

$pageTitle  = 'MMPharma Portal - Contacto';
$activePage = 'contacto';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Header -->
    <div class="mb-10 animate-reveal">
        <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Centro de Soporte</h1>
        <p class="text-on-surface-variant text-sm max-w-2xl">Bienvenido a nuestro canal de atención directa. Si tienes dudas sobre un pedido o necesitas asistencia técnica, nuestro equipo especializado está listo para apoyarte.</p>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-reveal delay-100">
        
        <!-- Form Column (Left) -->
        <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 shadow-sm">
            <h2 class="text-lg font-bold text-white flex items-center gap-2 mb-8">
                <span class="material-symbols-outlined text-primary">send</span> Enviar un mensaje
            </h2>
            
            <form action="#" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Categoría</label>
                        <div class="relative">
                            <select class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none transition-all">
                                <option>Soporte Técnico</option>
                                <option>Dudas de Facturación</option>
                                <option>Estatus de Pedido</option>
                                <option>Otro</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Cotización Relacionada (Opcional)</label>
                        <input type="text" placeholder="# COT-XXXX-XXX" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Asunto</label>
                    <input type="text" placeholder="Escribe el título de tu consulta" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Mensaje</label>
                    <textarea rows="5" placeholder="Describe tu situación con el mayor detalle posible..." class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all resize-none"></textarea>
                </div>

                <div>
                    <button type="button" class="px-6 py-3 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Formulario de contacto en desarrollo.', background:'#071628', color:'#fff'})">
                        Enviar Solicitud <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Column (Right) -->
        <div class="space-y-6">
            
            <!-- Contacto Directo -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 shadow-sm relative">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-white">Contacto Directo</h3>
                    <span class="px-2.5 py-1 bg-tertiary/10 text-tertiary text-[9px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-tertiary animate-pulse"></span> Soporte 24/7
                    </span>
                </div>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">call</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Línea Telefónica</p>
                            <p class="text-sm font-bold text-white"><?= htmlspecialchars($empresa_telefono) ?></p>
                            <p class="text-[10px] text-on-surface-variant mt-0.5">Ext. 402 - Urgencias</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">mail</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Correo Electrónico</p>
                            <p class="text-sm font-bold text-white"><?= htmlspecialchars($empresa_email) ?></p>
                            <p class="text-[10px] text-on-surface-variant mt-0.5">Respuesta en menos de 2h</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agente Asignado -->
            <div class="bg-gradient-to-br from-primary-container to-surface-container-highest border border-primary/20 rounded-2xl p-6 shadow-lg relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-primary/20 rounded-full blur-2xl pointer-events-none"></div>
                
                <h3 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Tu Agente Asignado</h3>
                
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="relative">
                        <img src="<?= htmlspecialchars($agente_foto) ?>" alt="Agente" class="w-14 h-14 rounded-xl object-cover border-2 border-primary/50">
                        <?php if ($tiene_agente): ?>
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-tertiary border-2 border-primary-container rounded-full"></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <p class="text-base font-bold text-white"><?= htmlspecialchars($agente_nombre) ?></p>
                        <?php if ($tiene_agente): ?>
                        <p class="text-xs text-primary-fixed-dim mb-1.5">Ejecutivo de Cuenta</p>
                        <span class="inline-block px-2 py-0.5 bg-white/10 border border-white/20 text-white text-[9px] font-bold rounded-md">
                            Especialista
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button class="w-full py-2.5 bg-white text-primary-container hover:bg-gray-100 text-sm font-bold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-sm relative z-10" <?php if(!$tiene_agente) echo 'disabled'; ?> onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Chat directo en desarrollo.', background:'#071628', color:'#fff'})">
                    <span class="material-symbols-outlined text-[18px]">chat</span> Iniciar Chat Directo
                </button>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-2 gap-4">
                <button class="bg-surface-container-lowest hover:bg-surface-container border border-outline-variant/30 rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-colors group" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Base de conocimientos en desarrollo.', background:'#071628', color:'#fff'})">
                    <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary mb-2 transition-colors">menu_book</span>
                    <span class="text-xs font-bold text-white">Base de<br>Conocimientos</span>
                </button>
                <button class="bg-surface-container-lowest hover:bg-surface-container border border-outline-variant/30 rounded-2xl p-4 flex flex-col items-center justify-center text-center transition-colors group" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'FAQ en desarrollo.', background:'#071628', color:'#fff'})">
                    <span class="material-symbols-outlined text-on-surface-variant group-hover:text-secondary mb-2 transition-colors">help</span>
                    <span class="text-xs font-bold text-white">Preguntas<br>Frecuentes</span>
                </button>
            </div>

        </div>
    </div>

</main>
<?php include('Includes/footer.php'); ?>
