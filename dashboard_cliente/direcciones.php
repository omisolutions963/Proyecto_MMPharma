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

$pageTitle = 'MMPharma Portal - Mis Direcciones';
$activePage = 'direcciones';
include('includes/header.php');
include('includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#ffffff">

 <!-- Header -->
 <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 animate-reveal">
 <div>
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-1">Mis direcciones</h1>
 <p class="text-slate-900 text-sm">Gestiona las ubicaciones donde recibirás tus pedidos.</p>
 </div>
 <button class="mt-4 md:mt-0 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2" onclick="abrirModalDireccion()">
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
 <div class="bg-surface-container-lowest border-2 <?= $dir['predeterminada'] ? 'border-primary/30 shadow-[0_0_20px_rgba(74,144,217,0.05)]' : 'border-outline-variant/30 ' ?> rounded-2xl p-6 relative flex flex-col transition-all hover:border-primary/50 animate-reveal" style="animation-delay: <?= $idx * 0.1 ?>s">
 <div class="mb-4 flex justify-between items-start">
 <div>
 <?php if($dir['predeterminada']): ?>
 <span class="px-2.5 py-1 bg-primary/20 text-primary text-[10px] font-black rounded-lg uppercase tracking-wider inline-block mb-3">Principal</span>
 <?php endif; ?>
 <h3 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($dir['alias']) ?></h3>
 </div>
 <span class="px-3 py-1 bg-primary/20 text-primary-light border border-primary/30 text-[10px] font-bold rounded-lg uppercase flex items-center gap-1 shadow-sm shrink-0">
    <span class="material-symbols-outlined text-[14px]">info</span> Envío se calcula al cotizar
 </span>
 </div>
 
 <div class="space-y-4 mb-8 flex-1">
 <div class="flex items-start gap-3">
 <span class="material-symbols-outlined text-on-surface-variant text-[18px] mt-0.5">location_on</span>
 <p class="text-sm text-slate-900 leading-relaxed"><?= htmlspecialchars($dir_completa) ?></p>
 </div>
 <?php if(!empty($dir['referencias'])): ?>
 <div class="flex items-start gap-3">
 <span class="material-symbols-outlined text-on-surface-variant text-[18px]">info</span>
 <p class="text-sm text-slate-900 leading-relaxed">Ref: <?= htmlspecialchars($dir['referencias']) ?></p>
 </div>
 <?php endif; ?>
 <?php if(!empty($dir['telefono'])): ?>
 <div class="flex items-start gap-3">
 <span class="material-symbols-outlined text-on-surface-variant text-[18px]">phone</span>
 <p class="text-sm text-slate-900 leading-relaxed"><?= htmlspecialchars($dir['telefono']) ?></p>
 </div>
 <?php endif; ?>
 </div>

 <div class="flex items-center gap-3 mt-auto">
 <button class="flex-1 py-2.5 bg-surface-container hover:bg-surface-container-high border border-outline-variant/50 text-slate-800 text-sm font-bold rounded-xl transition-colors" onclick='editarDireccion(<?= htmlspecialchars(json_encode($dir), ENT_QUOTES, "UTF-8") ?>)'>Editar</button>
 <?php if(count($direcciones) === 1): ?>
 <div class="relative group">
 <button class="px-4 py-2.5 border border-outline-variant/30 text-on-surface-variant/50 rounded-xl cursor-not-allowed flex items-center justify-center" disabled>
 <span class="material-symbols-outlined text-[18px]">delete</span>
 </button>
 <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-on-surface text-surface text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10 text-center">
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
   <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-16 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl flex flex-col items-center">
     <span class="material-symbols-outlined text-[48px] text-primary mb-4">location_off</span>
     <h3 class="text-xl font-bold text-slate-900 mb-2">No tienes direcciones registradas</h3>
    <p class="text-sm text-slate-900 mb-4">Agrega una dirección principal para poder recibir tus pedidos.</p>
    <p class="text-xs font-bold mb-6 flex items-center gap-2 bg-error/10 text-error px-4 py-2.5 rounded-xl border border-error/30 uppercase tracking-wider">
      <span class="material-symbols-outlined text-[18px]">warning</span> Se requiere una dirección para poder cotizar
    </p>
    <button class="px-6 py-2.5 bg-primary hover:bg-primary/90 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2" onclick="abrirModalDireccion()">
      <span class="material-symbols-outlined text-[18px]">add</span> Agregar dirección
    </button>
  </div>
 <?php endif; ?>

 </div>

</main>

<!-- Modal Agregar Dirección -->
<div id="modalDireccion" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity">
 <div class="bg-surface rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4 relative">
 <div class="sticky top-0 bg-surface/95 backdrop-blur-md px-8 py-6 border-b border-outline-variant/20 flex justify-between items-center z-10">
 <h2 id="modalTitle" class="text-xl font-bold text-on-surface flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">location_on</span> Agregar nueva dirección
 </h2>
 <button class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high flex items-center justify-center text-on-surface-variant transition-colors" onclick="cerrarModalDireccion()">
 <span class="material-symbols-outlined text-[20px]">close</span>
 </button>
 </div>
 <div class="p-8">
 <form id="formDireccion" class="space-y-6">
 <!-- Hidden ID for Edit -->
 <input type="hidden" name="id" id="direccion_id_input">
 <!-- Hidden Lat Lng -->
 <input type="hidden" name="latitud" id="latitud_input">
 <input type="hidden" name="longitud" id="longitud_input">
 
 <!-- Geolocation Toggle -->
 <div class="bg-surface-container p-5 rounded-2xl border border-outline-variant/30 flex items-center justify-between mb-2">
    <div>
        <p class="text-sm font-bold text-slate-900 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-xl">my_location</span> Usar mi ubicación actual</p>
        <p class="text-xs text-slate-900 mt-1.5 leading-relaxed pr-4">Actívalo si estás en la sucursal o domicilio de entrega en este momento. Esto calculará con exactitud tu tarifa de envío (Envío gratis a 10km a la redonda).</p>
    </div>
    <label class="relative inline-flex items-center cursor-pointer shrink-0">
      <input type="checkbox" id="geoToggle" class="sr-only peer">
      <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
    </label>
 </div>

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
 <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Código postal <span class="text-error">*</span></label>
 <input type="text" name="cp" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" required>
 </div>
 
 <div>
 <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Ciudad <span class="text-error">*</span></label>
 <div class="relative">
    <input type="text" id="ciudadInput" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" placeholder="Selecciona un estado primero" disabled>
    
    <div id="ciudadSelectWrapper" class="relative hidden">
        <select id="ciudadSelect" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface appearance-none bg-none">
            <option value="" disabled selected>Selecciona tu ciudad</option>
            <optgroup label="Más pobladas">
                <option value="Zapopan">Zapopan</option>
                <option value="Guadalajara">Guadalajara</option>
                <option value="San Pedro Tlaquepaque">San Pedro Tlaquepaque</option>
                <option value="Tlajomulco de Zúñiga">Tlajomulco de Zúñiga</option>
                <option value="Tonalá">Tonalá</option>
                <option value="Puerto Vallarta">Puerto Vallarta</option>
                <option value="El Salto">El Salto</option>
            </optgroup>
            <optgroup label="Otras ciudades de Jalisco">
                <option value="Ameca">Ameca</option>
                <option value="Arandas">Arandas</option>
                <option value="Autlán de Navarro">Autlán de Navarro</option>
                <option value="Chapala">Chapala</option>
                <option value="Lagos de Moreno">Lagos de Moreno</option>
                <option value="La Barca">La Barca</option>
                <option value="Ocotlán">Ocotlán</option>
                <option value="San Juan de los Lagos">San Juan de los Lagos</option>
                <option value="Tala">Tala</option>
                <option value="Tepatitlán de Morelos">Tepatitlán de Morelos</option>
                <option value="Zapotlán el Grande">Zapotlán el Grande (Cd. Guzmán)</option>
                <option value="Otra">Otra ciudad en Jalisco</option>
            </optgroup>
        </select>
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
    </div>
 </div>
 </div>
 
 <div>
 <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Delegación / municipio <span class="text-error">*</span></label>
 <input type="text" name="municipio" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface" required>
 </div>
 
 <div>
 <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wide">Estado <span class="text-error">*</span></label>
 <div class="relative">
 <select name="estado" class="w-full px-4 py-3 bg-surface-container-lowest border border-outline-variant/50 rounded-xl focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all text-sm text-on-surface appearance-none bg-none" required>
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
 <button type="submit" class="flex-1 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
 <span class="material-symbols-outlined text-[18px]">save</span> Guardar dirección
 </button>
 </div>
 </form>
 </div>
 </div>
</div>

<?php include('includes/footer.php'); ?>

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
 document.getElementById('direccion_id_input').value = '';
 document.getElementById('modalTitle').innerHTML = '<span class="material-symbols-outlined text-primary">location_on</span> Agregar nueva dirección';
 latInput.value = '';
 lngInput.value = '';
 geoToggle.checked = false;
 
 // Reset dynamic city input
 const selectEstado = document.querySelector('select[name="estado"]');
 selectEstado.dispatchEvent(new Event('change'));
 }
 
 function editarDireccion(dir) {
    document.getElementById('direccion_id_input').value = dir.id;
    document.querySelector('input[name="alias"]').value = dir.alias || '';
    
    // Construct calle depending on if there are exterior/interior numbers separately in DB
    let calleCompleta = dir.calle || '';
    if (dir.numero_exterior) calleCompleta += ' ' + dir.numero_exterior;
    if (dir.numero_interior) calleCompleta += ' Int. ' + dir.numero_interior;
    document.querySelector('input[name="calle"]').value = calleCompleta;
    
    document.querySelector('input[name="colonia"]').value = dir.colonia || '';
    document.querySelector('input[name="cp"]').value = dir.codigo_postal || '';
    
    // Ciudad and Municipio were merged with a comma earlier in procesar_direccion
    let ciudadStr = dir.ciudad || '';
    let ciudadParts = ciudadStr.split(', ');
    const ciudadVal = ciudadParts[0] || '';
    document.querySelector('input[name="municipio"]').value = ciudadParts[1] || '';
    
    // Estado select
    const selectEstado = document.querySelector('select[name="estado"]');
    if (dir.estado) {
        for (let i = 0; i < selectEstado.options.length; i++) {
            if (selectEstado.options[i].value.toLowerCase() === dir.estado.toLowerCase() || selectEstado.options[i].text.toLowerCase() === dir.estado.toLowerCase()) {
                selectEstado.selectedIndex = i;
                break;
            }
        }
    }
    
    // Trigger change to update UI for city (Input vs Select)
    selectEstado.dispatchEvent(new Event('change'));
    
    if (selectEstado.value === 'Jalisco') {
        const cSelect = document.getElementById('ciudadSelect');
        let matched = false;
        for (let i = 0; i < cSelect.options.length; i++) {
            if (cSelect.options[i].value.toLowerCase() === ciudadVal.toLowerCase()) {
                cSelect.selectedIndex = i;
                matched = true;
                break;
            }
        }
        if (!matched && ciudadVal !== '') {
            // If not found in our top list, select 'Otra'
            cSelect.value = 'Otra';
        }
    } else {
        document.getElementById('ciudadInput').value = ciudadVal;
    }
    
    document.querySelector('input[name="telefono"]').value = dir.telefono || '';
    
    latInput.value = dir.latitud || '';
    lngInput.value = dir.longitud || '';
    
    geoToggle.checked = !!(dir.latitud && dir.longitud);

    document.getElementById('modalTitle').innerHTML = '<span class="material-symbols-outlined text-primary">edit</span> Editar dirección';

    abrirModalDireccion();
 }
 
 const geoToggle = document.getElementById('geoToggle');
 const latInput = document.getElementById('latitud_input');
 const lngInput = document.getElementById('longitud_input');

 // Dynamic City/State Logic
 const selectEstadoForm = document.querySelector('select[name="estado"]');
 const ciudadInputEl = document.getElementById('ciudadInput');
 const ciudadSelectEl = document.getElementById('ciudadSelect');
 const ciudadSelectWrapperEl = document.getElementById('ciudadSelectWrapper');

 selectEstadoForm.addEventListener('change', function() {
    if (this.value === 'Jalisco') {
        ciudadInputEl.classList.add('hidden');
        ciudadInputEl.removeAttribute('name');
        ciudadInputEl.removeAttribute('required');
        
        ciudadSelectWrapperEl.classList.remove('hidden');
        ciudadSelectEl.setAttribute('name', 'ciudad');
        ciudadSelectEl.setAttribute('required', 'required');
    } else if (this.value !== '') {
        ciudadSelectWrapperEl.classList.add('hidden');
        ciudadSelectEl.removeAttribute('name');
        ciudadSelectEl.removeAttribute('required');
        
        ciudadInputEl.classList.remove('hidden');
        ciudadInputEl.removeAttribute('disabled');
        ciudadInputEl.setAttribute('name', 'ciudad');
        ciudadInputEl.setAttribute('required', 'required');
        ciudadInputEl.placeholder = 'Ej. Monterrey, Tijuana, etc.';
    } else {
        ciudadSelectWrapperEl.classList.add('hidden');
        ciudadSelectEl.removeAttribute('name');
        
        ciudadInputEl.classList.remove('hidden');
        ciudadInputEl.setAttribute('disabled', 'disabled');
        ciudadInputEl.removeAttribute('name');
        ciudadInputEl.placeholder = 'Selecciona un estado primero';
    }
 });

 geoToggle.addEventListener('change', function() {
    if(this.checked) {
        if (navigator.geolocation) {
            Swal.fire({
                title: 'Obteniendo ubicación...',
                text: 'Por favor, permite el acceso a tu ubicación en tu navegador.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    latInput.value = latitude;
                    lngInput.value = longitude;
                    
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&addressdetails=1`, {
                        headers: {
                            'User-Agent': 'MMPharma/1.0 (contacto@mmpharma.com)'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.address) {
                            const addr = data.address;
                            
                            // Fill fields
                            const road = addr.road || addr.pedestrian || '';
                            const houseNumber = addr.house_number ? ' ' + addr.house_number : '';
                            document.querySelector('input[name="calle"]').value = road + houseNumber;
                            
                            document.querySelector('input[name="colonia"]').value = addr.neighbourhood || addr.suburb || addr.village || '';
                            document.querySelector('input[name="cp"]').value = addr.postcode || '';
                            
                            const city = addr.city || addr.town || addr.county || '';
                            const municipio = addr.municipality || addr.city_district || addr.county || '';
                            document.querySelector('input[name="municipio"]').value = municipio !== city ? municipio : '';
                            
                            // Map state
                            const state = addr.state;
                            if (state) {
                                const select = document.querySelector('select[name="estado"]');
                                for (let i = 0; i < select.options.length; i++) {
                                    if (select.options[i].text.toLowerCase() === state.toLowerCase() || state.toLowerCase().includes(select.options[i].text.toLowerCase())) {
                                        select.selectedIndex = i;
                                        select.dispatchEvent(new Event('change')); // Trigger city UI logic
                                        break;
                                    }
                                }
                                
                                // Map city based on state selection
                                if (select.value === 'Jalisco') {
                                    const cSelect = document.getElementById('ciudadSelect');
                                    let matched = false;
                                    for (let i = 0; i < cSelect.options.length; i++) {
                                        if (cSelect.options[i].value.toLowerCase() === city.toLowerCase() || city.toLowerCase().includes(cSelect.options[i].value.toLowerCase())) {
                                            cSelect.selectedIndex = i;
                                            matched = true;
                                            break;
                                        }
                                    }
                                    if (!matched) cSelect.value = 'Otra';
                                } else {
                                    document.getElementById('ciudadInput').value = city;
                                }
                            }
                            
                            Swal.close();
                            Swal.fire({
                                icon: 'success', title: 'Ubicación obtenida', text: 'Se autocompletaron los datos basados en tu ubicación actual. Revisa que sean correctos.',
                                background: '#ffffff', color: '#1e293b', showConfirmButton: true, confirmButtonColor: '#003e79'
                            });
                        } else {
                            throw new Error("No address data");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.close();
                        Swal.fire({
                            icon: 'warning', title: 'Coordenadas obtenidas', text: 'Obtuvimos tus coordenadas, pero no pudimos autocompletar la dirección. Por favor, llénala manualmente.',
                            background: '#ffffff', color: '#1e293b', showConfirmButton: true, confirmButtonColor: '#003e79'
                        });
                    });
                },
                (error) => {
                    this.checked = false;
                    Swal.close();
                    Swal.fire({
                        icon: 'error', title: 'Error', text: 'No se pudo obtener tu ubicación. Por favor, asegúrate de dar permisos o ingresa la dirección manualmente.',
                        background: '#ffffff', color: '#1e293b'
                    });
                }, { enableHighAccuracy: true }
            );
        } else {
            this.checked = false;
            Swal.fire({
                icon: 'error', title: 'Error', text: 'Tu navegador no soporta geolocalización',
                background: '#ffffff', color: '#1e293b'
            });
        }
    } else {
        latInput.value = '';
        lngInput.value = '';
    }
 });

 document.getElementById('formDireccion').addEventListener('submit', function(e) {
 e.preventDefault();
 Swal.fire({
 title: 'Guardando...',
 allowOutsideClick: false,
 didOpen: () => { Swal.showLoading(); }
 });
 
 const formData = new FormData(this);
 const data = Object.fromEntries(formData.entries());

 fetch('procesar_direccion.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
 })
 .then(res => res.json())
 .then(res => {
     if(res.success) {
         Swal.fire({
            icon: 'success', title: 'Dirección agregada', text: 'La dirección se guardó correctamente.',
            background: '#ffffff', color: '#1e293b', showConfirmButton: false, timer: 1500
         }).then(() => {
            location.reload();
         });
     } else {
         Swal.fire({
            icon: 'error', title: 'Error', text: res.error || 'Ocurrió un error',
            background: '#ffffff', color: '#1e293b'
         });
     }
 })
 .catch(err => {
    Swal.fire({
        icon: 'error', title: 'Error', text: 'Ocurrió un error de red.',
        background: '#ffffff', color: '#1e293b'
    });
 });
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
 background: '#ffffff',
 color: '#1e293b'
 }).then((result) => {
 if (result.isConfirmed) {
 Swal.fire({
 title: 'Eliminando...',
 allowOutsideClick: false,
 didOpen: () => { Swal.showLoading(); }
 });
 
 fetch('eliminar_direccion.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: id })
 })
 .then(res => res.json())
 .then(res => {
     if(res.success) {
         Swal.fire({
             icon: 'success',
             title: 'Eliminada',
             text: 'La dirección fue eliminada.',
             background: '#ffffff',
             color: '#1e293b',
             showConfirmButton: false,
             timer: 1500
         }).then(() => {
             location.reload();
         });
     } else {
         Swal.fire({
             icon: 'error',
             title: 'Error',
             text: res.error || 'Ocurrió un error',
             background: '#ffffff',
             color: '#1e293b'
         });
     }
 })
 .catch(err => {
     Swal.fire({
         icon: 'error',
         title: 'Error',
         text: 'Error de red al eliminar la dirección.',
         background: '#ffffff',
         color: '#1e293b'
     });
 });
 }
 });
 }
</script>
