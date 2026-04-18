<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

// ── FILTROS Y PAGINACIÓN ──────────────────────────────────────────────────────
$q    = trim($_GET['q'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');
$pg   = max(1, (int)($_GET['pg'] ?? 1));
$perPage = 15;
$offset  = ($pg - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];
if ($q) {
    $where .= " AND (p.nombre LIKE ? OR p.codigo LIKE ?)";
    $l = "%$q%"; $params[] = $l; $params[] = $l;
}
if ($tipo) {
    $where .= " AND p.tipo = ?";
    $params[] = $tipo;
}

// Datos
$sql = "SELECT p.*, COALESCE(s.stock_actual, 0) as stock 
        FROM productos p 
        LEFT JOIN inventario_stock s ON p.id = s.producto_id 
        $where 
        ORDER BY p.nombre ASC 
        LIMIT $perPage OFFSET $offset";
$stData = $pdo->prepare($sql);
$stData->execute($params);
$productos = $stData->fetchAll();

// ── RESPUESTA AJAX PARA INFINITE SCROLL ────────────────────────────────────────
if (isset($_GET['ajax'])) {
    if (empty($productos)) die(""); 
    foreach ($productos as $p): ?>
        <tr class="group hover:bg-surface-container-low/30 transition-colors animate-fade-in">
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center gap-3">
              <div class="w-12 h-12 rounded-xl overflow-hidden bg-surface-container-high border border-outline-variant/10">
                <?php if($p['imagen']): ?>
                  <img src="../assets/productos/<?= $p['imagen'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20">
                    <span class="material-symbols-outlined text-[20px]">image</span>
                  </div>
                <?php endif; ?>
              </div>
              <div class="flex flex-col">
                <span class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($p['nombre']) ?></span>
                <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5">Sustancia activa aquí</span>
              </div>
            </div>
          </td>
          <td class="px-8 py-4 text-sm font-mono text-on-surface-variant text-center"><?= $p['codigo'] ?: '---' ?></td>
          <td class="px-8 py-4 text-center">
            <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase <?= $p['tipo']==='RED FRIA' ? 'bg-tertiary-container/20 text-on-tertiary-container' : 'bg-secondary-container/20 text-on-secondary-container' ?>">
              <?= $p['tipo'] ?>
            </span>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black <?= $p['stock']>0 ? 'bg-tertiary-container/10 text-on-tertiary-container' : 'bg-error-container/10 text-error' ?>">
              <?= $p['stock'] ?>
            </span>
          </td>
          <td class="px-8 py-4 text-center text-xs font-bold text-on-surface">
            $<?= number_format($p['precio_farmacia'],0) ?> / $<?= number_format($p['precio_distribuidor'],0) ?> / $<?= number_format($p['precio_empresa'],0) ?>
          </td>
          <td class="px-8 py-4">
            <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button onclick='abrirEditar(<?= json_encode($p) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <form method="POST" onsubmit="return confirm('¿Eliminar producto?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
    <?php endforeach;
    exit;
}

// ── ACCIONES POST (UPSERT/DELETE) ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'delete' && $id) {
        $pdo->prepare("DELETE FROM productos WHERE id = ?")->execute([$id]);
        header("Location: productos.php?msg=deleted"); exit;
    }

    if ($action === 'upsert') {
        $nombre = $_POST['nombre'] ?? ''; $codigo = $_POST['codigo'] ?? ''; $tipo = $_POST['tipo'] ?? 'SECO';
        $p_f = (float)$_POST['precio_farmacia']; $p_d = (float)$_POST['precio_distribuidor']; $p_e = (float)$_POST['precio_empresa'];
        $stock = (int)$_POST['stock'];
        $foto_base64 = $_POST['foto_base64'] ?? '';
        
        $nombre_archivo = null;
        if (!empty($foto_base64)) {
            $data = explode(',', $foto_base64);
            $img_content = base64_decode($data[1]);
            $nombre_archivo = 'prod_' . time() . '_' . uniqid() . '.jpg';
            $ruta = '../assets/productos/' . $nombre_archivo;
            file_put_contents($ruta, $img_content);
        }

        if ($id > 0) {
            $sql = "UPDATE productos SET nombre=?, codigo=?, tipo=?, precio_farmacia=?, precio_distribuidor=?, precio_empresa=?";
            $params = [$nombre, $codigo, $tipo, $p_f, $p_d, $p_e];
            if ($nombre_archivo) { $sql .= ", imagen=?"; $params[] = $nombre_archivo; }
            $sql .= " WHERE id=?"; $params[] = $id;
            $pdo->prepare($sql)->execute($params);
        } else {
            $sql = "INSERT INTO productos (nombre, codigo, tipo, precio_farmacia, precio_distribuidor, precio_empresa, imagen) VALUES (?,?,?,?,?,?,?)";
            $pdo->prepare($sql)->execute([$nombre, $codigo, $tipo, $p_f, $p_d, $p_e, $nombre_archivo]);
            $id = $pdo->lastInsertId();
        }
        $pdo->prepare("INSERT INTO inventario_stock (producto_id, stock_actual) VALUES (?, ?) ON DUPLICATE KEY UPDATE stock_actual = ?")
            ->execute([$id, $stock, $stock]);
        header("Location: productos.php?msg=saved"); exit;
    }
}

