<?php
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_nombre'] = 'Admin Test';
$_SESSION['admin_email'] = 'admin@mmpharma.com';
$_SESSION['admin_foto'] = '';
header('Location: /Proyecto_MMPharma/dashboard_admin/dashboard/dashboard.php');
exit;
