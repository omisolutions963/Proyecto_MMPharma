<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';
$pdo = getDB();

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No session']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $cliente_id = $_SESSION['cliente_id'];
    $tipo_documento = $_POST['tipo_documento'] ?? '';

    if (!$tipo_documento) {
        echo json_encode(['status' => 'error', 'message' => 'Falta el tipo de documento']);
        exit;
    }

    if ($action === 'upload') {
        if (!isset($_FILES['documento'])) {
            echo json_encode(['status' => 'error', 'message' => 'Archivo no recibido']);
            exit;
        }

        $file = $_FILES['documento'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($ext, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Tipo de archivo no permitido']);
            exit;
        }

        $filename = "DOC_" . $cliente_id . "_" . $tipo_documento . "_" . time() . "." . $ext;
        $upload_dir = "../uploads/documentos_clientes/";
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            $ruta_archivo = "uploads/documentos_clientes/" . $filename;
            
            // Check if exists
            $stmt = $pdo->prepare("SELECT id, ruta_archivo FROM clientes_documentos WHERE cliente_id = ? AND tipo_documento = ?");
            $stmt->execute([$cliente_id, $tipo_documento]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Delete old file
                if (file_exists("../" . $existing['ruta_archivo'])) {
                    @unlink("../" . $existing['ruta_archivo']);
                }
                // Update
                $stmt = $pdo->prepare("UPDATE clientes_documentos SET ruta_archivo = ?, estatus_validacion = 'PENDIENTE', fecha_subida = NOW() WHERE id = ?");
                $stmt->execute([$ruta_archivo, $existing['id']]);
            } else {
                // Insert
                $stmt = $pdo->prepare("INSERT INTO clientes_documentos (cliente_id, tipo_documento, ruta_archivo, estatus_validacion, fecha_subida) VALUES (?, ?, ?, 'PENDIENTE', NOW())");
                $stmt->execute([$cliente_id, $tipo_documento, $ruta_archivo]);
            }

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar el archivo en el servidor']);
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("SELECT id, ruta_archivo FROM clientes_documentos WHERE cliente_id = ? AND tipo_documento = ?");
        $stmt->execute([$cliente_id, $tipo_documento]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if (file_exists("../" . $existing['ruta_archivo'])) {
                @unlink("../" . $existing['ruta_archivo']);
            }
            $stmt = $pdo->prepare("DELETE FROM clientes_documentos WHERE id = ?");
            $stmt->execute([$existing['id']]);
        }

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción inválida']);
    }
}
?>