$pageTitle = "MMPharma Portal - Gestión de Inventario";
$activePage = "inventario";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen" style="background:#071628">

<!-- Header -->
<div class="flex justify-between items-end mb-8">
  <div>
    <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Gestión de Inventario</h2>
    <p class="text-on-surface-variant text-sm mt-1">Catálogo unificado y control de existencias en tiempo real.</p>
  </div>
  <button onclick="abrirModal()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
    <span class="material-symbols-outlined">add_box</span> Nuevo Producto
  </button>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php
    $total_p = (int)$pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
    $total_s = (int)$pdo->query("SELECT COUNT(*) FROM productos WHERE tipo='SECO'")->fetchColumn();
    $total_f = (int)$pdo->query("SELECT COUNT(*) FROM productos WHERE tipo='RED FRIA'")->fetchColumn();
    $total_k = (int)$pdo->query("SELECT SUM(stock_actual) FROM inventario_stock")->fetchColumn();
    $kpis = [
        ['l'=>'Productos', 'v'=>$total_p, 'i'=>'inventory_2', 'b'=>'border-primary/40'],
        ['l'=>'Seco', 'v'=>$total_s, 'i'=>'wb_sunny', 'b'=>'border-secondary/40'],
        ['l'=>'Frío', 'v'=>$total_f, 'i'=>'ac_unit', 'b'=>'border-tertiary/40'],
        ['l'=>'Existencias', 'v'=>$total_k, 'i'=>'warehouse', 'b'=>'border-amber-500/40'],
    ];
    foreach($kpis as $index => $k): ?>
    <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 <?= $k['b'] ?> shadow-sm animate-reveal" style="animation-delay: <?= $index * 0.1 ?>s">
        <div class="flex justify-between items-center mb-1">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant"><?= $k['l'] ?></span>
            <span class="material-symbols-outlined text-on-surface-variant/30 scale-75"><?= $k['i'] ?></span>
        </div>
        <h3 class="text-2xl font-black text-on-surface"><?= number_format($k['v']) ?></h3>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filtros -->
<form method="GET" class="bg-surface-container-low p-4 rounded-2xl flex items-center gap-4 mb-8">
    <div class="flex-1 relative">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
        <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por nombre o código..." class="w-full bg-white border-none rounded-xl py-3 pl-12 pr-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none shadow-sm"/>
    </div>
    <select name="tipo" class="bg-white border-none rounded-xl py-3 px-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none shadow-sm w-48 font-bold">
        <option value="">Todos los Tipos</option>
        <option value="SECO" <?= $tipo==='SECO'?'selected':'' ?>>Cadena Seca</option>
        <option value="RED FRIA" <?= $tipo==='RED FRIA'?'selected':'' ?>>Red Fría</option>
    </select>
    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">Filtrar</button>
</form>

<!-- Tabla Centrada con Fotos -->
<div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.4s">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
          <th class="px-8 py-4 text-center">Producto</th>
          <th class="px-8 py-4 text-center">Código</th>
          <th class="px-8 py-4 text-center">Tipo</th>
          <th class="px-8 py-4 text-center">Existencias</th>
          <th class="px-8 py-4 text-center">Precios (F/D/E)</th>
          <th class="px-8 py-4 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="tableBody" class="divide-y divide-outline-variant/10">
        <?php if (empty($productos)): ?>
        <tr><td colspan="6" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-reveal">No se encontraron productos.</td></tr>
        <?php else: ?>
        <?php foreach ($productos as $p): ?>
        <tr class="group hover:bg-surface-container-low/30 transition-colors">
          <td class="px-8 py-4 text-center">
            <div class="flex flex-col items-center gap-2">
              <div class="w-10 h-10 rounded-lg overflow-hidden bg-surface-container-high border border-outline-variant/10">
                <?php if($p['imagen']): ?>
                  <img src="../assets/productos/<?= $p['imagen'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20">
                    <span class="material-symbols-outlined text-[18px]">image</span>
                  </div>
                <?php endif; ?>
              </div>
              <div class="flex flex-col">
                <span class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($p['nombre']) ?></span>
                <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5">Sustancia activa</span>
              </div>
            </div>
          </td>
          <td class="px-8 py-4 text-sm font-mono text-on-surface-variant text-center"><?= $p['codigo'] ?: '---' ?></td>
          <td class="px-8 py-4 text-center">
            <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase <?= $p['tipo']==='RED FRIA' ? 'bg-tertiary-container/20 text-on-tertiary-container' : 'bg-secondary-container/20 text-on-secondary-container' ?>">
              <?= $p['tipo'] ?>
            </span>
          </td>
          <td class="px-8 py-4 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black <?= $p['stock']>0 ? 'bg-tertiary-container/10 text-on-tertiary-container' : 'bg-error-container/10 text-error' ?>">
              <?= $p['stock'] ?>
            </span>
          </td>
          <td class="px-8 py-4 text-center text-xs font-bold text-on-surface">
            $<?= number_format($p['precio_farmacia'],0) ?> / $<?= number_format($p['precio_distribuidor'],0) ?> / $<?= number_format($p['precio_empresa'],0) ?>
          </td>
          <td class="px-8 py-4">
            <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button onclick='abrirEditar(<?= json_encode($p) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <form method="POST" onsubmit="return confirm('¿Eliminar producto?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div id="loading" class="hidden px-8 py-6 text-center">
     <div class="inline-block w-6 h-6 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
  </div>
