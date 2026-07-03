<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

if (isset($_GET['action']) && $_GET['action'] === 'download_template') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=plantilla_productos.csv');
    
    $output = fopen('php://output', 'w');
    // Escribir BOM UTF-8 para compatibilidad con Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados
    fputcsv($output, [
        'SKU', 
        'Name', 
        'Description', 
        'Regular price', 
        'Precio Distribuidor', 
        'Precio Empresa', 
        'Precio Red Fria', 
        'Stock', 
        'Categories', 
        'Type'
    ]);
    
    // Fila 1 de ejemplo
    fputcsv($output, [
        '7501070635596', 
        'A.S. COR 1 G/100 ML SOL. FCO. GOTERO CON 24 ML', 
        'NORFENEFRINA NORFENILEFRINA', 
        '262.50', 
        '252.00', 
        '273.00', 
        '252.00', 
        '100', 
        'Medicamentos', 
        'RED FRIA'
    ]);
    
    // Fila 2 de ejemplo
    fputcsv($output, [
        '7502224244251', 
        'ABATELENGUAS DE MADERA BOLSA CON 25 PZS.', 
        'ABATELENGUAS DE MADERA', 
        '7.75', 
        '7.44', 
        '8.06', 
        '', 
        '4500', 
        'Material de Curación', 
        'SECO'
    ]);
    
    fclose($output);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Petición inválida']);
    exit;
}

