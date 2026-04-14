<?php
// Define los items del menú
$menuItems = [
    ['icon' => 'dashboard',        'label' => 'Dashboard',     'page' => 'dashboard'],
    ['icon' => 'inventory_2',      'label' => 'Productos',     'page' => 'productos'],
    ['icon' => 'warehouse',        'label' => 'Inventario',    'page' => 'inventario'],
    ['icon' => 'group',            'label' => 'Clientes',      'page' => 'clientes'],
    ['icon' => 'list_alt',         'label' => 'Solicitudes',   'page' => 'solicitudes'],
    ['icon' => 'shopping_cart',    'label' => 'Pedidos',       'page' => 'pedidos'],
    ['icon' => 'manage_accounts',  'label' => 'Usuarios',      'page' => 'usuarios'],
    ['icon' => 'settings',         'label' => 'Configuración', 'page' => 'configuracion'],
];

// Links de cada página (ajusta las rutas según tu estructura)
$menuLinks = [
    'dashboard'     => '../dashboard/dashboard.php',
    'productos'     => '../G_Productos/productos.php',
    'inventario'    => '../G_Inventario/inventario.php',
    'clientes'      => '../G_Clientes/clientes.php',
    'solicitudes'   => '../S_Registro/solicitudes.php',
    'pedidos'       => '../G_Pedidos/pedidos.php',
    'usuarios'      => '../G_Usuario/usuarios.php',
    'configuracion' => '../G_Usuario/configuracion.php',
];
?>

<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-[#1A5632] dark:bg-[#0a2314] shadow-xl shadow-emerald-950/20 flex flex-col py-6 px-4 z-50">
    <div class="mb-8 px-4">
    <img src="../Logos/MMPharma-Logotipo-Horizontal-Blanco.svg" alt="MMPharma Logo" class="w-44 object-contain"/>
</div>

    <nav class="flex-1 space-y-1">
        <?php foreach ($menuItems as $item): ?>
            <?php
                $isActive = isset($activePage) && $activePage === $item['page'];
                $href = $menuLinks[$item['page']] ?? '#';
                $classes = $isActive
                    ? 'flex items-center gap-3 px-4 py-3 bg-white/10 text-white rounded-lg font-medium transition-all duration-200 ease-in-out'
                    : 'flex items-center gap-3 px-4 py-3 text-emerald-100/70 hover:text-white hover:bg-white/5 transition-all duration-200 ease-in-out';
            ?>
            <a class="<?= $classes ?>" href="<?= $href ?>">
                <span class="material-symbols-outlined"><?= $item['icon'] ?></span>
                <span><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- User Profile -->
    <div class="mt-auto px-4 pt-4 border-t border-white/10">
        <div class="flex items-center gap-3">
            <img class="w-10 h-10 rounded-full border-2 border-emerald-500/30"
                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuDC_0X0u9v11asl1lCIwu3Seb4tb5fDpZxFtgharPt-q1iB5VJ1WMtFcfudvUgjd43y0u8aOQCtsq3VeOBXBpRtKKG51Jq0Nnyk6-cjyEpkZ4DQ0C5pBt5wz1iZAnUNHEwRgE1iZqWKhMce1YhkIMDdxJZ7zXTxVme0go8Gj1kPoL2VIg2ukW76YSznA1tdp448RAyt3xsGdAZMa0dJgYihyP3od4rbaIxZBzn1RrJ9JyG0w3xR3-RA7Iyo-P-nVl7pv9sxzPU4-6g"
                 alt="Admin Profile"/>
            <div class="overflow-hidden">
                <p class="text-white text-sm font-semibold truncate">Dr. Ricardo M.</p>
                <p class="text-emerald-100/50 text-xs truncate">Admin Profile</p>
            </div>
        </div>
    </div>
</aside>
