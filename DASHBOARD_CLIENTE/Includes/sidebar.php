<?php
$menuItems = [
 ['icon' => 'dashboard', 'label' => 'Dashboard', 'page' => 'dashboard'],
 ['icon' => 'request_quote', 'label' => 'Cotizaciones', 'page' => 'cotizaciones'],
 ['icon' => 'description', 'label' => 'Documentos', 'page' => 'documentos'],
 ['icon' => 'location_on', 'label' => 'Direcciones', 'page' => 'direcciones'],
 ['icon' => 'contact_support', 'label' => 'Contacto', 'page' => 'contacto'],
];

$menuLinks = [
 'dashboard' => 'dashboard.php',
 'cotizaciones' => 'cotizaciones.php',
 'perfil' => 'perfil.php',
 'documentos' => 'documentos.php',
 'direcciones' => 'direcciones.php',
 'contacto' => 'contacto.php',
];
?>

<!-- SideNavBar -->
<aside id="userSidebar" style="background:linear-gradient(180deg,#001a3d 0%,#002451 60%,#001830 100%)"
 class="h-screen w-64 fixed left-0 top-0 flex flex-col py-6 px-4 z-50 transition-transform duration-300 -translate-x-full lg:translate-x-0">

 <!-- Logo -->
 <div class="mb-8 px-4">
 <img src="../logos/mmpharma-logotipo-horizontal-blanco.svg"
 alt="MMPharma" class="w-44 object-contain" onerror="this.src='../logos/mmpharma-logotipo-horizontal-blanco.png'"/>
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
 <a href="../index/index.php"
 class="flex items-center gap-2 w-full py-2.5 px-3 bg-white/5 text-blue-200/70 rounded-xl text-xs font-semibold hover:bg-white/10 hover:text-white transition-all">
 <span class="material-symbols-outlined text-base">language</span>
 Ir al sitio público
 </a>
 <a href="../login/logout.php"
 class="flex items-center gap-2 w-full py-2.5 px-3 bg-red-500/10 text-red-300/80 rounded-xl text-xs font-semibold hover:bg-red-500/20 hover:text-red-200 transition-all">
 <span class="material-symbols-outlined text-base">logout</span>
 Cerrar sesión
 </a>
 </div>
</aside>

<!-- Mobile Sidebar Backdrop Overlay -->
<div id="userSidebarOverlay" onclick="toggleUserSidebar()" class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>
