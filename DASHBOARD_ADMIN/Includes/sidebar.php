<?php
$badgePedidos = 0;
$badgeSolicitudes = 0;

if (!isset($pdo)) {
    require_once __DIR__ . '/../clinical_core/db.php';
    $pdo = getDB();
}

try {
    $badgePedidos = (int)$pdo->query("SELECT COUNT(*) FROM clientes_pedidos WHERE estado_envio='PENDIENTE'")->fetchColumn();
    $badgeSolicitudes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn();
} catch (Exception $e) {}

$menuItems = [
    ['icon' => 'dashboard',       'label' => 'Dashboard',   'page' => 'dashboard',   'badge' => 0],
    ['icon' => 'inventory_2',     'label' => 'Inventario',  'page' => 'productos',   'badge' => 0],
    ['icon' => 'group',           'label' => 'Clientes',    'page' => 'clientes',    'badge' => 0],
    ['icon' => 'shopping_cart',   'label' => 'Pedidos',     'page' => 'pedidos',     'badge' => $badgePedidos],
    ['icon' => 'list_alt',        'label' => 'Solicitudes', 'page' => 'solicitudes', 'badge' => $badgeSolicitudes],
    ['icon' => 'campaign',        'label' => 'Marketing',   'page' => 'marketing',   'badge' => 0],
];

$menuLinks = [
    'dashboard'  => '../dashboard/dashboard.php',
    'productos'  => '../G_Productos/productos.php',
    'clientes'   => '../G_Clientes/clientes.php',
    'solicitudes'=> '../S_Registro/solicitudes.php',
    'pedidos'    => '../G_Pedidos/pedidos.php',
    'marketing'  => '../G_Marketing/marketing.php',
];
?>

<!-- SideNavBar -->
<aside style="background:linear-gradient(180deg,#020d08 0%,#051a10 60%,#010a06 100%)"
       class="h-screen w-64 fixed left-0 top-0 shadow-2xl flex flex-col py-6 px-4 z-50 border-r border-white/5">

    <!-- Logo -->
    <div class="mb-8 px-4">
        <img src="../Logos/MMPharma-Logotipo-Horizontal-Blanco.svg"
             alt="MMPharma" class="w-44 object-contain"/>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1">
        <?php foreach ($menuItems as $item):
            $isActive = isset($activePage) && $activePage === $item['page'];
            $href     = $menuLinks[$item['page']] ?? '#';
        ?>
        <a href="<?= $href ?>"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group
                  <?= $isActive
                        ? 'bg-white/15 text-white shadow-lg shadow-black/20'
                        : 'text-emerald-200/60 hover:text-white hover:bg-white/8' ?>">
            <span class="material-symbols-outlined text-[20px] transition-transform duration-200
                         <?= $isActive ? 'text-emerald-300' : 'group-hover:scale-110' ?>"
                  style="font-variation-settings:'FILL' <?= $isActive ? 1 : 0 ?>,'wght' 400,'GRAD' 0,'opsz' 24">
                <?= $item['icon'] ?>
            </span>
            <span><?= $item['label'] ?></span>
            <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
            <span class="ml-auto flex h-5 w-auto min-w-[20px] px-1.5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-lg shadow-red-500/40">
                <?= $item['badge'] > 99 ? '99+' : $item['badge'] ?>
            </span>
            <?php elseif ($isActive): ?>
            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Bottom: Ir al sitio + Perfil -->
    <div class="mt-auto pt-4 border-t border-white/10 flex flex-col gap-3 px-2">
        <a href="../../INDEX/index.php"
           class="flex items-center gap-2 w-full py-2.5 px-3 bg-emerald-500/5 text-emerald-300/60 rounded-xl text-xs font-semibold hover:bg-emerald-500/15 hover:text-white transition-all border border-emerald-500/10">
            <span class="material-symbols-outlined text-base">language</span>
            Ir al sitio público
        </a>
        <a href="../../LOGIN/logout.php"
           class="flex items-center gap-2 w-full py-2.5 px-3 bg-red-500/10 text-red-300/80 rounded-xl text-xs font-semibold hover:bg-red-500/20 hover:text-red-200 transition-all">
            <span class="material-symbols-outlined text-base">logout</span>
            Cerrar sesión
        </a>
        <div class="flex items-center gap-3 pt-2 cursor-pointer" onclick="abrirPerfil()">
            <?php $fotoSide = $_SESSION['admin_foto'] ?? ''; ?>
            <?php if ($fotoSide): ?>
            <img src="<?= htmlspecialchars($fotoSide) ?>" id="sidebarProfileImg"
                 class="w-9 h-9 rounded-xl object-cover border-2 border-emerald-400/40 flex-shrink-0"
                 alt="Perfil">
            <?php else: ?>
            <div id="sidebarProfileImg"
                 class="w-9 h-9 rounded-xl border border-emerald-400/30 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                 style="background:rgba(0,129,81,0.25)">
                <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
            </div>
            <?php endif; ?>
            <div class="overflow-hidden">
                <p class="text-white text-xs font-semibold truncate" id="sidebarNombre">
                    <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?>
                </p>
                <p class="text-emerald-400/50 text-[10px]">Admin portal</p>
            </div>
        </div>
    </div>
