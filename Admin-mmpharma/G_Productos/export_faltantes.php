<?php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Faltantes_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$faltantes = [
    ['sku' => 'PH-1044-AM', 'name' => 'Amoxicilina 500mg', 'stock' => 12, 'min' => 50],
    ['sku' => 'PH-2091-AC', 'name' => 'Ácido Ascórbico 1g', 'stock' => 5, 'min' => 20],
    ['sku' => 'PH-8832-RF', 'name' => 'Vacuna Antitetánica', 'stock' => 0, 'min' => 15],
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
    .alert-cell { color: #ba1a1a; font-weight: bold; }
</style>
</head>
<body>
    <table>
        <tr><td colspan="4" class="title">MMPharma - Reporte de Productos Faltantes</td></tr>
        <tr><td colspan="4" class="subtitle">Generado el: <?php echo date('d/m/Y H:i:s'); ?></td></tr>
        <tr><td colspan="4"></td></tr>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Producto</th>
                <th>Stock Actual</th>
                <th>Mínimo Requerido</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($faltantes as $p): ?>
            <tr>
                <td><?= $p['sku'] ?></td>
                <td><?= $p['name'] ?></td>
                <td class="alert-cell"><?= $p['stock'] ?></td>
                <td><?= $p['min'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
