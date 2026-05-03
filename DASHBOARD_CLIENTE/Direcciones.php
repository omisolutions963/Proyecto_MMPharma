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
        <button class="mt-4 md:mt-0 px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2" onclick="abrirModalDireccion()">
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
                <?php if(!empty($dir['telefono'])): ?>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">phone</span>
                    <p class="text-sm text-on-surface-variant leading-relaxed"><?= htmlspecialchars($dir['telefono']) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 mt-auto">
                <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-white text-sm font-bold rounded-xl transition-colors" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Edición de dirección en desarrollo.', background:'#071628', color:'#fff'})">Editar</button>
                <?php if(count($direcciones) === 1): ?>
                <div class="relative group">
                    <button class="px-4 py-2.5 border border-outline-variant/30 text-on-surface-variant/50 rounded-xl cursor-not-allowed flex items-center justify-center" disabled>
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-on-surface text-surface text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 text-center">
                        Debes tener al menos una dirección registrada. Agrega una nueva antes de eliminar esta.
                    </div>
                </div>
                <?php else: ?>
                <button class="px-4 py-2.5 border border-outline-variant/50 hover:bg-[#ffdad6] text-on-surface-variant hover:text-[#ba1a1a] rounded-xl transition-colors flex items-center justify-center" title="Eliminar" onclick="eliminarDireccion(<?= $dir['id'] ?>)">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-16 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-sm flex flex-col items-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant mb-4">location_off</span>
            <h3 class="text-xl font-bold text-white mb-2">No tienes direcciones registradas</h3>
            <p class="text-sm text-on-surface-variant mb-2">Agrega una dirección principal para poder recibir tus pedidos.</p>
            <p class="text-sm text-error font-bold mb-6 flex items-center gap-1.5 bg-error/10 px-3 py-1.5 rounded-lg border border-error/20"><span class="material-symbols-outlined text-[18px]">warning</span> Se requiere una dirección para poder cotizar</p>
            <button class="px-6 py-2 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20" onclick="abrirModalDireccion()">
                Agregar Dirección
            </button>
        </div>
        <?php endif; ?>

    </div>

</main>

<!-- Modal Agregar Dirección -->
<div id="modalDireccion" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity">
    <div class="bg-surface rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4 shadow-2xl relative">
        <div class="sticky top-0 bg-surface/95 backdrop-blur-md px-8 py-6 border-b border-outline-variant/20 flex justify-between items-center z-10">
            <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">location_on</span> Agregar Nueva Dirección
            </h2>
            <button class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high flex items-center justify-center text-on-surface-variant transition-colors" onclick="cerrarModalDireccion()">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-8">
            <form id="formDireccion" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Nombre de la dirección <span class="text-error">*</span></label>
                    <input type="text" name="alias" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" placeholder="Ej. Sucursal Norte, Almacén Centro, Farmacia Principal" required>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Calle y número <span class="text-error">*</span></label>
                        <input type="text" name="calle" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" placeholder="Ej. Av. Insurgentes Sur 1234" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Colonia <span class="text-error">*</span></label>
                        <input type="text" name="colonia" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Código Postal <span class="text-error">*</span></label>
                        <input type="text" name="cp" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Ciudad <span class="text-error">*</span></label>
                        <input type="text" name="ciudad" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Delegación / Municipio <span class="text-error">*</span></label>
                        <input type="text" name="municipio" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" required>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Estado <span class="text-error">*</span></label>
                        <div class="relative">
                            <select name="estado" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface appearance-none" required>
                                <option value="" disabled selected>Selecciona tu estado</option>
                                <option value="Jalisco">Jalisco</option>
                                <option value="Aguascalientes">Aguascalientes</option>
                                <option value="Baja California">Baja California</option>
                                <option value="Baja California Sur">Baja California Sur</option>
                                <option value="Campeche">Campeche</option>
                                <option value="Chiapas">Chiapas</option>
                                <option value="Chihuahua">Chihuahua</option>
                                <option value="Ciudad de México">Ciudad de México</option>
                                <option value="Coahuila">Coahuila</option>
                                <option value="Colima">Colima</option>
                                <option value="Durango">Durango</option>
                                <option value="Estado de México">Estado de México</option>
                                <option value="Guanajuato">Guanajuato</option>
                                <option value="Guerrero">Guerrero</option>
                                <option value="Hidalgo">Hidalgo</option>
                                <option value="Michoacán">Michoacán</option>
                                <option value="Morelos">Morelos</option>
                                <option value="Nayarit">Nayarit</option>
                                <option value="Nuevo León">Nuevo León</option>
                                <option value="Oaxaca">Oaxaca</option>
                                <option value="Puebla">Puebla</option>
                                <option value="Querétaro">Querétaro</option>
                                <option value="Quintana Roo">Quintana Roo</option>
                                <option value="San Luis Potosí">San Luis Potosí</option>
                                <option value="Sinaloa">Sinaloa</option>
                                <option value="Sonora">Sonora</option>
                                <option value="Tabasco">Tabasco</option>
                                <option value="Tamaulipas">Tamaulipas</option>
                                <option value="Tlaxcala">Tlaxcala</option>
                                <option value="Veracruz">Veracruz</option>
                                <option value="Yucatán">Yucatán</option>
                                <option value="Zacatecas">Zacatecas</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Teléfono de contacto <span class="text-error">*</span></label>
                        <input type="tel" name="telefono" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" placeholder="10 dígitos" required>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-outline-variant/20 flex gap-4">
                    <button type="button" class="flex-1 py-3 bg-surface-container hover:bg-surface-container-high text-on-surface font-bold rounded-xl transition-colors" onclick="cerrarModalDireccion()">Cancelar</button>
                    <button type="submit" class="flex-1 py-3 bg-primary hover:bg-primary-fixed-dim text-white font-bold rounded-xl shadow-lg shadow-primary/20 transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span> Guardar Dirección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('Includes/footer.php'); ?>

<script>
    const modalDireccion = document.getElementById('modalDireccion');
    
    function abrirModalDireccion() {
        modalDireccion.classList.remove('hidden');
        setTimeout(() => modalDireccion.classList.remove('opacity-0'), 10);
    }
    
    function cerrarModalDireccion() {
        modalDireccion.classList.add('opacity-0');
        setTimeout(() => modalDireccion.classList.add('hidden'), 300);
        document.getElementById('formDireccion').reset();
    }
    
    document.getElementById('formDireccion').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Dirección agregada',
                text: 'La dirección se guardó correctamente.',
                background: '#071628',
                color: '#fff',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        }, 1000);
    });
    
    function eliminarDireccion(id) {
        Swal.fire({
            title: '¿Eliminar dirección?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ba1a1a',
            cancelButtonColor: '#747780',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#071628',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminada',
                        text: 'La dirección fue eliminada.',
                        background: '#071628',
                        color: '#fff',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }, 1000);
            }
        });
    }
</script>
