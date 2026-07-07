<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
 header("Location: ../../login/login.php");
 exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

$activePage = 'marketing';
$pageTitle = "Marketing | MMPharma Admin";

// 1. Fetch Banners
$banners = [];
try {
 $stmt = $pdo->query("SELECT * FROM admin_banners_promocionales ORDER BY orden ASC");
 $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $banners = []; }

// 2. Fetch Clientes for notifications
$clientes = [];
try {
 $stmt = $pdo->query("SELECT id, razon_social FROM clientes_usuarios WHERE estatus = 'ACTIVO' ORDER BY razon_social ASC");
 $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $clientes = []; }

// 3. Fetch Recent Notifications
$notif_recientes = [];
try {
 $stmt = $pdo->query("SELECT n.*, c.razon_social FROM admin_alertas_notificaciones n JOIN clientes_usuarios c ON n.cliente_id = c.id ORDER BY n.created_at DESC LIMIT 10");
 $notif_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $notif_recientes = []; }

// 4. Fetch Products for banner link options
$all_products = [];
try {
 $stmt = $pdo->query("SELECT id, nombre FROM catalogo_productos ORDER BY nombre ASC");
 $all_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $all_products = []; }

// 5. Fetch Categories for banner link options
$all_categories = [];
try {
 $stmt = $pdo->query("SELECT id, nombre FROM catalogo_categorias ORDER BY nombre ASC");
 $all_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $all_categories = []; }

include("../includes/header.php");
include("../includes/sidebar.php");
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>


