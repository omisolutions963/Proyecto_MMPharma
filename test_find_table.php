<?php
$lines = file('c:/xampp/htdocs/Proyecto_MMPharma/mm_pharma_V2.sql');
foreach ($lines as $i => $line) {
    if (strpos($line, 'CREATE TABLE `clientes_documentos`') !== false) {
        echo "Found at line " . ($i + 1) . "\n";
        for ($j = $i; $j < $i + 20; $j++) {
            echo ($j + 1) . ": " . $lines[$j];
        }
        break;
    }
}
?>
