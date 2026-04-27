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

// Fetch addresses from the new table
$stmt = $pdo->prepare("
    SELECT *
    FROM clientes_direcciones 
    WHERE cliente_id = ?
    ORDER BY predeterminada DESC, id ASC
");
$stmt->execute([$cliente_id]);
$direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$tiene_direcciones = count($direcciones) > 0;

$pageTitle  = 'MMPharma Portal - Mis Direcciones';
$activePage = 'direcciones';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#071628">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 animate-reveal">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight mb-1">Mis Direcciones</h1>
            <p class="text-on-surface-variant text-sm">Gestiona las ubicaciones donde recibirás tus pedidos.</p>
        </div>
        <button class="mt-4 md:mt-0 px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Módulo de múltiples direcciones en desarrollo.', background:'#071628', color:'#fff'})">
            <span class="material-symbols-outlined text-[18px]">add</span> Agregar nueva dirección
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 animate-reveal delay-100">
        
        <?php if ($tiene_direcciones): ?>
        <?php 
        $idx = 0;
        foreach ($direcciones as $dir): 
            $idx++;
            $dir_completa = $dir['calle'];
            if (!empty($dir['numero_exterior'])) $dir_completa .= ' ' . $dir['numero_exterior'];
            if (!empty($dir['numero_interior'])) $dir_completa .= ' Int. ' . $dir['numero_interior'];
            $dir_completa .= ', Col. ' . $dir['colonia'] . ', C.P. ' . $dir['codigo_postal'] . ', ' . $dir['ciudad'] . ', ' . $dir['estado'];
        ?>
        <!-- Card Dirección -->
        <div class="bg-surface-container-lowest border-2 <?= $dir['predeterminada'] ? 'border-primary/30 shadow-[0_0_20px_rgba(74,144,217,0.05)]' : 'border-outline-variant/30 shadow-sm' ?> rounded-2xl p-6 relative flex flex-col transition-all hover:border-primary/50 animate-reveal" style="animation-delay: <?= $idx * 0.1 ?>s">
            <div class="mb-4">
                <?php if($dir['predeterminada']): ?>
                <span class="px-2.5 py-1 bg-primary/20 text-primary text-[10px] font-black rounded-lg uppercase tracking-wider inline-block mb-3">Principal</span>
                <?php endif; ?>
                <h3 class="text-lg font-bold text-white"><?= htmlspecialchars($dir['alias']) ?></h3>
            </div>
            
            <div class="space-y-4 mb-8 flex-1">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px] mt-0.5">location_on</span>
                    <p class="text-sm text-on-surface-variant leading-relaxed"><?= htmlspecialchars($dir_completa) ?></p>
                </div>
                <?php if(!empty($dir['referencias'])): ?>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">info</span>
                    <p class="text-sm text-on-surface-variant leading-relaxed">Ref: <?= htmlspecialchars($dir['referencias']) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 mt-auto">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-white text-sm font-bold rounded-xl transition-colors" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Edición de dirección en desarrollo.', background:'#071628', color:'#fff'})">Editar</button>
                <?php if(!$dir['predeterminada']): ?>
                <button class="px-4 py-2.5 border border-outline-variant/50 hover:bg-[#ffdad6] text-on-surface-variant hover:text-[#ba1a1a] rounded-xl transition-colors flex items-center justify-center" title="Eliminar" onclick="Swal.fire({icon:'warning', title:'¿Eliminar?', text:'Esta función estará lista pronto.', background:'#071628', color:'#fff'})">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-16 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant mb-4">location_off</span>
            <h3 class="text-xl font-bold text-white mb-2">No tienes direcciones registradas</h3>
            <p class="text-sm text-on-surface-variant mb-6">Agrega una dirección principal para poder recibir tus pedidos.</p>
            <button class="px-6 py-2 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Módulo de adición de dirección en desarrollo.', background:'#071628', color:'#fff'})">
                Agregar Dirección
            </button>
        </div>
        <?php endif; ?>

    </div>

</main>
<?php include('Includes/footer.php'); ?>