<main class="ml-64 flex-1 p-8 min-h-screen bg-background text-on-surface">
 
 <!-- Header -->
 <div class="flex justify-between items-end mb-10">
 <div>
 <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
 <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
 <span class="material-symbols-outlined text-[12px]">chevron_right</span>
 <span class="text-on-surface-variant">Marketing</span>
 </nav>
 <h1 class="text-3xl font-extrabold text-on-surface tracking-tight animate-reveal">Marketing</h1>
 <p class="text-on-surface-variant text-sm mt-1 animate-reveal delay-100">Gestiona banners promocionales y notificaciones directas a clientes.</p>
 </div>
 </div>

 <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
 
 <!-- SECCIÓN BANNERS -->
 <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-8 animate-reveal" style="animation-delay: 0.2s">
 <div class="flex items-center justify-between mb-6">
 <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">ads_click</span>
 Banners del dashboard
 </h2>
 <button onclick="abrirModalBanner()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:opacity-90 transition-all">
 <span class="material-symbols-outlined text-[18px]">add</span> Nuevo banner
 </button>
 </div>

 <div class="space-y-4">
 <?php if(empty($banners)): ?>
 <p class="text-center py-10 text-white/20 italic">No hay banners activos.</p>
 <?php else: ?>
 <?php foreach($banners as $b): ?>
 <div class="flex items-center gap-4 p-4 bg-surface-container-high/20 border border-outline-variant/10 rounded-2xl group hover:bg-surface-container-high/40 transition-all animate-scale-in">
 <div class="w-24 h-14 rounded-lg overflow-hidden border border-outline-variant/20">
 <img src="../../<?= htmlspecialchars($b['ruta_imagen']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
 </div>
 <div class="flex-1">
 <h3 class="text-sm font-bold text-on-surface"><?= htmlspecialchars($b['titulo']) ?></h3>
 <p class="text-[10px] text-on-surface-variant truncate w-40"><?= htmlspecialchars($b['enlace_url'] ?: 'Sin link') ?></p>
 </div>
 <div class="flex items-center gap-2">
 <button onclick="eliminarBanner(<?= $b['id'] ?>)" class="w-9 h-9 flex items-center justify-center text-error hover:bg-error/20 rounded-xl transition-colors">
 <span class="material-symbols-outlined text-sm">delete</span>
 </button>
 </div>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>
 </div>
 </div>

 <!-- SECCIÓN NOTIFICACIONES -->
 <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-8 animate-reveal" style="animation-delay: 0.3s">
 <h2 class="text-xl font-bold text-on-surface flex items-center gap-2 mb-6">
 <span class="material-symbols-outlined text-primary">send</span>
 Enviar notificación
 </h2>

 <form id="formNotif" class="space-y-6">
 <div>
 <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Seleccionar cliente</label>
 <select name="cliente_id" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-primary outline-none text-white">
 <option value="todos" selected class="font-bold text-white">Enviar a todos los clientes</option>
 <?php foreach($clientes as $c): ?>
 <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['razon_social']) ?></option>
 <?php endforeach; ?>
 </select>
 </div>

 <div>
 <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tipo de alerta</label>
 <div class="grid grid-cols-3 gap-3">
 <label class="cursor-pointer">
 <input type="radio" name="tipo" value="INFO" checked class="hidden peer">
 <div class="p-3 border border-outline-variant/10 rounded-xl text-center text-[10px] font-bold text-on-surface-variant peer-checked:bg-primary peer-checked:text-white transition-all">INFO</div>
 </label>
 <label class="cursor-pointer">
 <input type="radio" name="tipo" value="SUCCESS" class="hidden peer">
 <div class="p-3 border border-outline-variant/10 rounded-xl text-center text-[10px] font-bold text-on-surface-variant peer-checked:bg-tertiary peer-checked:text-white transition-all">ÉXITO</div>
 </label>
 <label class="cursor-pointer">
 <input type="radio" name="tipo" value="WARNING" class="hidden peer">
 <div class="p-3 border border-outline-variant/10 rounded-xl text-center text-[10px] font-bold text-on-surface-variant peer-checked:bg-error peer-checked:text-white transition-all">ALERTA</div>
 </label>
 </div>
 </div>

 <div>
 <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Mensaje</label>
 <textarea name="mensaje" rows="3" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-primary outline-none text-white placeholder:text-white/20" placeholder="Escribe el mensaje para el cliente..."></textarea>
 </div>

 <button type="button" onclick="enviarNotificacion()" class="w-full py-4 bg-primary hover:opacity-90 text-white font-bold rounded-xl transition-all active:scale-[0.98]">
 Enviar notificación directa
 </button>
 </form>
 </div>

 <!-- HISTORIAL RECIENTE -->
 <div class="xl:col-span-2 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-8 mt-8 animate-reveal" style="animation-delay: 0.4s">
 <h2 class="text-xl font-bold text-on-surface mb-6 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">history</span>
 Historial reciente
 </h2>
 <div class="overflow-hidden rounded-2xl border border-outline-variant/10">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface-container-low text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
 <th class="px-6 py-4">Cliente</th>
 <th class="px-6 py-4">Mensaje</th>
 <th class="px-6 py-4 text-center">Tipo</th>
 <th class="px-6 py-4">Fecha</th>
 <th class="px-6 py-4 text-center">Estado</th>
 </tr>
 </thead>
 <tbody class="text-sm divide-y divide-outline-variant/10">
 <?php foreach($notif_recientes as $nr): ?>
 <tr class="group hover:bg-white/5 transition-colors">
 <td class="px-6 py-4 font-bold text-white"><?= htmlspecialchars($nr['razon_social']) ?></td>
 <td class="px-6 py-4 text-on-surface-variant"><?= htmlspecialchars($nr['mensaje']) ?></td>
 <td class="px-6 py-4 text-center">
 <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $nr['tipo'] === 'SUCCESS' ? 'bg-tertiary/20 text-tertiary' : ($nr['tipo'] === 'WARNING' ? 'bg-error/20 text-error' : 'bg-primary/20 text-primary') ?>">
 <?= $nr['tipo'] ?>
 </span>
 </td>
 <td class="px-6 py-4 text-[11px] text-on-surface-variant/60"><?= date('d/m/Y H:i', strtotime($nr['created_at'])) ?></td>
 <td class="px-6 py-4 text-center">
 <?php if($nr['leida']): ?>
 <span class="material-symbols-outlined text-tertiary text-lg" title="Leída">done_all</span>
 <?php else: ?>
 <span class="material-symbols-outlined text-on-surface-variant/30 text-lg" title="Pendiente">mail</span>
 <?php endif; ?>
 </td>
 </tr>
 <?php endforeach; ?>
 </tbody>
 </table>
 </div>
 </div>

 </div>

