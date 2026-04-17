<?php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Ventas_Pedidos_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$pedidos = [
    ['id' => '#ORD-2024-8841', 'cliente' => 'Farmacias del Valle Central', 'fecha' => '24 Oct 2023', 'monto' => '$12,450.00', 'status' => 'Entregado'],
    ['id' => '#ORD-2024-8902', 'cliente' => 'BioLab S.A. de C.V.', 'fecha' => '25 Oct 2023', 'monto' => '$8,290.50', 'status' => 'Enviado'],
    ['id' => '#ORD-2024-9011', 'cliente' => 'Botica Médica Universal', 'fecha' => '26 Oct 2023', 'monto' => '$42,000.00', 'status' => 'Procesando'],
    ['id' => '#ORD-2024-9104', 'cliente' => 'Red Hospitalaria Norte', 'fecha' => '27 Oct 2023', 'monto' => '$156,720.00', 'status' => 'Pendiente'],
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
        <tr><td colspan="5" class="title">MMPharma - Reporte de Ventas (Pedidos)</td></tr>
        <tr><td colspan="5" class="subtitle">Generado el: <?php echo date('d/m/Y H:i:s'); ?></td></tr>
        <tr><td colspan="5"></td></tr>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Monto Total</th>
                <th>Estado Envío</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['cliente'] ?></td>
                <td><?= $p['fecha'] ?></td>
                <td><?= $p['monto'] ?></td>
                <td><?= $p['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
