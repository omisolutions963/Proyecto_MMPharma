<?php
$ruta = 'IMG/productos/test.txt';
if (file_put_contents($ruta, 'test') !== false) {
    echo "SUCCESS";
} else {
    echo "FAILED";
}
