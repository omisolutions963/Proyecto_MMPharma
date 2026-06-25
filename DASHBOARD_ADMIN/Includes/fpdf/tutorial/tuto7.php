<?php
require('../fpdf.php');

$pdf = new FPDF();
$pdf->AddFont('CevicheOne','','cevicheone-regular.php','.');
$pdf->AddPage();
$pdf->SetFont('CevicheOne','',45);
$pdf->Write(10,'Enjoy new fonts with FPDF!');
$pdf->Output();
?>
