<?php
$menuItems = [
 ['icon' => 'dashboard', 'label' => 'Dashboard', 'page' => 'dashboard'],
 ['icon' => 'request_quote', 'label' => 'Cotizaciones', 'page' => 'cotizaciones'],
 ['icon' => 'description', 'label' => 'Documentos', 'page' => 'documentos'],
 ['icon' => 'location_on', 'label' => 'Direcciones', 'page' => 'direcciones'],
 ['icon' => 'contact_support', 'label' => 'Contacto', 'page' => 'contacto'],
];

$menuLinks = [
 'dashboard' => 'Dashboard.php',
 'cotizaciones' => 'Cotizaciones.php',
 'perfil' => 'Perfil.php',
 'documentos' => 'Documentos.php',
 'direcciones' => 'Direcciones.php',
 'contacto' => 'Contacto.php',
];
?>

<!-- SideNavBar -->
<aside style="background:linear-gradient(180deg,#001a3d 0%,#002451 60%,#001830 100%)"
 class="h-screen w-64 fixed left-0 top-0 flex flex-col py-6 px-4 z-50">

 <!-- Logo -->
 <div class="mb-8 px-4">
 <img src="../Logos/MMPharma-Logotipo-Horizontal-Blanco.svg"
 alt="MMPharma" class="w-44 object-contain" onerror="this.src='../logos/MMPharma-Logotipo-Horizontal-Blanco.png'"/>
 </div>

 <!-- Navigation -->
 <nav class="flex-1 space-y-1 overflow-y-auto">
 <?php foreach ($menuItems as $item):
 $isActive = isset($activePage) && $activePage === $item['page'];
 $href = $menuLinks[$item['page']] ?? '#';
 ?>
 <a href="<?= $href ?>"
 class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group
 <?= $isActive
 ? 'bg-white/15 text-white '
 : 'text-blue-200/60 hover:text-white hover:bg-white/8' ?>">
 <span class="material-symbols-outlined text-[20px] transition-transform duration-200
 <?= $isActive ? 'text-blue-300' : 'group-hover:scale-110' ?>"
 style="font-variation-settings:'FILL' <?= $isActive ? 1 : 0 ?>,'wght' 400,'GRAD' 0,'opsz' 24">
 <?= $item['icon'] ?>
 </span>
 <span><?= $item['label'] ?></span>
 <?php if ($isActive): ?>
 <span class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-400"></span>
 <?php endif; ?>
 </a>
 <?php endforeach; ?>
 </nav>

 <!-- Bottom: Ir al sitio + Cerrar Sesión -->
 <div class="mt-auto pt-4 border-t border-white/10 flex flex-col gap-3 px-2">
 <a href="../INDEX/index.php"
 class="flex items-center gap-2 w-full py-2.5 px-3 bg-white/5 text-blue-200/70 rounded-xl text-xs font-semibold hover:bg-white/10 hover:text-white transition-all">
 <span class="material-symbols-outlined text-base">language</span>
 Ir al sitio público
 </a>
 <a href="../LOGIN/logout.php"
 class="flex items-center gap-2 w-full py-2.5 px-3 bg-red-500/10 text-red-300/80 rounded-xl text-xs font-semibold hover:bg-red-500/20 hover:text-red-200 transition-all">
 <span class="material-symbols-outlined text-base">logout</span>
 Cerrar sesión
 </a>
        <a href="Perfil.php" class="flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 rounded-xl transition-all border border-white/5 hover:border-white/20 mt-2 cursor-pointer group relative">
            <?php $fotoSide = $_SESSION['cliente_foto'] ?? ''; ?>
            <?php if ($fotoSide): ?>
                <img src="<?= htmlspecialchars($fotoSide) ?>"
                     class="w-10 h-10 rounded-xl object-cover border-2 border-blue-400/40 flex-shrink-0"
                     alt="Perfil">
            <?php else: ?>
                <div class="w-10 h-10 rounded-xl border border-blue-400/30 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                     style="background:rgba(74,144,217,0.25)">
                    <?= strtoupper(substr($_SESSION['cliente_nombre'] ?? 'C', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="overflow-hidden flex-1">
                <p class="text-white text-sm font-semibold truncate group-hover:text-blue-300 transition-colors">
                    <?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente') ?>
                </p>
                <p class="text-blue-400/50 text-[10px] uppercase tracking-widest flex items-center gap-1 mt-0.5">
                    <span class="material-symbols-outlined text-[12px]">manage_accounts</span> Mi Perfil
                </p>
            </div>
            <span class="material-symbols-outlined text-white/30 group-hover:text-blue-300 absolute right-3 transition-colors">chevron_right</span>
        </a>
</aside>
