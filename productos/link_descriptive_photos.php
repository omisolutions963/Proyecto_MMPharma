<?php
/**
 * link_descriptive_photos.php
 * Sincroniza y vincula las imágenes de productos/fotos_listas (frente y atras) y productos/fotos_google
 * con los productos del catálogo, manteniendo los nombres de archivo originales y corrigiendo el mapeo CSV.
 */

// 1. Configuración de base de datos y rutas
require_once __DIR__ . '/../includes/db.php';
$pdo = getDB();

$frente_dir = __DIR__ . '/fotos_listas/frente/';
$atras_dir = __DIR__ . '/fotos_listas/atras/';
$google_dir = __DIR__ . '/fotos_google/';
$manual_dir = __DIR__ . '/fotos_manuales/';
$dest_dir = __DIR__ . '/../img/productos/';

$csv_paths = [
    'root' => __DIR__ . '/../mmpharma_sin_encabezado.csv',
    'proc' => __DIR__ . '/mmpharma_sin_encabezado.csv'
];

$log_paths = [
    'proc' => __DIR__ . '/fotos_no_utilizadas/no_encontrados.txt',
    'parent' => __DIR__ . '/no_encontrados.txt',
    'downloads' => 'C:/Users/alexi/Downloads/mm pharma/procesamiento_medicamentos/no_encontrados.txt'
];

echo "=== INICIANDO VINCULACIÓN DE FOTOS CON NOMBRES ORIGINALES ===\n";
echo "Origen GOOGLE: $google_dir\n";
echo "Origen frente: $frente_dir\n";
echo "Origen atras: $atras_dir\n";
echo "Origen MANUAL: $manual_dir\n";
echo "Destino: $dest_dir\n\n";

// Crear directorio destino si no existe
if (!is_dir($dest_dir)) {
    mkdir($dest_dir, 0777, true);
    echo "Creado directorio destino: $dest_dir\n";
}

// 2. Leer archivos en directorios locales
$google_files = is_dir($google_dir) ? array_diff(scandir($google_dir), ['.', '..']) : [];
$frente_files = is_dir($frente_dir) ? array_diff(scandir($frente_dir), ['.', '..']) : [];
$atras_files = is_dir($atras_dir) ? array_diff(scandir($atras_dir), ['.', '..']) : [];
$manual_files = is_dir($manual_dir) ? array_diff(scandir($manual_dir), ['.', '..']) : [];

echo "Archivos detectados en GOOGLE: " . count($google_files) . "\n";
echo "Archivos detectados en frente: " . count($frente_files) . "\n";
echo "Archivos detectados en atras: " . count($atras_files) . "\n";
echo "Archivos detectados en MANUAL: " . count($manual_files) . "\n\n";

// Limpiar antiguos archivos que tengan "_google." en el nombre de destino para evitar basura
echo "Limpiando archivos obsoletos del destino...\n";
if (is_dir($dest_dir)) {
    $existing_files = array_diff(scandir($dest_dir), ['.', '..']);
    foreach ($existing_files as $f) {
        if (preg_match('/_google\./i', $f) || preg_match('/_atras\./i', $f)) {
            @unlink($dest_dir . $f);
        }
    }
}

// 3. Resetear la columna imagen en base de datos para una sincronización limpia
echo "Reiniciando imágenes de catálogo en base de datos...\n";
$pdo->exec("UPDATE catalogo_productos SET imagen = 'PENDIENTE'");

// 4. Cargar todos los productos de la base de datos
$stmt = $pdo->query("SELECT id, codigo, nombre, sustancia, imagen FROM catalogo_productos");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$max_id = 0;
$by_id = [];
$by_code = [];
$by_name = [];
$by_substance = [];

foreach ($products as $p) {
    $by_id[(int)$p['id']] = $p;
    if (!empty($p['codigo'])) {
        $by_code[trim($p['codigo'])] = $p;
    }
    $norm = normalize($p['nombre']);
    if ($norm) {
        $by_name[$norm] = $p;
    }
    $norm_sub = normalize($p['sustancia']);
    if ($norm_sub) {
        $by_substance[$norm_sub][] = $p;
    }
    if ((int)$p['id'] > $max_id) {
        $max_id = (int)$p['id'];
    }
}

