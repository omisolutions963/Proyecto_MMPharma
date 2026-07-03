<?php
session_start();
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
 die("No autorizado");
}

require_once '../includes/db.php';
require_once '../includes/shipping_calculator.php';
require_once '../dashboard_admin/includes/fpdf/fpdf.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['carrito_data'])) {
 die("Datos del carrito vacíos o método inválido.");
}

$carrito = json_decode($_POST['carrito_data'], true);
if (!$carrito || !is_array($carrito)) {
 die("Datos del carrito inválidos.");
}

// Obtener datos del cliente actual
$cliente_id = $_SESSION['cliente_id'];
$stmt = $pdo->prepare("SELECT razon_social, rfc, domicilio_fiscal, email, telefono_local FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

if (!$cliente) {
 die("Cliente no encontrado.");
}

$tipo_cliente = $_SESSION['cliente_tipo'] ?? 'FARMACIA';

// Generar folio único (basado en timestamp y random)
$folio = 'COT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));

// Calcular vigencia: 10 días hábiles (lunes–viernes)
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
$fecha_emision = new DateTime();
$fecha_vigencia = sumarDiasHabilesVigencia(clone $fecha_emision, 10);

// Obtener detalles de los productos desde la base de datos (tasa_iva, sustancia, codigo)
$product_ids = array_column($carrito, 'id');
$product_details = [];
if (!empty($product_ids)) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT id, tasa_iva, sustancia, codigo, tipo FROM catalogo_productos WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $product_details = $stmt->fetchAll(PDO::FETCH_UNIQUE);
}

// Calcular subtotal
$subtotal_productos = 0;
$total_items_sin_iva = 0;
$total_items_iva = 0;
$total_normal_con_iva = 0;
$tiene_red_fria = false;
foreach ($carrito as $item) {
    $item_total = (float)$item['precio'] * (int)$item['cantidad'];
    $subtotal_productos += $item_total;
    $tasa = isset($product_details[$item['id']]) ? (float)$product_details[$item['id']]['tasa_iva'] : 0.00;
    $tipo = isset($product_details[$item['id']]) ? strtoupper($product_details[$item['id']]['tipo']) : 'SECO';
    
    $item_sin_iva = $item_total;
    $item_iva = $item_total * $tasa;
    $total_items_sin_iva += $item_sin_iva;
    $total_items_iva += $item_iva;
    
    if ($tipo === 'RED FRIA') {
        $tiene_red_fria = true;
    } else {
        $total_normal_con_iva += ($item_total + $item_iva);
    }
}

$costo_envio = 0.00;
$mensaje_envio = '';
$costo_envio_original = 0.00;
$recoger_sucursal = isset($_POST['recoger_sucursal']) && $_POST['recoger_sucursal'] === '1';

if (!empty($_POST['direccion_id'])) {
    $stmtDir = $pdo->prepare("SELECT estado, latitud, longitud FROM clientes_direcciones WHERE id = ? AND cliente_id = ?");
    $stmtDir->execute([$_POST['direccion_id'], $cliente_id]);
    $dirInfo = $stmtDir->fetch(PDO::FETCH_ASSOC);
    if ($dirInfo) {
        $lat = $dirInfo['latitud'] !== null ? (float)$dirInfo['latitud'] : null;
        $lng = $dirInfo['longitud'] !== null ? (float)$dirInfo['longitud'] : null;
        $calc = calcularCostoEnvio($total_normal_con_iva, $dirInfo['estado'], $lat, $lng, $tiene_red_fria);
        $costo_envio_original = $calc['costo'];
        
        if ($recoger_sucursal || ($total_normal_con_iva == 0 && $tiene_red_fria)) {
            $costo_envio = 0.00;
            $mensaje_envio = 'Recoger en sucursal';
        } else {
            $costo_envio = $calc['costo'];
            $mensaje_envio = mb_convert_encoding($calc['mensaje'], 'ISO-8859-1', 'UTF-8');
        }
    }
} else {
    if ($tiene_red_fria && $total_normal_con_iva == 0) {
        $costo_envio = 0.00;
        $mensaje_envio = 'Recoger en sucursal';
    }
}

$monto_total = $subtotal_productos + $total_items_iva + $costo_envio;
$envio_sin_iva = $costo_envio;
$envio_iva = 0;

$subtotal_sin_iva = $total_items_sin_iva + $envio_sin_iva;
$iva = $total_items_iva + $envio_iva;

