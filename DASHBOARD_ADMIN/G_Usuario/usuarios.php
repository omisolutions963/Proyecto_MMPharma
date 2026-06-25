<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login/login.php");
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

// ── Asegurar claves de configuración existen ──────────────────────────────────
$claves_default = [
    'empresa_nombre'    => ['MMPharma', 'Nombre o razón social de la empresa'],
    'empresa_rfc'       => ['MMP120514PH1', 'RFC fiscal de la empresa'],
    'empresa_sede'      => ['Guadalajara', 'Ciudad sede principal'],
    'empresa_direccion' => ['Av. Insurgentes Sur 1450, Col. Actipan, CDMX', 'Dirección fiscal completa'],
    'empresa_telefono'  => ['+52 33 1234 5678', 'Teléfono de contacto'],
    'empresa_email'     => ['contacto@mmpharma.com', 'Email principal de contacto'],
];

$insertDefault = $pdo->prepare(
    "INSERT IGNORE INTO admin_configuracion (clave, valor, descripcion) VALUES (?, ?, ?)"
);
foreach ($claves_default as $clave => [$valor, $desc]) {
    $insertDefault->execute([$clave, $valor, $desc]);
}

// ── Leer configuración ─────────────────────────────────────────────────────────
$configRows = $pdo->query("SELECT clave, valor FROM admin_configuracion")->fetchAll(PDO::FETCH_KEY_PAIR);

$cfg = array_merge([
    'empresa_nombre'    => '',
    'empresa_rfc'       => '',
    'empresa_sede'      => '',
    'empresa_direccion' => '',
    'empresa_telefono'  => '',
    'empresa_email'     => '',
], $configRows);

// ── Cargar usuarios admin ─────────────────────────────────────────────────────
$admins      = $pdo->query("SELECT id, nombre, email, telefono, activo, created_at FROM admin_usuarios ORDER BY id ASC")->fetchAll();
$total_activos = (int)$pdo->query("SELECT COUNT(*) FROM admin_usuarios WHERE activo = 1")->fetchColumn();
$total_admins  = count($admins);

$flash = $_GET['msg'] ?? '';

$pageTitle = "MMPharma Portal - Configuración y usuarios";
$activePage = "usuarios";
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-[calc(100vh-4rem)]">

<!-- Header & Breadcrumbs -->
<div class="mb-8 animate-reveal">
    <div class="flex items-center gap-2 text-xs font-medium text-on-surface-variant mb-2">
        <span>Portal</span>
        <span class="material-symbols-outlined text-[12px]">chevron_right</span>
        <span class="text-primary font-bold">Configuración y usuarios</span>
    </div>
    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Administración del sistema</h2>
    <p class="text-on-surface-variant text-sm mt-1">Gestione los parámetros globales y el acceso del personal administrativo.</p>
</div>

<?php if ($flash === 'config_ok'): ?>
<div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 animate-fade-in">
    <span class="material-symbols-outlined text-[18px]">check_circle</span> Configuración guardada correctamente.
</div>
<?php endif; ?>