// 5. Funciones de normalización y alineación
function normalize($str) {
    $str = mb_strtolower($str, 'UTF-8');
    
    // Quitar extensiones de nombres de archivo
    $str = preg_replace('/\.(jpg|png|jpeg|webp)$/i', '', $str);
    
    // Normalizar espacios y guiones en marcas conocidas
    $str = str_replace('le roy', 'leroy', $str);
    $str = str_replace('xl-3', 'xl3', $str);
    
    // Mapear nombres comerciales a sustancias activas / sinónimos y corregir pegados en base de datos
    $aliases = [
        'diurmessel' => 'furosemida',
        'alcomex' => 'alcohol',
        'pzaambiderm' => 'pieza ambiderm',
        'pzaamb' => 'pieza ambiderm'
    ];
    foreach ($aliases as $from => $to) {
        $str = preg_replace('/\b' . preg_quote($from, '/') . '\b/', $to, $str);
    }
    
    // Separar números y unidades (ej. 500mg -> 500 mg)
    $str = preg_replace('/\b1\s*(?:litro|l)\b/', '1000 ml', $str);
    $str = preg_replace('/(\d+(?:\.\d+)?)([a-zA-Z]+)/', '$1 $2', $str);
    
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
    $str = preg_replace('/[^a-z0-9.]/', ' ', $str);
    $str = preg_replace('/\s+/', ' ', $str);
    return trim($str);
}

function normalize_word($w) {
    $plurals = [
        'guantes' => 'guante',
        'gasas' => 'gasa',
        'vendas' => 'venda',
        'tiras' => 'tira',
        'apositos' => 'aposito',
        'apocito' => 'aposito',
        'apocitos' => 'aposito',
        'cintas' => 'cinta',
        'microporosas' => 'microporosa',
        
        'ampolletas' => 'ampula',
        'ampolleta' => 'ampula',
        'ampulas' => 'ampula',
        'amp' => 'ampula',
        
        'tabletas' => 'tableta',
        'capsulas' => 'capsula',
        'capsula' => 'capsula',
        
        'piezas' => 'pieza',
        'pza' => 'pieza',
        'pzs' => 'pieza',
        'pzas' => 'pieza',
        
        'abatelenguas' => 'abatelengua',
        'otico' => 'otic',
        'otica' => 'otic',
        'protect' => 'protec',
        
        // Sizes
        'chicos' => 'chico',
        'chica' => 'chico',
        'ch' => 'chico',
        'mediano' => 'med',
        'mediana' => 'med',
        'm' => 'med',
        'grande' => 'grande',
        'gde' => 'grande',
        'g' => 'grande',
        'extrachico' => 'xch',
        'xchico' => 'xch',
        
        // Units
        'fco' => 'frasco',
        'fcos' => 'frasco',
        'frascos' => 'frasco',
        'sol' => 'solucion',
        'soluciones' => 'solucion',
        'iny' => 'inyectable',
        'inyectables' => 'inyectable',
        'grs' => 'g',
        'gr' => 'g',
        'gramos' => 'g',
        'gramo' => 'g',
        'mts' => 'm',
        'mt' => 'm',
        'metros' => 'm',
        'metro' => 'm',
        
        // Colors
        'azules' => 'azul',
        'colores' => 'surtidos'
    ];
    return isset($plurals[$w]) ? $plurals[$w] : $w;
}

function get_words($str) {
    $norm = normalize($str);
    $words = explode(' ', $norm);
    $filtered = [];
    foreach ($words as $w) {
        $w = trim($w);
        if ((strlen($w) > 1 || ctype_digit($w)) && !in_array($w, ['con', 'caja', 'de', 'para', 'en', 'del', 'la', 'el'])) {
            $filtered[] = normalize_word($w);
        }
    }
    return array_unique($filtered);
}

function extract_numbers($str) {
    $norm = normalize($str);
    preg_match_all('/\b\d+(?:\.\d+)?\b/', $norm, $matches);
    return !empty($matches[0]) ? $matches[0] : [];
}

function compute_match_score($file_words, $prod_words) {
    $overlap = array_intersect($file_words, $prod_words);
    return count($overlap);
}

function product_has_brand($p, $brand) {
    $norm_name = normalize($p['nombre']);
    $norm_sub = normalize($p['sustancia']);
    if (strpos($norm_name, $brand) !== false || strpos($norm_sub, $brand) !== false) {
        return true;
    }
    if ($brand === 'ambiderm') {
        $ean = trim($p['codigo']);
        if (preg_match('/^(3750222424|1750222424|750222424)/', $ean)) {
            return true;
        }
    }
    return false;
}

