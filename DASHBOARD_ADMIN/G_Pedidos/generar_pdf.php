<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../LOGIN/login.php");
    exit;
}

require_once '../clinical_core/db.php';
require_once '../Includes/fpdf/fpdf.php';

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) die("ID inválido");

// Obtener datos del pedido
$stmt = $pdo->prepare("SELECT p.*, c.razon_social, c.rfc, c.domicilio_fiscal, c.email, c.telefono_local FROM clientes_pedidos p JOIN clientes_usuarios c ON p.cliente_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$pedido = $stmt->fetch();

if (!$pedido) die("Pedido no encontrado");

// Obtener detalles
$stmt = $pdo->prepare("SELECT * FROM clientes_pedidos_detalle WHERE pedido_id = ?");
$stmt->execute([$id]);
$detalles = $stmt->fetchAll();

// Crear PDF
class PDF extends FPDF {
    function Header() {
        // Logo (Asegúrate de que la ruta sea correcta)
        if (file_exists('../../logos/MMPharma-Logotipo-Horizontal.png')) {
            $this->Image('../../logos/MMPharma-Logotipo-Horizontal.png', 10, 10, 50);
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
$pdf->Cell(25, 5, 'Nivel:', 0, 0);
$pdf->Cell(75, 5, mb_convert_encoding($pedido['tipo_cliente'], 'ISO-8859-1', 'UTF-8'), 0, 1);

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
    $pdf->Cell(15, 8, $det['cantidad'], 1, 0, 'C', $fill);
    
    // Truncar nombre si es muy largo
    $nombre = mb_convert_encoding($det['nombre_producto'], 'ISO-8859-1', 'UTF-8');
    if (strlen($nombre) > 55) {
        $nombre = substr($nombre, 0, 52) . '...';
    }
    
    $pdf->Cell(105, 8, $nombre, 1, 0, 'L', $fill);
    $pdf->Cell(35, 8, '$' . number_format($det['precio_unitario'], 2), 1, 0, 'R', $fill);
    $pdf->Cell(35, 8, '$' . number_format($det['subtotal'], 2), 1, 1, 'R', $fill);
    $fill = !$fill;
}

// Totales
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(120, 8, '', 0, 0);
$pdf->Cell(35, 8, 'TOTAL:', 1, 0, 'R');
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(35, 8, '$' . number_format($pedido['monto_total'], 2), 1, 1, 'R');

$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, mb_convert_encoding("ESTE DOCUMENTO ES UNA COTIZACIÓN INFORMATIVA. LOS PRECIOS Y DISPONIBILIDAD ESTÁN SUJETOS A CAMBIOS SIN PREVIO AVISO HASTA QUE SE CONFIRME LA DISPONIBILIDAD EN ALMACÉN Y SE REALICE EL PAGO CORRESPONDIENTE.", 'ISO-8859-1', 'UTF-8'), 0, 'C');

$pdf->Output('I', 'Cotizacion_' . $pedido['folio'] . '.pdf');
?>
