<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'action' => 'upsert',
    'id' => 0,
    'nombre' => 'TEST PRODUCT 2',
    'codigo' => 'TEST002',
    'tipo' => 'SECO',
    'categoria_id' => 1,
    'precio_farmacia' => 10,
    'precio_distribuidor' => 9,
    'precio_empresa' => 8,
    'stock' => 50,
    'en_promocion' => 1,
    'descuento_porcentaje' => 15,
    'foto_base64' => ''
];
require_once 'DASHBOARD_ADMIN/G_Productos/productos.php';
