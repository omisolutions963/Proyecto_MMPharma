<?php
// Detección automática del puerto de MySQL
$detected_port = '3307';
try {
    $test_dsn = 'mysql:host=127.0.0.1;port=3307;dbname=mm_pharma;charset=utf8mb4';
    $test_pdo = new PDO($test_dsn, 'root', '', [
        PDO::ATTR_TIMEOUT => 1,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    $detected_port = '3306';
}

if (!defined('DB_PORT')) {
    define('DB_PORT', $detected_port);
}

require_once '../clinical_core/db.php';
$pdo = getDB();

echo "=== INICIANDO REPARACIÓN DE CODIFICACIÓN EN BD ===\n";
echo "Conectado a puerto: " . DB_PORT . "\n\n";

// Array de correcciones exactas para los 8 productos
$corrections = [
    20 => [
        'field' => 'sustancia',
        'value' => 'ÁCIDO ACETILSALICÍLICO, ÁCIDO ASCÓRBICO, SULFADIAZINA'
    ],
    57 => [
        'field' => 'nombre',
        'value' => 'AMBIDERM GUANTES LATEX CIRUGÍA ESTERIL TALLA 7 1/2 CON 1 PAR'
    ],
    107 => [
        'field' => 'nombre',
        'value' => "BENZATINA BENCILPENICILINA 1'200,000 AMPULA DE 5 ML AMSA"
    ],
    134 => [
        'field' => 'sustancia',
        'value' => '1 ALCOHOL 60ML+1 AGUA OXIGENADA 112ML+1 MERTODOL BLANCO 40ML+1 ALGODÓN 3G+1 VENDA ELÁSTICA 5X5CM+2 GASAS 7.5X5CM+4 VENDITAS ADHESIVAS+1 CINTA DE TELA ADHESIVA SEDOSA 1.25X100CM+1 ÁRNICA GEL 60ML.'
    ],
    192 => [
        'field' => 'nombre',
        'value' => 'COLLARÍN CERVICAL BLANDO MEDIANO BLANCO'
    ],
    339 => [
        'field' => 'nombre',
        'value' => 'GUANTE NO ESTERIL LATEX EXPLORACIÓN GRANDES CAJA CON 100'
    ],
    352 => [
        'field' => 'nombre',
        'value' => 'HOJA PARA BISTURÍ ACERO INOXIDABLE (AMBIDERM) No. 24 CON 1 P'
    ],
    614 => [
        'field' => 'nombre',
        'value' => 'SINPEBAC 2% UNGÜENTO TUBO CON 15 G'
    ]
];

try {
    foreach ($corrections as $id => $info) {
        $field = $info['field'];
        $value = $info['value'];
        
        $stmt = $pdo->prepare("UPDATE catalogo_productos SET $field = ? WHERE id = ?");
        $stmt->execute([$value, $id]);
        
        echo "ID {$id}: Campo '{$field}' corregido con éxito.\n";
    }
    
    echo "\n=== REPARACIÓN DE CODIFICACIÓN COMPLETADA ===\n";
} catch (Exception $e) {
    echo "\n[ERROR] Falló la reparación: " . $e->getMessage() . "\n";
}
?>
