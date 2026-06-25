<?php
/**
 * API de Sincronización de Inventarios y Precios (Hostinger / Local)
 * Recibe actualizaciones masivas desde CONTPAQi local.
 */

// 1. Inclusión de Base de Datos y Conexión (PDO)
include_once '../includes/db.php';

// Compatibilidad para entornos locales que exponen PDO a través de la función getDB()
if (!isset($conn) && function_exists('getDB')) {
    $conn = getDB();
}

// Asegurar que exista una conexión activa de PDO
if (!isset($conn) || !($conn instanceof PDO)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed.'
    ]);
    exit;
}

// 2. Capa de Validación y Seguridad de la API
// Filtro de método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Method Not Allowed. Only POST requests are accepted.'
    ]);
    exit;
}

// Autenticación por Token Bearer
$headers = function_exists('getallheaders') ? getallheaders() : [];
$authHeader = null;

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
} elseif (isset($headers['authorization'])) {
    $authHeader = $headers['authorization'];
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
}

$token = null;
if ($authHeader && preg_match('/Bearer\s+(.*)$/i', trim($authHeader), $matches)) {
    $token = $matches[1];
}

// Validar exactamente el token estático asignado
if (!$token || $token !== 'MMPharma_Super_Secret_Token_2026') {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized. Invalid or missing authentication token.'
    ]);
    exit;
}

// 3. Captura y Estructura del JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON payload. Malformed syntax.'
    ]);
    exit;
}

if (!isset($data['productos']) || !is_array($data['productos'])) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing or invalid "productos" node structure.'
    ]);
    exit;
}

// 4. Lógica de Actualización Optimizada (Prepared Statements)
// Preparamos la consulta una sola vez fuera del ciclo
try {
    $query = "UPDATE catalogo_productos 
              SET orden = :existencia, 
                  precio_empresa = :precio_empresa, 
                  precio_farmacia = :precio_farmacia, 
                  precio_distribuidor = :precio_distribuidor, 
                  precio_red_fria = :precio_red_fria 
              WHERE codigo = :codigo";
    $stmt = $conn->prepare($query);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to prepare database statement: ' . $e->getMessage()
    ]);
    exit;
}

// 5. Ciclo de Procesamiento, Métricas y Respuesta
$total_recibidos = count($data['productos']);
$productos_actualizados = 0;
$errores = 0;

foreach ($data['productos'] as $prod) {
    // Sanitizar y tipificar valores de entrada
    $codigo = isset($prod['codigo']) ? trim((string)$prod['codigo']) : '';
    $existencia = isset($prod['existencia']) ? (int)$prod['existencia'] : 0;
    $precio_empresa = isset($prod['precio_empresa']) ? (float)$prod['precio_empresa'] : 0.00;
    $precio_farmacia = isset($prod['precio_farmacia']) ? (float)$prod['precio_farmacia'] : 0.00;
    $precio_distribuidor = isset($prod['precio_distribuidor']) ? (float)$prod['precio_distribuidor'] : 0.00;
    $precio_red_fria = isset($prod['precio_red_fria']) ? (float)$prod['precio_red_fria'] : 0.00;

    // Validación básica del elemento
    if (empty($codigo)) {
        $errores++;
        continue;
    }

    try {
        $exito = $stmt->execute([
            ':existencia' => $existencia,
            ':precio_empresa' => $precio_empresa,
            ':precio_farmacia' => $precio_farmacia,
            ':precio_distribuidor' => $precio_distribuidor,
            ':precio_red_fria' => $precio_red_fria,
            ':codigo' => $codigo
        ]);

        if ($exito) {
            // El contador incrementa solo si afectó filas (existencia/precios cambiaron)
            if ($stmt->rowCount() > 0) {
                $productos_actualizados++;
            }
        } else {
            $errores++;
        }
    } catch (PDOException $e) {
        $errores++;
        // Registrar error en log para depuración sin interrumpir el flujo masivo
        error_log("Error al sincronizar producto código $codigo: " . $e->getMessage());
    }
}

// Responder estatus de éxito
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'success',
    'total_recibidos' => $total_recibidos,
    'productos_actualizados' => $productos_actualizados,
    'errores' => $errores
]);
exit;
