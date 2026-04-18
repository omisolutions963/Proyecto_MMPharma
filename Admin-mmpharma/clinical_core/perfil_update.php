<?php
/**
 * MMPharma — Actualización de perfil del administrador
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'msg'=>'No autenticado']);
    exit;
}

$pdo    = getDB();
$adminId = (int)($_SESSION['admin_id'] ?? 0);

// Si no tenemos ID en sesión, lo buscamos por email
if (!$adminId && !empty($_SESSION['admin_email'])) {
    $row = $pdo->prepare("SELECT id FROM administradores WHERE email=?");
    $row->execute([$_SESSION['admin_email']]);
    $adminId = (int)($row->fetchColumn() ?: 0);
    $_SESSION['admin_id'] = $adminId;
}

if (!$adminId) {
    echo json_encode(['ok'=>false,'msg'=>'Admin no encontrado']); exit;
}

$action = $_POST['action'] ?? '';

// ── 1. ACTUALIZAR DATOS PERSONALES ────────────────────────────────────────────
if ($action === 'update_perfil') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = trim($_POST['email']  ?? '');

    if (!$nombre || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok'=>false,'msg'=>'Datos inválidos']); exit;
    }

    // Verificar email único
    $dup = $pdo->prepare("SELECT id FROM administradores WHERE email=? AND id<>?");
    $dup->execute([$email, $adminId]);
    if ($dup->fetchColumn()) {
        echo json_encode(['ok'=>false,'msg'=>'Ese email ya está en uso']); exit;
    }

    $pdo->prepare("UPDATE administradores SET nombre=?, email=? WHERE id=?")
        ->execute([$nombre, $email, $adminId]);

    $_SESSION['admin_nombre'] = $nombre;
    $_SESSION['admin_email']  = $email;

    echo json_encode(['ok'=>true,'msg'=>'Datos actualizados correctamente','nombre'=>$nombre]);
    exit;
}

// ── 2. CAMBIAR CONTRASEÑA ─────────────────────────────────────────────────────
if ($action === 'change_password') {
    $actual  = $_POST['actual']   ?? '';
    $nueva   = $_POST['nueva']    ?? '';
    $confirm = $_POST['confirmar']?? '';

    if (strlen($nueva) < 8) {
        echo json_encode(['ok'=>false,'msg'=>'La contraseña debe tener al menos 8 caracteres']); exit;
    }
    if ($nueva !== $confirm) {
        echo json_encode(['ok'=>false,'msg'=>'Las contraseñas no coinciden']); exit;
    }

    $row = $pdo->prepare("SELECT password_hash FROM administradores WHERE id=?");
    $row->execute([$adminId]);
    $hash = $row->fetchColumn();

    if (!password_verify($actual, $hash)) {
        echo json_encode(['ok'=>false,'msg'=>'La contraseña actual es incorrecta']); exit;
    }

    $newHash = password_hash($nueva, PASSWORD_BCRYPT);
    $pdo->prepare("UPDATE administradores SET password_hash=? WHERE id=?")
        ->execute([$newHash, $adminId]);

    echo json_encode(['ok'=>true,'msg'=>'Contraseña actualizada correctamente']);
    exit;
}

// ── 3. SUBIR FOTO DE PERFIL ───────────────────────────────────────────────────
if ($action === 'upload_foto') {
    if (empty($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok'=>false,'msg'=>'No se recibió ningún archivo']); exit;
    }

    $file     = $_FILES['foto'];
    $maxSize  = 5 * 1024 * 1024; // 5 MB
    $allowed  = ['image/jpeg','image/png','image/webp'];
    $mimeType = mime_content_type($file['tmp_name']);

    if ($file['size'] > $maxSize) {
        echo json_encode(['ok'=>false,'msg'=>'La imagen excede 5 MB']); exit;
    }
    if (!in_array($mimeType, $allowed)) {
        echo json_encode(['ok'=>false,'msg'=>'Solo se permiten JPG, PNG o WEBP']); exit;
    }

    $ext      = match($mimeType) {
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => 'jpg',
    };
    $filename = 'admin_' . $adminId . '_' . time() . '.' . $ext;
    $uploadDir= __DIR__ . '/../uploads/perfiles/';
    $destPath = $uploadDir . $filename;

    // Borrar foto anterior
    $oldRow = $pdo->prepare("SELECT foto_perfil FROM administradores WHERE id=?");
    $oldRow->execute([$adminId]);
    $oldFoto = $oldRow->fetchColumn();
    if ($oldFoto && file_exists($uploadDir . basename($oldFoto))) {
        @unlink($uploadDir . basename($oldFoto));
    }

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        echo json_encode(['ok'=>false,'msg'=>'Error al guardar la imagen']); exit;
    }

    $fotoUrl = '../uploads/perfiles/' . $filename;
    $pdo->prepare("UPDATE administradores SET foto_perfil=? WHERE id=?")
        ->execute([$fotoUrl, $adminId]);

    $_SESSION['admin_foto'] = $fotoUrl;
    echo json_encode(['ok'=>true,'msg'=>'Foto actualizada','url'=>$fotoUrl]);
    exit;
}

echo json_encode(['ok'=>false,'msg'=>'Acción desconocida']);
