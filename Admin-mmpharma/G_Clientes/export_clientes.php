<?php
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Reporte_Clientes_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$clientes = [
    ['nombre' => 'Farmacias del Centro', 'rfc' => 'FDC930215ABC', 'tipo' => 'FARMACIA', 'contacto' => 'Dr. Carlos Mendez', 'status' => 'Activo'],
    ['nombre' => 'Dist. Médica Norte', 'rfc' => 'DMN010101XYZ', 'tipo' => 'DISTRIBUIDORA', 'contacto' => 'Lic. Elena Gomez', 'status' => 'Activo'],
    ['nombre' => 'Enlace Medico del Norte', 'rfc' => 'EMN080505RTY', 'tipo' => 'EMPRESA', 'contacto' => 'Dr. Samuel Torres', 'status' => 'Inactivo'],
    ['nombre' => 'ProFarma Center', 'rfc' => 'PFC201111QWE', 'tipo' => 'FARMACIA', 'contacto' => 'Dra. Monica Ruiz', 'status' => 'Docs Pendientes'],
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
        <tr><td colspan="5" class="title">MMPharma - Reporte General de Clientes</td></tr>
        <tr><td colspan="5" class="subtitle">Generado el: <?php echo date('d/m/Y H:i:s'); ?></td></tr>
        <tr><td colspan="5"></td></tr>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>Cliente / Razón Social</th>
                <th>RFC</th>
                <th>Tipo</th>
                <th>Contacto</th>
                <th>Estatus</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $c): ?>
            <tr>
                <td><?= $c['nombre'] ?></td>
                <td><?= $c['rfc'] ?></td>
                <td><?= $c['tipo'] ?></td>
                <td><?= $c['contacto'] ?></td>
                <td><?= $c['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
