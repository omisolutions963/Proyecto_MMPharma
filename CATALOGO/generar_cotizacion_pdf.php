<?php
session_start();
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
 die("No autorizado");
}

require_once '../INCLUDES/db.php';
require_once '../INCLUDES/shipping_calculator.php';
require_once '../DASHBOARD_ADMIN/Includes/fpdf/fpdf.php';

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

// Calcular subtotal
$subtotal_productos = 0;
foreach ($carrito as $item) {
 $subtotal_productos += (float)$item['precio'] * (int)$item['cantidad'];
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
        $calc = calcularCostoEnvio($subtotal_productos, $dirInfo['estado'], $lat, $lng);
        $costo_envio_original = $calc['costo'];
        
        if ($recoger_sucursal) {
            $costo_envio = 0.00;
            $mensaje_envio = 'Recoger en sucursal';
        } else {
            $costo_envio = $calc['costo'];
            $mensaje_envio = mb_convert_encoding($calc['mensaje'], 'ISO-8859-1', 'UTF-8');
        }
    }
}

$monto_total = $subtotal_productos + $costo_envio;
$subtotal_sin_iva = $monto_total / 1.16;
$iva = $monto_total - $subtotal_sin_iva;

// Crear PDF
class PDF extends FPDF {
 function Header() {
 if (file_exists('../logos/MMPharma-Logotipo-Horizontal.png')) {
 $this->Image('../logos/MMPharma-Logotipo-Horizontal.png', 10, 10, 50);
 }
 
 $this->SetFont('Arial', 'B', 15);
 $this->SetTextColor(0, 36, 81); // Primary color
 $this->Cell(80);
 $this->Cell(110, 10, mb_convert_encoding('COTIZACIÓN DE PRODUCTOS', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
 
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
$pdf->SetXY(10, $y+15);
$pdf->Cell(25, 5, 'Nivel:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($tipo_cliente, 'ISO-8859-1', 'UTF-8'), 0, 1);

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
$pdf->Cell(55, 5, '15 Dias', 0, 1, 'R');

$pdf->Ln(20);

// Tabla de Productos
$pdf->SetFillColor(0, 36, 81);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(15, 8, 'CANT.', 1, 0, 'C', true);
$pdf->Cell(105, 8, 'DESCRIPCION', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'P. UNITARIO', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'SUBTOTAL', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$fill = false;
foreach ($carrito as $item) {
 $pdf->SetFillColor(245, 245, 245);
 $pdf->Cell(15, 8, $item['cantidad'], 1, 0, 'C', $fill);
 
 // Truncar nombre si es muy largo
 $nombre = mb_convert_encoding($item['nombre'], 'ISO-8859-1', 'UTF-8');
 if (strlen($nombre) > 55) {
 $nombre = substr($nombre, 0, 52) . '...';
 }
 
 $pdf->Cell(105, 8, $nombre, 1, 0, 'L', $fill);
 $pdf->Cell(35, 8, '$' . number_format($item['precio'], 2), 1, 0, 'R', $fill);
 $pdf->Cell(35, 8, '$' . number_format($item['precio'] * $item['cantidad'], 2), 1, 1, 'R', $fill);
 $fill = !$fill;
}

// Totales
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Subtotal Productos:', 0, 0, 'R');
$pdf->Cell(35, 6, '$' . number_format($subtotal_productos, 2), 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, mb_convert_encoding('Envío:', 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
$texto_envio = $costo_envio > 0 ? '$' . number_format($costo_envio, 2) : ($mensaje_envio !== '' ? $mensaje_envio : mb_convert_encoding('Envío Gratis', 'ISO-8859-1', 'UTF-8'));
$pdf->Cell(35, 6, $texto_envio, 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'Subtotal (sin IVA):', 0, 0, 'R');
$pdf->Cell(35, 6, '$' . number_format($subtotal_sin_iva, 2), 0, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, 'IVA (16%):', 0, 0, 'R');
$pdf->Cell(35, 6, '$' . number_format($iva, 2), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(120, 8, '', 0, 0);
$pdf->Cell(35, 8, 'TOTAL:', 1, 0, 'R');
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(35, 8, '$' . number_format($monto_total, 2), 1, 1, 'R');

$pdf->Ln(20);

if ($costo_envio > 0 || ($recoger_sucursal && $costo_envio_original > 0)) {
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
    "* Todos los precios unitarios y de envío mostrados incluyen el 16% de IVA.\nESTE DOCUMENTO ES UNA COTIZACIÓN INFORMATIVA. LOS PRECIOS Y DISPONIBILIDAD ESTÁN SUJETOS A CAMBIOS SIN PREVIO AVISO HASTA QUE SE CONFIRME LA DISPONIBILIDAD EN ALMACÉN Y SE REALICE EL PAGO CORRESPONDIENTE.",
    'ISO-8859-1', 'UTF-8'
), 0, 'C');

$pdf->Output('I', 'Cotizacion_' . $folio . '.pdf');
?>
