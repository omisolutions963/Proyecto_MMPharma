<?php
session_start();
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
 header("Location: ../LOGIN/login.php");
 exit;
}

require_once '../INCLUDES/db.php';
require_once '../DASHBOARD_ADMIN/Includes/fpdf/fpdf.php'; 

$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT razon_social, rfc FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();

// Obtener todas las cotizaciones del cliente
$stmt = $pdo->prepare("
 SELECT p.folio, p.monto_total, p.estado_envio, p.created_at, COUNT(pd.id) as total_items 
 FROM clientes_pedidos p
 LEFT JOIN clientes_pedidos_detalle pd ON p.id = pd.pedido_id
 WHERE p.cliente_id = ?
 GROUP BY p.id
 ORDER BY p.created_at DESC
");
$stmt->execute([$cliente_id]);
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

class PDF extends FPDF {
 function Header() {
 if (file_exists('../logos/MMPharma-Logotipo-Horizontal.png')) {
     $this->Image('../logos/MMPharma-Logotipo-Horizontal.png', 10, 10, 50);
 }
 
 $this->SetFont('Arial', 'B', 15);
 $this->SetTextColor(0, 36, 81);
 $this->Cell(80);
 $this->Cell(110, 10, mb_convert_encoding('HISTORIAL DE COTIZACIONES', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');
 
 $this->SetFont('Arial', '', 10);
 $this->SetTextColor(100, 100, 100);
 $this->Cell(80);
 $this->Cell(110, 5, 'MMPharma Clinical Systems S.A. de C.V.', 0, 1, 'R');
 $this->Ln(10);
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

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 36, 81);
$pdf->Cell(190, 6, 'CLIENTE: ' . mb_convert_encoding($cliente['razon_social'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
if (!empty($cliente['rfc'])) {
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(190, 5, 'RFC: ' . mb_convert_encoding($cliente['rfc'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
}
$pdf->Cell(190, 5, 'Fecha de reporte: ' . date('d/m/Y'), 0, 1, 'L');
$pdf->Ln(10);

if (empty($cotizaciones)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, 'No hay cotizaciones registradas.', 0, 1, 'C');
} else {
    // Tabla
    $pdf->SetFillColor(0, 36, 81);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $pdf->Cell(35, 8, 'FOLIO', 1, 0, 'C', true);
    $pdf->Cell(35, 8, 'FECHA', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'ITEMS', 1, 0, 'C', true);
    $pdf->Cell(50, 8, 'ESTADO', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'TOTAL', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(50, 50, 50);

    $fill = false;
    $gran_total = 0;
    
    foreach ($cotizaciones as $cot) {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell(35, 8, $cot['folio'], 1, 0, 'C', $fill);
        $pdf->Cell(35, 8, date('d/m/Y', strtotime($cot['created_at'])), 1, 0, 'C', $fill);
        $pdf->Cell(25, 8, $cot['total_items'], 1, 0, 'C', $fill);
        
        $estado = mb_convert_encoding($cot['estado_envio'], 'ISO-8859-1', 'UTF-8');
        $pdf->Cell(50, 8, $estado, 1, 0, 'C', $fill);
        
        $pdf->Cell(45, 8, '$' . number_format($cot['monto_total'], 2), 1, 1, 'R', $fill);
        
        if ($cot['estado_envio'] !== 'CANCELADO') {
            $gran_total += $cot['monto_total'];
        }
        $fill = !$fill;
    }
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(145, 8, 'TOTAL (Sin Canceladas):', 1, 0, 'R');
    $pdf->SetTextColor(0, 36, 81);
    $pdf->Cell(45, 8, '$' . number_format($gran_total, 2), 1, 1, 'R');
}

$pdf->Output('I', 'Reporte_Cotizaciones.pdf');
?>
