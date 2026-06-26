<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

/**
 * Mezcla una imagen de origen (con transparencia PNG) en una de destino con opacidad personalizada.
 */
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
    imagealphablending($dst_im, true);
    for ($x = 0; $x < $src_w; $x++) {
        for ($y = 0; $y < $src_h; $y++) {
            $color = imagecolorat($src_im, $src_x + $x, $src_y + $y);
            $rgba = imagecolorsforindex($src_im, $color);
            
            $alpha = $rgba['alpha']; // 0 (opaco) a 127 (transparente)
            if ($alpha >= 127) {
                continue;
            }
            
            // Combinar la opacidad original de cada píxel del PNG con la opacidad global ($pct)
            $src_alpha_pct = 1 - ($alpha / 127);
            $target_alpha_pct = $src_alpha_pct * ($pct / 100);
            $new_alpha = (int)(127 * (1 - $target_alpha_pct));
            
            $bg_x = $dst_x + $x;
            $bg_y = $dst_y + $y;
            if ($bg_x >= 0 && $bg_x < imagesx($dst_im) && $bg_y >= 0 && $bg_y < imagesy($dst_im)) {
                $allocated_color = imagecolorallocatealpha($dst_im, $rgba['red'], $rgba['green'], $rgba['blue'], $new_alpha);
                imagesetpixel($dst_im, $bg_x, $bg_y, $allocated_color);
            }
        }
    }
}

/**
 * Procesa la imagen del producto para encuadrarla (hacerla cuadrada con fondo blanco),
 * aplicar un enfoque suave (retoque sutil) e insertar la marca de agua centrada con baja opacidad.
 */
function procesarImagenProducto($img_binary, $dest_path) {
    $src_img = imagecreatefromstring($img_binary);
    if (!$src_img) {
        return false;
    }
    
    $orig_w = imagesx($src_img);
    $orig_h = imagesy($src_img);
    
    // 1. Determinar tamaño del lienzo cuadrado (máximo de ancho/alto para conservar calidad, cap a 1200px)
    $max_dim = max($orig_w, $orig_h);
    $target_size = min(1200, max(600, $max_dim)); // Entre 600px y 1200px
    
    // Crear lienzo de destino cuadrado
    $dst_img = imagecreatetruecolor($target_size, $target_size);
    
    // Rellenar fondo con blanco limpio (para encuadrar de forma estética)
    $white = imagecolorallocate($dst_img, 255, 255, 255);
    imagefill($dst_img, 0, 0, $white);
    
    // Redimensionar y centrar la imagen original dentro del cuadrado blanco conservando relación de aspecto
    // Se deja un pequeño margen a los lados (el producto ocupa el 90% del lienzo cuadrado)
    $inner_size = (int)($target_size * 0.90);
    $ratio = $orig_w / $orig_h;
    if ($ratio > 1) {
        // Más ancho que alto (horizontal)
        $new_w = $inner_size;
        $new_h = (int)($inner_size / $ratio);
    } else {
        // Más alto que ancho (vertical)
        $new_h = $inner_size;
        $new_w = (int)($inner_size * $ratio);
    }
    
    $dst_x = (int)(($target_size - $new_w) / 2);
    $dst_y = (int)(($target_size - $new_h) / 2);
    
    // Copiar la imagen redimensionándola con interpolación suave
    imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $new_w, $new_h, $orig_w, $orig_h);
    imagedestroy($src_img);
    
    // 2. Retoque sutil: Enfoque ligero (sharpen) para que la imagen se vea nítida y profesional
    $sharpen_matrix = [
        [-0.1, -0.1, -0.1],
        [-0.1,  1.8, -0.1],
        [-0.1, -0.1, -0.1]
    ];
    $div = array_sum(array_map('array_sum', $sharpen_matrix)); // Da 1.0 para mantener el brillo
    imageconvolution($dst_img, $sharpen_matrix, $div, 0);
    
    // 3. Insertar marca de agua si existe
    $watermark_path = '../../productos/marca_agua.png';
    if (!file_exists($watermark_path)) {
        $watermark_path = '../../img/productos/marca_agua.png';
    }
    if (!file_exists($watermark_path)) {
        $watermark_path = '../../logos/mmpharma-isotipo.png';
    }
    
    if (file_exists($watermark_path)) {
        $wmark_src = imagecreatefrompng($watermark_path);
        if ($wmark_src) {
            imagealphablending($wmark_src, false);
            imagesavealpha($wmark_src, true);
            
            $wmark_orig_w = imagesx($wmark_src);
            $wmark_orig_h = imagesy($wmark_src);
            
            // Tamaño de marca de agua: "mediano" -> 48% del tamaño de la foto
            $wmark_target_w = (int)($target_size * 0.48);
            $wmark_target_h = (int)($wmark_orig_h * ($wmark_target_w / $wmark_orig_w));
            
            // Crear copia a escala de la marca de agua
            $wmark_scaled = imagecreatetruecolor($wmark_target_w, $wmark_target_h);
            imagealphablending($wmark_scaled, false);
            imagesavealpha($wmark_scaled, true);
            $transparent = imagecolorallocatealpha($wmark_scaled, 0, 0, 0, 127);
            imagefill($wmark_scaled, 0, 0, $transparent);
            
            imagecopyresampled($wmark_scaled, $wmark_src, 0, 0, 0, 0, $wmark_target_w, $wmark_target_h, $wmark_orig_w, $wmark_orig_h);
            
            // Calcular posición centrada
            $wmark_x = (int)(($target_size - $wmark_target_w) / 2);
            $wmark_y = (int)(($target_size - $wmark_target_h) / 2);
            
            // Mezclar marca de agua con opacidad baja (20%)
            imagecopymerge_alpha($dst_img, $wmark_scaled, $wmark_x, $wmark_y, 0, 0, $wmark_target_w, $wmark_target_h, 20);
            
            imagedestroy($wmark_src);
            imagedestroy($wmark_scaled);
        }
    }
    
    // Guardar imagen resultante como JPEG con calidad 90% (buen balance de peso y calidad)
    $result = imagejpeg($dst_img, $dest_path, 90);
    imagedestroy($dst_img);
    return $result;
}


