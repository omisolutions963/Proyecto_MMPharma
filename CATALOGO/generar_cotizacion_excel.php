<?php
session_start();
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    die("No autorizado");
}

require_once '../includes/db.php';
require_once '../includes/shipping_calculator.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['carrito_data'])) {
    die("Datos del carrito vacíos o método inválido.");
}
$carrito = json_decode($_POST['carrito_data'], true);
if (!$carrito || !is_array($carrito)) die("Datos del carrito inválidos.");

// ── Datos del cliente ──────────────────────────────────────────────────────
$cliente_id   = $_SESSION['cliente_id'];
$tipo_cliente = $_SESSION['cliente_tipo'] ?? 'FARMACIA';

$stmt = $pdo->prepare("SELECT razon_social, rfc, email, telefono_local FROM clientes_usuarios WHERE id = ?");
$stmt->execute([$cliente_id]);
$cliente = $stmt->fetch();
if (!$cliente) die("Cliente no encontrado.");

// ── Folio y vigencia ───────────────────────────────────────────────────────
$folio = 'COT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));

function diasHabilesXlsx(DateTime $d, int $n): DateTime {
    $c = 0;
    while ($c < $n) { $d->modify('+1 day'); if ((int)$d->format('N') < 6) $c++; }
    return $d;
}
$hoy     = new DateTime();
$vigencia = diasHabilesXlsx(clone $hoy, 10);

// ── Tipos de producto, tasas de IVA y otros detalles de la BD ──────────────
$ids = array_column($carrito, 'id');
$product_details = [];
if ($ids) {
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $pdo->prepare("SELECT id, tipo, tasa_iva, sustancia, codigo FROM catalogo_productos WHERE id IN ($ph)");
    $st->execute($ids);
    $product_details = $st->fetchAll(PDO::FETCH_UNIQUE);
}

// ── Subtotal / envío ───────────────────────────────────────────────────────
$subtotal_prod = 0;
$total_items_sin_iva = 0;
$total_items_iva = 0;
$total_normal_con_iva = 0;
$tiene_red_fria = false;