<div class="grid grid-cols-12 gap-8">
    <!-- Left Column: Configuration Form -->
    <div class="col-span-12 lg:col-span-7 space-y-8 animate-reveal" style="animation-delay: 0.2s">
        <section class="bg-surface-container-lowest p-8 rounded-xl border border-outline-variant/10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-on-surface tracking-tight">Configuración general</h3>
                    <p class="text-xs text-on-surface-variant">Información de identidad corporativa y fiscal.</p>
                </div>
                <span class="material-symbols-outlined text-primary-container/40 text-4xl">domain</span>
            </div>
            <form id="formConfig" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Nombre del negocio</label>
                        <input name="empresa_nombre" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed text-white outline-none" type="text" value="<?= htmlspecialchars($cfg['empresa_nombre']) ?>"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">RFC</label>
                        <input name="empresa_rfc" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed text-white outline-none" type="text" value="<?= htmlspecialchars($cfg['empresa_rfc']) ?>"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Sede principal</label>
                        <input name="empresa_sede" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed text-white outline-none" type="text" value="<?= htmlspecialchars($cfg['empresa_sede']) ?>"/>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Dirección fiscal</label>
                        <input name="empresa_direccion" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed text-white outline-none" type="text" value="<?= htmlspecialchars($cfg['empresa_direccion']) ?>"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Teléfono de contacto</label>
                        <input name="empresa_telefono" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed text-white outline-none" type="tel" value="<?= htmlspecialchars($cfg['empresa_telefono']) ?>"/>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-2">Correo del sistema</label>
                        <input name="empresa_email" class="w-full bg-surface-container-low border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-fixed text-white outline-none" type="email" value="<?= htmlspecialchars($cfg['empresa_email']) ?>"/>
                    </div>
                </div>
                <div class="pt-4 flex justify-end">
                    <button type="submit" id="btnGuardarConfig" class="px-8 py-3 bg-gradient-to-br from-primary to-secondary text-white rounded-lg text-sm font-bold hover:scale-[1.02] transition-transform flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </section>
    </div>

    <!-- Right Column -->
    <div class="col-span-12 lg:col-span-5 space-y-6 animate-reveal" style="animation-delay: 0.3s">
        <div class="bg-surface-container-low p-6 rounded-xl border-l-4 border-primary">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-white">
                    <span class="material-symbols-outlined">shield</span>
                </div>
                <div>
                    <h4 class="font-bold text-on-surface">Estado de seguridad</h4>
                    <p class="text-xs text-on-surface-variant">Protección de datos nivel 3 activa</p>
                </div>
            </div>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1">Adm. activos</p>
                <h3 class="text-3xl font-extrabold text-primary"><?= $total_activos ?></h3>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/10">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1">Total admins</p>
                <h3 class="text-3xl font-extrabold text-secondary"><?= str_pad($total_admins, 2, '0', STR_PAD_LEFT) ?></h3>
            </div>
        </div>

        <div class="bg-primary text-white p-8 rounded-xl relative overflow-hidden">
            <div class="relative z-10">
                <h4 class="text-lg font-bold mb-2">Certificación sanitaria</h4>
                <p class="text-emerald-100/70 text-sm mb-4 leading-relaxed">Su licencia de operación clínica vence en 45 días. Asegúrese de actualizar su documentación.</p>
                <a href="mailto:contacto@mmpharma.com" class="text-xs font-bold uppercase tracking-widest flex items-center gap-2 hover:gap-3 transition-all">
                    Renovar ahora <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <span class="material-symbols-outlined text-[120px]">medical_services</span>
            </div>
        </div>
    </div>

    <!-- Full Width: Users Table -->
    <div class="col-span-12 animate-reveal" style="animation-delay: 0.4s">
        <section class="bg-surface-container-lowest rounded-xl overflow-hidden border border-outline-variant/10">
            <div class="p-6 border-b border-outline-variant/10 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-on-surface tracking-tight">Usuarios admin</h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">Control de acceso para el personal administrativo.</p>
                </div>
                <button onclick="abrirModalCrear()" class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-bold hover:opacity-90 transition-all uppercase tracking-widest">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Agregar usuario admin
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
                            <th class="px-8 py-4">Nombre</th>
                            <th class="px-8 py-4">Correo</th>
                            <th class="px-8 py-4">Teléfono</th>
                            <th class="px-8 py-4 text-center">Último acceso</th>
                            <th class="px-8 py-4 text-center">Estatus</th>
                            <th class="px-8 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10" id="tabla-admins">
                    <?php if (empty($admins)): ?>
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center text-on-surface-variant text-sm italic">
                                No hay usuarios administradores registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admins as $u):
                            $iniciales = strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', $u['nombre']), 0, 2))));
                            $colorClasses = ['bg-primary/10 text-primary', 'bg-secondary/10 text-secondary', 'bg-tertiary/10 text-tertiary'];
                            $colorClass   = $colorClasses[$u['id'] % 3];
                            $esYo = ((int)$u['id'] === (int)($_SESSION['admin_id'] ?? 0));
                        ?>
                        <tr id="row-admin-<?= $u['id'] ?>" class="hover:bg-surface-container-low/30 transition-colors animate-fade-in <?= $u['activo'] ? '' : 'opacity-50' ?>">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full <?= $colorClass ?> flex items-center justify-center font-bold text-xs"><?= $iniciales ?></div>
                                    <div>
                                        <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($u['nombre']) ?></span>
                                        <?php if ($esYo): ?>
                                        <span class="ml-2 text-[9px] px-1.5 py-0.5 bg-primary/10 text-primary rounded-full font-bold uppercase">Tú</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-on-surface-variant"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="px-8 py-5 text-sm text-on-surface-variant"><?= htmlspecialchars($u['telefono'] ?: '—') ?></td>
                            <td class="px-8 py-5 text-center text-xs text-on-surface-variant"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td class="px-8 py-5 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $u['activo'] ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-error/10 text-error border border-error/20' ?>">
                                    <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="abrirModalEditar(<?= htmlspecialchars(json_encode($u)) ?>)"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-on-surface hover:bg-primary hover:text-white transition-all" title="Editar">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                    <button onclick="toggleActivo(<?= $u['id'] ?>, <?= $u['activo'] ? 'true' : 'false' ?>)"
                                        <?= $esYo ? 'disabled title="No puedes deshabilitarte a ti mismo"' : 'title="' . ($u['activo'] ? 'Deshabilitar' : 'Habilitar') . '"' ?>
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high <?= $esYo ? 'opacity-30 cursor-not-allowed' : ($u['activo'] ? 'hover:bg-error hover:text-white text-error' : 'hover:bg-emerald-600 hover:text-white text-emerald-500') ?> transition-all">
                                        <span class="material-symbols-outlined text-[16px]"><?= $u['activo'] ? 'block' : 'check_circle' ?></span>
                                    </button>
                                    <?php if (!$esYo): ?>
                                    <button onclick="eliminarAdmin(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['nombre'])) ?>')"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all" title="Eliminar">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-3 bg-surface-container-low text-[10px] text-on-surface-variant font-black uppercase tracking-widest border-t border-outline-variant/10">
                Total: <span class="text-on-surface"><?= $total_admins ?></span> administrador<?= $total_admins !== 1 ? 'es' : '' ?> · <span class="text-emerald-400"><?= $total_activos ?> activo<?= $total_activos !== 1 ? 's' : '' ?></span>
            </div>
        </section>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ MODAL CREAR ═══ -->