function find_match($file, $products, $by_id, $by_code, $by_name, $by_substance, $max_id) {
    $pathinfo = pathinfo($file);
    $filename = $pathinfo['filename'];
    $ext = strtolower($pathinfo['extension']);
    
    $filename = preg_replace('/_atras$/i', '', $filename);
    $clean_fn = trim($filename);
    $clean_fn = preg_replace('/\.(jpg|png|jpeg|webp)$/i', '', $clean_fn);

    $candidates = [$clean_fn];
    if (preg_match('/^(.+)R$/i', $clean_fn, $m)) {
        $candidates[] = trim($m[1]);
    }

    $brands = [
        'nexcare', 'ambiderm', 'jaloma', '3m', 'portem', 'valclan', 'tribedoce', 
        'soldrin', 'aspirina', 'alfa', 'american', 'leroy', 'purex', 
        'codifarma', 'nipro', 'dlp', 'perrigo', 'next', 'rosel', 'sedalmerck', 
        'sensibit', 'syncol', 'tempire', 'viladol', 'xl3', 'aderogyl',
        'cafiaspirina', 'capsiflu', 'carbafen', 'desenfriol', 'dualgos', 
        'flexenol', 'exalver', 'frosdem', 'ladexgel', 'migraña', 
        'sensimedical', 'antibenzil', 'microdacyn', 'sedasiva', 'elasto',
        'alcomex', 'leukoplast', 'quirmex', 'hergom', 'homecare',
        'furosemida', 'diurmessel'
    ];

    foreach ($candidates as $cfn) {
        $norm_fn = normalize($cfn);
        
        $is_brand = in_array($norm_fn, $brands);
        $has_multiple_words = (strpos($norm_fn, ' ') !== false);
        $has_numbers = preg_match('/\d/', $norm_fn);
        $allow_substring = !$is_brand && !$has_multiple_words && !$has_numbers;

        if (ctype_digit($cfn) && (int)$cfn <= $max_id) {
            if (isset($by_id[(int)$cfn])) {
                return [$by_id[(int)$cfn], 'Direct ID', $ext];
            }
        }
        
        if (preg_match('/^(\d+)_(\d+)$/', $cfn, $m)) {
            $id = (int)$m[1];
            if ($id <= $max_id && isset($by_id[$id])) {
                return [$by_id[$id], 'Direct ID with suffix', $ext];
            }
        }
        
        if (isset($by_code[$cfn])) {
            return [$by_code[$cfn], 'Exact Barcode', $ext];
        }
        
        if (preg_match('/^([a-zA-Z0-9]+)_(\d+)$/', $cfn, $m)) {
            $bc = $m[1];
            if (isset($by_code[$bc])) {
                return [$by_code[$bc], 'Barcode with suffix', $ext];
            }
        }
        
        if ($norm_fn && isset($by_name[$norm_fn])) {
            return [$by_name[$norm_fn], 'Exact Name', $ext];
        }
        
        if ($allow_substring && $norm_fn) {
            foreach ($by_name as $norm_prod_name => $p) {
                if (strpos($norm_prod_name, $norm_fn) !== false || strpos($norm_fn, $norm_prod_name) !== false) {
                    return [$p, 'Substring Name Match', $ext];
                }
            }
        }

        if ($norm_fn && isset($by_substance[$norm_fn])) {
            return [$by_substance[$norm_fn][0], 'Exact Substance Match', $ext];
        }

        if ($allow_substring && $norm_fn) {
            foreach ($by_substance as $norm_sub_name => $list) {
                if (strpos($norm_sub_name, $norm_fn) !== false || strpos($norm_fn, $norm_sub_name) !== false) {
                    return [$list[0], 'Substring Substance Match', $ext];
                }
            }
        }
    }
    
    foreach ($candidates as $cfn) {
        foreach ($by_code as $bc => $p) {
            if (strlen($cfn) >= 6 && strpos($bc, $cfn) === 0) {
                return [$p, 'Barcode Prefix Match', $ext];
            }
            if (strlen($bc) >= 6 && strpos($cfn, $bc) === 0) {
                return [$p, 'Barcode Suffix Match', $ext];
            }
        }
    }

    $file_words = get_words($clean_fn);
    $file_numbers = extract_numbers($clean_fn);
    
    $categories = [
        'abatelenguas' => ['abatelengua'],
        'guante' => ['guante'],
        'jeringa' => ['jeringa'],
        'venda' => ['venda'],
        'bisturi' => ['bisturi', 'hoja'],
        'cubrebocas' => ['cubrebocas'],
        'tira' => ['tira'],
        'glucosa' => ['glucosa', 'glucometro', 'accu', 'active', 'instant'],
        'botiquin' => ['botiquin'],
        'alcohol' => ['alcohol'],
        'gasa' => ['gasa'],
        'agua' => ['agua', 'oxigenada'],
        'estetoscopio' => ['estetoscopio'],
        'termometro' => ['termometro'],
        'aposito' => ['aposito'],
        'cinta' => ['cinta', 'micropore', 'transpore', 'microporosa'],
        'collarin' => ['collarin'],
        'parche' => ['parche']
    ];

    $best_p = null;
    $best_score = 0;
    
    foreach ($products as $p) {
        $prod_words = array_unique(array_merge(get_words($p['nombre']), get_words($p['sustancia'])));
        $score = compute_match_score($file_words, $prod_words);
        
        if ($score > 0) {
            $prod_numbers = extract_numbers($p['nombre']);
            $numbers_aligned = true;
            
            if (!empty($file_numbers)) {
                $intersect = array_intersect($file_numbers, $prod_numbers);
                if (empty($intersect)) {
                    $numbers_aligned = false;
                }
            }
            
            $category_aligned = true;
            $norm_fn_local = normalize($clean_fn);
            $norm_pn_local = normalize($p['nombre']);
            
            foreach ($categories as $cat => $keywords) {
                $file_has_cat = false;
                foreach ($keywords as $kw) {
                    if (strpos($norm_fn_local, $kw) !== false) {
                        $file_has_cat = true;
                        break;
                    }
                }
                
                if ($file_has_cat) {
                    $prod_has_cat = false;
                    foreach ($keywords as $kw) {
                        if (strpos($norm_pn_local, $kw) !== false || strpos(normalize($p['sustancia']), $kw) !== false) {
                            $prod_has_cat = true;
                            break;
                        }
                    }
                    if (!$prod_has_cat) {
                        $category_aligned = false;
                        break;
                    }
                }
            }

            $brand_aligned = true;
            foreach ($brands as $b) {
                if (strpos($norm_fn_local, $b) !== false) {
                    if (!product_has_brand($p, $b)) {
                        $brand_aligned = false;
                        break;
                    }
                }
            }

            // Material conflict check
            $material_aligned = true;
            $materials = ['latex', 'nitrilo', 'vinilo', 'tela', 'seda', 'papel', 'plastico', 'madera', 'metal', 'acero', 'inoxidable'];
            $fn_materials = [];
            foreach ($materials as $m) {
                if (strpos($norm_fn_local, $m) !== false) {
                    $fn_materials[] = $m;
                }
            }
            if (!empty($fn_materials)) {
                foreach ($materials as $m) {
                    if (in_array($m, $fn_materials)) continue;
                    if (strpos($norm_pn_local, $m) !== false || strpos(normalize($p['sustancia']), $m) !== false) {
                        $material_aligned = false;
                        break;
                    }
                }
            }

            // Size conflict check
            $size_aligned = true;
            $sizes = ['chico', 'med', 'grande', 'xch', 'xl'];
            $fn_sizes = [];
            foreach ($sizes as $s) {
                if (in_array($s, $file_words)) {
                    $fn_sizes[] = $s;
                }
            }
            if (!empty($fn_sizes)) {
                foreach ($sizes as $s) {
                    if (in_array($s, $fn_sizes)) continue;
                    if (in_array($s, $prod_words)) {
                        $size_aligned = false;
                        break;
                    }
                }
            }
            
            if ($numbers_aligned && $category_aligned && $brand_aligned && $material_aligned && $size_aligned && $score > $best_score) {
                $best_score = $score;
                $best_p = $p;
            }
        }
    }
    
    if ($best_score >= 2) {
        return [$best_p, 'Filename word overlap with alignment', $ext];
    }
    
    return [null, '', $ext];
}