// Obtener Categorías para el modal
$stmt_cats = $pdo->query("SELECT * FROM catalogo_categorias ORDER BY nombre ASC");
$categorias = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);

// ── FILTROS Y PAGINACIÓN ──────────────────────────────────────────────────────
$q = trim($_GET['q'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');
$filtro = trim($_GET['filtro'] ?? '');
$pg = max(1, (int)($_GET['pg'] ?? 1));
$perPage = 15;
$offset = ($pg - 1) * $perPage;

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
if ($filtro === 'sin_precio') {
  $where .= " AND p.precio_farmacia = 0 AND p.precio_distribuidor = 0";
} elseif ($filtro === 'sin_stock') {
  $where .= " AND COALESCE(s.stock_actual, 0) <= 0";
} elseif ($filtro === 'sin_imagen') {
  $where .= " AND (p.imagen IS NULL OR p.imagen = 'PENDIENTE' OR p.imagen = '')";
}

// Datos
$sql = "SELECT p.*, COALESCE(s.stock_actual, 0) as stock, c.nombre as categoria_nombre 
 FROM catalogo_productos p 
 LEFT JOIN admin_inventario_stock s ON p.id = s.producto_id 
 LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
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
 <img src="../../img/productos/<?= $p['imagen'] ?>" class="w-full h-full object-cover">
 <?php else: ?>
 <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20">
 <span class="material-symbols-outlined text-[20px]">image</span>
 </div>
 <?php endif; ?>
 </div>
 <div class="flex flex-col items-center">
 <span class="text-sm font-bold text-on-surface leading-tight text-center"><?= htmlspecialchars($p['nombre']) ?></span>
 <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5 text-center"><?= htmlspecialchars($p['sustancia'] ?: 'Sustancia no registrada') ?></span>
 <?php if ($p['en_promocion']): ?>
 <span class="mt-2 inline-flex items-center justify-center gap-1 px-2.5 py-0.5 rounded-full bg-error/10 text-error text-[9px] font-black tracking-widest uppercase border border-error/20">
 Promoción <?= (float)$p['descuento_porcentaje'] > 0 ? '-' . (float)$p['descuento_porcentaje'] . '%' : '' ?> <?= $p['promocion_perfil'] !== 'TODOS' ? '(' . $p['promocion_perfil'] . ')' : '' ?>
 </span>
 <?php endif; ?>
 </div>
 </div>
 </td>
 <td class="px-8 py-4 text-sm font-mono text-on-surface-variant text-center"><?= $p['codigo'] ?: '---' ?></td>
 <td class="px-8 py-4 text-center">
 <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase <?= $p['tipo']==='RED FRIA' ? 'bg-sky-500/10 text-sky-500' : 'bg-emerald-500/10 text-emerald-500' ?>">
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
 <td class="px-8 py-4 text-center text-sm text-on-surface-variant">
 <?= ($p['tasa_iva'] * 100) ?>%
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
 
 $qs = $_GET; unset($qs['msg']); $query_str = http_build_query($qs);
 $redirect_url = "productos.php?" . ($query_str ? $query_str . "&" : "");

 if ($action === 'delete' && $id) {
 $pdo->prepare("DELETE FROM catalogo_productos WHERE id = ?")->execute([$id]);
 header("Location: " . $redirect_url . "msg=deleted"); exit;
 }

  if ($action === 'ajuste_masivo') {
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $tipo_ajuste = $_POST['tipo_ajuste'] ?? '';
    $valor = (float)($_POST['valor'] ?? 0);
    $precios_seleccionados = $_POST['precios'] ?? [];

    if (!empty($precios_seleccionados) && in_array($tipo_ajuste, ['porcentaje', 'fijo'])) {
      $updates = [];
      $params = [];
      $i = 0;
      foreach ($precios_seleccionados as $col) {
        if (in_array($col, ['precio_farmacia', 'precio_distribuidor', 'precio_empresa'])) {
          $param_name = ":valor_" . $i;
          if ($tipo_ajuste === 'porcentaje') {
            $updates[] = "$col = ROUND($col * (1 + ($param_name / 100)), 2)";
          } else {
            $updates[] = "$col = ROUND($col + $param_name, 2)";
          }
          $params[$param_name] = $valor;
          $i++;
        }
      }
      if (!empty($updates)) {
        $sql = "UPDATE catalogo_productos SET " . implode(", ", $updates);
        if ($categoria_id > 0) {
          $sql .= " WHERE categoria_id = :categoria_id";
          $params[':categoria_id'] = $categoria_id;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header("Location: " . $redirect_url . "msg=bulk_updated"); exit;
      }
    }
    header("Location: " . $redirect_url . "msg=bulk_error"); exit;
  }

 if ($action === 'upsert') {
 $nombre = $_POST['nombre'] ?? ''; $codigo = $_POST['codigo'] ?? ''; $tipo = $_POST['tipo'] ?? 'SECO';
 $cat_id = (int)($_POST['categoria_id'] ?? 0);
 if ($cat_id === 0) $cat_id = null;
 $p_f = (float)$_POST['precio_farmacia']; $p_d = (float)$_POST['precio_distribuidor']; $p_e = (float)$_POST['precio_empresa'];
 $stock = (int)$_POST['stock'];
 $en_promocion = isset($_POST['en_promocion']) ? 1 : 0;
 $descuento_porcentaje = (float)($_POST['descuento_porcentaje'] ?? 0);
 $promocion_perfil = $_POST['promocion_perfil'] ?? 'TODOS';
 $tasa_iva = 0.16;
 $foto_base64 = $_POST['foto_base64'] ?? '';
 $procesar_imagen = isset($_POST['procesar_imagen']) ? 1 : 0;
 
 $nombre_archivo = null;
  if (!empty($foto_base64)) {
    $data = explode(',', $foto_base64);
    $img_content = base64_decode($data[1]);
    $nombre_archivo = 'prod_' . time() . '_' . uniqid() . '.jpg';
    $ruta = '../../img/productos/' . $nombre_archivo;
    if ($procesar_imagen) {
      procesarImagenProducto($img_content, $ruta);
    } else {
      file_put_contents($ruta, $img_content);
    }
  }

 if ($id > 0) {
 $sql = "UPDATE catalogo_productos SET nombre=?, codigo=?, tipo=?, categoria_id=?, precio_farmacia=?, precio_distribuidor=?, precio_empresa=?, en_promocion=?, descuento_porcentaje=?, promocion_perfil=?, tasa_iva=?";
 $params = [$nombre, $codigo, $tipo, $cat_id, $p_f, $p_d, $p_e, $en_promocion, $descuento_porcentaje, $promocion_perfil, $tasa_iva];
 if ($nombre_archivo) { $sql .= ", imagen=?"; $params[] = $nombre_archivo; }
 $sql .= " WHERE id=?"; $params[] = $id;
 $pdo->prepare($sql)->execute($params);
 } else {
 $sql = "INSERT INTO catalogo_productos (nombre, codigo, tipo, categoria_id, precio_farmacia, precio_distribuidor, precio_empresa, en_promocion, descuento_porcentaje, promocion_perfil, tasa_iva, imagen) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
 $pdo->prepare($sql)->execute([$nombre, $codigo, $tipo, $cat_id, $p_f, $p_d, $p_e, $en_promocion, $descuento_porcentaje, $promocion_perfil, $tasa_iva, $nombre_archivo]);
 $id = $pdo->lastInsertId();
 }
 $pdo->prepare("INSERT INTO admin_inventario_stock (producto_id, stock_actual) VALUES (?, ?) ON DUPLICATE KEY UPDATE stock_actual = ?")
 ->execute([$id, $stock, $stock]);
 header("Location: " . $redirect_url . "msg=saved"); exit;
 }
}

$pageTitle = "MMPharma Portal - Gestión de inventario";
$activePage = "productos";
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<!-- Header -->
<div class="flex justify-between items-end mb-8 animate-reveal">
 <div>
 <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
 <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
 <span class="material-symbols-outlined text-[12px]">chevron_right</span>
 <span class="text-on-surface-variant">Inventario</span>
 </nav>
 <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Gestión de inventario</h2>
 <p class="text-on-surface-variant text-sm mt-1">Catálogo unificado y control de existencias en tiempo real.</p>
 </div>
 <div class="flex gap-3">
  <button onclick="abrirModalAjuste()" class="bg-surface-container-high text-primary border border-primary/20 px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:bg-primary hover:text-white transition-all">
   <span class="material-symbols-outlined text-[18px]">price_change</span> Ajuste masivo
  </button>
  <a href="categorias.php" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:opacity-90 transition-all">
  <span class="material-symbols-outlined text-[18px]">category</span> Categorías
  </a>
  <button onclick="abrirModal()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:opacity-90 transition-all">
  <span class="material-symbols-outlined text-[18px]">add_box</span> Nuevo producto
  </button>
 </div>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
 <?php
 $total_p = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos")->fetchColumn();
 $total_s = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE tipo='SECO'")->fetchColumn();
 $total_f = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE tipo='RED FRIA'")->fetchColumn();
 $total_k = (int)$pdo->query("SELECT SUM(stock_actual) FROM admin_inventario_stock")->fetchColumn();
 $kpis = [
 ['l'=>'Productos', 'v'=>$total_p, 'i'=>'inventory_2', 'b'=>'border-primary/40'],
 ['l'=>'Seco', 'v'=>$total_s, 'i'=>'wb_sunny', 'b'=>'border-secondary/40'],
 ['l'=>'Frío', 'v'=>$total_f, 'i'=>'ac_unit', 'b'=>'border-sky-500/40'],
 ['l'=>'Existencias', 'v'=>$total_k, 'i'=>'warehouse', 'b'=>'border-amber-500/40'],
 ];
 foreach($kpis as $index => $k): ?>
 <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 <?= $k['b'] ?> animate-reveal" style="animation-delay: <?= $index * 0.1 ?>s">
 <div class="flex justify-between items-center mb-1">
 <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant"><?= $k['l'] ?></span>
 <span class="material-symbols-outlined text-on-surface-variant/30 scale-75"><?= $k['i'] ?></span>
 </div>
 <h3 class="text-2xl font-black text-on-surface"><?= number_format($k['v']) ?></h3>
 </div>
 <?php endforeach; ?>
 </div>

<!-- Filtro Activo Aviso -->
<?php if ($filtro): ?>
<div class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-xl flex justify-between items-center animate-reveal">
    <div class="flex items-center gap-2 text-primary font-bold text-sm">
        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
        <?php if ($filtro === 'sin_precio'): ?>
            Mostrando solo productos sin precio
        <?php elseif ($filtro === 'sin_stock'): ?>
            Mostrando solo productos sin stock
        <?php elseif ($filtro === 'sin_imagen'): ?>
            Mostrando solo productos sin imagen
        <?php endif; ?>
    </div>
    <a href="productos.php" class="text-xs text-primary hover:underline font-bold flex items-center gap-1">
        <span class="material-symbols-outlined text-[16px]">close</span> Quitar filtro
    </a>
</div>
<?php endif; ?>

<!-- Filtros -->
<form method="GET" class="bg-surface-container-low p-4 rounded-2xl flex items-center gap-4 mb-8 animate-reveal" style="animation-delay: 0.35s">
 <div class="flex-1 relative">
 <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
 <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por nombre o código..." class="w-full bg-white border-none rounded-xl py-3 pl-12 pr-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none "/>
 </div>
 <select name="tipo" class="bg-white border-none rounded-xl py-3 px-4 text-sm text-surface focus:ring-2 focus:ring-primary outline-none w-48 font-bold">
 <option value="">Todos los tipos</option>
 <option value="SECO" <?= $tipo==='SECO'?'selected':'' ?>>Cadena seca</option>
 <option value="RED FRIA" <?= $tipo==='RED FRIA'?'selected':'' ?>>Red fría</option>
 </select>
 <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:opacity-90 transition-opacity ">Filtrar</button>
</form>

<!-- Tabla Centrada con Fotos -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.4s">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
 <th class="px-8 py-4 text-center">Producto</th>
 <th class="px-8 py-4 text-center">Código</th>
 <th class="px-8 py-4 text-center">Tipo</th>
 <th class="px-8 py-4 text-center">Existencias</th>
 <th class="px-8 py-4 text-center">Precios (F/D/E)</th>
 <th class="px-8 py-4 text-center">IVA</th>
 <th class="px-8 py-4 text-center">Acciones</th>
 </tr>
 </thead>
 <tbody id="tableBody" class="divide-y divide-outline-variant/10">
 <?php if (empty($productos)): ?>
 <tr><td colspan="7" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-reveal">No se encontraron productos.</td></tr>
 <?php else: ?>
 <?php foreach ($productos as $p): ?>
 <tr class="group hover:bg-surface-container-low/30 transition-colors animate-fade-in">
 <td class="px-8 py-4 text-center">
 <div class="flex flex-col items-center gap-2">
 <div class="w-10 h-10 rounded-lg overflow-hidden bg-surface-container-high border border-outline-variant/10">
 <?php if($p['imagen']): ?>
 <img src="../../img/productos/<?= $p['imagen'] ?>" class="w-full h-full object-cover">
 <?php else: ?>
 <div class="w-full h-full flex items-center justify-center text-on-surface-variant/20">
 <span class="material-symbols-outlined text-[18px]">image</span>
 </div>
 <?php endif; ?>
 </div>
 <div class="flex flex-col items-center">
 <span class="text-sm font-bold text-on-surface leading-tight text-center"><?= htmlspecialchars($p['nombre']) ?></span>
 <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5 text-center"><?= htmlspecialchars($p['sustancia'] ?: 'Sustancia no registrada') ?></span>
 <?php if ($p['en_promocion']): ?>
 <span class="mt-2 inline-flex justify-center items-center gap-1 px-2.5 py-0.5 rounded-full bg-error/10 text-error text-[9px] font-black tracking-widest uppercase border border-error/20">
 Promoción <?= (float)$p['descuento_porcentaje'] > 0 ? '-' . (float)$p['descuento_porcentaje'] . '%' : '' ?> <?= $p['promocion_perfil'] !== 'TODOS' ? '(' . $p['promocion_perfil'] . ')' : '' ?>
 </span>
 <?php endif; ?>
 </div>
 </div>
 </td>
 <td class="px-8 py-4 text-sm font-mono text-on-surface-variant text-center"><?= $p['codigo'] ?: '---' ?></td>
 <td class="px-8 py-4 text-center">
 <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase <?= $p['tipo']==='RED FRIA' ? 'bg-sky-500/10 text-sky-500' : 'bg-emerald-500/10 text-emerald-500' ?>">
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
 <td class="px-8 py-4 text-center text-sm text-on-surface-variant"><?= ($p['tasa_iva'] * 100) ?>%</td>
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
 <div id="modalPanel" class="absolute right-0 top-0 h-full w-full max-w-2xl bg-surface transition-transform duration-300 translate-x-full flex flex-col">
 <div class="px-8 py-6 border-b border-outline-variant/10 bg-primary/5">
 <h3 id="modalTitle" class="text-xl font-black text-on-surface tracking-tight">Nuevo producto</h3>
 <p class="text-on-surface-variant text-xs mt-1">Registra o actualiza la información del catálogo.</p>
 </div>
 <form method="POST" class="flex-1 overflow-y-auto p-8 space-y-6">
 <input type="hidden" name="action" value="upsert">
 <input type="hidden" name="id" id="prod_id">
 <input type="hidden" name="foto_base64" id="foto_base64">

 <!-- Subir Foto -->
 <div class="flex flex-col items-center gap-4">
 <div class="relative group cursor-pointer w-32 h-32 rounded-2xl overflow-hidden bg-surface-container-low border-2 border-dashed border-outline-variant/30 flex items-center justify-center transition-all hover:border-primary/50" 
 onclick="document.getElementById('fotoInputProducto').click()">
 <img id="previewModal" class="w-full h-full object-cover hidden">
 <div id="placeholderModal" class="flex flex-col items-center text-on-surface-variant/40">
 <span class="material-symbols-outlined text-3xl">add_a_photo</span>
 <span class="text-[10px] font-bold mt-1 uppercase">Subir foto</span>
 </div>
 <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
 <span class="text-white text-[10px] font-bold uppercase tracking-widest">Cambiar</span>
 </div>
 </div>
 <input type="file" id="fotoInputProducto" accept="image/jpeg, image/png, image/webp" class="hidden" onchange="procesarFoto(this)">
 <label class="flex items-center gap-2 mt-3 bg-surface-container-low px-4 py-2 rounded-xl border border-outline-variant/10 cursor-pointer select-none">
    <input type="checkbox" name="procesar_imagen" id="procesar_imagen" value="1" checked class="w-4 h-4 rounded border-outline-variant/30 text-primary focus:ring-primary accent-primary cursor-pointer">
    <span class="text-xs font-semibold text-on-surface-variant">Encuadrar y marca de agua automático</span>
  </label>
 </div>

 <div class="space-y-4">
 <div class="grid grid-cols-2 gap-4">
 <div class="col-span-2">
 <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Nombre del producto</label>
 <input type="text" name="nombre" id="prod_nombre" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
 </div>
 <div>
 <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Código</label>
 <input type="text" name="codigo" id="prod_codigo" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
 </div>
  <div class="col-span-2 grid grid-cols-3 gap-4">
  <div>
  <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Categoría</label>
  <select name="categoria_id" id="prod_cat" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
  <option value="0">Sin categoría</option>
  <?php foreach($categorias as $cat): ?>
  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
  <?php endforeach; ?>
  </select>
  </div>
  <div>
  <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tipo</label>
  <select name="tipo" id="prod_tipo" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
  <option value="SECO">Cadena seca</option>
  <option value="RED FRIA">Red fría</option>
  </select>
  </div>
   <div>
   <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tasa IVA</label>
   <input type="hidden" name="tasa_iva" value="0.16">
   <select id="prod_tasa_iva" disabled class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none opacity-60 cursor-not-allowed">
   <option value="0.16">16%</option>
   </select>
   </div>
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
 <div class="grid grid-cols-2 gap-4">
  <div>
  <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Existencias en inventario</label>
  <input type="number" name="stock" id="prod_stock" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm font-bold text-primary focus:ring-2 focus:ring-primary outline-none">
  </div>
  <div class="bg-error/5 border border-error/20 p-3 rounded-xl">
  <label class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-error mb-2 cursor-pointer">
  <input type="checkbox" name="en_promocion" id="prod_promo" value="1" class="rounded border-error/50 text-error focus:ring-error w-4 h-4 bg-transparent cursor-pointer">
  Destacar en promoción
  </label>
 <div class="flex flex-col gap-2">
 <div class="flex items-center gap-2">
 <span class="text-xs text-error font-bold">% Desc:</span>
 <input type="number" step="0.01" name="descuento_porcentaje" id="prod_desc" class="w-full bg-surface border-none rounded-lg p-2 text-xs font-bold text-error focus:ring-2 focus:ring-error outline-none" placeholder="0.00">
 </div>
 <div class="flex items-center gap-2">
 <span class="text-xs text-error font-bold">Perfil:</span>
 <select name="promocion_perfil" id="prod_promo_perfil" class="w-full bg-surface border-none rounded-lg p-2 text-xs font-bold text-error focus:ring-2 focus:ring-error outline-none">
 <option value="TODOS">Todos</option>
 <option value="FARMACIA">Farmacia</option>
 <option value="DISTRIBUIDORA">Distribuidor</option>
 <option value="EMPRESA">Empresa</option>
 </select>
 </div>
 </div>
  </div>
  </div>
 </div>
 <div class="flex gap-4 pt-4 sticky bottom-0 bg-surface">
 <button type="button" onclick="cerrarModal()" class="flex-1 py-4 text-xs font-bold text-on-surface-variant hover:bg-surface-container-low rounded-xl transition-all">Cancelar</button>
 <button type="submit" class="flex-1 py-4 bg-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all flex items-center justify-center gap-2">
 <span class="material-symbols-outlined text-[18px]">save</span> Guardar
 </button>
 </div>
 </form>
 </div>
</div>

<!-- MODAL AJUSTE MASIVO DE PRECIOS -->
<div id="modalAjusteMasivo" class="fixed inset-0 z-[100] hidden">
 <div onclick="cerrarModalAjuste()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
 <div id="panelAjusteMasivo" class="absolute right-0 top-0 h-full w-full max-w-md bg-surface transition-transform duration-300 translate-x-full flex flex-col border-l border-outline-variant/30 shadow-2xl font-body">
  <div class="px-8 py-6 border-b border-outline-variant/10 bg-primary/5">
   <h3 class="text-xl font-black text-on-surface tracking-tight">Ajuste masivo de precios</h3>
   <p class="text-on-surface-variant text-xs mt-1">Aplica incrementos o disminuciones a precios en lote.</p>
  </div>
  <form id="formAjusteMasivo" method="POST" class="flex-1 overflow-y-auto p-8 space-y-6" onsubmit="confirmarAjusteMasivo(event)">
   <input type="hidden" name="action" value="ajuste_masivo">

   <!-- Categorías -->
   <div>
    <label class="block text-[10px] font-black tracking-widest text-on-surface-variant mb-2">Categoría a afectar</label>
    <select name="categoria_id" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-white font-bold">
     <option value="0">Todas las categorías</option>
     <?php foreach($categorias as $cat): ?>
     <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
     <?php endforeach; ?>
    </select>
   </div>

   <!-- Precios a ajustar (Checkboxes) -->
   <div>
    <label class="block text-[10px] font-black tracking-widest text-on-surface-variant mb-3">Precios a modificar</label>
    <div class="space-y-2.5 bg-surface-container-low p-4 rounded-xl">
     <label class="flex items-center gap-3 text-sm text-on-surface cursor-pointer">
      <input type="checkbox" name="precios[]" value="precio_farmacia" checked class="rounded border-primary/50 text-primary focus:ring-primary w-4.5 h-4.5 bg-transparent cursor-pointer">
      <span>Precio farmacia</span>
     </label>
     <label class="flex items-center gap-3 text-sm text-on-surface cursor-pointer">
      <input type="checkbox" name="precios[]" value="precio_distribuidor" checked class="rounded border-primary/50 text-primary focus:ring-primary w-4.5 h-4.5 bg-transparent cursor-pointer">
      <span>Precio distribuidor</span>
     </label>
     <label class="flex items-center gap-3 text-sm text-on-surface cursor-pointer">
      <input type="checkbox" name="precios[]" value="precio_empresa" checked class="rounded border-primary/50 text-primary focus:ring-primary w-4.5 h-4.5 bg-transparent cursor-pointer">
      <span>Precio empresa</span>
     </label>
    </div>
   </div>

   <!-- Tipo de Ajuste -->
   <div>
    <label class="block text-[10px] font-black tracking-widest text-on-surface-variant mb-3">Tipo de ajuste</label>
    <div class="grid grid-cols-2 gap-3 bg-surface-container-low p-2 rounded-xl">
     <label class="flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold text-on-surface bg-surface-container-lowest border border-outline-variant/30 cursor-pointer hover:bg-surface-container-high transition-colors">
      <input type="radio" name="tipo_ajuste" value="porcentaje" checked class="text-primary focus:ring-primary bg-transparent cursor-pointer">
      <span>Porcentual (%)</span>
     </label>
     <label class="flex items-center justify-center gap-2 p-2.5 rounded-lg text-xs font-bold text-on-surface bg-surface-container-lowest border border-outline-variant/30 cursor-pointer hover:bg-surface-container-high transition-colors">
      <input type="radio" name="tipo_ajuste" value="fijo" class="text-primary focus:ring-primary bg-transparent cursor-pointer">
      <span>Monto fijo ($)</span>
     </label>
    </div>
   </div>

   <!-- Valor del Ajuste -->
   <div>
    <label class="block text-[10px] font-black tracking-widest text-on-surface-variant mb-2">Valor del ajuste (positivo para incremento, negativo para descuento)</label>
    <input type="number" step="0.01" name="valor" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none font-bold text-white" placeholder="0.00">
   </div>

   <div class="flex gap-4 pt-4 sticky bottom-0 bg-surface">
    <button type="button" onclick="cerrarModalAjuste()" class="flex-1 py-4 text-xs font-bold text-on-surface-variant hover:bg-surface-container-low rounded-xl transition-all font-bold">Cancelar</button>
    <button type="submit" class="flex-1 py-4 bg-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all flex items-center justify-center gap-2">
     <span class="material-symbols-outlined text-[18px]">price_change</span> Aplicar ajuste
    </button>
   </div>
  </form>
 </div>
</div>

<!-- MODAL CROPPER -->
<div id="cropperModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
 <div class="bg-surface w-full max-w-lg rounded-3xl overflow-hidden border border-white/10">
 <div class="px-6 py-4 border-b border-outline-variant/10 flex justify-between items-center">
 <h3 class="text-on-surface font-bold flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">crop</span> Recortar foto del producto
 </h3>
 <button onclick="cerrarCropper()" class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined">close</span></button>
 </div>
 <div class="p-6">
 <div class="aspect-square w-full overflow-hidden rounded-2xl bg-black/20 mb-6">
 <img id="cropperImage" class="max-w-full block">
 </div>
 <div class="flex gap-3">
 <button onclick="cerrarCropper()" class="flex-1 py-3 rounded-xl font-bold text-on-surface-variant bg-surface-container-low">Cancelar</button>
 <button id="btnConfirmarRecorte" class="flex-1 py-3 rounded-xl font-bold text-white bg-primary">Aplicar recorte</button>
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
  const res = await fetch(`productos.php?ajax=1&pg=${currentPage}&q=<?= urlencode($q) ?>&tipo=<?= urlencode($tipo) ?>&filtro=<?= urlencode($filtro) ?>`);
  const html = await res.text();
  if (html.trim() === "") { hasMore = false; } else { document.getElementById('tableBody').insertAdjacentHTML('beforeend', html); }
  } catch (e) { console.error(e); } finally { loading = false; document.getElementById('loading').classList.add('hidden'); }
}

