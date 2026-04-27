<?php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Mas_Vendidos_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$products = [
    ['name' => 'Amoxicilina 500mg',    'category' => 'Antibiótico',       'units' => 240, 'rank' => 1],
    ['name' => 'Insulina Glargina',    'category' => 'Red Fría',           'units' => 185, 'rank' => 2],
    ['name' => 'Paracetamol 1g',       'category' => 'Analgésico',         'units' => 150, 'rank' => 3],
    ['name' => 'Ibuprofeno Suspensión','category' => 'Antiinflamatorio',   'units' => 98,  'rank' => 4],
    ['name' => 'Losartán 50mg',        'category' => 'Antihipertensivo',   'units' => 72,  'rank' => 5],
];
?>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        background-color: #002451;
        color: #ffffff;
        padding: 10px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #002451;
    }
    .table td {
        padding: 10px;
        border: 1px solid #c4c6d0;
        color: #051d30;
    }
    .table tr:nth-child(even) {
        background-color: #f7f9ff;
    }
    .title {
        color: #002451;
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        height: 40px;
    }
    .subtitle {
        color: #006397;
        font-size: 14px;
        text-align: center;
        height: 30px;
    }
    .rank-cell {
        font-weight: bold;
        color: #002c13;
        text-align: center;
    }
    .units-cell {
        font-weight: bold;
        color: #006397;
        text-align: center;
    }
</style>
</head>
<body>
    <table>
        <tr>
            <td colspan="4" class="title">MMPharma - Reporte de Productos Más Vendidos</td>
        </tr>
        <tr>
            <td colspan="4" class="subtitle">Generado el: <?php echo date('d/m/Y H:i:s'); ?></td>
        </tr>
        <tr>
            <td colspan="4"></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Nombre del Producto</th>
                <th>Categoría</th>
                <th>Unidades Vendidas</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td class="rank-cell">#<?= $p['rank'] ?></td>
                <td><?= $p['name'] ?></td>
                <td><?= $p['category'] ?></td>
                <td class="units-cell"><?= $p['units'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