try {
    $file = $_FILES['csv_file'];
    if ($file['error'] !== 0) {
        throw new Exception("Error al subir el archivo.");
    }

    $file_path = $file['tmp_name'];
    
    // Detectar codificación
    $test_content = file_get_contents($file_path, false, null, 0, 4096);
    $encoding = mb_detect_encoding($test_content, ['UTF-8', 'ISO-8859-1', 'ASCII', 'Windows-1252'], true) ?: 'UTF-8';
    
    // Detectar separador (coma o punto y coma)
    $first_line = fgets(fopen($file_path, 'r'));
    $separator = (strpos($first_line, ';') !== false) ? ';' : ',';

    // Mapeo de columnas con sinónimos
    $headers_map = [
        'codigo' => ['codigo', 'sku', 'código', 'barras', 'upc', 'ean', 'cod', 'id'],
        'nombre' => ['nombre', 'name', 'producto', 'product', 'título', 'titulo'],
        'sustancia' => ['sustancia', 'description', 'descripción', 'sustancia_activa', 'sustancia activa', 'detalle', 'sustancia_quimica'],
        'precio_farmacia' => ['precio_farmacia', 'regular_price', 'regular price', 'precio farmacia', 'p. farmacia', 'farmacia', 'pf', 'precio'],
        'precio_distribuidor' => ['precio_distribuidor', 'precio distribuidor', 'p. distribuidor', 'distribuidor', 'pd', 'precio_distribuidora'],
        'precio_empresa' => ['precio_empresa', 'precio empresa', 'p. empresa', 'empresa', 'pe'],
        'precio_red_fria' => ['precio_red_fria', 'precio red fria', 'precio red fría', 'p. red fria', 'p. red fría', 'red fria', 'red fría', 'prf'],
        'stock' => ['stock', 'cantidad', 'existencias', 'existencia', 'qty', 'quantity', 'inventario', 'orden'],
        'categoria' => ['categoria', 'categoría', 'categories', 'category', 'categorías', 'categoria_nombre'],
        'tipo' => ['tipo', 'type', 'conservacion', 'conservación']
    ];

    $handle = fopen($file_path, "r");
    if ($handle === FALSE) {
        throw new Exception("No se pudo abrir el archivo CSV.");
    }

    // Leer encabezados
    $header_row = fgetcsv($handle, 10000, $separator);
    if ($header_row === FALSE) {
        fclose($handle);
        throw new Exception("El archivo CSV está vacío.");
    }

    $headers = [];
    foreach ($header_row as $col) {
        if ($encoding !== 'UTF-8') {
            $col = mb_convert_encoding($col, 'UTF-8', $encoding);
        }
        $headers[] = mb_strtolower(trim($col), 'UTF-8');
    }

    // Mapear índices
    $indices = [];
    foreach ($headers_map as $field => $aliases) {
        foreach ($aliases as $alias) {
            $idx = array_search(mb_strtolower($alias, 'UTF-8'), $headers);
            if ($idx !== FALSE) {
                $indices[$field] = $idx;
                break;
            }
        }
    }

    // Validar columnas obligatorias
    if (!isset($indices['nombre'])) {
        fclose($handle);
        throw new Exception("No se encontró la columna de Nombre del producto.");
    }
    if (!isset($indices['codigo'])) {
        fclose($handle);
        throw new Exception("No se encontró la columna de Código o SKU.");
    }

    $pdo->beginTransaction();

    $imported = 0;
    $created = 0;
    $updated = 0;
    $errors = [];
    $row_count = 1;

    $categories_cache = [];

    // Prepared statements
    $stmt_check = $pdo->prepare("SELECT id FROM catalogo_productos WHERE codigo = ?");
    $stmt_insert = $pdo->prepare("
        INSERT INTO catalogo_productos 
        (nombre, codigo, tipo, categoria_id, precio_farmacia, precio_distribuidor, precio_empresa, precio_red_fria, sustancia, imagen, solo_empresa) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'NO')
    ");
    $stmt_update = $pdo->prepare("
        UPDATE catalogo_productos 
        SET nombre = ?, tipo = ?, categoria_id = ?, precio_farmacia = ?, precio_distribuidor = ?, precio_empresa = ?, precio_red_fria = ?, sustancia = ?
        WHERE id = ?
    ");
    $stmt_stock = $pdo->prepare("
        INSERT INTO admin_inventario_stock (producto_id, stock_actual) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE stock_actual = ?
    ");
    $stmt_cat_search = $pdo->prepare("SELECT id FROM catalogo_categorias WHERE nombre = ?");
    $stmt_cat_insert = $pdo->prepare("INSERT INTO catalogo_categorias (nombre) VALUES (?)");

    while (($row = fgetcsv($handle, 10000, $separator)) !== FALSE) {
        $row_count++;
        
        // Convertir codificación de celdas
        $cells = [];
        foreach ($row as $cell) {
            if ($encoding !== 'UTF-8') {
                $cell = mb_convert_encoding($cell, 'UTF-8', $encoding);
            }
            $cells[] = trim($cell);
        }

        $nombre = $cells[$indices['nombre']] ?? '';
        $codigo = $cells[$indices['codigo']] ?? '';

        if (empty($nombre) || empty($codigo)) {
            $errors[] = "Fila $row_count omitida: Nombre o Código vacío.";
            continue;
        }

        // Leer campos opcionales
        $sustancia = isset($indices['sustancia']) ? ($cells[$indices['sustancia']] ?? '') : '';
        $p_f = isset($indices['precio_farmacia']) ? (float)str_replace(',', '', $cells[$indices['precio_farmacia']]) : 0.00;
        $p_d = isset($indices['precio_distribuidor']) ? (float)str_replace(',', '', $cells[$indices['precio_distribuidor']]) : 0.00;
        $p_e = isset($indices['precio_empresa']) ? (float)str_replace(',', '', $cells[$indices['precio_empresa']]) : 0.00;
        $p_rf = isset($indices['precio_red_fria']) ? (float)str_replace(',', '', $cells[$indices['precio_red_fria']]) : 0.00;
        $stock = isset($indices['stock']) ? (int)$cells[$indices['stock']] : 0;
        $imagen = isset($indices['imagen']) ? ($cells[$indices['imagen']] ?? '') : '';
        
        // Categoría lookup
        $cat_id = null;
        if (isset($indices['categoria']) && !empty($cells[$indices['categoria']])) {
            $cat_name = trim($cells[$indices['categoria']]);
            if (isset($categories_cache[$cat_name])) {
                $cat_id = $categories_cache[$cat_name];
            } else {
                $stmt_cat_search->execute([$cat_name]);
                $cat_id = $stmt_cat_search->fetchColumn();
                if (!$cat_id) {
                    $stmt_cat_insert->execute([$cat_name]);
                    $cat_id = $pdo->lastInsertId();
                }
                $categories_cache[$cat_name] = $cat_id;
            }
        }

        // Tipo lookup
        $tipo = 'SECO';
        if (isset($indices['tipo'])) {
            $tipo_raw = strtoupper(trim($cells[$indices['tipo']]));
            if (in_array($tipo_raw, ['RED FRIA', 'RED FRÍA', 'RED_FRIA', 'FRIO', 'FRÍO', 'COLD'])) {
                $tipo = 'RED FRIA';
            }
        }

        try {
            $stmt_check->execute([$codigo]);
            $existing_id = $stmt_check->fetchColumn();

            if ($existing_id) {
                // Update
                $stmt_update->execute([$nombre, $tipo, $cat_id, $p_f, $p_d, $p_e, $p_rf, $sustancia, $existing_id]);
                $prod_id = $existing_id;
                $updated++;
            } else {
                // Insert
                $stmt_insert->execute([$nombre, $codigo, $tipo, $cat_id, $p_f, $p_d, $p_e, $p_rf, $sustancia, $imagen]);
                $prod_id = $pdo->lastInsertId();
                $created++;
            }

            // Update Stock
            $stmt_stock->execute([$prod_id, $stock, $stock]);
            $imported++;
        } catch (Exception $rowEx) {
            $errors[] = "Fila $row_count (Código $codigo): " . $rowEx->getMessage();
        }
    }

    fclose($handle);
    $pdo->commit();

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'imported' => $imported,
        'created' => $created,
        'updated' => $updated,
        'errors' => $errors
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