// Crear PDF
class PDF extends FPDF {
 function Header() {
 if (file_exists('../logos/mmpharma-logotipo-horizontal.png')) {
 $this->Image('../logos/mmpharma-logotipo-horizontal.png', 10, 10, 50);
 }
 
 $this->SetFont('Arial', 'B', 15);
 $this->SetTextColor(0, 36, 81); // Primary color
 $this->Cell(80);
 $this->Cell(110, 10, mb_convert_encoding('Cotización de productos', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
 
 $this->SetFont('Arial', '', 10);
 $this->SetTextColor(100, 100, 100);
 $this->Cell(80);
 $this->Cell(110, 5, 'Distribuidora de medicamentos MM', 0, 1, 'R');
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

// Datos del Cliente y Cotización
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(100, 6, 'DATOS DEL CLIENTE', 0, 0, 'L');
$pdf->Cell(90, 6, mb_convert_encoding('DATOS DE COTIZACIÓN', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$y = $pdf->GetY();

// Columna Izquierda (Cliente)
$pdf->SetXY(10, $y);
$pdf->Cell(25, 5, 'Nombre:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($cliente['razon_social'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetXY(10, $y+5);
$pdf->Cell(25, 5, 'RFC:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($cliente['rfc'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->SetXY(10, $y+10);
$pdf->Cell(25, 5, 'Email:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($cliente['email'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 0, 1);


// Columna Derecha (Cotización)
$pdf->SetXY(110, $y);
$pdf->Cell(35, 5, 'Folio:', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(55, 5, $folio, 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$pdf->SetXY(110, $y+5);
$pdf->Cell(35, 5, 'Fecha:', 0, 0, 'R');
$pdf->Cell(55, 5, date('d/m/Y'), 0, 1, 'R');

$pdf->SetXY(110, $y+10);
$pdf->Cell(35, 5, 'Vigencia:', 0, 0, 'R');
$pdf->Cell(55, 5, mb_convert_encoding('Al ' . $fecha_vigencia->format('d/m/Y') . ' (10 ds. hab.)', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Ln(20);

// Tabla de Productos
$pdf->SetFillColor(0, 36, 81);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(15, 8, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(90, 8, 'DESCRIPCION', 1, 0, 'C', true);
$pdf->Cell(15, 8, 'IVA', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'P. UNITARIO', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'SUBTOTAL', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$fill = false;
foreach ($carrito as $item) {
 $pdf->SetFillColor(245, 245, 245);
 $pdf->Cell(15, 10, $item['cantidad'], 1, 0, 'C', $fill);
 
 // Truncar nombre si es muy largo
 $nombre = mb_convert_encoding($item['nombre'], 'ISO-8859-1', 'UTF-8');
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
  $subst = isset($product_details[$item['id']]) ? $product_details[$item['id']]['sustancia'] : '';
  $tasa = isset($product_details[$item['id']]) ? (float)$product_details[$item['id']]['tasa_iva'] : 0.16;
  $item_total = (float)$item['precio'] * (int)$item['cantidad'];
  $item_sin_iva = $item_total;
  $item_iva = $item_total * $tasa;
  
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
 
 $tasa = isset($product_details[$item['id']]) ? (float)$product_details[$item['id']]['tasa_iva'] : 0.00;
 $tasa_percentage = ($tasa * 100) . '%';
 $pdf->Cell(15, 10, $tasa_percentage, 1, 0, 'C', $fill);
 
 $pdf->Cell(35, 10, '$' . number_format($item['precio'], 2), 1, 0, 'R', $fill);
 $pdf->Cell(35, 10, '$' . number_format($item['precio'] * $item['cantidad'], 2), 1, 1, 'R', $fill);
 $fill = !$fill;
}

// Totales
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, 'Subtotal productos:', 0, 0, 'R');
$pdf->Cell(50, 6, '$' . number_format($subtotal_productos, 2), 0, 1, 'R');

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, mb_convert_encoding('Envío:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
$texto_envio = $costo_envio > 0 ? '$' . number_format($costo_envio, 2) : ($mensaje_envio !== '' ? $mensaje_envio : mb_convert_encoding('Envío gratis', 'ISO-8859-1', 'UTF-8'));
$pdf->Cell(50, 6, $texto_envio, 0, 1, 'R');

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, 'Subtotal (sin IVA):', 0, 0, 'R');
$pdf->Cell(50, 6, '$' . number_format($subtotal_sin_iva, 2), 0, 1, 'R');

$pdf->Cell(90, 6, '', 0, 0);
$pdf->Cell(50, 6, 'IVA:', 0, 0, 'R');
$pdf->Cell(50, 6, '$' . number_format($iva, 2), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 8, '', 0, 0);
$pdf->Cell(50, 8, 'TOTAL:', 1, 0, 'R');
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(50, 8, '$' . number_format($monto_total, 2), 1, 1, 'R');

$pdf->Ln(20);

if ($costo_envio > 0 || $recoger_sucursal || ($tiene_red_fria && $total_normal_con_iva == 0)) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(0, 36, 81);
    $pdf->MultiCell(0, 5, mb_convert_encoding(
        "Horario de entrega en sucursal: De 9am a 6pm todos los días de la semana.\nEl lugar donde pasará a recoger será proporcionado por un asesor de nosotros (para mantener la confidencialidad del lugar).",
        'ISO-8859-1', 'UTF-8'
    ), 0, 'C');
    $pdf->Ln(2);
}

if ($tiene_red_fria) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(200, 0, 0); // Rojo para destacar
    $pdf->MultiCell(0, 5, mb_convert_encoding(
        "Los productos de Red Fría requieren recolección por parte del cliente o transportista propio. MM Pharma no gestiona ni cobra este envío.",
        'ISO-8859-1', 'UTF-8'
    ), 0, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, mb_convert_encoding(
    "* Los precios unitarios y de envío mostrados incluyen el IVA correspondiente (16%).\nESTE DOCUMENTO ES UNA COTIZACIÓN INFORMATIVA. LOS PRECIOS Y DISPONIBILIDAD ESTÁN SUJETOS A CAMBIOS SIN PREVIO AVISO HASTA QUE SE CONFIRME LA DISPONIBILIDAD EN ALMACÉN Y SE REALICE EL PAGO CORRESPONDIENTE.",
    'ISO-8859-1', 'UTF-8'
), 0, 'C');

$pdf->Output('I', 'Cotizacion_' . $folio . '.pdf');
?>
