<?php
$ruta = '../../img/productos/test2.txt';
if (file_put_contents($ruta, 'test') !== false) {
    echo "SUCCESS";
} else {
    echo "FAILED";
}