// Cropper Logic
function procesarFoto(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    if (!file.type.match('image/jpeg|image/png|image/webp')) {
      mockAction('Formato no soportado', 'Por favor sube una imagen en formato JPG, PNG o WEBP. Los formatos como HEIC no son compatibles.', 'error');
      input.value = '';
      return;
    }
    
    const autoProcess = document.getElementById('procesar_imagen') && document.getElementById('procesar_imagen').checked;
    
    const reader = new FileReader();
    reader.onload = (e) => {
      if (autoProcess) {
        // Bypass Cropper.js! Just set base64 and preview directly.
        const base64 = e.target.result;
        document.getElementById('foto_base64').value = base64;
        document.getElementById('previewModal').src = base64;
        document.getElementById('previewModal').classList.remove('hidden');
        document.getElementById('placeholderModal').classList.add('hidden');
      } else {
        // Show manual Cropper.js modal
        document.getElementById('cropperImage').src = e.target.result;
        document.getElementById('cropperModal').classList.remove('hidden');
        if (cropper) cropper.destroy();
        cropper = new Cropper(document.getElementById('cropperImage'), { aspectRatio: 1, viewMode: 2 });
      }
    };
    reader.readAsDataURL(file);
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
  document.getElementById('modalTitle').textContent = "Nuevo producto";
  document.getElementById('prod_id').value = "0";
  document.getElementById('foto_base64').value = "";
  if (document.getElementById('fotoInputProducto')) {
    document.getElementById('fotoInputProducto').value = "";
  }
  if (document.getElementById('procesar_imagen')) {
    document.getElementById('procesar_imagen').checked = true;
  }
  document.getElementById('previewModal').classList.add('hidden');
  document.getElementById('placeholderModal').classList.remove('hidden');
  document.getElementById('prod_nombre').value = "";
  document.getElementById('prod_codigo').value = "";
  document.getElementById('prod_tipo').value = "SECO";
  document.getElementById('prod_cat').value = "0";
  document.getElementById('prod_tasa_iva').value = "0.00";
  document.getElementById('prod_pf').value = "0";
  document.getElementById('prod_pd').value = "0";
  document.getElementById('prod_pe').value = "0";
  document.getElementById('prod_stock').value = "0";
  document.getElementById('prod_promo').checked = false;
  document.getElementById('prod_desc').value = "0";
  document.getElementById('prod_promo_perfil').value = "TODOS";
  document.getElementById('modalProducto').classList.remove('hidden');
  setTimeout(() => document.getElementById('modalPanel').classList.remove('translate-x-full'), 10);
}