</main>

<script>
  const listProducts = <?= json_encode($all_products) ?>;
  const listCategories = <?= json_encode($all_categories) ?>;

  function onChangeLinkType() {
      const type = document.getElementById('b_link_type').value;
      document.getElementById('container_b_url').classList.toggle('hidden', type !== 'url');
      document.getElementById('container_b_producto').classList.toggle('hidden', type !== 'producto');
      document.getElementById('container_b_categoria').classList.toggle('hidden', type !== 'categoria');
  }

  async function enviarNotificacion() {
  const form = document.getElementById('formNotif');
  const data = new FormData(form);
  data.append('action', 'send_notif');

  if (data.get('cliente_id') == '0' || !data.get('mensaje')) {
  return Swal.fire({title:'Error', text:'Completa los campos', icon:'error', background: '#05160e', color: '#f1fdf7', confirmButtonColor: '#008151'});
  }

  const res = await fetch('procesar_marketing.php', { method: 'POST', body: data });
  const json = await res.json();

  if (json.status === 'success') {
  Swal.fire({title:'¡Enviado!', text:'La notificación llegará al dashboard del cliente.', icon:'success', background: '#05160e', color: '#f1fdf7', confirmButtonColor: '#008151'}).then(() => location.reload());
  } else {
  Swal.fire({title:'Error', text:json.message, icon:'error', background: '#05160e', color: '#f1fdf7', confirmButtonColor: '#008151'});
  }
  }

  function abrirModalBanner() {
  Swal.fire({
  title: 'Nuevo banner',
  html: `
  <div class="space-y-4 text-left p-2">
  <div>
  <label class="text-[10px] font-bold text-primary uppercase">Título</label>
  <input type="text" id="b_titulo" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary" placeholder="Ej: Oferta de Verano">
  </div>
  
  <div>
  <label class="text-[10px] font-bold text-primary uppercase">Tipo de Destino</label>
  <select id="b_link_type" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary" onchange="onChangeLinkType()">
    <option value="url">Enlace personalizado (URL)</option>
    <option value="producto">Producto del Inventario</option>
    <option value="categoria">Categoría del Catálogo</option>
  </select>
  </div>

  <div id="container_b_url">
  <label class="text-[10px] font-bold text-primary uppercase">URL (Opcional)</label>
  <input type="text" id="b_url" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary" placeholder="https://...">
  </div>

  <div id="container_b_producto" class="hidden">
  <label class="text-[10px] font-bold text-primary uppercase">Seleccionar Producto</label>
  <select id="b_producto" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary">
    <option value="">-- Seleccionar producto --</option>
  </select>
  </div>

  <div id="container_b_categoria" class="hidden">
  <label class="text-[10px] font-bold text-primary uppercase">Seleccionar Categoría</label>
  <select id="b_categoria" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary">
    <option value="">-- Seleccionar categoría --</option>
  </select>
  </div>

  <div>
  <label class="text-[10px] font-bold text-primary uppercase">Imagen del banner</label>
  <span class="block text-[10px] text-on-surface-variant/60 mt-0.5">Relación recomendada: 4:1 (ej. 1200 x 300 px)</span>
  <input type="file" id="b_file" class="w-full mt-2 text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/20 file:text-primary hover:file:bg-primary/30" accept="image/*">
  </div>
  <div id="crop_container" class="hidden mt-4 border border-outline-variant/30 rounded-xl overflow-hidden max-w-full bg-slate-950" style="max-height: 250px;">
  <img id="crop_image" class="max-w-full block" style="height: auto; max-height: 240px; margin: 0 auto;">
  </div>
  </div>
  `,
  confirmButtonText: 'Publicar banner',
  showCancelButton: true,
  cancelButtonText: 'Cancelar',
  background: '#05160e',
  color: '#f1fdf7',
  buttonsStyling: false,
  customClass: { 
    confirmButton: 'bg-primary text-white px-8 py-3 rounded-xl font-bold mx-2 hover:bg-primary/90 transition-colors', 
    cancelButton: 'bg-[#284a3c] text-[#f1fdf7] px-8 py-3 rounded-xl font-bold mx-2 hover:bg-[#1f382e] transition-colors' 
  },
  didOpen: () => {
     const fileInput = document.getElementById('b_file');
     const cropContainer = document.getElementById('crop_container');
     const cropImage = document.getElementById('crop_image');
     let cropper = null;

     // Populate products select
     const prodSelect = document.getElementById('b_producto');
     listProducts.forEach(p => {
         const opt = document.createElement('option');
         opt.value = p.id;
         opt.textContent = p.nombre;
         prodSelect.appendChild(opt);
     });

     // Populate categories select
     const catSelect = document.getElementById('b_categoria');
     listCategories.forEach(c => {
         const opt = document.createElement('option');
         opt.value = c.id;
         opt.textContent = c.nombre;
         catSelect.appendChild(opt);
     });

     fileInput.addEventListener('change', function(e) {
       const file = e.target.files[0];
       if (file) {
         const reader = new FileReader();
         reader.onload = function(event) {
           cropImage.src = event.target.result;
           cropContainer.classList.remove('hidden');
           
           if (cropper) {
             cropper.destroy();
           }
           
           cropper = new Cropper(cropImage, {
             aspectRatio: 4 / 1, // Relación de aspecto del banner del catálogo (Widescreen 4:1)
             viewMode: 1,
             autoCropArea: 1,
             responsive: true,
             restore: false,
             guides: true,
             center: true,
             highlight: false,
             cropBoxMovable: true,
             cropBoxResizable: true,
             toggleDragModeOnDblclick: false,
           });
           window.currentCropper = cropper;
         };
         reader.readAsDataURL(file);
       }
     });
   },
   willClose: () => {
     if (window.currentCropper) {
       window.currentCropper.destroy();
       delete window.currentCropper;
     }
   },
   preConfirm: async () => {
     const titulo = document.getElementById('b_titulo').value;
     const linkType = document.getElementById('b_link_type').value;
     let url = '';
     if (linkType === 'url') {
       url = document.getElementById('b_url').value;
     } else if (linkType === 'producto') {
       const prodId = document.getElementById('b_producto').value;
       if (prodId) {
         url = 'catalogo/producto.php?id=' + prodId;
       }
     } else if (linkType === 'categoria') {
       const catId = document.getElementById('b_categoria').value;
       if (catId) {
         url = 'catalogo/catalogo.php?categoria_id=' + catId;
       }
     }
     
     const fileInput = document.getElementById('b_file');
     const file = fileInput.files[0];
     
     if (!titulo || !file) {
       Swal.showValidationMessage('Título e imagen son requeridos');
       return false;
     }
     
     if (window.currentCropper) {
       return new Promise((resolve) => {
         window.currentCropper.getCroppedCanvas({
           width: 1200,
           height: 300,
         }).toBlob((blob) => {
           const croppedFile = new File([blob], file.name, { type: file.type });
           resolve({ titulo, url, file: croppedFile });
         }, file.type);
       });
     }
     
     return { titulo, url, file };
   }
  }).then(async (result) => {
  if (result.isConfirmed) {
  const data = new FormData();
  data.append('action', 'add_banner');
  data.append('titulo', result.value.titulo);
  data.append('url', result.value.url);
  data.append('banner_img', result.value.file);

  const res = await fetch('procesar_marketing.php', { method: 'POST', body: data });
  const json = await res.json();
  if (json.status === 'success') {
  Swal.fire({title:'¡Éxito!', text:'Banner publicado correctamente.', icon:'success', background: '#05160e', color: '#f1fdf7', confirmButtonColor: '#008151'}).then(() => location.reload());
  } else {
  Swal.fire({title:'Error', text:json.message, icon:'error', background: '#05160e', color: '#f1fdf7', confirmButtonColor: '#008151'});
  }
  }
  });
  }

  async function eliminarBanner(id) {
  confirmAction('¿Eliminar banner?', 'Esta acción no se puede deshacer.', 'Sí, eliminar', async () => {
  const data = new FormData();
  data.append('action', 'del_banner');
  data.append('id', id);
  await fetch('procesar_marketing.php', { method: 'POST', body: data });
  location.reload();
  });
  }
</script>

<?php include("../includes/footer.php"); ?>