</div>

</main>

<!-- MODAL PRODUCTO CON FOTO -->
<div id="modalProducto" class="fixed inset-0 z-[100] hidden">
    <div onclick="cerrarModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div id="modalPanel" class="absolute right-0 top-0 h-full w-full max-w-lg bg-surface shadow-2xl transition-transform duration-300 translate-x-full flex flex-col">
        <div class="px-8 py-6 border-b border-outline-variant/10 bg-primary/5">
            <h3 id="modalTitle" class="text-xl font-black text-on-surface tracking-tight">Nuevo Producto</h3>
            <p class="text-on-surface-variant text-xs mt-1">Registra o actualiza la información del catálogo.</p>
        </div>
        <form method="POST" class="flex-1 overflow-y-auto p-8 space-y-6">
            <input type="hidden" name="action" value="upsert">
            <input type="hidden" name="id" id="prod_id">
            <input type="hidden" name="foto_base64" id="foto_base64">

            <!-- Subir Foto -->
            <div class="flex flex-col items-center gap-4">
                <div class="relative group cursor-pointer w-32 h-32 rounded-2xl overflow-hidden bg-surface-container-low border-2 border-dashed border-outline-variant/30 flex items-center justify-center transition-all hover:border-primary/50" 
                     onclick="document.getElementById('fotoInput').click()">
                    <img id="previewModal" class="w-full h-full object-cover hidden">
                    <div id="placeholderModal" class="flex flex-col items-center text-on-surface-variant/40">
                        <span class="material-symbols-outlined text-3xl">add_a_photo</span>
                        <span class="text-[10px] font-bold mt-1 uppercase">Subir Foto</span>
                    </div>
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="text-white text-[10px] font-bold uppercase tracking-widest">Cambiar</span>
                    </div>
                </div>
                <input type="file" id="fotoInput" accept="image/*" class="hidden" onchange="procesarFoto(this)">
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Nombre del Producto</label>
                        <input type="text" name="nombre" id="prod_nombre" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Código</label>
                        <input type="text" name="codigo" id="prod_codigo" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tipo / Cadena</label>
                        <select name="tipo" id="prod_tipo" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
                            <option value="SECO">Cadena Seca</option>
                            <option value="RED FRIA">Red Fría</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 p-4 bg-surface-container-low rounded-2xl">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">P. Farmacia</label>
                        <input type="number" step="0.01" name="precio_farmacia" id="prod_pf" class="w-full bg-surface-container-lowest border-none rounded-lg p-2.5 text-xs font-bold focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">P. Distrib.</label>
                        <input type="number" step="0.01" name="precio_distribuidor" id="prod_pd" class="w-full bg-surface-container-lowest border-none rounded-lg p-2.5 text-xs font-bold focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">P. Empresa</label>
                        <input type="number" step="0.01" name="precio_empresa" id="prod_pe" class="w-full bg-surface-container-lowest border-none rounded-lg p-2.5 text-xs font-bold focus:ring-2 focus:ring-primary outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Existencias en Inventario</label>
                    <input type="number" name="stock" id="prod_stock" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm font-bold text-primary focus:ring-2 focus:ring-primary outline-none">
                </div>
            </div>
            <div class="flex gap-4 pt-4 sticky bottom-0 bg-surface">
                <button type="button" onclick="cerrarModal()" class="flex-1 py-4 text-xs font-bold text-on-surface-variant hover:bg-surface-container-low rounded-xl transition-all">Cancelar</button>
                <button type="submit" class="flex-1 py-4 bg-primary text-white text-xs font-bold rounded-xl shadow-lg shadow-primary/30 hover:opacity-90 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL CROPPER -->
