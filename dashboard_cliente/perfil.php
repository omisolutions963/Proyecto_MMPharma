<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

require_once '../includes/db.php';
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

$pageTitle = 'MMPharma Portal - Mi Perfil';
$activePage = 'perfil';
include('includes/header.php');
include('includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#ffffff">

    <!-- Header -->
    <div class="mb-6 animate-reveal">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Mi perfil</h1>
    </div>

    <!-- Hero Profile Card -->
    <div class="bg-gradient-to-r from-surface-container-highest to-surface-container rounded-3xl p-8 mb-8 relative overflow-hidden animate-reveal delay-100 border border-outline-variant/30">
        <!-- Abstract Decoration -->
        <div class="absolute right-0 top-0 w-1/2 h-full bg-gradient-to-l from-primary/10 to-transparent pointer-events-none"></div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 relative z-10">
            <!-- Avatar -->
            <div class="relative group">
                <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-surface flex items-center justify-center bg-surface-container-high">
                    <?php if ($foto_perfil): ?>
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-4xl font-black text-primary"><?= strtoupper(substr($nombre, 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <button type="button" class="absolute -bottom-3 -right-3 w-10 h-10 bg-surface text-primary rounded-xl flex items-center justify-center border border-outline-variant hover:text-white hover:bg-primary transition-colors" onclick="document.getElementById('avatarInput').click()">
                    <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                </button>
                <input type="file" id="avatarInput" accept="image/png, image/jpeg, image/webp" class="hidden" onchange="subirAvatar(this)">
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
                            <span class="material-symbols-outlined text-[14px]">verified</span> Cuenta activa
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1.5 bg-error/20 border border-error/30 text-error text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">warning</span> Docs pendientes
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3 mt-4 md:mt-auto self-stretch md:self-end">
                <a href="documentos.php" class="px-6 py-2 bg-transparent border border-outline hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors text-center">Ver documentos</a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-reveal delay-300">
        
        <!-- Left Column (General Data & Billing) -->
        <form id="perfilForm" class="lg:col-span-2 space-y-8" onsubmit="actualizarPerfil(event)">
            
            <!-- Datos Generales -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 ">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span> Datos generales
                    </h3>
                    <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:shadow-primary/30 flex items-center gap-2 border-none cursor-pointer"><span class="material-symbols-outlined text-[18px]">save</span> Guardar cambios</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Razón social</label>
                        <input type="text" name="razon_social" value="<?= $nombre ?>" required class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">RFC</label>
                        <input type="text" name="rfc" value="<?= $rfc ?>" required class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Email profesional</label>
                        <input type="email" name="email" value="<?= $email ?>" required class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Teléfono celular</label>
                        <input type="text" name="telefono_celular" value="<?= $telefono ?>" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Régimen fiscal</label>
                        <input type="text" name="regimen_fiscal" value="<?= $regimen ?>" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Preferencias de Facturación -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 ">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-white mb-1">Preferencias de facturación</h3>
                    <p class="text-xs text-on-surface-variant">Configura tus documentos fiscales por defecto para agilizar tus pedidos.</p>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-3 ml-1">Tipo de documento</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer <?= $tipo_doc === 'FACTURA' ? 'bg-primary/10 border-primary/30' : 'bg-surface-container-low border-outline-variant/50' ?> px-4 py-2.5 rounded-xl border">
                                <input type="radio" name="documento_tipo" value="FACTURA" <?= $tipo_doc === 'FACTURA' ? 'checked' : '' ?> class="text-primary focus:ring-primary bg-surface-container border-outline-variant accent-primary">
                                <span class="text-sm font-semibold <?= $tipo_doc === 'FACTURA' ? 'text-white' : 'text-on-surface-variant' ?>">Factura (CFDI 4.0)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer <?= $tipo_doc === 'NOTA' ? 'bg-primary/10 border-primary/30' : 'bg-surface-container-low border-outline-variant/50' ?> px-4 py-2.5 rounded-xl border hover:border-outline-variant">
                                <input type="radio" name="documento_tipo" value="NOTA" <?= $tipo_doc === 'NOTA' ? 'checked' : '' ?> class="text-primary focus:ring-primary bg-surface-container border-outline-variant accent-primary">
                                <span class="text-sm font-semibold <?= $tipo_doc === 'NOTA' ? 'text-white' : 'text-on-surface-variant' ?>">Nota de crédito</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Método de pago</label>
                            <div class="relative">
                                <select name="metodo_pago" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none bg-none focus:border-primary outline-none transition-all">
                                    <option value="TRANSFERENCIA" <?= $metodo_pago == 'TRANSFERENCIA' ? 'selected' : '' ?>>Transferencia</option>
                                    <option value="EFECTIVO" <?= $metodo_pago == 'EFECTIVO' ? 'selected' : '' ?>>Efectivo</option>
                                    <option value="CHEQUE" <?= $metodo_pago == 'CHEQUE' ? 'selected' : '' ?>>Cheque</option>
                                    <option value="CREDITO_30D" <?= $metodo_pago == 'CREDITO_30D' ? 'selected' : '' ?>>Crédito 30 días</option>
                                    <option value="CREDITO_60D" <?= $metodo_pago == 'CREDITO_60D' ? 'selected' : '' ?>>Crédito 60 días</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Uso de CFDI</label>
                            <input type="text" name="uso_cfdi" value="<?= $uso_cfdi ?>" placeholder="Ej. G03 - Gastos en general" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined">delete</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 mb-0.5">Solicitar baja de cuenta</h4>
                        <p class="text-xs text-slate-500">Este proceso es irreversible y requiere validación legal.</p>
                    </div>
                </div>
                <div class="flex gap-3 w-full md:w-auto mt-2 md:mt-0">
                    <button type="button" class="px-5 py-2.5 bg-surface-container hover:bg-surface-container-high text-white text-sm font-semibold rounded-xl transition-all w-full md:w-auto" onclick="Swal.fire({icon:'info', title:'Baja de cuenta', text:'Para solicitar la baja de tu cuenta, por favor envíanos un mensaje desde nuestro Centro de Soporte.', background:'#ffffff', color:'#1e293b', confirmButtonColor:'#4a90d9', confirmButtonText:'Ir a soporte'}).then((result) => { if(result.isConfirmed) { location.href='contacto.php'; } })">Solicitar</button>
                </div>
            </div>

        </form>

        <!-- Right Column (Security & Support) -->
        <div class="space-y-8">
            
            <!-- Seguridad de la Cuenta -->
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 ">
                <h3 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">shield</span> Seguridad de la cuenta
                </h3>
                
                <div class="bg-surface-container-low border border-primary/30 rounded-xl p-4 mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary">laptop_mac</span>
                        <div>
                            <p class="text-sm font-bold text-white"><?= $email ?></p>
                            <p class="text-[10px] text-on-surface-variant">Sesión activa</p>
                        </div>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-tertiary 0_0_8px_rgba(52,196,122,0.6)]"></span>
                </div>

                <div class="space-y-3">
                    <button type="button" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent border border-outline-variant hover:bg-white/5 text-white text-sm font-semibold rounded-xl transition-colors" onclick="cambiarPassword()">
                        <span class="material-symbols-outlined text-[18px]">key</span> Cambiar contraseña
                    </button>
                </div>
            </div>

            <!-- Soporte Técnico -->
            <div class="bg-gradient-to-br from-tertiary-container/80 to-surface-container border border-tertiary/20 rounded-2xl p-6 relative overflow-hidden">
                <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-[100px] text-tertiary/10 rotate-[-15deg] pointer-events-none">support_agent</span>
                <div class="relative z-10">
                    <h3 class="text-sm font-bold text-white mb-1">¿Necesitas ayuda técnica?</h3>
                    <p class="text-[10px] text-on-surface-variant mb-4">Tu gestor de cuenta asignado está disponible.</p>
                    <a href="contacto.php" class="block w-full px-4 py-2.5 bg-white text-tertiary-container text-center text-sm font-bold rounded-xl hover:bg-gray-100 transition-colors ">
                        Contactar soporte
                    </a>
                </div>
            </div>

        </div>
    </div>

