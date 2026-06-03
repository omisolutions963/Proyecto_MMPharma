<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_info') {
        $razon_social = trim($_POST['razon_social'] ?? '');
        $rfc = trim($_POST['rfc'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono_celular = trim($_POST['telefono_celular'] ?? '');
        $regimen_fiscal = trim($_POST['regimen_fiscal'] ?? '');
        $documento_tipo = $_POST['documento_tipo'] ?? 'FACTURA';
        $metodo_pago = $_POST['metodo_pago'] ?? 'TRANSFERENCIA';
        $uso_cfdi = trim($_POST['uso_cfdi'] ?? '');

        if (!$razon_social || !$email || !$rfc) {
            echo json_encode(['status' => 'error', 'message' => 'Razón social, RFC y Email son obligatorios.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE clientes_usuarios SET razon_social = ?, rfc = ?, email = ?, telefono_celular = ?, regimen_fiscal = ?, documento_tipo = ?, metodo_pago = ?, uso_cfdi = ? WHERE id = ?");
            $stmt->execute([$razon_social, $rfc, $email, $telefono_celular, $regimen_fiscal, $documento_tipo, $metodo_pago, $uso_cfdi, $cliente_id]);
            
            // Actualizar sesión
            $_SESSION['cliente_nombre'] = $razon_social;
            $_SESSION['cliente_email'] = $email;

            echo json_encode(['status' => 'success', 'message' => 'Información actualizada correctamente.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar información: ' . $e->getMessage()]);
        }

    } elseif ($action === 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        if (!$current_password || !$new_password) {
            echo json_encode(['status' => 'error', 'message' => 'Ambas contraseñas son requeridas.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT password_hash FROM clientes_usuarios WHERE id = ?");
            $stmt->execute([$cliente_id]);
            $user = $stmt->fetch();

            if ($user && password_verify($current_password, $user['password_hash'])) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_upd = $pdo->prepare("UPDATE clientes_usuarios SET password_hash = ? WHERE id = ?");
                $stmt_upd->execute([$new_hash, $cliente_id]);
                echo json_encode(['status' => 'success', 'message' => 'Contraseña actualizada correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'La contraseña actual es incorrecta.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar contraseña.']);
        }

    } elseif ($action === 'update_avatar') {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibió ninguna imagen válida.']);
            exit;
        }

        $file = $_FILES['avatar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($ext, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Solo se permiten imágenes (jpg, png, webp).']);
            exit;
        }

        // Limit to 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'La imagen no debe superar los 5MB.']);
            exit;
        }

        $upload_dir = '../uploads/perfiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = 'cliente_' . $cliente_id . '_' . time() . '.' . $ext;
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $db_path = '../uploads/perfiles/' . $filename;

            try {
                // Delete old avatar
                $stmt = $pdo->prepare("SELECT foto_perfil FROM clientes_usuarios WHERE id = ?");
                $stmt->execute([$cliente_id]);
                $old = $stmt->fetch();
                if ($old && $old['foto_perfil']) {
                    if (file_exists($old['foto_perfil'])) {
                        @unlink($old['foto_perfil']);
                    }
                }

                $stmt_upd = $pdo->prepare("UPDATE clientes_usuarios SET foto_perfil = ? WHERE id = ?");
                $stmt_upd->execute([$db_path, $cliente_id]);
                $_SESSION['cliente_foto'] = $db_path;

                echo json_encode(['status' => 'success', 'ruta' => $db_path]);
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar la ruta de la imagen en base de datos.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al subir el archivo al servidor.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción inválida.']);
    }
}
?>
