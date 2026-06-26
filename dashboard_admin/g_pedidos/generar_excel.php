<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../login/login.php");
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) die("ID inválido");

// Obtener datos del pedido
$stmt = $pdo->prepare("
    SELECT p.*, c.razon_social, c.rfc, c.domicilio_fiscal, c.email, c.telefono_local 
    FROM clientes_pedidos p 
    JOIN clientes_usuarios c ON p.cliente_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$pedido = $stmt->fetch();

if (!$pedido) die("Pedido no encontrado");

// Obtener detalles
$stmt = $pdo->prepare("
    SELECT pd.*, cp.tasa_iva, cp.codigo, cp.sustancia 
    FROM clientes_pedidos_detalle pd 
    LEFT JOIN catalogo_productos cp ON pd.producto_id = cp.id 
    WHERE pd.pedido_id = ?
");
$stmt->execute([$id]);
$detalles = $stmt->fetchAll();

// Enviar cabeceras de Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=Cotizacion_" . $pedido['folio'] . "_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
    body { font-family: Arial, sans-serif; }
    .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .table th { background: #002451; color: #fff; padding: 10px; text-align: center; border: 1px solid #002451; font-weight: bold; }
    .table td { padding: 10px; border: 1px solid #c4c6d0; color: #051d30; text-align: left; }
    .table tr:nth-child(even) { background: #f7f9ff; }
    .title { color: #002451; font-size: 20px; font-weight: bold; text-align: left; }
    .subtitle { color: #006397; font-size: 12px; text-align: left; margin-bottom: 20px; }
    .section-title { font-weight: bold; color: #002451; border-bottom: 2px solid #002451; padding-bottom: 5px; font-size: 14px; }
    .info-table { border-collapse: collapse; margin-bottom: 20px; width: 100%; }
    .info-table td { padding: 5px 10px; border: none; font-size: 12px; }
    .info-label { font-weight: bold; color: #002451; }
    .right-align { text-align: right; }
    .center-align { text-align: center; }
    .total-label { font-weight: bold; color: #002451; background: #e8f0ff; text-align: right; font-size: 12px; }
    .total-value { font-weight: bold; color: #002451; background: #e8f0ff; text-align: right; font-size: 12px; }
    .grand-total-label { font-weight: bold; color: #fff; background: #002451; text-align: right; font-size: 13px; }
    .grand-total-value { font-weight: bold; color: #fff; background: #002451; text-align: right; font-size: 13px; }
    .disclaimer { font-style: italic; color: #666; font-size: 10px; margin-top: 20px; text-align: center; }
</style>
</head>
<body>

<table style="width:100%">
    <tr>
        <td colspan="7" class="title">MMPharma — Cotización de productos</td>
    </tr>
    <tr>
        <td colspan="7" class="subtitle">Distribuidora de medicamentos MM — Venta y Distribución Nacional</td>
    </tr>
    <tr><td colspan="7"></td></tr>
</table>

<table class="info-table">
    <tr>
        <td colspan="3" class="section-title">DATOS DEL CLIENTE</td>
        <td></td>
        <td colspan="3" class="section-title">DATOS DEL PEDIDO</td>
    </tr>
    <tr>
        <td class="info-label" width="10%">Nombre:</td>
        <td colspan="2" width="40%"><?= htmlspecialchars($pedido['razon_social']) ?></td>
        <td width="5%"></td>
        <td class="info-label" width="15%">Folio:</td>
        <td colspan="2" width="30%"><?= htmlspecialchars($pedido['folio']) ?></td>
    </tr>
    <tr>
        <td class="info-label">RFC:</td>
        <td colspan="2"><?= htmlspecialchars($pedido['rfc'] ?: 'N/A') ?></td>
        <td></td>
        <td class="info-label">Fecha:</td>
        <td colspan="2"><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
    </tr>
    <tr>
        <td class="info-label">Email:</td>
        <td colspan="2"><?= htmlspecialchars($pedido['email'] ?: 'N/A') ?></td>
        <td></td>
        <td class="info-label">Estado Envío:</td>
        <td colspan="2"><?= htmlspecialchars($pedido['estado_envio']) ?></td>
    </tr>
    <tr>
        <td class="info-label">Teléfono:</td>
        <td colspan="2"><?= htmlspecialchars($pedido['telefono_local'] ?: 'N/A') ?></td>
        <td></td>
        <td class="info-label">Método Pago:</td>
        <td colspan="2"><?= htmlspecialchars(str_replace('_', ' ', $pedido['metodo_pago'])) ?></td>
    </tr>
</table>

<table class="table">
    <thead>
        <tr>
            <th width="8%">CANT.</th>
            <th width="15%">CÓDIGO</th>
            <th width="35%">DESCRIPCIÓN</th>
            <th width="20%">SUSTANCIA ACTIVA</th>
            <th width="10%">TASA IVA</th>
            <th width="12%">P. UNITARIO</th>
            <th width="12%">SUBTOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $suma_subtotales = 0;
        $total_iva = 0;
        foreach ($detalles as $det): 
            $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
            $subtotal_linea = (float)$det['subtotal'];
            $suma_subtotales += $subtotal_linea;
            $item_iva = $subtotal_linea * $tasa;
            $total_iva += $item_iva;
        ?>
        <tr>
            <td class="center-align"><?= (int)$det['cantidad'] ?></td>
            <td class="center-align"><?= htmlspecialchars($det['codigo'] ?: '—') ?></td>
            <td><?= htmlspecialchars($det['nombre_producto']) ?></td>
            <td><?= htmlspecialchars($det['sustancia'] ?: 'No registrada') ?></td>
            <td class="center-align"><?= ($tasa * 100) ?>%</td>
            <td class="right-align">$<?= number_format($det['precio_unitario'], 2) ?></td>
            <td class="right-align">$<?= number_format($det['subtotal'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5"></td>
            <td class="total-label">Subtotal productos:</td>
            <td class="total-value">$<?= number_format($suma_subtotales, 2) ?></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td class="total-label">IVA:</td>
            <td class="total-value">$<?= number_format($total_iva, 2) ?></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td class="total-label">Costo de envío:</td>
            <td class="total-value">
                <?php if ($suma_subtotales < 4000.00 || $pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO'): ?>
                    Gratis (Recoger en almacén)
                <?php else: ?>
                    $<?= number_format($pedido['costo_envio'], 2) ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td class="grand-total-label">TOTAL:</td>
            <td class="grand-total-value">$<?= number_format($pedido['monto_total'], 2) ?></td>
        </tr>
    </tfoot>
</table>

<table style="width:100%; margin-top:20px;">
    <tr>
        <td colspan="7" class="disclaimer">
            ESTE DOCUMENTO ES UNA COTIZACIÓN INFORMATIVA. LOS PRECIOS Y DISPONIBILIDAD ESTÁN SUJETOS A CAMBIOS SIN PREVIO AVISO HASTA QUE SE CONFIRME LA DISPONIBILIDAD EN ALMACÉN Y SE REALICE EL PAGO CORRESPONDIENTE.
        </td>
    </tr>
</table>

</body>
</html>
