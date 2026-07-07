<?php
/**
 * MMPharma — Migración de Visibilidad de Productos
 * Ejecutar UNA VEZ: http://[dominio]/dashboard_admin/clinical_core/migrar_visibilidad.php
 */
require_once 'db.php';
$pdo = getDB();

$success = false;
$msg = '';
$count_updated = 0;

try {
    $pdo->beginTransaction();

    // 1. Reset all products to medical-only by default (FARMACIA, DISTRIBUIDORA)
    $stmtReset = $pdo->prepare("UPDATE catalogo_productos SET visibilidad = 'FARMACIA,DISTRIBUIDORA', solo_empresa = 'NO'");
    $stmtReset->execute();

    // 2. Fetch category IDs for basic categories
    $stmtCats = $pdo->query("SELECT id, nombre FROM catalogo_categorias WHERE nombre IN ('Material de Curación', 'Antisépticos e Higiene', 'Equipos Médicos', 'Otros')");
    $cats = $stmtCats->fetchAll(PDO::FETCH_ASSOC);
    $catIds = array_column($cats, 'id');
    $catNames = array_column($cats, 'nombre');

    // 3. Select all products to determine which ones should be public/TODOS
    $stmtProds = $pdo->query("SELECT id, nombre, sustancia, categoria_id FROM catalogo_productos");
    $prods = $stmtProds->fetchAll(PDO::FETCH_ASSOC);

    $otcKeywords = [
        'ASPIRINA', 'LORATADINA', 'LORATIDINA', 'BUSCAPINA', 'BUTILHIOSCINA', 'JERINGA', 
        'ALKA-SELTER', 'ALKA SELTZER', 'AGRIFEN', 'ANTIFLU', 'BEPANTHEN', 'BARMICIL', 
        'BIO ELECTRO', 'BIOFLUSIN', 'BIOMESINA', 'LYSOL', 'OXIGENADA', 'ALCOHOL', 'ALGODON', 
        'CURITA', 'VENDITAS', 'VENDA', 'GASAS', 'TAPABOCAS', 'CUBREBOCAS', 'MASCARILLA', 
        'TERMOMETRO', 'GLUCOMETRO', 'ESTETOSCOPIO', 'BASCULA', 'BOTIQUIN', 'ABATELENGUAS'
    ];

    $excludeKeywords = [
        'FILGRASTIM', 'EPOETINA', 'CISPLATINO', 'DOXORUBICINA', 'VINCRISTINA', 'DOCETAXEL', 
        'RITUXIMAB', 'DENOSUMAB', 'VINBLASTINA', 'CARBOPLATINO', 'OXALIPLATINO', 'IFOSFAMIDA'
    ];

    $updateToTodos = [];
    
    foreach ($prods as $p) {
        $nameUpper = mb_strtoupper($p['nombre']);
        $sustUpper = mb_strtoupper($p['sustancia'] ?? '');

        // Check if belongs to basic category
        $isBasicCat = $p['categoria_id'] !== null && in_array($p['categoria_id'], $catIds);

        // Check if matches any OTC keyword
        $matchesOtc = false;
        foreach ($otcKeywords as $kw) {
            if (strpos($nameUpper, $kw) !== false || strpos($sustUpper, $kw) !== false) {
                $matchesOtc = true;
                break;
            }
        }

        // Check if matches any exclude keyword
        $isExcluded = false;
        foreach ($excludeKeywords as $exKw) {
            if (strpos($nameUpper, $exKw) !== false || strpos($sustUpper, $exKw) !== false) {
                $isExcluded = true;
                break;
            }
        }

        // A product should be TODOS if (it's in a basic category OR it matches OTC keywords) AND it is NOT excluded
        if (($isBasicCat || $matchesOtc) && !$isExcluded) {
            $updateToTodos[] = $p['id'];
        }
    }

    if (!empty($updateToTodos)) {
        $placeholders = implode(',', array_fill(0, count($updateToTodos), '?'));
        $stmtUpdate = $pdo->prepare("UPDATE catalogo_productos SET visibilidad = 'TODOS', solo_empresa = 'SI' WHERE id IN ($placeholders)");
        $stmtUpdate->execute($updateToTodos);
        $count_updated = count($updateToTodos);
    }

    $pdo->commit();
    $success = true;
    $msg = "Migración completada con éxito. Se actualizaron {$count_updated} productos a visibilidad pública (TODOS / solo_empresa = SI).";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $success = false;
    $msg = "Error durante la migración: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>MMPharma — Migración de Visibilidad</title>
<style>
 body{font-family:monospace;background:#020d08;color:#f1fdf7;padding:2rem}
 h1{color:#008151}
 .ok{color:#34c47a} .err{color:#f28b82}
</style>
</head>
<body>
<h1>🔧 Migración de Visibilidad de Productos</h1>
<h2 class="<?= $success ? 'ok' : 'err' ?>">
 <?= $success ? '✓' : '✗' ?> <?= htmlspecialchars($msg) ?>
</h2>
<p style="margin-top:2rem;color:#8aaad4;font-size:.8rem">
 ⚠️ Borra o protege este archivo después de ejecutarlo:<br>
 <code>dashboard_admin/clinical_core/migrar_visibilidad.php</code>
</p>
<p><a href="../dashboard/dashboard.php" style="color:#4a90d9">← Volver al Dashboard</a></p>
</body>
</html>
