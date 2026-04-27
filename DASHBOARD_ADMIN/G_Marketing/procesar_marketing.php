<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../clinical_core/db.php';
$pdo = getDB();

$action = $_POST['action'] ?? '';

if ($action === 'send_notif') {
    $cliente_id = (int)$_POST['cliente_id'];
    $tipo = $_POST['tipo'] ?? 'INFO';
    $mensaje = $_POST['mensaje'] ?? '';

    if ($cliente_id === 0 || empty($mensaje)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO admin_alertas_notificaciones (cliente_id, tipo, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$cliente_id, $tipo, $mensaje]);

    echo json_encode(['status' => 'success']);
    exit;
}

if ($action === 'add_banner') {
    $titulo = $_POST['titulo'] ?? '';
    $url = $_POST['url'] ?? '';
    
    if (isset($_FILES['banner_img']) && $_FILES['banner_img']['error'] === 0) {
        $file = $_FILES['banner_img'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = "BANNER_" . time() . "." . $ext;
        $relative_path = "uploads/marketing/" . $filename;
        $upload_dir = "../../uploads/marketing/";

        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            $stmt = $pdo->prepare("INSERT INTO admin_banners_promocionales (titulo, ruta_imagen, enlace_url, activo) VALUES (?, ?, ?, 1)");
            $stmt->execute([$titulo, $relative_path, $url]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al subir imagen']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Archivo inválido']);
    }
    exit;
}

if ($action === 'del_banner') {
    $id = (int)$_POST['id'];
    $pdo->prepare("DELETE FROM admin_banners_promocionales WHERE id = ?")->execute([$id]);
    echo json_encode(['status' => 'success']);
    exit;
}