</main>
<?php include('includes/footer.php'); ?>

<script>
// Radio button styling toggle
document.querySelectorAll('input[name="documento_tipo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('input[name="documento_tipo"]').forEach(r => {
            const label = r.closest('label');
            const span = label.querySelector('span');
            if(r.checked) {
                label.classList.remove('bg-surface-container-low', 'border-outline-variant/50');
                label.classList.add('bg-primary/10', 'border-primary/30');
                span.classList.remove('text-on-surface-variant');
                span.classList.add('text-white');
            } else {
                label.classList.remove('bg-primary/10', 'border-primary/30');
                label.classList.add('bg-surface-container-low', 'border-outline-variant/50');
                span.classList.remove('text-white');
                span.classList.add('text-on-surface-variant');
            }
        });
    });
});

function actualizarPerfil(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('action', 'update_info');

    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#1e293b',
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('api_perfil.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({icon: 'success', title: '¡Éxito!', text: data.message, background: '#ffffff', color: '#1e293b', showConfirmButton: false, timer: 1500})
            .then(() => location.reload());
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.message, background: '#ffffff', color: '#1e293b'});
        }
    })
    .catch(err => {
        Swal.fire({icon: 'error', title: 'Error de conexión', background: '#ffffff', color: '#1e293b'});
    });
}

