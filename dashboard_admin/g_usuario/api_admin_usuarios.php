<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No autorizado']);
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

header('Content-Type: application/json; charset=utf-8');

$action      = $_POST['action'] ?? '';
$admin_id_yo = (int)($_SESSION['admin_id'] ?? 0);

// ─── Guardar configuración general ───────────────────────────────────────────
if ($action === 'guardar_config') {
    $campos = [
        'empresa_nombre',
        'empresa_rfc',
        'empresa_sede',
        'empresa_direccion',
        'empresa_telefono',
        'empresa_email',
    ];

    $sql = "INSERT INTO admin_configuracion (clave, valor, descripcion)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE valor = VALUES(valor), updated_at = CURRENT_TIMESTAMP";

    $descripciones = [
        'empresa_nombre'    => 'Nombre o razón social de la empresa',
        'empresa_rfc'       => 'RFC fiscal de la empresa',
        'empresa_sede'      => 'Ciudad sede principal',
        'empresa_direccion' => 'Dirección fiscal completa',
        'empresa_telefono'  => 'Teléfono de contacto',
        'empresa_email'     => 'Email principal de contacto',
    ];

    $stmt = $pdo->prepare($sql);
    foreach ($campos as $clave) {
        $valor = trim($_POST[$clave] ?? '');
        $stmt->execute([$clave, $valor, $descripciones[$clave]]);
    }

    echo json_encode(['ok' => true, 'msg' => 'Configuración guardada correctamente.']);
    exit;
}

// ─── Crear usuario admin ──────────────────────────────────────────────────────
if ($action === 'crear') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = strtolower(trim($_POST['email']    ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol      = in_array($_POST['rol'] ?? '', ['ADMIN', 'EMPLEADO']) ? $_POST['rol'] : 'EMPLEADO';

    if (!$nombre || !$email || !$password) {
        echo json_encode(['ok' => false, 'error' => 'Nombre, email y contraseña son obligatorios.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok' => false, 'error' => 'El email no es válido.']);
        exit;
    }
    if (strlen($password) < 8) {
        echo json_encode(['ok' => false, 'error' => 'La contraseña debe tener al menos 8 caracteres.']);
        exit;
    }

    // Verificar email único
    $check = $pdo->prepare("SELECT id FROM admin_usuarios WHERE email = ? LIMIT 1");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Ya existe un usuario con ese email.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare(
        "INSERT INTO admin_usuarios (nombre, email, password_hash, telefono, activo)
         VALUES (?, ?, ?, ?, 1)"
    );
    $stmt->execute([$nombre, $email, $hash, $telefono ?: null]);

    echo json_encode(['ok' => true, 'msg' => 'Usuario creado correctamente.', 'id' => (int)$pdo->lastInsertId()]);
    exit;
}

// ─── Editar usuario admin ─────────────────────────────────────────────────────
if ($action === 'editar') {
    $id       = (int)($_POST['id'] ?? 0);
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = strtolower(trim($_POST['email']    ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');

    if (!$id || !$nombre || !$email) {
        echo json_encode(['ok' => false, 'error' => 'Datos incompletos.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['ok' => false, 'error' => 'El email no es válido.']);
        exit;
    }

    // Verificar email único (excluyendo el propio usuario)
    $check = $pdo->prepare("SELECT id FROM admin_usuarios WHERE email = ? AND id != ? LIMIT 1");
    $check->execute([$email, $id]);
    if ($check->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Ese email ya está en uso por otro usuario.']);
        exit;
    }

    $stmt = $pdo->prepare(
        "UPDATE admin_usuarios SET nombre = ?, email = ?, telefono = ? WHERE id = ?"
    );
    $stmt->execute([$nombre, $email, $telefono ?: null, $id]);

    // Si edito mi propio nombre, actualizo la sesión
    if ($id === $admin_id_yo) {
        $_SESSION['admin_nombre'] = $nombre;
        $_SESSION['admin_email']  = $email;
    }

    echo json_encode(['ok' => true, 'msg' => 'Usuario actualizado correctamente.']);
    exit;
}

// ─── Toggle activo/inactivo ───────────────────────────────────────────────────
if ($action === 'toggle_activo') {
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['ok' => false, 'error' => 'ID inválido.']);
        exit;
    }
    if ($id === $admin_id_yo) {
        echo json_encode(['ok' => false, 'error' => 'No puedes deshabilitarte a ti mismo.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE admin_usuarios SET activo = NOT activo WHERE id = ?");
    $stmt->execute([$id]);

    // Obtener nuevo estado
    $nuevo = $pdo->prepare("SELECT activo FROM admin_usuarios WHERE id = ?");
    $nuevo->execute([$id]);
    $row = $nuevo->fetch();

    echo json_encode(['ok' => true, 'activo' => (bool)$row['activo']]);
    exit;
}

// ─── Eliminar usuario admin ───────────────────────────────────────────────────
if ($action === 'eliminar') {
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['ok' => false, 'error' => 'ID inválido.']);
        exit;
    }
    if ($id === $admin_id_yo) {
        echo json_encode(['ok' => false, 'error' => 'No puedes eliminar tu propia cuenta.']);
        exit;
    }

    $pdo->prepare("DELETE FROM admin_usuarios WHERE id = ?")->execute([$id]);
    echo json_encode(['ok' => true, 'msg' => 'Usuario eliminado.']);
    exit;
}

// ─── Cambiar contraseña ───────────────────────────────────────────────────────
if ($action === 'cambiar_password') {
    $id       = (int)($_POST['id'] ?? 0);
    $password = $_POST['password'] ?? '';

    if (!$id || strlen($password) < 8) {
        echo json_encode(['ok' => false, 'error' => 'La contraseña debe tener al menos 8 caracteres.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $pdo->prepare("UPDATE admin_usuarios SET password_hash = ? WHERE id = ?")->execute([$hash, $id]);

    echo json_encode(['ok' => true, 'msg' => 'Contraseña actualizada.']);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'Acción no reconocida.']);