function abrirEditar(p) {
  document.getElementById('modalTitle').textContent = "Editar producto";
  document.getElementById('prod_id').value = p.id;
  document.getElementById('foto_base64').value = "";
  if (document.getElementById('fotoInputProducto')) {
    document.getElementById('fotoInputProducto').value = "";
  }
  if (document.getElementById('procesar_imagen')) {
    document.getElementById('procesar_imagen').checked = true;
  }
  if (p.imagen) {
  document.getElementById('previewModal').src = "../../img/productos/" + p.imagen;
  document.getElementById('previewModal').classList.remove('hidden');
  document.getElementById('placeholderModal').classList.add('hidden');
  } else {
  document.getElementById('previewModal').classList.add('hidden');
  document.getElementById('placeholderModal').classList.remove('hidden');
  }
 document.getElementById('prod_nombre').value = p.nombre;
 document.getElementById('prod_codigo').value = p.codigo || '';
 document.getElementById('prod_tipo').value = p.tipo;
 document.getElementById('prod_cat').value = p.categoria_id || "0";
 document.getElementById('prod_tasa_iva').value = parseFloat(p.tasa_iva || 0).toFixed(2);
 document.getElementById('prod_pf').value = p.precio_farmacia;
 document.getElementById('prod_pd').value = p.precio_distribuidor;
 document.getElementById('prod_pe').value = p.precio_empresa;
 document.getElementById('prod_stock').value = p.stock || 0;
 document.getElementById('prod_promo').checked = p.en_promocion == 1;
 document.getElementById('prod_desc').value = p.descuento_porcentaje || 0;
 document.getElementById('prod_promo_perfil').value = p.promocion_perfil || "TODOS";
 document.getElementById('modalProducto').classList.remove('hidden');
 setTimeout(() => document.getElementById('modalPanel').classList.remove('translate-x-full'), 10);
}