</aside>

<!-- ══════════════════════════════════════════════════════════════════════════
     PANEL DE PERFIL (Slide-in drawer desde la derecha) - GLOBALIZADO
     ═════════════════════════════════════════════════════════════════════════ -->

<!-- Overlay -->
<div id="perfilOverlay" onclick="cerrarPerfil()"
     class="fixed inset-0 z-50 hidden transition-opacity duration-300"
     style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px)"></div>

<!-- Drawer -->
<div id="perfilDrawer"
     class="fixed top-0 right-0 h-full w-full max-w-md z-[100] overflow-y-auto
            transition-transform duration-300 translate-x-full"
     style="background:#071a10;border-left:1px solid rgba(0,129,81,0.2);box-shadow:-20px 0 60px rgba(0,0,0,0.5)">

    <!-- Header del drawer -->
    <div class="flex items-center justify-between px-6 py-5 sticky top-0 z-10"
         style="background:#071a10;border-bottom:1px solid rgba(0,129,81,0.15)">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-primary">manage_accounts</span>
            <h2 class="text-base font-bold text-white">Mi perfil</h2>
        </div>
        <button onclick="cerrarPerfil()"
                class="w-9 h-9 flex items-center justify-center rounded-xl text-on-surface-variant hover:bg-white/5 transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="px-6 py-6 space-y-6">

        <!-- ── FOTO DE PERFIL ── -->
        <div class="flex flex-col items-center py-6 px-4 rounded-2xl gap-4"
             style="background:linear-gradient(135deg,#05160e,#0a2115);border:1px solid rgba(0,129,81,0.15)">

            <!-- Avatar con botón de edición -->
            <div class="relative group cursor-pointer" onclick="document.getElementById('fotoInput').click()">
                <?php $fotoActual = $_SESSION['admin_foto'] ?? ''; ?>
                <?php if ($fotoActual): ?>
                <img id="previewFoto" src="<?= htmlspecialchars($fotoActual) ?>"
                     class="w-28 h-28 rounded-full object-cover border-4 border-emerald-500/40"
                     alt="Foto de perfil">
                <?php else: ?>
                <div id="previewFoto"
                     class="w-28 h-28 rounded-full flex items-center justify-center text-3xl font-black text-emerald-500 border-4 border-emerald-500/30"
                     style="background:rgba(0,129,81,0.2)">
                    <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
                </div>
                <?php endif; ?>
                <!-- Overlay hover -->
                <div class="absolute inset-0 rounded-full flex flex-col items-center justify-center
                            bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                    <span class="text-white text-[10px] font-bold mt-1">Cambiar</span>
                </div>
            </div>

            <input type="file" id="fotoInput" accept="image/jpeg,image/png,image/webp" class="hidden">

            <div class="text-center">
                <p class="text-white font-bold text-lg" id="drawerNombre">
                    <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Admin') ?>
                </p>
                <p class="text-on-surface-variant text-xs mt-0.5">
                    <?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>
                </p>
            </div>

            <!-- Barra de progreso subida -->
            <div id="uploadProgress" class="w-full hidden">
                <div class="w-full h-1.5 rounded-full" style="background:rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full bg-primary animate-pulse w-full"></div>
                </div>
                <p class="text-xs text-on-surface-variant text-center mt-1">Subiendo foto...</p>
            </div>

            <p class="text-[10px] text-on-surface-variant text-center opacity-60">
                JPG, PNG o WEBP · Máximo 5 MB
            </p>
        </div>

        <!-- ── DATOS PERSONALES ── -->
        <div class="rounded-2xl overflow-hidden" style="background:#05160e;border:1px solid rgba(0,129,81,0.15)">
            <div class="px-5 py-4" style="border-bottom:1px solid rgba(0,129,81,0.1)">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500 text-[16px]">person</span>
                    Datos personales
                </h3>
            </div>
            <form id="formDatos" class="p-5 space-y-4" onsubmit="guardarDatos(event)">
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                        Nombre completo
                    </label>
                    <input type="text" name="nombre" required
                           value="<?= htmlspecialchars($_SESSION['admin_nombre'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                           style="background:#071628;border:1px solid rgba(74,144,217,0.25)">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                        Correo electrónico
                    </label>
                    <input type="email" name="email" required
                           value="<?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>"
                           class="w-full px-4 py-2.5 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                           style="background:#071628;border:1px solid rgba(74,144,217,0.25)">
                </div>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-all hover:opacity-90 flex items-center justify-center gap-2"
                        style="background:#008151">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Guardar cambios
                </button>
            </form>
        </div>

        <!-- ── CAMBIAR CONTRASEÑA ── -->
        <div class="rounded-2xl overflow-hidden" style="background:#05160e;border:1px solid rgba(0,129,81,0.15)">
            <div class="px-5 py-4" style="border-bottom:1px solid rgba(0,129,81,0.1)">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[16px]">lock</span>
                    Cambiar contraseña
                </h3>
            </div>
            <form id="formPassword" class="p-5 space-y-4" onsubmit="cambiarPassword(event)">
                <?php foreach (['actual'=>'Contraseña actual','nueva'=>'Nueva contraseña','confirmar'=>'Confirmar nueva'] as $n=>$l): ?>
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mb-1.5">
                        <?= $l ?>
                    </label>
                    <div class="relative">
                        <input type="password" name="<?= $n ?>" required minlength="<?= $n==='actual'?1:8 ?>"
                               class="w-full pl-4 pr-10 py-2.5 rounded-xl text-sm text-on-surface focus:ring-2 focus:ring-primary focus:outline-none transition-all"
                               style="background:#020d08;border:1px solid rgba(0,129,81,0.25)">
                        <button type="button" onclick="togglePasswordVis(this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-white transition-colors">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <button type="submit"
                        class="w-full py-2.5 rounded-xl text-sm font-semibold transition-all hover:opacity-90 flex items-center justify-center gap-2"
                        style="background:rgba(0,129,81,0.15);color:#00a669;border:1px solid rgba(0,129,81,0.3)">
                    <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                    Actualizar contraseña
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cropper Global -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<div id="cropperModalProfile" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,0.8); backdrop-filter:blur(8px)">
    <div class="bg-[#020d08] w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">crop</span>
                Recortar Foto de Perfil
            </h3>
            <button onclick="cerrarCropperProfile()" class="text-on-surface-variant hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6">
            <div class="aspect-square w-full overflow-hidden rounded-2xl bg-black/20 mb-6">
                <img id="cropperImageProfile" src="" class="max-w-full block">
            </div>
            <div class="flex gap-3">
                <button onclick="cerrarCropperProfile()" class="flex-1 py-3 rounded-xl font-bold text-on-surface-variant bg-white/5 hover:bg-white/10 transition-all">
                    Cancelar
                </button>
                <button id="btnConfirmarRecorteProfile" class="flex-1 py-3 rounded-xl font-bold text-white bg-primary hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Aplicar y Subir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cropperProfile = null;
