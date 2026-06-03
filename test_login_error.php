<?php
$lines = file('mm_pharma_V2.sql');
$inside = false;
foreach ($lines as $line) {
    if (stripos($line, 'INSERT INTO `clientes_usuarios`') !== false) {
        $inside = true;
    }
    if ($inside) {
        echo trim($line) . "\n";
        if (strpos($line, ';') !== false) {
            $inside = false;
        }
    }
}
?>
