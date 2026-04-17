<?php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Inventario_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$inventario = [
    ['sku' => 'IGL-2024-042', 'name' => 'Insulina Glargina', 'categoria' => 'Endocrinología / Red Fría', 'stock' => 45, 'status' => 'Crítico'],
    ['sku' => 'PAR-500-GEN', 'name' => 'Paracetamol 500mg', 'categoria' => 'Analgésicos', 'stock' => 1200, 'status' => 'Óptimo'],
    ['sku' => 'AMX-SUS-250', 'name' => 'Amoxicilina Suspensión', 'categoria' => 'Pediatría', 'stock' => 0, 'status' => 'Agotado'],
    ['sku' => 'LID-SOL-002', 'name' => 'Lidocaína 2%', 'categoria' => 'Anestesiología', 'stock' => 85, 'status' => 'Precaución'],
];
?>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    body { font-family: Arial, sans-serif; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background-color: #002451; color: #ffffff; padding: 10px; text-align: left; border: 1px solid #002451; }
    .table td { padding: 10px; border: 1px solid #c4c6d0; color: #051d30; }
    .table tr:nth-child(even) { background-color: #f7f9ff; }
    .title { color: #002451; font-size: 24px; font-weight: bold; text-align: center; height: 40px; }
    .subtitle { color: #006397; font-size: 14px; text-align: center; height: 30px; }
</style>
</head>
<body>
    <table>
        <tr><td colspan="5" class="title">MMPharma - Reporte General de Inventario</td></tr>
        <tr><td colspan="5" class="subtitle">Generado el: <?php echo date('d/m/Y H:i:s'); ?></td></tr>
        <tr><td colspan="5"></td></tr>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Stock Actual</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventario as $p): ?>
            <tr>
                <td><?= $p['sku'] ?></td>
                <td><?= $p['name'] ?></td>
                <td><?= $p['categoria'] ?></td>
                <td><?= $p['stock'] ?></td>
                <td><?= $p['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