function cerrarModal() {
 document.getElementById('modalPanel').classList.add('translate-x-full');
 setTimeout(() => document.getElementById('modalProducto').classList.add('hidden'), 300);
}

function abrirModalAjuste() {
  document.getElementById('modalAjusteMasivo').classList.remove('hidden');
  setTimeout(() => document.getElementById('panelAjusteMasivo').classList.remove('translate-x-full'), 10);
}

function cerrarModalAjuste() {
  document.getElementById('panelAjusteMasivo').classList.add('translate-x-full');
  setTimeout(() => document.getElementById('modalAjusteMasivo').classList.add('hidden'), 300);
}

function confirmarAjusteMasivo(e) {
  e.preventDefault();
  
  const form = e.target;
  const checkboxes = form.querySelectorAll('input[name="precios[]"]:checked');
  if (checkboxes.length === 0) {
    Swal.fire({
      title: 'Atención',
      text: 'Debes seleccionar al menos un precio para modificar.',
      icon: 'warning',
      confirmButtonColor: '#008151',
      background: '#05160e',
      color: '#f1fdf7'
    });
    return;
  }
  
  const categoriaSelect = form.querySelector('select[name="categoria_id"]');
  const categoriaNombre = categoriaSelect.options[categoriaSelect.selectedIndex].text;
  const tipoAjuste = form.querySelector('input[name="tipo_ajuste"]:checked').value;
  const valor = parseFloat(form.querySelector('input[name="valor"]').value);
  
  let textoAjuste = '';
  if (tipoAjuste === 'porcentaje') {
    textoAjuste = (valor > 0 ? 'un incremento del ' : 'un descuento del ') + Math.abs(valor) + '%';
  } else {
    textoAjuste = (valor > 0 ? 'un incremento de $' : 'un descuento de $') + Math.abs(valor);
  }
  
  const preciosList = Array.from(checkboxes).map(cb => {
    if (cb.value === 'precio_farmacia') return 'precio farmacia';
    if (cb.value === 'precio_distribuidor') return 'precio distribuidor';
    if (cb.value === 'precio_empresa') return 'precio empresa';
  }).join(', ');

  Swal.fire({
    title: '¿Confirmar ajuste masivo?',
    text: `Se aplicará ${textoAjuste} en los siguientes precios: [${preciosList}] para la categoría: "${categoriaNombre}". Esta acción afectará a todos los productos correspondientes.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#f28b82',
    cancelButtonColor: '#284a3c',
    confirmButtonText: 'Sí, aplicar',
    cancelButtonText: 'Cancelar',
    background: '#05160e',
    color: '#f1fdf7'
  }).then(result => {
    if (result.isConfirmed) {
      form.submit();
    }
  });
}
</script>

<!-- SweetAlert Notificaciones -->
<?php if (isset($_GET['msg'])): ?>
<script>
  let msgText = '';
  if ('<?= $_GET['msg'] ?>' === 'deleted') {
    msgText = 'El producto ha sido eliminado.';
  } else if ('<?= $_GET['msg'] ?>' === 'saved') {
    msgText = 'El producto ha sido guardado correctamente.';
  } else if ('<?= $_GET['msg'] ?>' === 'bulk_updated') {
    msgText = 'Los precios se han ajustado de forma masiva correctamente.';
  } else if ('<?= $_GET['msg'] ?>' === 'bulk_error') {
    msgText = 'Ocurrió un error al realizar el ajuste masivo de precios.';
  }
  
  if (msgText) {
    Swal.fire({
      title: '<?= $_GET['msg'] === 'bulk_error' ? 'Error' : '¡Operación Exitosa!' ?>',
      text: msgText,
      icon: '<?= $_GET['msg'] === 'bulk_error' ? 'error' : 'success' ?>',
      confirmButtonColor: '#008151',
      background: '#05160e',
      color: '#f1fdf7'
    });
  }
</script>
<?php endif; ?>

<?php include("../includes/footer.php"); ?>
