<?php
session_start();
require_once '../INCLUDES/db.php';

$error_login = false;
$error_msg = "Correo o contraseña incorrectos.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 $email = trim($_POST['email'] ?? '');
 $password = trim($_POST['password'] ?? '');

 if ($email && $password) {
 try {
 $pdo = getDB();
 
 // 1. Intentar como ADMINISTRADOR
 $stmt_admin = $pdo->prepare("SELECT id, nombre, password_hash, activo, foto_perfil FROM admin_usuarios WHERE email = ? LIMIT 1");
 $stmt_admin->execute([$email]);
 $admin = $stmt_admin->fetch();

 if ($admin && password_verify($password, $admin['password_hash'])) {
 if ($admin['activo']) {
 $_SESSION['admin_logged_in'] = true;
 $_SESSION['admin_id'] = $admin['id'];
 $_SESSION['admin_email'] = $email;
 $_SESSION['admin_nombre'] = $admin['nombre'];
 $_SESSION['admin_foto'] = $admin['foto_perfil'];
 header("Location: ../INDEX/index.php");
 exit;
 } else {
 $error_login = true;
 $error_msg = "Tu cuenta de administrador está inactiva.";
 }
 } 
 
 // 2. Intentar como CLIENTE (si no se encontró admin o falló el password de admin)
 if (!$error_login) {
 $stmt_cli = $pdo->prepare("SELECT id, razon_social, password_hash, estatus, tipo, foto_perfil FROM clientes_usuarios WHERE email = ? LIMIT 1");
 $stmt_cli->execute([$email]);
 $cliente = $stmt_cli->fetch();

 if ($cliente && password_verify($password, $cliente['password_hash'])) {
 if ($cliente['estatus'] === 'ACTIVO') {
 $_SESSION['cliente_logged_in'] = true;
 $_SESSION['cliente_id'] = $cliente['id'];
 $_SESSION['cliente_email'] = $email;
 $_SESSION['cliente_nombre'] = $cliente['razon_social'];
 $_SESSION['cliente_tipo'] = $cliente['tipo'];
 $_SESSION['cliente_foto'] = $cliente['foto_perfil'];
 
 header("Location: ../INDEX/index.php");
 exit;
 } else {
 $error_login = true;
 $error_msg = "Tu cuenta aún no ha sido activada o está suspendida.";
 }
 }
 }

 // 3. Credenciales de emergencia (Admin)
 if (!$error_login && $email === 'omi.mendivil@gmail.com' && $password === 'MMPharma2024!') {
 $_SESSION['admin_logged_in'] = true;
 $_SESSION['admin_email'] = $email;
 $_SESSION['admin_nombre'] = 'Omar Alexis Alquicires Mendivil';
 header("Location: ../INDEX/index.php");
 exit;
 }

 $error_login = true;

 } catch (Exception $e) {
 $error_login = true;
 $error_msg = "Error de conexión al servidor.";
 }
 } else {
 $error_login = true;
 }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Iniciar sesión — MMPharma</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="../logos/MMPharma-Isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
 body { font-family: 'Inter', sans-serif; min-height: 100vh; margin:0; }
 @media (min-width: 1024px) {
  body { overflow: hidden; height: 100vh; }
 }
 .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
 .glass-panel { background: rgba(17, 34, 64, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
</style>
<script id="tailwind-config">
 tailwind.config = {
 darkMode: "class",
 theme: {
 extend: {
 colors: {
 "primary": "#003e79",
 "secondary": "#1e60aa",
 "tertiary": "#2ca1b5",
 "primary-light": "#60a5fa",
 "secondary-light": "#93c5fd",
 "tertiary-light": "#67e8f9",
 "background": "#0a192f",
 "surface": "#112240",
 "surface-container-low": "#1a365d",
 "surface-container": "#2a4365",
 "surface-container-high": "#2c5282",
 },
 fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] },
 },
 },
 }
</script>
</head>
<body class="bg-background text-slate-300 antialiased">

