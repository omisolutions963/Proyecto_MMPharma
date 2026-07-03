<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
 header("Location: ../../login/login.php");
 exit;
}

require_once '../clinical_core/db.php';
require_once '../includes/fpdf/fpdf.php';

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) die("ID inválido");

// Obtener datos del pedido
$stmt = $pdo->prepare("SELECT p.*, c.razon_social, c.rfc, c.domicilio_fiscal, c.email, c.telefono_local FROM clientes_pedidos p JOIN clientes_usuarios c ON p.cliente_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$pedido = $stmt->fetch();

if (!$pedido) die("Pedido no encontrado");

// Obtener detalles
$stmt = $pdo->prepare("
    SELECT pd.*, cp.tasa_iva, cp.codigo, cp.sustancia, cp.tipo 
    FROM clientes_pedidos_detalle pd 
    LEFT JOIN catalogo_productos cp ON pd.producto_id = cp.id 
    WHERE pd.pedido_id = ?
");
$stmt->execute([$id]);
$detalles = $stmt->fetchAll();

// Crear PDF
class PDF extends FPDF {
 function Header() {
 // Logo (Asegúrate de que la ruta sea correcta)
 if (file_exists('../../logos/mmpharma-logotipo-horizontal.png')) {
 $this->Image('../../logos/mmpharma-logotipo-horizontal.png', 10, 10, 50);
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

// Datos del Cliente y Pedido
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(100, 6, 'DATOS DEL CLIENTE', 0, 0, 'L');
$pdf->Cell(90, 6, 'DATOS DEL PEDIDO', 0, 1, 'R');

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(50, 50, 50);

$y = $pdf->GetY();

// Columna Izquierda (Cliente)
$pdf->SetXY(10, $y);
$pdf->Cell(25, 5, 'Nombre:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['razon_social'], 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Cell(25, 5, 'RFC:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['rfc'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 0, 1);
$pdf->Cell(25, 5, 'Email:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['email'] ?? 'N/A', 'ISO-8859-1', 'UTF-8'), 0, 1);


// Columna Derecha (Pedido)
$pdf->SetXY(110, $y);
$pdf->Cell(35, 5, 'Folio:', 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(55, 5, $pedido['folio'], 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$pdf->SetXY(110, $y+5);
$pdf->Cell(35, 5, 'Fecha:', 0, 0, 'R');
$pdf->Cell(55, 5, date('d/m/Y', strtotime($pedido['fecha_pedido'])), 0, 1, 'R');

$pdf->SetXY(110, $y+10);
$pdf->Cell(35, 5, 'Estado:', 0, 0, 'R');
$pdf->Cell(55, 5, mb_convert_encoding($pedido['estado_envio'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Ln(15);

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
foreach ($detalles as $det) {
  $pdf->SetFillColor(245, 245, 245);
  $pdf->Cell(15, 10, $det['cantidad'], 1, 0, 'C', $fill);
  
  // Truncar nombre si es muy largo
  $nombre = mb_convert_encoding($det['nombre_producto'], 'ISO-8859-1', 'UTF-8');
  if (strlen($nombre) > 55) {
  $nombre = substr($nombre, 0, 52) . '...';
  }
  
  $x = $pdf->GetX();
  $y = $pdf->GetY();
  $pdf->Rect($x, $y, 105, 10, $fill ? 'DF' : 'D');
  $pdf->SetXY($x + 2, $y + 1);
  $pdf->SetFont('Arial', 'B', 8);
  $pdf->Cell(101, 4, $nombre, 0, 1, 'L');
  
  $pdf->SetXY($x + 2, $y + 5);
  $pdf->SetFont('Arial', 'I', 7);
  $pdf->SetTextColor(100, 100, 100);
  
  $subst = $det['sustancia'] ?? '';
  $tasa = isset($det['tasa_iva']) ? (float)$det['tasa_iva'] : 0.16;
  $subtotal_linea = (float)$det['subtotal'];
  $item_sin_iva = $subtotal_linea;
  $item_iva = $subtotal_linea * $tasa;
  
  // Truncar sustancia si es muy larga para evitar desbordamiento
  $subst_txt = $subst ?: 'No registrada';
  if (strlen($subst_txt) > 45) {
      $subst_txt = substr($subst_txt, 0, 42) . '...';
  }
  $desc_secundaria = 'Sustancia: ' . $subst_txt . ' | IVA: +$' . number_format($item_iva, 2);
  
  $pdf->Cell(101, 4, mb_convert_encoding($desc_secundaria, 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
  $pdf->SetTextColor(50, 50, 50);
  $pdf->SetFont('Arial', '', 9);
  $pdf->SetXY($x + 105, $y);
  
  $pdf->Cell(35, 10, '$' . number_format($det['precio_unitario'], 2), 1, 0, 'R', $fill);
  $pdf->Cell(35, 10, '$' . number_format($det['subtotal'], 2), 1, 1, 'R', $fill);
  $fill = !$fill;
}

// Totales
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(50, 50, 50);

$subtotal_prod = 0;
foreach ($detalles as $det) {
    $subtotal_prod += (float)$det['subtotal'];
}
$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, mb_convert_encoding('SUBTOTAL PROD:', 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
$pdf->Cell(35, 6, '$' . number_format($subtotal_prod, 2), 1, 1, 'R');

$pdf->Cell(120, 6, '', 0, 0);
$pdf->Cell(35, 6, mb_convert_encoding('ENVIO:', 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
if ($pedido['estado_envio'] === 'SU PEDIDO ESTARÁ LISTO PARA QUE PASE A RECOLECTARLO' || $pedido['estado_envio'] === 'RECOGER EN SUCURSAL' || !empty($pedido['recoger_sucursal'])) {
    $pdf->Cell(35, 6, 'RECOGE ALMACEN', 1, 1, 'R');
} else {
    $pdf->Cell(35, 6, '$' . number_format($pedido['costo_envio'], 2), 1, 1, 'R');
}

if ((float)($pedido['embalaje_red_fria'] ?? 0) > 0) {
    $pdf->Cell(120, 6, '', 0, 0);
    $pdf->Cell(35, 6, mb_convert_encoding('EMB. RED FRIA:', 'ISO-8859-1', 'UTF-8'), 1, 0, 'R');
    $pdf->Cell(35, 6, '$' . number_format($pedido['embalaje_red_fria'], 2), 1, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(120, 8, '', 0, 0);
$pdf->Cell(35, 8, 'TOTAL:', 1, 0, 'R');
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(35, 8, '$' . number_format($pedido['monto_total'], 2), 1, 1, 'R');

$pdf->Ln(20);

$tiene_red_fria = false;
foreach ($detalles as $det) {
    if (strtoupper($det['tipo'] ?? '') === 'RED FRIA') {
        $tiene_red_fria = true;
        break;
    }
}

if ($tiene_red_fria) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetTextColor(200, 0, 0); // Rojo
    $pdf->MultiCell(0, 5, mb_convert_encoding("ESTE PEDIDO INCLUYE PRODUCTOS DE RED FRÍA. ES RESPONSABILIDAD DEL CLIENTE ORGANIZAR SU PROPIO TRANSPORTE, MM PHARMA NO GESTIONA NI COBRA ESTE ENVÍO.", 'ISO-8859-1', 'UTF-8'), 0, 'C');
    $pdf->Ln(5);
}

$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, mb_convert_encoding("ESTE DOCUMENTO ES UNA COTIZACIÓN INFORMATIVA. LOS PRECIOS Y DISPONIBILIDAD ESTÁN SUJETOS A CAMBIOS SIN PREVIO AVISO HASTA QUE SE CONFIRME LA DISPONIBILIDAD EN ALMACÉN Y SE REALICE EL PAGO CORRESPONDIENTE.", 'ISO-8859-1', 'UTF-8'), 0, 'C');

$pdf->Output('I', 'Cotizacion_' . $pedido['folio'] . '.pdf');
?>
