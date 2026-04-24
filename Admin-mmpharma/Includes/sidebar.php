<?php
$badgePedidos = 0;
$badgeSolicitudes = 0;

if (!isset($pdo)) {
    require_once __DIR__ . '/../clinical_core/db.php';
    $pdo = getDB();
}

try {
    $badgePedidos = (int)$pdo->query("SELECT COUNT(*) FROM pedidos WHERE estado_envio='PENDIENTE'")->fetchColumn();
    $badgeSolicitudes = (int)$pdo->query("SELECT COUNT(*) FROM solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn();
} catch (Exception $e) {}

$menuItems = [
    ['icon' => 'dashboard',       'label' => 'Dashboard',   'page' => 'dashboard',   'badge' => 0],
    ['icon' => 'inventory_2',     'label' => 'Inventario',  'page' => 'productos',   'badge' => 0],
    ['icon' => 'group',           'label' => 'Clientes',    'page' => 'clientes',    'badge' => 0],
    ['icon' => 'shopping_cart',   'label' => 'Pedidos',     'page' => 'pedidos',     'badge' => $badgePedidos],
    ['icon' => 'list_alt',        'label' => 'Solicitudes', 'page' => 'solicitudes', 'badge' => $badgeSolicitudes],
];

$menuLinks = [
    'dashboard'  => '../dashboard/dashboard.php',
    'productos'  => '../G_Productos/productos.php',
    'clientes'   => '../G_Clientes/clientes.php',
    'solicitudes'=> '../S_Registro/solicitudes.php',
    'pedidos'    => '../G_Pedidos/pedidos.php',
];
?>

<!-- SideNavBar -->
<aside style="background:linear-gradient(180deg,#001a3d 0%,#002451 60%,#001830 100%)"
       class="h-screen w-64 fixed left-0 top-0 shadow-2xl flex flex-col py-6 px-4 z-50">

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
                        : 'text-blue-200/60 hover:text-white hover:bg-white/8' ?>">
            <span class="material-symbols-outlined text-[20px] transition-transform duration-200
                         <?= $isActive ? 'text-blue-300' : 'group-hover:scale-110' ?>"
                  style="font-variation-settings:'FILL' <?= $isActive ? 1 : 0 ?>,'wght' 400,'GRAD' 0,'opsz' 24">
                <?= $item['icon'] ?>
            </span>
            <span><?= $item['label'] ?></span>
            <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
            <span class="ml-auto flex h-5 w-auto min-w-[20px] px-1.5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-lg shadow-red-500/40">
                <?= $item['badge'] > 99 ? '99+' : $item['badge'] ?>
            </span>
            <?php elseif ($isActive): ?>
            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-400"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Bottom: Ir al sitio + Perfil -->
    <div class="mt-auto pt-4 border-t border-white/10 flex flex-col gap-3 px-2">
        <a href="../../INDEX/index.php"
           class="flex items-center gap-2 w-full py-2.5 px-3 bg-white/5 text-blue-200/70 rounded-xl text-xs font-semibold hover:bg-white/10 hover:text-white transition-all">
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
                 class="w-9 h-9 rounded-full object-cover border-2 border-blue-400/40 flex-shrink-0"
                 alt="Perfil">
            <?php else: ?>
            <div id="sidebarProfileImg"
                 class="w-9 h-9 rounded-full border border-blue-400/30 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                 style="background:rgba(74,144,217,0.25)">
                <?= strtoupper(substr($_SESSION['admin_nombre'] ?? 'A', 0, 1)) ?>
            </div>
            <?php endif; ?>
            <div class="overflow-hidden">
                <p class="text-white text-xs font-semibold truncate" id="sidebarNombre">
                    <?= htmlspecialchars($_SESSION['admin_nombre'] ?? 'Administrador') ?>
                </p>
                <p class="text-blue-400/50 text-[10px]">Admin Portal</p>
            </div>
        </div>
    </div>
</aside>