// 7. Mapear y Copiar
$frente_updates = [];

// A. PROCESAR FOTOS GOOGLE (Baja Prioridad)
$stats_google_matched = 0;
$stats_google_copied = 0;
echo "--- PROCESANDO FOTOS GOOGLE ---\n";
foreach ($google_files as $file) {
    $path = $google_dir . $file;
    if (!is_file($path)) continue;
    
    list($p, $match_type, $ext) = find_match($file, $products, $by_id, $by_code, $by_name, $by_substance, $max_id);
    
    if ($p) {
        $stats_google_matched++;
        $id = $p['id'];
        $dest_filename = $file;
        $dest_path = $dest_dir . $dest_filename;
        
        if (copy($path, $dest_path)) {
            $stats_google_copied++;
            $frente_updates[$id] = $dest_filename;
            echo "[GOOGLE] Matched: '$file' -> ID $id ('{$p['nombre']}') Vía $match_type. Copiada como '$dest_filename'\n";
        } else {
            echo "[GOOGLE] Error: No se pudo copiar '$file' a '$dest_filename'\n";
        }
    }
}

// B. PROCESAR FOTOS frente (Alta Prioridad - sobreescribirá fotos_google si coincide)
$stats_frente_matched = 0;
$stats_frente_copied = 0;
echo "\n--- PROCESANDO IMÁGENES frente ---\n";
foreach ($frente_files as $file) {
    $path = $frente_dir . $file;
    if (!is_file($path)) continue;
    
    list($p, $match_type, $ext) = find_match($file, $products, $by_id, $by_code, $by_name, $by_substance, $max_id);
    
    if ($p) {
        $stats_frente_matched++;
        $id = $p['id'];
        $dest_filename = $file;
        $dest_path = $dest_dir . $dest_filename;
        
        if (copy($path, $dest_path)) {
            $stats_frente_copied++;
            $frente_updates[$id] = $dest_filename;
            echo "[frente] Matched: '$file' -> ID $id ('{$p['nombre']}') Vía $match_type. Copiada como '$dest_filename'\n";
        } else {
            echo "[frente] Error: No se pudo copiar '$file' a '$dest_filename'\n";
        }
    }
}