<div id="cropperModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
    <div class="bg-surface w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border border-white/10">
        <div class="px-6 py-4 border-b border-outline-variant/10 flex justify-between items-center">
            <h3 class="text-on-surface font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">crop</span> Recortar Foto del Producto
            </h3>
            <button onclick="cerrarCropper()" class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-6">
            <div class="aspect-square w-full overflow-hidden rounded-2xl bg-black/20 mb-6">
                <img id="cropperImage" class="max-w-full block">
            </div>
            <div class="flex gap-3">
                <button onclick="cerrarCropper()" class="flex-1 py-3 rounded-xl font-bold text-on-surface-variant bg-surface-container-low">Cancelar</button>
                <button id="btnConfirmarRecorte" class="flex-1 py-3 rounded-xl font-bold text-white bg-primary">Aplicar Recorte</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let loading = false;
let hasMore = true;
let cropper = null;

// Infinite Scroll
window.addEventListener('scroll', () => {
    if (loading || !hasMore) return;
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) { loadMore(); }
});

async function loadMore() {
    loading = true; document.getElementById('loading').classList.remove('hidden'); currentPage++;
    try {
        const res = await fetch(`productos.php?ajax=1&pg=${currentPage}&q=<?= urlencode($q) ?>&tipo=<?= urlencode($tipo) ?>`);
        const html = await res.text();
        if (html.trim() === "") { hasMore = false; } else { document.getElementById('tableBody').insertAdjacentHTML('beforeend', html); }
    } catch (e) { console.error(e); } finally { loading = false; document.getElementById('loading').classList.add('hidden'); }
}

// Cropper Logic
function procesarFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('cropperImage').src = e.target.result;
            document.getElementById('cropperModal').classList.remove('hidden');
            if (cropper) cropper.destroy();
            cropper = new Cropper(document.getElementById('cropperImage'), { aspectRatio: 1, viewMode: 2 });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('btnConfirmarRecorte').addEventListener('click', () => {
    const canvas = cropper.getCroppedCanvas({ width: 600, height: 600 });
    const base64 = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('foto_base64').value = base64;
    document.getElementById('previewModal').src = base64;
    document.getElementById('previewModal').classList.remove('hidden');
    document.getElementById('placeholderModal').classList.add('hidden');
    cerrarCropper();
});

function cerrarCropper() { document.getElementById('cropperModal').classList.add('hidden'); if(cropper) cropper.destroy(); }

function abrirModal() {
    document.getElementById('modalTitle').textContent = "Nuevo Producto";
    document.getElementById('prod_id').value = "0";
    document.getElementById('foto_base64').value = "";
    document.getElementById('previewModal').classList.add('hidden');
    document.getElementById('placeholderModal').classList.remove('hidden');
    document.getElementById('prod_nombre').value = "";
    document.getElementById('prod_codigo').value = "";
    document.getElementById('prod_tipo').value = "SECO";
    document.getElementById('prod_pf').value = "0";
    document.getElementById('prod_pd').value = "0";
    document.getElementById('prod_pe').value = "0";
    document.getElementById('prod_stock').value = "0";
    document.getElementById('modalProducto').classList.remove('hidden');
    setTimeout(() => document.getElementById('modalPanel').classList.remove('translate-x-full'), 10);
}

function abrirEditar(p) {
    document.getElementById('modalTitle').textContent = "Editar Producto";
    document.getElementById('prod_id').value = p.id;
    document.getElementById('foto_base64').value = "";
    if (p.imagen) {
        document.getElementById('previewModal').src = "../assets/productos/" + p.imagen;
        document.getElementById('previewModal').classList.remove('hidden');
        document.getElementById('placeholderModal').classList.add('hidden');
    } else {
        document.getElementById('previewModal').classList.add('hidden');
        document.getElementById('placeholderModal').classList.remove('hidden');
    }
    document.getElementById('prod_nombre').value = p.nombre;
    document.getElementById('prod_codigo').value = p.codigo || '';
    document.getElementById('prod_tipo').value = p.tipo;
    document.getElementById('prod_pf').value = p.precio_farmacia;
    document.getElementById('prod_pd').value = p.precio_distribuidor;
    document.getElementById('prod_pe').value = p.precio_empresa;
    document.getElementById('prod_stock').value = p.stock || 0;
    document.getElementById('modalProducto').classList.remove('hidden');
    setTimeout(() => document.getElementById('modalPanel').classList.remove('translate-x-full'), 10);
}

function cerrarModal() {
    document.getElementById('modalPanel').classList.add('translate-x-full');
    setTimeout(() => document.getElementById('modalProducto').classList.add('hidden'), 300);
}
</script>


<?php include("../Includes/footer.php"); ?>