function subirAvatar(input) {
    if(!input.files || input.files.length === 0) return;
    const file = input.files[0];

    const formData = new FormData();
    formData.append('action', 'update_avatar');
    formData.append('avatar', file);

    Swal.fire({
        title: 'Subiendo foto...',
        allowOutsideClick: false,
        background: '#ffffff',
        color: '#1e293b',
        didOpen: () => { Swal.showLoading(); }
    });

    fetch('api_perfil.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.status === 'success') {
            Swal.fire({icon: 'success', title: '¡Éxito!', text: 'Foto actualizada.', background: '#ffffff', color: '#1e293b', showConfirmButton: false, timer: 1500})
            .then(() => location.reload());
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.message, background: '#ffffff', color: '#1e293b'});
        }
        input.value = '';
    })
    .catch(err => {
        Swal.fire({icon: 'error', title: 'Error de conexión', background: '#ffffff', color: '#1e293b'});
        input.value = '';
    });
}

function cambiarPassword() {
    Swal.fire({
        title: 'Cambiar contraseña',
        html: `
            <div class="flex flex-col gap-4 text-left">
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Contraseña actual</label>
                    <input type="password" id="curr_pass" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 mt-1 text-sm outline-none focus:border-primary">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nueva contraseña</label>
                    <input type="password" id="new_pass" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 mt-1 text-sm outline-none focus:border-primary">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Confirmar nueva contraseña</label>
                    <input type="password" id="conf_pass" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 mt-1 text-sm outline-none focus:border-primary">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#32b4ca',
        background: '#ffffff',
        color: '#1e293b',
        preConfirm: () => {
            const curr = document.getElementById('curr_pass').value;
            const newp = document.getElementById('new_pass').value;
            const conf = document.getElementById('conf_pass').value;
            if(!curr || !newp || !conf) {
                Swal.showValidationMessage('Todos los campos son obligatorios');
                return false;
            }
            if(newp !== conf) {
                Swal.showValidationMessage('La nueva contraseña no coincide');
                return false;
            }
            return {curr, newp};
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'update_password');
            formData.append('current_password', result.value.curr);
            formData.append('new_password', result.value.newp);

            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                background: '#ffffff',
                color: '#1e293b',
                didOpen: () => { Swal.showLoading(); }
            });

            fetch('api_perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({icon: 'success', title: '¡Actualizada!', text: data.message, background: '#ffffff', color: '#1e293b'});
                } else {
                    Swal.fire({icon: 'error', title: 'Error', text: data.message, background: '#ffffff', color: '#1e293b'});
                }
            })
            .catch(err => {
                Swal.fire({icon: 'error', title: 'Error de conexión', background: '#ffffff', color: '#1e293b'});
            });
        }
    });
}
</script>