// C. PROCESAR FOTOS atras
$stats_atras_matched = 0;
$stats_atras_copied = 0;
echo "\n--- PROCESANDO IMÁGENES atras ---\n";
foreach ($atras_files as $file) {
    $path = $atras_dir . $file;
    if (!is_file($path)) continue;
    
    list($p, $match_type, $ext) = find_match($file, $products, $by_id, $by_code, $by_name, $by_substance, $max_id);
    
    if ($p) {
        $stats_atras_matched++;
        $dest_filename = $file;
        $dest_path = $dest_dir . $dest_filename;
        
        if (copy($path, $dest_path)) {
            $stats_atras_copied++;
            echo "[atras] Matched: '$file' -> ID {$p['id']} ('{$p['nombre']}') Vía $match_type. Copiada como '$dest_filename'\n";
        } else {
            echo "[atras] Error: No se pudo copiar '$file' a '$dest_filename'\n";
        }
    }
}

// D. PROCESAR IMÁGENES MANUALES (Máxima Prioridad - sobreescribirá frente y google si coincide)
$stats_manual_matched = 0;
$stats_manual_copied = 0;
echo "\n--- PROCESANDO IMÁGENES MANUALES ---\n";
foreach ($manual_files as $file) {
    $path = $manual_dir . $file;
    if (!is_file($path)) continue;
    
    list($p, $match_type, $ext) = find_match($file, $products, $by_id, $by_code, $by_name, $by_substance, $max_id);
    
    if ($p) {
        $stats_manual_matched++;
        $id = $p['id'];
        $dest_filename = $file;
        $dest_path = $dest_dir . $dest_filename;
        
        if (copy($path, $dest_path)) {
            $stats_manual_copied++;
            $frente_updates[$id] = $dest_filename;
            echo "[MANUAL] Matched: '$file' -> ID $id ('{$p['nombre']}') Vía $match_type. Copiada como '$dest_filename'\n";
        } else {
            echo "[MANUAL] Error: No se pudo copiar '$file' a '$dest_filename'\n";
        }
    }
}

// 8. Actualizar la base de datos con los nuevos nombres de archivo de imagen
$stats_db_updated = 0;
if (!empty($frente_updates)) {
    echo "\n--- ACTUALIZANDO BASE DE DATOS ---\n";
    $stmt_up = $pdo->prepare("UPDATE catalogo_productos SET imagen = ? WHERE id = ?");
    foreach ($frente_updates as $id => $filename) {
        $stmt_up->execute([$filename, $id]);
        $stats_db_updated++;
    }
    echo "Base de datos actualizada con $stats_db_updated enlaces de imagen.\n";
}