<div class="flex flex-col lg:flex-row min-h-screen lg:h-screen w-screen overflow-y-auto lg:overflow-hidden">
 <!-- Panel izquierdo: Branding -->
 <div class="hidden lg:flex w-1/2 bg-surface flex-col justify-between p-16 relative overflow-hidden" data-aos="fade-right">
 <!-- Background Image Overlay -->
 <div class="absolute inset-0 z-0 overflow-hidden">
 <img src="../IMG/33.webp" class="absolute inset-0 w-full h-full object-cover object-[center_60%] transform scale-125">
 <div class="absolute inset-0 bg-background/80"></div>
 </div>

 <div class="relative z-10">
 <a href="../INDEX/index.php">
 <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-10 object-contain hover:scale-105 transition-transform duration-300">
 </a>
 </div>
 <div class="relative z-10 mt-20">
 <h1 class="text-5xl lg:text-6xl font-black text-white leading-tight mb-6 tracking-tight">Bienvenido a<br><span class="text-tertiary">MMPharma</span></h1>
 <p class="text-slate-300 text-xl leading-relaxed max-w-lg font-medium">
 Accede al portal unificado para gestionar tu cuenta, explorar nuestro catálogo completo y administrar tus pedidos con la mayor precisión clínica.
 </p>
 </div>
 
 <div class="relative z-10 flex flex-col gap-6 mt-auto">
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 rounded-2xl bg-primary/20 flex items-center justify-center border border-primary/30">
 <span class="material-symbols-outlined text-tertiary text-2xl">security</span>
 </div>
 <p class="text-slate-300 text-sm font-bold tracking-wide">Plataforma segura y encriptada</p>
 </div>
 <div class="flex items-center gap-4">
 <div class="w-12 h-12 rounded-2xl bg-primary/20 flex items-center justify-center border border-primary/30">
 <span class="material-symbols-outlined text-tertiary text-2xl">local_shipping</span>
 </div>
 <p class="text-slate-300 text-sm font-bold tracking-wide">Envíos rápidos a todo el país</p>
 </div>
 </div>
 </div>

 <!-- Panel derecho: Formulario -->
 <div class="flex-1 flex flex-col items-center justify-start lg:justify-center px-6 pt-12 pb-12 sm:p-8 bg-background relative overflow-y-auto w-full min-h-screen lg:min-h-0">
  <!-- Logo para Móvil (Oculto en Desktop) -->
  <div class="lg:hidden mb-8 mt-2 z-10" data-aos="fade-down">
   <a href="../INDEX/index.php">
    <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-10 object-contain hover:scale-105 transition-transform duration-300">
   </a>
  </div>

  <div class="w-full max-w-md relative z-10 bg-tertiary px-6 py-8 sm:p-10 rounded-3xl" data-aos="fade-left">
   <div class="flex items-center gap-4 mb-6 sm:mb-10">
 <a href="../INDEX/index.php" class="w-12 h-12 flex items-center justify-center bg-white/20 hover:bg-white/30 text-white rounded-2xl transition-all group">
 <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
 </a>
 <div>
 <h2 class="text-3xl font-black text-white tracking-tight">Iniciar sesión</h2>
 <p class="text-white text-sm mt-1 font-medium">Ingresa para acceder a tu panel.</p>
 </div>
 </div>

  <?php if ($error_login): ?>
  <div class="mb-6 sm:mb-8 flex items-center gap-3 bg-white border border-red-200 text-red-600 px-5 py-4 rounded-2xl text-sm font-bold ">
  <span class="material-symbols-outlined text-red-500 text-xl">error</span>
  <?= htmlspecialchars($error_msg) ?>
  </div>
  <?php endif; ?>

  <form method="POST" class="space-y-5 sm:space-y-6">
 <div>
 <label class="block text-xs font-black text-white uppercase tracking-widest mb-3">Correo electrónico</label>
 <div class="relative group">
 <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-surface/50 group-focus-within:text-primary transition-colors">mail</span>
 <input type="email" name="email" required autocomplete="email"
 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
 placeholder="tu@correo.com"
 class="w-full pl-14 pr-5 py-4 bg-white border border-transparent rounded-2xl text-surface text-base focus:ring-2 focus:ring-surface outline-none transition-all placeholder:text-surface/40 ">
 </div>
 </div>
 <div>
 <div class="flex justify-between items-end mb-3">
 <label class="block text-xs font-black text-white uppercase tracking-widest">Contraseña</label>
 </div>
 <div class="relative group">
 <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-surface/50 group-focus-within:text-primary transition-colors">lock</span>
 <input type="password" name="password" id="passwordInput" required autocomplete="current-password"
 placeholder="••••••••"
 class="w-full pl-14 pr-14 py-4 bg-white border border-transparent rounded-2xl text-surface text-base focus:ring-2 focus:ring-surface outline-none transition-all placeholder:text-surface/40 ">
 <button type="button" onclick="togglePass()" class="absolute right-5 top-1/2 -translate-y-1/2 text-surface/50 hover:text-primary transition-colors focus:outline-none">
 <span class="material-symbols-outlined text-xl" id="eyeIcon">visibility</span>
 </button>
 </div>
 </div>
 
  <div class="pt-4 sm:pt-6">
 <button type="submit"
  class="w-full h-14 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">
 <span class="tracking-wide text-lg">Entrar al portal</span>
 <span class="material-symbols-outlined text-[22px] group-hover:translate-x-1 transition-transform">login</span>
 </button>
 </div>
 </form>

  <div class="mt-8 pt-6 sm:mt-10 sm:pt-8 border-t border-white/20 text-center">
  <p class="text-sm text-white mb-4 font-medium">¿Aún no tienes cuenta?</p>
  <a href="../SELECCIÓN_REGISTRO/selección_registro.php" class="inline-flex items-center gap-2 text-white font-bold hover:text-white/80 transition-colors group">
  Solicitar acceso al portal <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
  </a>
  </div>

  <p class="text-center text-xs text-white mt-8 sm:mt-12 font-medium">
  © <?= date('Y') ?> MMPharma. Todos los derechos reservados.
  </p>
 </div>
 </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
 AOS.init({ duration: 800, once: true });
 function togglePass() {
 const inp = document.getElementById('passwordInput');
 const icon = document.getElementById('eyeIcon');
 if (inp.type === 'password') { inp.type = 'text'; icon.textContent = 'visibility_off'; }
 else { inp.type = 'password'; icon.textContent = 'visibility'; }
 }
</script>
</body>
</html>
