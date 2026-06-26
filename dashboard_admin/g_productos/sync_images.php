<?php
// 1. Detección automática del puerto de MySQL
$detected_port = '3307';
try {
    $test_dsn = 'mysql:host=127.0.0.1;port=3307;dbname=mm_pharma;charset=utf8mb4';
    $test_pdo = new PDO($test_dsn, 'root', '', [
        PDO::ATTR_TIMEOUT => 1,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    $detected_port = '3306';
}

if (!defined('DB_PORT')) {
    define('DB_PORT', $detected_port);
}

require_once '../clinical_core/db.php';
$pdo = getDB();

// 2. Helper de Normalización de Texto
function normalize($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $unwanted_array = array(
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u',
        'ä'=>'a', 'ë'=>'e', 'ï'=>'i', 'ö'=>'o', 'ü'=>'u',
        'â'=>'a', 'ê'=>'e', 'î'=>'i', 'ô'=>'o', 'û'=>'u',
        'ñ'=>'n', 'ç'=>'c',
        'Á'=>'a', 'É'=>'e', 'Í'=>'i', 'Ó'=>'o', 'Ú'=>'u',
        'Ñ'=>'n'
    );
    $str = strtr($str, $unwanted_array);
    $str = preg_replace('/[^a-z0-9]/', ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

// 3. Helper de búsqueda de imagen en Google Custom Search
function searchGoogleImage($query, $apiKey, $cx) {
    $encodedQuery = urlencode($query . " farmacia producto");
    $url = "https://www.googleapis.com/customsearch/v1?q={$encodedQuery}&searchType=image&key={$apiKey}&cx={$cx}&num=1";
    
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
            "timeout" => 10
        ]
    ];
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['items'][0]['link'])) {
                return $data['items'][0]['link'];
            }
        }
    } catch (Exception $e) {
        // Ignorar
    }
    return null;
}

// 4. Helper para descargar imagen
function downloadImage($url, $destPath) {
    $options = [
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
            "timeout" => 15
        ]
    ];
    $context = stream_context_create($options);
    try {
        $content = @file_get_contents($url, false, $context);
        if ($content !== false && strlen($content) > 100) {
            file_put_contents($destPath, $content);
            return true;
        }
    } catch (Exception $e) {
        // Ignorar
    }
    return false;
}