const base_perfil = '<?= str_replace('Includes','',dirname($_SERVER['PHP_SELF'])) ?>clinical_core/perfil_update.php';

function abrirPerfil() {
    document.getElementById('perfilOverlay').classList.remove('hidden');
    setTimeout(() => { document.getElementById('perfilDrawer').classList.remove('translate-x-full'); }, 10);
}
function cerrarPerfil() {
    document.getElementById('perfilDrawer').classList.add('translate-x-full');
    setTimeout(() => { document.getElementById('perfilOverlay').classList.add('hidden'); }, 300);
}
function cerrarCropperProfile() {
    document.getElementById('cropperModalProfile').classList.add('hidden');
    if(cropperProfile) { cropperProfile.destroy(); cropperProfile = null; }
}

document.getElementById('fotoInput').addEventListener('change', function(e) {
    const file = e.target.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
        document.getElementById('cropperImageProfile').src = event.target.result;
        document.getElementById('cropperModalProfile').classList.remove('hidden');
        if(cropperProfile) cropperProfile.destroy();
        cropperProfile = new Cropper(document.getElementById('cropperImageProfile'), { aspectRatio: 1, viewMode: 2 });
    };
    reader.readAsDataURL(file);
});

document.getElementById('btnConfirmarRecorteProfile').addEventListener('click', function() {
    if (!cropperProfile) return;
    const canvas = cropperProfile.getCroppedCanvas({ width: 400, height: 400 });
    canvas.toBlob((blob) => {
        const file = new File([blob], "perfil.jpg", { type: "image/jpeg" });
        ejecutarSubida(file);
        cerrarCropperProfile();
    }, 'image/jpeg', 0.9);
});

