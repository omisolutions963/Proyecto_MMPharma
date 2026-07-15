<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    echo "No autorizado. Por favor inicie sesión como administrador.";
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

// Cabeceras para forzar la descarga del archivo CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventario_productos_' . date('Y-m-d') . '.csv');
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

// Escribir el BOM UTF-8 para que Excel detecte correctamente la codificación
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeceras exactas del formato de importación
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

// Consulta a la base de datos para obtener el inventario actual
$query = "
    SELECT 
        p.codigo AS sku,
        p.nombre AS name,
        p.sustancia AS description,
        p.precio_farmacia AS regular_price,
        p.precio_distribuidor AS precio_distribuidor,
        p.precio_empresa AS precio_empresa,
        p.precio_red_fria AS precio_red_fria,
        COALESCE(s.stock_actual, 0) AS stock,
        c.nombre AS categories,
        p.tipo AS type
    FROM catalogo_productos p
    LEFT JOIN admin_inventario_stock s ON p.id = s.producto_id
    LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
    ORDER BY p.id DESC
";

try {
    $stmt = $pdo->query($query);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['sku'],
            $row['name'],
            $row['description'],
            $row['regular_price'],
            $row['precio_distribuidor'],
            $row['precio_empresa'],
            $row['precio_red_fria'],
            $row['stock'],
            $row['categories'],
            $row['type']
        ]);
    }
} catch (Exception $e) {
    // Si hay un error, limpiar cabeceras y mostrar mensaje de error
    ob_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error al exportar inventario: " . $e->getMessage();
}

fclose($output);
exit;