// 9. Actualizar archivos CSV
$stats_csv_updated = 0;
if (!empty($frente_updates)) {
    echo "\n--- ACTUALIZANDO ARCHIVOS CSV ---\n";
    
    // Crear mapas para buscar actualización en base a código y nombre en el CSV
    $code_map = [];
    $name_map = [];
    foreach ($frente_updates as $id => $filename) {
        $p = $by_id[$id];
        if (!empty($p['codigo'])) {
            $code_map[trim($p['codigo'])] = $filename;
        }
        $norm = normalize($p['nombre']);
        if ($norm) {
            $name_map[$norm] = $filename;
        }
    }

    foreach ($csv_paths as $key => $csv_path) {
        if (file_exists($csv_path)) {
            copy($csv_path, $csv_path . '.bak');
            
            $rows = [];
            if (($handle = fopen($csv_path, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
            
            $updated_in_file = 0;
            foreach ($rows as &$row) {
                $row_ean = isset($row[1]) ? trim($row[1]) : '';
                $row_name = isset($row[2]) ? trim($row[2]) : '';
                $norm_row_name = normalize($row_name);
                
                // Reiniciar celda si es obsoleta o de tipo _google
                if (isset($row[10])) {
                    $row[10] = 'PENDIENTE';
                }
                
                $matched_filename = null;
                if ($row_ean && isset($code_map[$row_ean])) {
                    $matched_filename = $code_map[$row_ean];
                } elseif ($norm_row_name && isset($name_map[$norm_row_name])) {
                    $matched_filename = $name_map[$norm_row_name];
                }
                
                if ($matched_filename) {
                    while (count($row) < 11) {
                        $row[] = '';
                    }
                    $row[10] = $matched_filename;
                    $updated_in_file++;
                }
            }
            
            if (($handle = fopen($csv_path, "w")) !== FALSE) {
                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            }
            echo "CSV $key ('" . basename($csv_path) . "'): actualizados $updated_in_file registros.\n";
            $stats_csv_updated += $updated_in_file;
        }
    }
}

// 10. Regenerar no_encontrados.txt
echo "\n--- REGENERANDO LOGS DE productos SIN IMAGEN ---\n";
$stmt = $pdo->query("SELECT id, codigo, nombre, imagen FROM catalogo_productos WHERE imagen = 'PENDIENTE' OR imagen IS NULL OR imagen = '' ORDER BY id ASC");
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

$log_content = "";
foreach ($pending as $p) {
    $barcode = !empty($p['codigo']) ? $p['codigo'] : 'N/A';
    $log_content .= "Fila N/A | ID: {$p['id']} | CB: $barcode | Nombre: {$p['nombre']}\n";
}

foreach ($log_paths as $key => $log_path) {
    $dir_log = dirname($log_path);
    if (!is_dir($dir_log)) {
        mkdir($dir_log, 0777, true);
    }
    if (file_put_contents($log_path, $log_content) !== FALSE) {
        echo "Log $key: Regenerado exitosamente en '$log_path' (" . count($pending) . " productos faltantes).\n";
    } else {
        echo "Log $key: ERROR al escribir en '$log_path'\n";
    }
}

// 11. Reporte Estadístico Final
$total_products = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos")->fetchColumn();
$with_img = (int)$pdo->query("SELECT COUNT(*) FROM catalogo_productos WHERE imagen IS NOT NULL AND imagen != 'PENDIENTE' AND imagen != ''")->fetchColumn();
$pending_count = $total_products - $with_img;
$pct = $total_products > 0 ? round(($with_img / $total_products) * 100, 1) : 0;

echo "\n" . str_repeat("=", 60) . "\n";
echo "=== RESUMEN DE EJECUCIÓN ===\n";
echo "Fotos GOOGLE emparejadas : $stats_google_matched / " . count($google_files) . "\n";
echo "Fotos GOOGLE copiadas    : $stats_google_copied\n";
echo "Fotos frente emparejadas : $stats_frente_matched / " . count($frente_files) . "\n";
echo "Fotos frente copiadas    : $stats_frente_copied\n";
echo "Fotos atras emparejadas  : $stats_atras_matched / " . count($atras_files) . "\n";
echo "Fotos atras copiadas     : $stats_atras_copied\n";
echo "Fotos MANUALES emparejadas : $stats_manual_matched / " . count($manual_files) . "\n";
echo "Fotos MANUALES copiadas    : $stats_manual_copied\n";
echo "Registros DB actualizados: $stats_db_updated\n";
echo "\n--- ESTADÍSTICAS DEL CATÁLOGO ---\n";
echo "Total Productos en DB    : $total_products\n";
echo "Con Imagen               : $with_img\n";
echo "Sin Imagen (Pendientes)  : $pending_count\n";
echo "Cobertura del catálogo   : $pct%\n";
echo str_repeat("=", 60) . "\n";