async function ejecutarSubida(file) {
    document.getElementById('uploadProgress').classList.remove('hidden');
    const fd = new FormData();
    fd.append('action', 'upload_foto');
    fd.append('foto', file);
    try {
        const res = await fetch(base_perfil, {method:'POST', body:fd});
        const data = await res.json();
        document.getElementById('uploadProgress').classList.add('hidden');
        if (data.ok) {
            location.reload();
        } else {
            Swal.fire({title:'Error', text:data.msg, icon:'error', background:'#0d1f3c', color:'#fff'});
        }
    } catch (e) {
        document.getElementById('uploadProgress').classList.add('hidden');
    }
}

async function guardarDatos(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'update_perfil');
    try {
        const res = await fetch(base_perfil, {method:'POST', body:fd});
        const data = await res.json();
        if (data.ok) {
            Swal.fire({title:'¡Datos guardados!', icon:'success', timer:1500, showConfirmButton:false, background:'#0d1f3c', color:'#fff'}).then(() => location.reload());
        }
    } catch(e) {}
}

async function cambiarPassword(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', 'change_password');
    try {
        const res = await fetch(base_perfil, {method:'POST', body:fd});
        const data = await res.json();
        if (data.ok) {
            e.target.reset();
            Swal.fire({title:'¡Contraseña actualizada!', icon:'success', timer:1500, showConfirmButton:false, background:'#0d1f3c', color:'#fff'});
        } else {
            Swal.fire({title:'Error', text:data.msg, icon:'error', background:'#0d1f3c', color:'#fff'});
        }
    } catch(e) {}
}

function togglePasswordVis(btn) {
    const inp = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('.material-symbols-outlined');
    if (inp.type === 'password') { inp.type = 'text'; icon.textContent = 'visibility_off'; }
    else { inp.type = 'password'; icon.textContent = 'visibility'; }
}
</script>