foreach ($carrito as $item) {
    $cant = (int)$item['cantidad'];
    $precio = (float)$item['precio'];
    $item_total = $cant * $precio;
    $subtotal_prod += $item_total;
    
    $tasa = isset($product_details[$item['id']]['tasa_iva']) ? (float)$product_details[$item['id']]['tasa_iva'] : 0.16;
    $tipo = isset($product_details[$item['id']]['tipo']) ? strtoupper($product_details[$item['id']]['tipo']) : 'SECO';
    
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

$costo_envio = 0.00; $msg_envio = 'Envío gratis';
$recoger     = isset($_POST['recoger_sucursal']) && $_POST['recoger_sucursal'] === '1';

if (!empty($_POST['direccion_id'])) {
    $sd = $pdo->prepare("SELECT estado, latitud, longitud FROM clientes_direcciones WHERE id = ? AND cliente_id = ?");
    $sd->execute([$_POST['direccion_id'], $cliente_id]);
    $dir = $sd->fetch(PDO::FETCH_ASSOC);
    if ($dir) {
        $lat = $dir['latitud']  !== null ? (float)$dir['latitud']  : null;
        $lng = $dir['longitud'] !== null ? (float)$dir['longitud'] : null;
        $c = calcularCostoEnvio($total_normal_con_iva, $dir['estado'], $lat, $lng, $tiene_red_fria);
        
        if ($recoger || ($total_normal_con_iva == 0 && $tiene_red_fria)) {
            $costo_envio = 0.00;
            $msg_envio = 'Recoger en sucursal';
        } else {
            $costo_envio = $c['costo'];
            $msg_envio   = $c['mensaje'];
        }
    }
} else {
    if ($tiene_red_fria && $total_normal_con_iva == 0) {
        $costo_envio = 0.00;
        $msg_envio = 'Recoger en sucursal';
    }
}

$envio_sin_iva = $costo_envio;
$envio_iva = 0;

$sin_iva = $total_items_sin_iva + $envio_sin_iva;
$iva = $total_items_iva + $envio_iva;
$total = $subtotal_prod + $total_items_iva + $costo_envio;

// ══════════════════════════════════════════════════════════════════════════
// XLSX builder (ZipArchive + OOXML)
// ══════════════════════════════════════════════════════════════════════════

// ── Helpers ────────────────────────────────────────────────────────────────
function colLetter(int $n): string {          // 1=A, 2=B …
    return chr(64 + $n);
}
function xesc(string $v): string {
    return htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

// Cell builders
// type: 's'=inlineStr, 'n'=number (default)
// style index matches cellXfs order defined in styles.xml
function cStr(int $row, int $col, string $val, int $s = 0): string {
    $r = colLetter($col) . $row;
    if ($val === '') return '<c r="' . $r . '" s="' . $s . '"/>';
    return '<c r="' . $r . '" t="inlineStr" s="' . $s . '"><is><t>' . xesc($val) . '</t></is></c>';
}
function cNum(int $row, int $col, float $val, int $s = 0): string {
    $r = colLetter($col) . $row;
    return '<c r="' . $r . '" s="' . $s . '"><v>' . $val . '</v></c>';
}

// ── Estilos (cellXfs indices) ──────────────────────────────────────────────
// 0  Normal
// 1  Título grande (bold blue)
// 2  Sección header (bold blue, izquierda)
// 3  Meta-label (bold blue, derecha)
// 4  Meta-value (normal)
// 5  Col header (bold blanco sobre azul oscuro, centrado)
// 6  Fila impar texto
// 7  Fila par  texto  (fondo azul claro)
// 8  Fila impar money (derecha)
// 9  Fila par  money  (fondo azul claro, derecha)
// 10 Total label  (bold azul, derecha, fondo claro)
// 11 Total money  (bold azul, derecha, fondo claro, money format)
// 12 Grand label  (bold blanco, derecha, azul oscuro)
// 13 Grand money  (bold blanco, derecha, azul oscuro, money format)
// 14 Nota (italic, wrap)

$styles_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <numFmts count="1">
    <numFmt numFmtId="164" formatCode="&quot;$&quot;#,##0.00"/>
  </numFmts>
  <fonts count="6">
    <font><sz val="10"/><name val="Arial"/></font>
    <font><b/><sz val="13"/><color rgb="FF002451"/><name val="Arial"/></font>
    <font><b/><sz val="10"/><color rgb="FF002451"/><name val="Arial"/></font>
    <font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Arial"/></font>
    <font><sz val="10"/><color rgb="FF333333"/><name val="Arial"/></font>
    <font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Arial"/></font>
  </fonts>
  <fills count="5">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF002451"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFE8F0FF"/><bgColor indexed="64"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFF0F5FF"/><bgColor indexed="64"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/><diagonal/></border>
    <border><left/><right/><top/><bottom style="thin"><color rgb="FFCCCCCC"/></bottom><diagonal/></border>
  </borders>
  <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
  <cellXfs count="15">
    <!-- 0 Normal -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <!-- 1 Titulo -->
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    <!-- 2 Seccion bold azul izq -->
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    <!-- 3 Meta-label bold azul der -->
    <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 4 Meta-value normal -->
    <xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/>
    <!-- 5 Col header blanco/azul centrado -->
    <xf numFmtId="0" fontId="3" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
    <!-- 6 Fila impar texto -->
    <xf numFmtId="0" fontId="4" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1"/>
    <!-- 7 Fila par texto bg claro -->
    <xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>
    <!-- 8 Fila impar money -->
    <xf numFmtId="164" fontId="4" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 9 Fila par money bg claro -->
    <xf numFmtId="164" fontId="4" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 10 Total label -->
    <xf numFmtId="0" fontId="2" fillId="4" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 11 Total money -->
    <xf numFmtId="164" fontId="2" fillId="4" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 12 Grand label blanco/azul -->
    <xf numFmtId="0" fontId="5" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 13 Grand money blanco/azul -->
    <xf numFmtId="164" fontId="5" fillId="2" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyAlignment="1"><alignment horizontal="right"/></xf>
    <!-- 14 Nota italic wrap -->
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment wrapText="1" vertical="top"/></xf>
  </cellXfs>
</styleSheet>';

// ── Sheet XML ──────────────────────────────────────────────────────────────
$rows = [];   // [rowNum => xml_string]
$merges = []; // merge refs

$r = 1; // current row

// Row 1 – Título
$rows[$r] = cStr($r, 1, 'Distribuidora de medicamentos MM — Cotización de productos', 1)
           . cStr($r, 2, '', 1) . cStr($r, 3, '', 1) . cStr($r, 4, '', 1) . cStr($r, 5, '', 1)
           . cStr($r, 6, '', 1) . cStr($r, 7, '', 1) . cStr($r, 8, '', 1);
$merges[] = 'A'.$r.':H'.$r;
$r++;

// Row 2 – blanco
$rows[$r] = ''; $r++;

// Row 3 – Encabezados secciones
$rows[$r] = cStr($r, 1, 'DATOS DEL CLIENTE', 2) . cStr($r, 2, '', 0)
          . cStr($r, 3, '', 0) . cStr($r, 4, '', 0)
          . cStr($r, 5, 'DATOS DE COTIZACIÓN', 2) . cStr($r, 6, '', 0)
          . cStr($r, 7, '', 0) . cStr($r, 8, '', 0);
$merges[] = 'A'.$r.':D'.$r;
$merges[] = 'E'.$r.':H'.$r;
$r++;

// Rows 4-7 – Metadatos
$meta = [
    ['Cliente:',  $cliente['razon_social'],        'Folio:',    $folio],
    ['RFC:',      $cliente['rfc'] ?? 'N/A',         'Fecha:',    $hoy->format('d/m/Y')],
    ['Email:',    $cliente['email'] ?? 'N/A',        'Vigencia:', 'Al '.$vigencia->format('d/m/Y').' (10 días hábiles)'],
    ['',          '',                               'Envío:',    $msg_envio],
];
foreach ($meta as $m) {
    $rows[$r] = cStr($r, 1, $m[0], 3) . cStr($r, 2, $m[1], 4)
              . cStr($r, 3, '', 0) . cStr($r, 4, '', 0)
              . cStr($r, 5, $m[2], 3) . cStr($r, 6, $m[3], 4)
              . cStr($r, 7, '', 0) . cStr($r, 8, '', 0);
    $merges[] = 'B'.$r.':D'.$r;
    $merges[] = 'F'.$r.':H'.$r;
    $r++;
}

// Row 8 – blanco
$rows[$r] = ''; $r++;

// Row 9 – Encabezado tabla
$rows[$r] = cStr($r, 1, 'CANTIDAD', 5)
          . cStr($r, 2, 'CÓDIGO DE BARRAS', 5)
          . cStr($r, 3, 'DESCRIPCIÓN DEL PRODUCTO', 5)
          . cStr($r, 4, 'SUSTANCIA ACTIVA', 5)
          . cStr($r, 5, 'TIPO', 5)
          . cStr($r, 6, 'TASA IVA', 5)
          . cStr($r, 7, 'PRECIO UNITARIO', 5)
          . cStr($r, 8, 'SUBTOTAL', 5);
$r++;

// Rows 10+ – Productos
$rowIdx = 0;
foreach ($carrito as $item) {
    $rowIdx++;
    $even  = ($rowIdx % 2 === 0);
    $sTxt  = $even ? 7 : 6;
    $sMon  = $even ? 9 : 8;
    $cant  = (int)$item['cantidad'];
    $precio = (float)$item['precio'];
    $subtotalLinea = $cant * $precio;
    
    $details = $product_details[$item['id']] ?? [];
    $tipo  = $details['tipo'] ?? 'SECO';
    $tasa  = isset($details['tasa_iva']) ? (float)$details['tasa_iva'] : 0.16;
    
    $item_sin_iva = $subtotalLinea;
    $item_iva = $subtotalLinea * $tasa;
    
    $tasa_percentage = ($tasa * 100) . '% (+$' . number_format($item_iva, 2) . ')';
    $sustancia = $details['sustancia'] ?? 'No registrada';
    $barcode = $details['codigo'] ?? '';

    $nombre_con_iva = $item['nombre'] . ' (Precios más IVA)';

    $rows[$r] = cNum($r, 1, $cant, $sTxt)
              . cStr($r, 2, $barcode, $sTxt)
              . cStr($r, 3, $nombre_con_iva, $sTxt)
              . cStr($r, 4, $sustancia, $sTxt)
              . cStr($r, 5, $tipo, $sTxt)
              . cStr($r, 6, $tasa_percentage, $sTxt)
              . cNum($r, 7, $precio, $sMon)
              . cNum($r, 8, $subtotalLinea, $sMon);
    $r++;
}

// Blanco
$rows[$r] = ''; $r++;

// Totales
$totales = [
    ['Subtotal productos:', $subtotal_prod],
    [($costo_envio > 0 ? 'Costo de envío:' : 'Envío: ' . $msg_envio), $costo_envio],
    ['Subtotal (sin IVA):', $sin_iva],
    ['IVA:', $iva],
];
foreach ($totales as $t) {
    $rows[$r] = cStr($r, 1, '', 0) . cStr($r, 2, '', 0) . cStr($r, 3, '', 0)
              . cStr($r, 4, '', 0) . cStr($r, 5, '', 0) . cStr($r, 6, '', 0)
              . cStr($r, 7, $t[0], 10) . cNum($r, 8, $t[1], 11);
    $r++;
}
// TOTAL destacado
$rows[$r] = cStr($r, 1, '', 0) . cStr($r, 2, '', 0) . cStr($r, 3, '', 0)
          . cStr($r, 4, '', 0) . cStr($r, 5, '', 0) . cStr($r, 6, '', 0)
          . cStr($r, 7, 'TOTAL:', 12) . cNum($r, 8, $total, 13);
$r++;

// Blanco
$rows[$r] = ''; $r++;

// Nota legal
$nota = '* Los precios unitarios y de envío mostrados incluyen el IVA correspondiente (16%). Este documento es una cotización informativa. '
      . 'Los precios y disponibilidad están sujetos a cambios sin previo aviso. '
      . 'Vigencia: 10 días hábiles a partir de la fecha de emisión.';
      
if ($tiene_red_fria) {
    $nota .= "\n** IMPORTANTE: Los productos de Red Fría requieren recolección por parte del cliente. MM Pharma no gestiona ni cobra este envío.";
}

$rows[$r] = cStr($r, 1, $nota, 14) . cStr($r, 2, '', 14) . cStr($r, 3, '', 14)
          . cStr($r, 4, '', 14) . cStr($r, 5, '', 14) . cStr($r, 6, '', 14)
          . cStr($r, 7, '', 14) . cStr($r, 8, '', 14);
$merges[] = 'A'.$r.':H'.$r;

// ── Generar sheet XML ──────────────────────────────────────────────────────
$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<sheetFormatPr defaultRowHeight="15"/>'
    . '<cols>'
    . '<col min="1" max="1" width="10" customWidth="1"/>'
    . '<col min="2" max="2" width="20" customWidth="1"/>'
    . '<col min="3" max="3" width="52" customWidth="1"/>'
    . '<col min="4" max="4" width="25" customWidth="1"/>'
    . '<col min="5" max="5" width="15" customWidth="1"/>'
    . '<col min="6" max="6" width="12" customWidth="1"/>'
    . '<col min="7" max="7" width="18" customWidth="1"/>'
    . '<col min="8" max="8" width="18" customWidth="1"/>'
    . '</cols>'
    . '<sheetData>';

foreach ($rows as $rowNum => $cells) {
    $ht = ($rowNum === 1) ? ' ht="22" customHeight="1"' : ($rowNum === 9 ? ' ht="20" customHeight="1"' : '');
    $sheetXml .= '<row r="' . $rowNum . '"' . $ht . '>' . $cells . '</row>';
}

$sheetXml .= '</sheetData>';

if (!empty($merges)) {
    $sheetXml .= '<mergeCells count="' . count($merges) . '">';
    foreach ($merges as $m) $sheetXml .= '<mergeCell ref="' . $m . '"/>';
    $sheetXml .= '</mergeCells>';
}

$sheetXml .= '</worksheet>';

// ── Armar el ZIP (.xlsx) ───────────────────────────────────────────────────
$tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
$zip = new ZipArchive();
$zip->open($tmpFile, ZipArchive::OVERWRITE);

// [Content_Types].xml
$zip->addFromString('[Content_Types].xml',
'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml"  ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml"            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml"   ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml"              ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>');

// _rels/.rels
$zip->addFromString('_rels/.rels',
'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

// xl/workbook.xml
$zip->addFromString('xl/workbook.xml',
'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"
          xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Cotización" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

// xl/_rels/workbook.xml.rels
$zip->addFromString('xl/_rels/workbook.xml.rels',
'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"    Target="styles.xml"/>
</Relationships>');

$zip->addFromString('xl/styles.xml',             $styles_xml);
$zip->addFromString('xl/worksheets/sheet1.xml',  $sheetXml);
$zip->close();

// ── Enviar al navegador ────────────────────────────────────────────────────
$filename = 'Cotizacion_' . $folio . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($tmpFile);
unlink($tmpFile);
