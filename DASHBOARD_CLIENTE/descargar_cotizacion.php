<?php
session_start();
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

require_once '../includes/db.php';
require_once '../dashboard_admin/includes/fpdf/fpdf.php';

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cliente_id = $_SESSION['cliente_id'];

if (!$id) die("ID inválido");

// Obtener datos del pedido verificando que pertenece al cliente
$stmt = $pdo->prepare("
    SELECT p.*, c.razon_social, c.rfc, c.domicilio_fiscal, c.email, c.telefono_local 
    FROM clientes_pedidos p 
    JOIN clientes_usuarios c ON p.cliente_id = c.id 
    WHERE p.id = ? AND p.cliente_id = ?
");
$stmt->execute([$id, $cliente_id]);
$pedido = $stmt->fetch();

if (!$pedido) die("Cotización no encontrada o no autorizada.");

// Obtener detalles del pedido
$stmt = $pdo->prepare("
    SELECT pd.*, cp.tasa_iva, cp.codigo, cp.sustancia 
    FROM clientes_pedidos_detalle pd 
    LEFT JOIN catalogo_productos cp ON pd.producto_id = cp.id 
    WHERE pd.pedido_id = ?
");
$stmt->execute([$id]);
$detalles = $stmt->fetchAll();

$tipo_cliente = $_SESSION['cliente_tipo'] ?? ($pedido['tipo_cliente'] ?? 'FARMACIA');

// Calcular totales
$costo_envio = isset($pedido['costo_envio']) ? (float)$pedido['costo_envio'] : 0.00;
$monto_total = (float)$pedido['monto_total'];

$subtotal_productos = 0;
$total_items_sin_iva = 0;
$total_items_iva = 0;
foreach($detalles as $det) {
    $subtotal_linea = (float)$det['subtotal'];
    $subtotal_productos += $subtotal_linea;
    $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.00;
    $item_sin_iva = $subtotal_linea / (1 + $tasa);
    $item_iva = $subtotal_linea - $item_sin_iva;
    $total_items_sin_iva += $item_sin_iva;
    $total_items_iva += $item_iva;
}

$recoger_sucursal = ($pedido['estado_envio'] === 'RECOGER EN SUCURSAL' || $pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || !empty($pedido['recoger_sucursal']) || $subtotal_productos < 4000.00);

if ($subtotal_productos < 4000.00 || $pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO') {
    $costo_envio = 0.00;
} else {
    if ($costo_envio == 0 && abs($monto_total - $subtotal_productos) > 0.01) {
        $costo_envio = $monto_total - $subtotal_productos;
    }
}

$envio_sin_iva = $costo_envio / 1.16;
$envio_iva = $costo_envio - $envio_sin_iva;

$subtotal_sin_iva = $total_items_sin_iva + $envio_sin_iva;
$iva = $total_items_iva + $envio_iva;

// Fechas
if (!function_exists('sumarDiasHabilesVigencia')) {
    function sumarDiasHabilesVigencia(DateTime $fecha, int $dias): DateTime {
        $agregados = 0;
        while ($agregados < $dias) {
            $fecha->modify('+1 day');
            $dow = (int)$fecha->format('N'); // 1=Lun … 7=Dom
            if ($dow < 6) {
                $agregados++;
            }
        }
        return $fecha;
    }
}
$created_at_dt = new DateTime($pedido['created_at']);
$fecha_emision = $created_at_dt->format('d/m/Y');
$fecha_vigencia_dt = sumarDiasHabilesVigencia(clone $created_at_dt, 10);
$vigencia = $fecha_vigencia_dt->format('d/m/Y');

// ─── Crear PDF con el mismo diseño del carrito ─────────────────────────────
class PDF extends FPDF {
    function Header() {
        if (file_exists('../logos/mmpharma-logotipo-horizontal.png')) {
            $this->Image('../logos/mmpharma-logotipo-horizontal.png', 10, 10, 50);
        }

        $this->SetFont('Arial', 'B', 15);
        $this->SetTextColor(0, 36, 81);
        $this->Cell(80);
        $this->Cell(110, 10, mb_convert_encoding('COTIZACIÓN DE productos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(80);
        $this->Cell(110, 5, 'MMPharma Clinical Systems S.A. de C.V.', 0, 1, 'R');
        $this->Cell(80);
        $this->Cell(110, 5, 'Venta y Distribucion Nacional', 0, 1, 'R');
        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, mb_convert_encoding('Página ', 'ISO-8859-1', 'UTF-8') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// ─── Datos del cliente y de la cotización (2 columnas) ────────────────────
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(100, 6, 'DATOS DEL CLIENTE', 0, 0, 'L');
$pdf->Cell(90, 6, mb_convert_encoding('DATOS DE COTIZACIÓN', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);
$y = $pdf->GetY();

// Columna Izquierda – Cliente
$pdf->SetXY(10, $y);
$pdf->Cell(25, 5, 'Nombre:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['razon_social'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetXY(10, $y + 5);
$pdf->Cell(25, 5, 'RFC:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['rfc'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetXY(10, $y + 10);
$pdf->Cell(25, 5, 'Email:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['email'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetXY(10, $y + 15);
$pdf->Cell(25, 5, 'Nivel:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($tipo_cliente, 'ISO-8859-1', 'UTF-8'), 0, 1);

// Columna Derecha – Cotización
$pdf->SetXY(110, $y);
$pdf->Cell(35, 5, 'Folio:', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(55, 5, $pedido['folio'], 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$pdf->SetXY(110, $y + 5);
$pdf->Cell(35, 5, 'Fecha:', 0, 0, 'R');
$pdf->Cell(55, 5, $fecha_emision, 0, 1, 'R');

$pdf->SetXY(110, $y + 10);
$pdf->Cell(35, 5, 'Vigencia:', 0, 0, 'R');
$pdf->Cell(55, 5, $vigencia, 0, 1, 'R');

$pdf->SetXY(110, $y + 15);
$pdf->Cell(35, 5, 'Estado:', 0, 0, 'R');
$pdf->Cell(55, 5, mb_convert_encoding($pedido['estado_envio'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Ln(20);

// ─── Tabla de productos ────────────────────────────────────────────────────
$pdf->SetFillColor(0, 36, 81);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(15,  8, 'CANT.',       1, 0, 'C', true);
$pdf->Cell(90, 8, 'DESCRIPCION', 1, 0, 'C', true);
$pdf->Cell(15,  8, 'IVA',          1, 0, 'C', true);
$pdf->Cell(35,  8, 'P. UNITARIO', 1, 0, 'C', true);
$pdf->Cell(35,  8, 'SUBTOTAL',    1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$fill = false;
foreach ($detalles as $det) {
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(15, 10, $det['cantidad'], 1, 0, 'C', $fill);

    $nombre = mb_convert_encoding($det['nombre_producto'], 'ISO-8859-1', 'UTF-8');
    if (strlen($nombre) > 48) {
        $nombre = substr($nombre, 0, 45) . '...';
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Rect($x, $y, 90, 10, $fill ? 'DF' : 'D');
    $pdf->SetXY($x + 2, $y + 1);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(86, 4, $nombre, 0, 1, 'L');
    $pdf->SetXY($x + 2, $y + 5);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->SetTextColor(100, 100, 100);
    $subst = $det['sustancia'] ?? '';
    $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
    $subtotal_linea = (float)$det['subtotal'];
    $item_sin_iva = $subtotal_linea / (1 + $tasa);
    $item_iva = $subtotal_linea - $item_sin_iva;
    
    // Truncar sustancia si es muy larga para evitar desbordamiento
    $subst_txt = $subst ?: 'No registrada';
    if (strlen($subst_txt) > 40) {
        $subst_txt = substr($subst_txt, 0, 37) . '...';
    }
    $desc_secundaria = 'Sustancia: ' . $subst_txt . ' | IVA: +$' . number_format($item_iva, 2);
    $pdf->Cell(86, 4, mb_convert_encoding($desc_secundaria, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
    $pdf->SetTextColor(50, 50, 50);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetXY($x + 90, $y);

    $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.00;
    $tasa_percentage = ($tasa * 100) . '%';
    $pdf->Cell(15, 10, $tasa_percentage, 1, 0, 'C', $fill);

    $pdf->Cell(35,  10, '$' . number_format($det['precio_unitario'], 2), 1, 0, 'R', $fill);
    $pdf->Cell(35,  10, '$' . number_format($det['subtotal'], 2),        1, 1, 'R', $fill);
    $fill = !$fill;
}

// ─── Totales ───────────────────────────────────────────────────────────────
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, 'Subtotal productos:', 0, 0, 'R');
$pdf->Cell(50, 6, '$' . number_format($subtotal_productos, 2), 0, 1, 'R');

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, mb_convert_encoding('Envío:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');

if ($subtotal_productos < 4000.00 || $pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO') {
    $texto_envio = mb_convert_encoding('Recoger en almacén (Pickup)', 'ISO-8859-1', 'UTF-8');
} else if ($pedido['estado_envio'] === 'RECOGER EN SUCURSAL' || !empty($pedido['recoger_sucursal'])) {
    $texto_envio = mb_convert_encoding('Recoger en sucursal', 'ISO-8859-1', 'UTF-8');
} else {
    $texto_envio = $costo_envio > 0 ? '$' . number_format($costo_envio, 2) : mb_convert_encoding('Envío gratis', 'ISO-8859-1', 'UTF-8');
}

$pdf->Cell(50, 6, $texto_envio, 0, 1, 'R');

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, 'Subtotal (sin IVA):', 0, 0, 'R');
$pdf->Cell(50, 6, '$' . number_format($subtotal_sin_iva, 2), 0, 1, 'R');

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, 'IVA:', 0, 0, 'R');
$pdf->Cell(50, 6, '$' . number_format($iva, 2), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 8, '', 0, 0);
$pdf->Cell(50,  8, 'TOTAL:', 1, 0, 'R');
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(50,  8, '$' . number_format($monto_total, 2), 1, 1, 'R');

// ─── Aviso legal ──────────────────────────────────────────────────────────
$pdf->Ln(20);

if ($costo_envio > 0 || $recoger_sucursal) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(0, 36, 81);
    $pdf->MultiCell(0, 5, mb_convert_encoding(
        "Horario de entrega en sucursal: De 9am a 6pm todos los días de la semana.\nEl lugar donde pasará a recoger será proporcionado por un asesor de nosotros (para mantener la confidencialidad del lugar).",
        'ISO-8859-1', 'UTF-8'
    ), 0, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, mb_convert_encoding(
    "* Los precios unitarios y de envío mostrados incluyen el IVA correspondiente (16% o 0%).\nESTE DOCUMENTO ES UNA COTIZACIÓN INFORMATIVA. LOS PRECIOS Y DISPONIBILIDAD ESTÁN SUJETOS A CAMBIOS SIN PREVIO AVISO HASTA QUE SE CONFIRME LA DISPONIBILIDAD EN ALMACÉN Y SE REALICE EL PAGO CORRESPONDIENTE.",
    'ISO-8859-1', 'UTF-8'
), 0, 'C');

// 'D' = descarga directa   |   'I' = inline (abre en el navegador)
$action = isset($_GET['action']) && $_GET['action'] === 'view' ? 'I' : 'D';
$pdf->Output($action, 'Cotizacion_' . $pedido['folio'] . '.pdf');
?>