<div id="modalCrear" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/20 shadow-2xl w-full max-w-md animate-reveal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/10">
            <h3 class="text-lg font-black text-on-surface">Agregar usuario admin</h3>
            <button onclick="cerrarModal('modalCrear')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant">close</span>
            </button>
        </div>
        <form id="formCrear" class="p-6 space-y-4">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Nombre completo *</label>
                <input type="text" name="nombre" required class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary" placeholder="Ej. Ana García López">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Correo electrónico *</label>
                <input type="email" name="email" required class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary" placeholder="correo@mmpharma.com">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Teléfono</label>
                <input type="tel" name="telefono" class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary" placeholder="+52 33 1234 5678">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Contraseña *</label>
                <div class="relative">
                    <input type="password" id="crear_password" name="password" required minlength="8" class="w-full bg-surface-container-low rounded-xl px-4 py-3 pr-12 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary" placeholder="Mínimo 8 caracteres">
                    <button type="button" onclick="togglePwd('crear_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>
            <div class="pt-2 flex gap-3">
                <button type="button" onclick="cerrarModal('modalCrear')" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-on-surface-variant hover:bg-surface-container-high transition-all text-sm font-bold">Cancelar</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:opacity-90 transition-all">Crear usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ MODAL EDITAR ═══ -->
<div id="modalEditar" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/20 shadow-2xl w-full max-w-md animate-reveal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/10">
            <h3 class="text-lg font-black text-on-surface">Editar usuario admin</h3>
            <button onclick="cerrarModal('modalEditar')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant">close</span>
            </button>
        </div>
        <form id="formEditar" class="p-6 space-y-4">
            <input type="hidden" name="id" id="edit_id">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Nombre completo *</label>
                <input type="text" name="nombre" id="edit_nombre" required class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Correo electrónico *</label>
                <input type="email" name="email" id="edit_email" required class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Teléfono</label>
                <input type="tel" name="telefono" id="edit_telefono" class="w-full bg-surface-container-low rounded-xl px-4 py-3 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary">
            </div>
            <details class="group">
                <summary class="cursor-pointer text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-1 select-none">
                    <span class="material-symbols-outlined text-[14px] group-open:rotate-90 transition-transform">chevron_right</span>
                    Cambiar contraseña
                </summary>
                <div class="mt-3">
                    <div class="relative">
                        <input type="password" id="edit_password" name="password" minlength="8" class="w-full bg-surface-container-low rounded-xl px-4 py-3 pr-12 text-sm text-white border-none outline-none focus:ring-2 focus:ring-primary" placeholder="Nueva contraseña (mínimo 8 caracteres)">
                        <button type="button" onclick="togglePwd('edit_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-on-surface-variant mt-1.5">Déjala en blanco para no cambiarla.</p>
                </div>
            </details>
            <div class="pt-2 flex gap-3">
                <button type="button" onclick="cerrarModal('modalEditar')" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-on-surface-variant hover:bg-surface-container-high transition-all text-sm font-bold">Cancelar</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:opacity-90 transition-all">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

</main>

<script>
// ─── Guardar configuración general ────────────────────────────────────────────
document.getElementById('formConfig').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnGuardarConfig');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span> Guardando...';

    const fd = new FormData(this);
    fd.append('action', 'guardar_config');

    try {
        const res  = await fetch('api_admin_usuarios.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.ok) {
            Swal.fire({
                title: '¡Configuración guardada!',
                text: data.msg,
                icon: 'success',
                confirmButtonColor: '#003e79',
                background: '#05160e',
                color: '#f1fdf7',
            });
        } else {
            Swal.fire({ title: 'Error', text: data.error, icon: 'error', background: '#05160e', color: '#f1fdf7' });
        }
    } catch (err) {
        Swal.fire({ title: 'Error de red', text: 'No se pudo conectar con el servidor.', icon: 'error', background: '#05160e', color: '#f1fdf7' });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">save</span> Guardar cambios';
    }
});