// 5. Manejo de solicitudes AJAX/POST
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    
    if ($action === 'stats') {
        $total = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos")->fetchColumn();
        $with_img = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE imagen IS NOT NULL AND imagen != 'PENDIENTE' AND imagen != ''")->fetchColumn();
        $pending = $total - $with_img;
        $pct = $total > 0 ? round(($with_img / $total) * 100, 1) : 0;
        echo json_encode([
            'total' => $total,
            'with_img' => $with_img,
            'pending' => $pending,
            'pct' => $pct
        ]);
        exit;
    }
    
    if ($action === 'scan_local') {
        $paths = ['../../fotos_google', '../../fotos google'];
        $found_dir = null;
        $files = [];
        
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $found_dir = $path;
                $files = array_diff(scandir($path), array('.', '..'));
                break;
            }
        }
        
        if (!$found_dir) {
            echo json_encode(['error' => 'No se encontró la carpeta fotos_google ni fotos google en la raíz del proyecto.']);
            exit;
        }
        
        echo json_encode([
            'dir' => basename($found_dir),
            'count' => count($files),
            'files' => array_slice(array_values($files), 0, 10)
        ]);
        exit;
    }
    
    if ($action === 'run_local') {
        $paths = ['../../fotos_google', '../../fotos google'];
        $found_dir = null;
        $files = [];
        
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $found_dir = $path;
                $files = array_diff(scandir($path), array('.', '..'));
                break;
            }
        }
        
        if (!$found_dir) {
            echo json_encode(['error' => 'Carpeta de imágenes no encontrada.']);
            exit;
        }
        
        $dest_dir = '../../img/productos';
        if (!is_dir($dest_dir)) {
            mkdir($dest_dir, 0777, true);
        }
        
        $stmt = $pdo->query("SELECT id, codigo, nombre FROM catalogo_productos");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $by_code = [];
        $by_name = [];
        foreach ($products as $p) {
            if (!empty($p['codigo'])) {
                $by_code[trim($p['codigo'])] = $p;
            }
            $norm = normalize($p['nombre']);
            if ($norm) {
                $by_name[$norm] = $p;
            }
        }
        
        $copied = 0;
        $logs = [];
        
        foreach ($files as $file) {
            $pathinfo = pathinfo($file);
            $filename = $pathinfo['filename'];
            $ext = strtolower($pathinfo['extension']);
            
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }
            
            $matched_product = null;
            $match_type = '';
            
            $trimmed_filename = trim($filename);
            if (isset($by_code[$trimmed_filename])) {
                $matched_product = $by_code[$trimmed_filename];
                $match_type = 'Código';
            }
            
            if (!$matched_product) {
                $norm_filename = normalize($filename);
                if (isset($by_name[$norm_filename])) {
                    $matched_product = $by_name[$norm_filename];
                    $match_type = 'Nombre';
                }
            }
            
            if (!$matched_product) {
                $norm_filename = normalize($filename);
                foreach ($by_name as $norm_prod_name => $p) {
                    if ($norm_filename !== '' && (strpos($norm_prod_name, $norm_filename) !== false || strpos($norm_filename, $norm_prod_name) !== false)) {
                        $matched_product = $p;
                        $match_type = 'Subcadena';
                        break;
                    }
                }
            }
            
            if ($matched_product) {
                $new_filename = $matched_product['id'] . '_google.' . $ext;
                $src = $found_dir . '/' . $file;
                $dst = $dest_dir . '/' . $new_filename;
                
                if (copy($src, $dst)) {
                    $stmt_up = $pdo->prepare("UPDATE catalogo_productos SET imagen = ? WHERE id = ?");
                    $stmt_up->execute([$new_filename, $matched_product['id']]);
                    $copied++;
                    $logs[] = "Emparejado: '{$file}' -> Producto ID {$matched_product['id']} ({$matched_product['nombre']}) [Vía {$match_type}]";
                }
            } else {
                $logs[] = "No se pudo emparejar: '{$file}'";
            }
        }
        
        echo json_encode([
            'success' => true,
            'copied' => $copied,
            'total_files' => count($files),
            'logs' => $logs
        ]);
        exit;
    }
    
    if ($action === 'get_pending') {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 90;
        $stmt = $pdo->prepare("SELECT id, nombre FROM catalogo_productos WHERE imagen = 'PENDIENTE' OR imagen IS NULL OR imagen = '' LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
        exit;
    }
    
    if ($action === 'sync_single_google') {
        $id = (int)$_GET['id'];
        $apiKey = trim($_GET['apiKey'] ?? '');
        $cx = trim($_GET['cx'] ?? '');
        
        if (empty($apiKey) || empty($cx)) {
            echo json_encode(['error' => 'API Key y Search Engine ID son requeridos.']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT id, nombre FROM catalogo_productos WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        
        if (!$p) {
            echo json_encode(['error' => 'Producto no encontrado.']);
            exit;
        }
        
        $search_name = $p['nombre'];
        $search_name = str_ireplace([" CON ", " CAJA ", " GR ", " MG ", " PZS ", " PZA ", " CAPSULAS ", " TABLETAS "], " ", $search_name);
        $words = explode(" ", $search_name);
        $search_term = implode(" ", array_slice($words, 0, 4));
        
        $img_url = searchGoogleImage($search_term, $apiKey, $cx);
        
        if ($img_url) {
            $ext = 'jpg';
            $pathinfo = pathinfo(parse_url($img_url, PHP_URL_PATH));
            if (isset($pathinfo['extension'])) {
                $possible_ext = strtolower($pathinfo['extension']);
                if (in_array($possible_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = $possible_ext;
                }
            }
            
            $filename = "{$id}_google.{$ext}";
            $dest_dir = '../../img/productos';
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0777, true);
            }
            $dest_path = "{$dest_dir}/{$filename}";
            
            if (downloadImage($img_url, $dest_path)) {
                $stmt_up = $pdo->prepare("UPDATE catalogo_productos SET imagen = ? WHERE id = ?");
                $stmt_up->execute([$filename, $id]);
                
                echo json_encode([
                    'success' => true,
                    'msg' => "Sincronizado: ID {$id} - {$p['nombre']} -> {$filename}",
                    'img_url' => $img_url
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'No se pudo descargar el archivo de imagen.',
                    'img_url' => $img_url
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Google no devolvió ningún resultado para este producto.'
            ]);
        }
        exit;
    }

    if ($action === 'list_duplicates') {
        $stmt = $pdo->query("SELECT id, nombre, codigo, imagen, precio_farmacia FROM catalogo_productos WHERE imagen IS NOT NULL AND imagen != 'PENDIENTE' AND imagen != ''");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $img_dir = '../../img/productos';
        $by_hash = [];
        
        foreach ($products as $p) {
            $file_path = $img_dir . '/' . $p['imagen'];
            if (is_file($file_path)) {
                $md5 = md5_file($file_path);
                if ($md5) {
                    $by_hash[$md5][] = $p;
                }
            }
        }
        
        $duplicates = [];
        foreach ($by_hash as $hash => $list) {
            if (count($list) > 1) {
                $duplicates[] = [
                    'hash' => $hash,
                    'count' => count($list),
                    'image' => $list[0]['imagen'],
                    'products' => $list
                ];
            }
        }
        
        echo json_encode($duplicates);
        exit;
    }
    
    if ($action === 'unlink_image') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE catalogo_productos SET imagen = 'PENDIENTE' WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'ID de producto inválido.']);
        }
        exit;
    }

    if ($action === 'choose_correct') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT imagen FROM catalogo_productos WHERE id = ?");
            $stmt->execute([$id]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($prod && !empty($prod['imagen'])) {
                $img_name = $prod['imagen'];
                $img_dir = '../../img/productos';
                $file_path = $img_dir . '/' . $img_name;
                
                if (is_file($file_path)) {
                    $correct_md5 = md5_file($file_path);
                    if ($correct_md5) {
                        $stmt_all = $pdo->prepare("SELECT id, imagen FROM catalogo_productos WHERE imagen IS NOT NULL AND imagen != 'PENDIENTE' AND imagen != '' AND id != ?");
                        $stmt_all->execute([$id]);
                        $other_products = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
                        
                        $to_unlink = [];
                        foreach ($other_products as $op) {
                            if ($op['imagen'] === $img_name) {
                                $to_unlink[] = $op['id'];
                            } else {
                                $op_file = $img_dir . '/' . $op['imagen'];
                                if (is_file($op_file)) {
                                    if (md5_file($op_file) === $correct_md5) {
                                        $to_unlink[] = $op['id'];
                                    }
                                }
                            }
                        }
                        
                        if (!empty($to_unlink)) {
                            $placeholders = implode(',', array_fill(0, count($to_unlink), '?'));
                            $stmt_up = $pdo->prepare("UPDATE catalogo_productos SET imagen = 'PENDIENTE' WHERE id IN ($placeholders)");
                            $stmt_up->execute($to_unlink);
                        }
                        
                        echo json_encode(['success' => true, 'unlinked_count' => count($to_unlink)]);
                        exit;
                    }
                }
            }
            echo json_encode(['error' => 'No se pudo encontrar el archivo de imagen del producto especificado o no tiene imagen asignada.']);
        } else {
            echo json_encode(['error' => 'ID de producto inválido.']);
        }
        exit;
    }

    if ($action === 'search_custom') {
        $id = (int)$_GET['id'];
        $query = trim($_GET['query'] ?? '');
        $apiKey = trim($_GET['apiKey'] ?? '');
        $cx = trim($_GET['cx'] ?? '');
        
        if (empty($query)) {
            echo json_encode(['error' => 'El término de búsqueda es requerido.']);
            exit;
        }
        
        if (empty($apiKey) || empty($cx)) {
            echo json_encode(['error' => 'API Key y Search Engine ID de Google son requeridos para esta acción.']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT id, nombre FROM catalogo_productos WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        
        if (!$p) {
            echo json_encode(['error' => 'Producto no encontrado.']);
            exit;
        }
        
        $img_url = searchGoogleImage($query, $apiKey, $cx);
        
        if ($img_url) {
            $ext = 'jpg';
            $pathinfo = pathinfo(parse_url($img_url, PHP_URL_PATH));
            if (isset($pathinfo['extension'])) {
                $possible_ext = strtolower($pathinfo['extension']);
                if (in_array($possible_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = $possible_ext;
                }
            }
            
            $filename = "{$id}_google.{$ext}";
            $dest_dir = '../../img/productos';
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0777, true);
            }
            $dest_path = "{$dest_dir}/{$filename}";
            
            if (downloadImage($img_url, $dest_path)) {
                $stmt_up = $pdo->prepare("UPDATE catalogo_productos SET imagen = ? WHERE id = ?");
                $stmt_up->execute([$filename, $id]);
                
                echo json_encode([
                    'success' => true,
                    'msg' => "Sincronizado personalizado: ID {$id} - {$p['nombre']} -> {$filename}",
                    'img_url' => $img_url
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'No se pudo descargar la imagen.',
                    'img_url' => $img_url
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Google no devolvió ningún resultado para la búsqueda.'
            ]);
        }
        exit;
    }
}

$pageTitle = "MMPharma Portal - Imágenes Duplicadas";
$activePage = "productos";
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8 animate-reveal">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
                <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Inicio</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <a href="productos.php" class="hover:text-primary transition-colors">Productos</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-on-surface-variant">Imágenes duplicadas</span>
            </nav>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Imágenes duplicadas</h2>
            <p class="text-on-surface-variant text-sm mt-1">Administra y resuelve asociaciones de imágenes duplicadas en el catálogo.</p>
        </div>
        <div>
            <a href="productos.php" class="bg-surface-container-high text-primary border border-primary/20 px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Volver a Inventario
            </a>
        </div>
    </div>

    <!-- KPIs del catálogo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 animate-reveal" style="animation-delay: 0.1s">
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-primary/40">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant block mb-1">Total Productos</span>
            <h3 id="stat-total" class="text-2xl font-black text-on-surface">---</h3>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-emerald-500/40">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant block mb-1">Con Imagen</span>
            <h3 id="stat-with-img" class="text-2xl font-black text-on-surface">---</h3>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-error/40">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant block mb-1">Sin Imagen (Pendientes)</span>
            <h3 id="stat-pending" class="text-2xl font-black text-on-surface">---</h3>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-amber-500/40">
            <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant block mb-1">Cobertura</span>
            <h3 id="stat-pct" class="text-2xl font-black text-on-surface">---%</h3>
        </div>
    </div>

    <!-- Interfaz Principal (Full Width) -->
    <div class="w-full">
        <!-- Control Panel -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 overflow-hidden">
                <!-- Navigation Tabs (Hidden) -->
                <div class="hidden border-b border-outline-variant/10 bg-surface-container-low">
                    <button onclick="switchTab('local')" id="tab-btn-local" class="flex-1 py-4 text-sm font-bold text-primary border-b-2 border-primary transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">folder_open</span> Sincronización local
                    </button>
                    <button onclick="switchTab('duplicates')" id="tab-btn-duplicates" class="flex-1 py-4 text-sm font-bold text-on-surface-variant hover:text-white transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">content_copy</span> Administrar duplicados
                    </button>
                </div>

                <!-- Tab Contents -->
                <div class="p-8">
                    <!-- LOCAL TAB (Hidden) -->
                    <div id="tab-local" class="space-y-6 hidden">
                        <div class="bg-primary/5 rounded-2xl p-5 flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary text-3xl shrink-0 mt-1">info</span>
                            <div>
                                <h4 class="text-sm font-bold text-primary tracking-wider mb-1">Coincidencia de imágenes locales</h4>
                                <p class="text-xs text-on-surface-variant leading-relaxed">
                                    Este módulo lee todas las fotos dentro de la carpeta de origen en el servidor y las asocia con los productos correspondientes usando los códigos de barras (EAN/UPC) o nombres limpios. Las imágenes válidas se copian a <strong>img/productos/</strong>.
                                </p>
                            </div>
                        </div>

                        <div id="local-status-box" class="bg-surface-container-low p-6 rounded-2xl space-y-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-[10px] font-black tracking-widest text-on-surface-variant">Carpeta de origen detectada</span>
                                    <h4 id="local-dir-name" class="text-lg font-black text-white mt-0.5">Escaneando...</h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black tracking-widest text-on-surface-variant">Imágenes disponibles</span>
                                    <h4 id="local-img-count" class="text-2xl font-black text-primary mt-0.5">0</h4>
                                </div>
                            </div>
                            <div id="local-preview" class="border-t border-outline-variant/10 pt-4 hidden">
                                <span class="text-[10px] font-black tracking-widest text-on-surface-variant block mb-2">Muestra de archivos encontrados:</span>
                                <div id="local-preview-list" class="flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button onclick="scanLocal()" class="px-6 py-4 bg-surface-container-high text-primary hover:bg-primary hover:text-white rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">scan</span> Escanear carpeta
                            </button>
                            <button id="btn-run-local" onclick="runLocal()" disabled class="flex-1 py-4 bg-primary text-white rounded-xl font-bold text-sm hover:opacity-90 disabled:opacity-30 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">play_arrow</span> Iniciar importación local
                            </button>
                        </div>
                    </div>

                    <!-- DUPLICATES TAB -->
                    <div id="tab-duplicates" class="hidden space-y-6">
                        <div class="bg-primary/5 rounded-2xl p-5 flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary text-3xl shrink-0 mt-1">content_copy</span>
                            <div>
                                <h4 class="text-sm font-bold text-primary tracking-wider mb-1">Detección de imágenes duplicadas</h4>
                                <p class="text-xs text-on-surface-variant leading-relaxed">
                                    Esta sección analiza el contenido real de los archivos de imagen. Agrupa los productos que tienen imágenes exactamente iguales (comparación por hash MD5). Puedes desvincular imágenes incorrectas o realizar búsquedas personalizadas en la nube para corregirlas.
                                </p>
                            </div>
                        </div>

                        <div id="duplicates-loading" class="text-center py-8">
                            <div class="inline-block w-8 h-8 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                            <p class="text-xs text-on-surface-variant mt-2 font-mono">Buscando grupos duplicados en base de datos...</p>
                        </div>
                        
                        <div id="duplicates-empty" class="hidden text-center py-12 bg-surface-container-low rounded-2xl border border-dashed border-outline-variant/30">
                            <span class="material-symbols-outlined text-4xl text-on-surface-variant/40">check_circle</span>
                            <h4 class="text-sm font-bold text-white mt-2">¡No se encontraron imágenes duplicadas!</h4>
                            <p class="text-xs text-on-surface-variant mt-1">Todas las fotos asignadas a los productos son únicas.</p>
                        </div>

                        <div id="duplicates-container" class="space-y-6 hidden">
                            <!-- Contenido dinámico -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- MODAL BUSQUEDA PERSONALIZADA -->
<div id="modalCustomSearch" class="fixed inset-0 z-[120] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
    <div class="bg-surface w-full max-w-lg rounded-3xl overflow-hidden border border-white/10">
        <div class="px-6 py-4 border-b border-outline-variant/10 flex justify-between items-center bg-primary/5">
            <h3 class="text-on-surface font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">search</span> Buscar imagen específica
            </h3>
            <button onclick="closeCustomSearchModal()" class="text-on-surface-variant hover:text-white"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-xs text-on-surface-variant leading-relaxed">
                Ingresa un término de búsqueda muy descriptivo para encontrar la imagen correcta de este producto en Google (ej: nombre exacto, presentación, miligramos o piezas).
            </p>
            <input type="hidden" id="custom-search-id">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Término de búsqueda</label>
                <input type="text" id="custom-search-query" class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary outline-none text-white font-semibold">
            </div>
            
            <div class="flex gap-3 pt-2">
                <button onclick="closeCustomSearchModal()" class="flex-1 py-3 rounded-xl font-bold text-on-surface-variant bg-surface-container-low hover:bg-surface-container-low/80 transition-all text-sm">Cancelar</button>
                <button onclick="runCustomSearch()" class="flex-1 py-3 rounded-xl font-bold text-white bg-primary hover:bg-primary/90 transition-all text-sm flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">cloud_download</span> Descargar e Vincular
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        loadStats();
        switchTab('duplicates');
    });

    let currentTab = 'duplicates';
    let localFilesCount = 0;

    function switchTab(tab) {
        currentTab = tab;
        document.getElementById('tab-local').classList.add('hidden');
        document.getElementById('tab-duplicates').classList.remove('hidden');
        
        const btnLocal = document.getElementById('tab-btn-local');
        const btnDuplicates = document.getElementById('tab-btn-duplicates');
        
        if (btnLocal) btnLocal.className = "flex-1 py-4 text-sm font-bold text-on-surface-variant hover:text-white transition-all flex items-center justify-center gap-2";
        if (btnDuplicates) btnDuplicates.className = "flex-1 py-4 text-sm font-bold text-primary border-b-2 border-primary transition-all flex items-center justify-center gap-2";
        
        loadDuplicates();
    }

    async function loadStats() {
        try {
            const res = await fetch('sync_images.php?action=stats');
            const data = await res.json();
            document.getElementById('stat-total').textContent = data.total;
            document.getElementById('stat-with-img').textContent = data.with_img;
            document.getElementById('stat-pending').textContent = data.pending;
            document.getElementById('stat-pct').textContent = data.pct + '%';
        } catch (e) {
            console.error("Error cargando estadísticas:", e);
        }
    }

    async function scanLocal(silent = false) {
        try {
            const res = await fetch('sync_images.php?action=scan_local');
            const data = await res.json();
            
            if (data.error) {
                document.getElementById('local-dir-name').textContent = "No disponible";
                document.getElementById('local-dir-name').classList.add('text-error');
                document.getElementById('local-img-count').textContent = "0";
                document.getElementById('btn-run-local').disabled = true;
                if (!silent) {
                    logToConsole(`[ERROR] ${data.error}`, 'error');
                }
                return;
            }
            
            localFilesCount = data.count;
            document.getElementById('local-dir-name').textContent = '/' + data.dir;
            document.getElementById('local-dir-name').classList.remove('text-error');
            document.getElementById('local-img-count').textContent = data.count;
            
            const btnRun = document.getElementById('btn-run-local');
            if (data.count > 0) {
                btnRun.disabled = false;
                document.getElementById('local-preview').classList.remove('hidden');
                const list = document.getElementById('local-preview-list');
                list.innerHTML = '';
                data.files.forEach(f => {
                    const span = document.createElement('span');
                    span.className = "px-2.5 py-1 bg-surface-container-high rounded text-[10px] font-mono text-on-surface-variant";
                    span.textContent = f;
                    list.appendChild(span);
                });
                if (!silent) {
                    logToConsole(`[SCAN] Carpeta '${data.dir}' escaneada. Se encontraron ${data.count} imágenes locales.`);
                }
            } else {
                btnRun.disabled = true;
                document.getElementById('local-preview').classList.add('hidden');
                if (!silent) {
                    logToConsole(`[SCAN] Carpeta vacía o sin imágenes soportadas.`);
                }
            }
        } catch (e) {
            console.error("Error escaneando local:", e);
            if (!silent) {
                logToConsole(`[ERROR] Fallo técnico al escanear la carpeta local.`, 'error');
            }
        }
    }

    async function runLocal() {
        logToConsole(`[INICIO] Iniciando importación local...`);
        document.getElementById('btn-run-local').disabled = true;
        showProgress('Sincronización Local', localFilesCount);
        
        try {
            const res = await fetch('sync_images.php?action=run_local');
            const data = await res.json();
            
            if (data.error) {
                logToConsole(`[ERROR] ${data.error}`, 'error');
                hideProgress();
                return;
            }
            
            data.logs.forEach(l => {
                if (l.includes("Emparejado")) {
                    logToConsole(l);
                } else {
                    logToConsole(l, 'warning');
                }
            });
            
            logToConsole(`[FIN] Importación local completada. Copiadas: ${data.copied} de ${data.total_files} imágenes.`);
            updateProgress(localFilesCount, localFilesCount);
            
            loadStats();
            scanLocal(true);
            
            Swal.fire({
                title: 'Importación Completada',
                text: `Se importaron y vincularon con éxito ${data.copied} imágenes.`,
                icon: 'success',
                confirmButtonColor: '#008151',
                background: '#05160e',
                color: '#f1fdf7'
            });
            
        } catch (e) {
            console.error(e);
            logToConsole(`[ERROR] Fallo crítico durante la ejecución.`, 'error');
        } finally {
            setTimeout(hideProgress, 1000);
        }
    }


    // DUPLICADOS FRONTEND
    async function loadDuplicates() {
        const container = document.getElementById('duplicates-container');
        const loader = document.getElementById('duplicates-loading');
        const emptyMsg = document.getElementById('duplicates-empty');
        
        loader.classList.remove('hidden');
        container.classList.add('hidden');
        emptyMsg.classList.add('hidden');
        
        try {
            const res = await fetch('sync_images.php?action=list_duplicates');
            const groups = await res.json();
            
            loader.classList.add('hidden');
            
            if (groups.length === 0) {
                emptyMsg.classList.remove('hidden');
                return;
            }
            
            container.innerHTML = '';
            container.classList.remove('hidden');
            
            groups.forEach((group, index) => {
                const card = document.createElement('div');
                card.className = "bg-surface-container-low border border-outline-variant/10 rounded-2xl p-6 space-y-4 animate-reveal";
                card.style.animationDelay = (index * 0.05) + 's';
                
                let productsHtml = '';
                group.products.forEach(p => {
                    productsHtml += `
                        <div class="bg-surface p-4 rounded-xl border border-outline-variant/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h5 class="text-sm font-bold text-white leading-snug break-words" title="${escapeHtml(p.nombre)}">${escapeHtml(p.nombre)}</h5>
                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-[10px] text-on-surface-variant font-mono">
                                    <span>Código: <strong class="text-white">${p.codigo || '---'}</strong></span>
                                    <span>Precio: <strong class="text-primary font-bold">$${parseFloat(p.precio_farmacia).toFixed(2)}</strong></span>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 shrink-0 md:justify-end">
                                <button onclick="chooseCorrect(${p.id}, '${escapeHtml(p.nombre)}')" class="px-3 py-2 bg-emerald-500/10 hover:bg-emerald-600 hover:text-white text-emerald-400 font-bold text-xs rounded-lg transition-all flex items-center gap-1.5" title="Elegir como correcto y desvincular los demás">
                                    <span class="material-symbols-outlined text-sm">check_circle</span> Elegir correcto
                                </button>
                                <button onclick="unlinkImage(${p.id}, '${escapeHtml(p.nombre)}')" class="px-3 py-2 bg-error/10 hover:bg-error hover:text-white text-error font-bold text-xs rounded-lg transition-all flex items-center gap-1.5" title="Desvincular imagen">
                                    <span class="material-symbols-outlined text-sm">link_off</span> Desvincular
                                </button>
                                <button onclick="openCustomSearchModal(${p.id}, '${escapeHtml(p.nombre)}')" class="px-3 py-2 bg-primary/10 hover:bg-primary hover:text-white text-primary font-bold text-xs rounded-lg transition-all flex items-center gap-1.5" title="Buscar imagen correcta en Google">
                                    <span class="material-symbols-outlined text-sm">search</span> Buscar nueva
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                card.innerHTML = `
                    <div class="flex flex-col sm:flex-row gap-6">
                        <!-- Imagen compartida -->
                        <div class="w-32 h-32 rounded-xl bg-white flex items-center justify-center shrink-0 border border-outline-variant/10 overflow-hidden shadow-lg p-2 self-start">
                            <img src="../../img/productos/${group.image}" class="max-w-full max-h-full object-contain mix-blend-multiply">
                        </div>
                        <!-- Lista de productos -->
                        <div class="flex-1 space-y-3 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-3 border-b border-outline-variant/10 pb-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Grupo de duplicados #${index+1}</span>
                                <span class="text-xs font-bold bg-amber-500/10 text-amber-500 px-2 py-1 rounded-full w-fit">${group.count} productos comparten esta foto</span>
                            </div>
                            <div class="space-y-3">
                                ${productsHtml}
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
            
        } catch (e) {
            console.error(e);
            loader.classList.add('hidden');
            logToConsole("[ERROR] Error al cargar los grupos duplicados.", 'error');
        }
    }

    function chooseCorrect(id, name) {
        confirmAction('¿Elegir como correcto?', `Se conservará esta imagen únicamente para el producto '${name}'. Se desvinculará de todos los demás productos duplicados del grupo.`, 'Sí, elegir correcto', async () => {
            logToConsole(`[ACCION] Resolviendo duplicados. ID ${id} (${name}) es el correcto...`);
            try {
                const res = await fetch(`sync_images.php?action=choose_correct&id=${id}`);
                const data = await res.json();
                if (data.success) {
                    logToConsole(`  -> [EXITO] Duplicados resueltos. Desvinculados ${data.unlinked_count} productos.`);
                    loadStats();
                    loadDuplicates();
                    Swal.fire({
                        title: 'Duplicados Resueltos',
                        text: `Se resolvió el grupo. El producto conserva su foto y se desvincularon ${data.unlinked_count} productos.`,
                        icon: 'success',
                        confirmButtonColor: '#008151',
                        background: '#05160e',
                        color: '#f1fdf7'
                    });
                } else {
                    logToConsole(`  -> [FALLO] ${data.error}`, 'error');
                    Swal.fire({
                        title: 'Error',
                        text: data.error,
                        icon: 'error',
                        confirmButtonColor: '#008151',
                        background: '#05160e',
                        color: '#f1fdf7'
                    });
                }
            } catch (e) {
                console.error(e);
                logToConsole(`  -> [ERROR] Error de red al resolver duplicados.`, 'error');
            }
        });
    }

    function unlinkImage(id, name) {
        confirmAction('¿Desvincular imagen?', `Se quitará la imagen del producto '${name}' y regresará a estado PENDIENTE.`, 'Sí, desvincular', async () => {
            logToConsole(`[ACCION] Desvinculando imagen del producto ID ${id} (${name})...`);
            try {
                const res = await fetch(`sync_images.php?action=unlink_image&id=${id}`);
                const data = await res.json();
                if (data.success) {
                    logToConsole(`  -> [EXITO] Imagen desvinculada para ID ${id}.`);
                    loadStats();
                    loadDuplicates();
                    Swal.fire({
                        title: 'Desvinculado',
                        text: 'El producto ha sido restablecido a PENDIENTE de imagen.',
                        icon: 'success',
                        confirmButtonColor: '#008151',
                        background: '#05160e',
                        color: '#f1fdf7'
                    });
                } else {
                    logToConsole(`  -> [FALLO] ${data.error}`, 'error');
                }
            } catch (e) {
                console.error(e);
                logToConsole(`  -> [ERROR] Error de red al desvincular.`, 'error');
            }
        });
    }

    function openCustomSearchModal(id, name) {
        document.getElementById('custom-search-id').value = id;
        document.getElementById('custom-search-query').value = name;
        document.getElementById('modalCustomSearch').classList.remove('hidden');
    }

    function closeCustomSearchModal() {
        document.getElementById('modalCustomSearch').classList.add('hidden');
    }

    async function runCustomSearch() {
        const id = document.getElementById('custom-search-id').value;
        const query = document.getElementById('custom-search-query').value.trim();
        const apiKey = document.getElementById('cloud-api-key').value.trim();
        const cx = document.getElementById('cloud-cx').value.trim();
        
        if (!apiKey || !cx) {
            Swal.fire({
                title: 'Credenciales de Google Requeridas',
                text: 'Por favor, ingresa tu API Key y tu Search Engine ID (CX) en la pestaña de Google Search API para poder realizar búsquedas en la nube.',
                icon: 'warning',
                confirmButtonColor: '#008151',
                background: '#05160e',
                color: '#f1fdf7'
            });
            closeCustomSearchModal();
            switchTab('cloud');
            return;
        }

        if (!query) {
            return;
        }

        closeCustomSearchModal();
        logToConsole(`[ACCION] Buscando imagen específica para ID ${id} con frase: "${query}"...`);
        showProgress('Búsqueda Personalizada Google', 1);

        try {
            const res = await fetch(`sync_images.php?action=search_custom&id=${id}&query=${encodeURIComponent(query)}&apiKey=${encodeURIComponent(apiKey)}&cx=${encodeURIComponent(cx)}`);
            const data = await res.json();

            if (data.success) {
                logToConsole(`  -> ${data.msg}`);
                loadStats();
                loadDuplicates();
                Swal.fire({
                    title: 'Imagen Actualizada',
                    text: 'Se descargó e vinculó la nueva imagen específica.',
                    icon: 'success',
                    confirmButtonColor: '#008151',
                    background: '#05160e',
                    color: '#f1fdf7'
                });
            } else {
                logToConsole(`  -> [FALLÓ] ${data.error}`, 'error');
                Swal.fire({
                    title: 'Sin Resultados',
                    text: data.error,
                    icon: 'error',
                    confirmButtonColor: '#008151',
                    background: '#05160e',
                    color: '#f1fdf7'
                });
            }
        } catch (e) {
            console.error(e);
            logToConsole(`  -> [ERROR] Error de conexión durante la búsqueda personalizada.`, 'error');
        } finally {
            hideProgress();
        }
    }

    // Terminal Utils
    function logToConsole(message, type = 'info') {
        const consoleLogs = document.getElementById('console-logs');
        const line = document.createElement('div');
        
        let colorClass = 'text-emerald-400';
        if (type === 'error') colorClass = 'text-red-400 font-bold';
        if (type === 'warning') colorClass = 'text-yellow-400';
        
        line.className = colorClass;
        line.textContent = message;
        
        consoleLogs.appendChild(line);
        consoleLogs.scrollTop = consoleLogs.scrollHeight;
    }

    function clearConsole() {
        document.getElementById('console-logs').innerHTML = '<div class="text-on-surface-variant/40">// Esperando acción...</div>';
    }

    // Progress Bar Utils
    function showProgress(title, total) {
        const card = document.getElementById('progress-card');
        card.classList.remove('hidden');
        document.getElementById('progress-title').textContent = title;
        document.getElementById('progress-pct').textContent = '0%';
        document.getElementById('progress-bar').style.width = '0%';
        document.getElementById('progress-details').textContent = `0 / ${total} procesados`;
    }

    function updateProgress(current, total) {
        const pct = total > 0 ? Math.round((current / total) * 100) : 0;
        document.getElementById('progress-pct').textContent = pct + '%';
        document.getElementById('progress-bar').style.width = pct + '%';
        document.getElementById('progress-details').textContent = `${current} / ${total} procesados`;
    }

    function hideProgress() {
        document.getElementById('progress-card').classList.add('hidden');
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>

<?php include("../includes/footer.php"); ?>