// ─── Modal helpers ────────────────────────────────────────────────────────────
function abrirModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function cerrarModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
}
// Cerrar al hacer clic en fondo
['modalCrear','modalEditar'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(id);
    });
});
function togglePwd(inputId, btn) {
    const input = document.getElementById(inputId);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.querySelector('.material-symbols-outlined').textContent = isPass ? 'visibility_off' : 'visibility';
}

// ─── Crear usuario ────────────────────────────────────────────────────────────
function abrirModalCrear() {
    document.getElementById('formCrear').reset();
    abrirModal('modalCrear');
}

document.getElementById('formCrear').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'crear');

    const res  = await fetch('api_admin_usuarios.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
        cerrarModal('modalCrear');
        await Swal.fire({ title: '¡Usuario creado!', text: data.msg, icon: 'success', confirmButtonColor: '#003e79', background: '#05160e', color: '#f1fdf7' });
        location.reload();
    } else {
        Swal.fire({ title: 'Error', text: data.error, icon: 'error', background: '#05160e', color: '#f1fdf7' });
    }
});

// ─── Editar usuario ───────────────────────────────────────────────────────────
function abrirModalEditar(usuario) {
    document.getElementById('edit_id').value       = usuario.id;
    document.getElementById('edit_nombre').value   = usuario.nombre;
    document.getElementById('edit_email').value    = usuario.email;
    document.getElementById('edit_telefono').value = usuario.telefono || '';
    document.getElementById('edit_password').value = '';
    abrirModal('modalEditar');
}

document.getElementById('formEditar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'editar');

    // Cambiar contraseña si se ingresó
    const pwd = fd.get('password');
    if (pwd && pwd.length > 0) {
        if (pwd.length < 8) {
            Swal.fire({ title: 'Error', text: 'La contraseña debe tener al menos 8 caracteres.', icon: 'error', background: '#05160e', color: '#f1fdf7' });
            return;
        }
        // Enviar cambio de contraseña
        const fdPwd = new FormData();
        fdPwd.append('action', 'cambiar_password');
        fdPwd.append('id', fd.get('id'));
        fdPwd.append('password', pwd);
        await fetch('api_admin_usuarios.php', { method: 'POST', body: fdPwd });
    }

    const res  = await fetch('api_admin_usuarios.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
        cerrarModal('modalEditar');
        await Swal.fire({ title: '¡Actualizado!', text: data.msg, icon: 'success', confirmButtonColor: '#003e79', background: '#05160e', color: '#f1fdf7' });
        location.reload();
    } else {
        Swal.fire({ title: 'Error', text: data.error, icon: 'error', background: '#05160e', color: '#f1fdf7' });
    }
});

// ─── Toggle activo/inactivo ───────────────────────────────────────────────────
async function toggleActivo(id, esActivo) {
    const accion  = esActivo ? 'deshabilitar' : 'habilitar';
    const confirm = await Swal.fire({
        title: `¿${esActivo ? 'Deshabilitar' : 'Habilitar'} usuario?`,
        text: esActivo
            ? 'Este usuario no podrá iniciar sesión mientras esté inactivo.'
            : 'Este usuario recuperará el acceso al panel.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: esActivo ? '#ba1a1a' : '#008151',
        cancelButtonColor: '#555',
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar',
        background: '#05160e',
        color: '#f1fdf7',
    });

    if (!confirm.isConfirmed) return;

    const fd = new FormData();
    fd.append('action', 'toggle_activo');
    fd.append('id', id);

    const res  = await fetch('api_admin_usuarios.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
        location.reload();
    } else {
        Swal.fire({ title: 'Error', text: data.error, icon: 'error', background: '#05160e', color: '#f1fdf7' });
    }
}

// ─── Eliminar usuario ─────────────────────────────────────────────────────────
async function eliminarAdmin(id, nombre) {
    const confirm = await Swal.fire({
        title: '¿Eliminar usuario?',
        html: `El usuario <strong>${nombre}</strong> será eliminado permanentemente.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ba1a1a',
        cancelButtonColor: '#555',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#05160e',
        color: '#f1fdf7',
    });

    if (!confirm.isConfirmed) return;

    const fd = new FormData();
    fd.append('action', 'eliminar');
    fd.append('id', id);

    const res  = await fetch('api_admin_usuarios.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
        document.getElementById(`row-admin-${id}`)?.remove();
        Swal.fire({ title: 'Eliminado', text: data.msg, icon: 'success', confirmButtonColor: '#003e79', background: '#05160e', color: '#f1fdf7' });
    } else {
        Swal.fire({ title: 'Error', text: data.error, icon: 'error', background: '#05160e', color: '#f1fdf7' });
    }
}
</script>

<?php include("../includes/footer.php"); ?>
