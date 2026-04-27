<?php
session_start();
require_once '../INCLUDES/db.php';

$error_login = false;
$error_msg   = "Correo o contraseña incorrectos.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
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
                    $_SESSION['admin_id']        = $admin['id'];
                    $_SESSION['admin_email']     = $email;
                    $_SESSION['admin_nombre']    = $admin['nombre'];
                    $_SESSION['admin_foto']      = $admin['foto_perfil'];
                    header("Location: ../INDEX/index.php");
                    exit;
                } else {
                    $error_login = true;
                    $error_msg   = "Tu cuenta de administrador está inactiva.";
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
                        $_SESSION['cliente_id']        = $cliente['id'];
                        $_SESSION['cliente_email']     = $email;
                        $_SESSION['cliente_nombre']    = $cliente['razon_social'];
                        $_SESSION['cliente_tipo']      = $cliente['tipo'];
                        $_SESSION['cliente_foto']      = $cliente['foto_perfil'];
                        
                        header("Location: ../INDEX/index.php");
                        exit;
                    } else {
                        $error_login = true;
                        $error_msg   = "Tu cuenta aún no ha sido activada o está suspendida.";
                    }
                }
            }

            // 3. Credenciales de emergencia (Admin)
            if (!$error_login && $email === 'omi.mendivil@gmail.com' && $password === 'MMPharma2024!') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email']     = $email;
                $_SESSION['admin_nombre']    = 'Omar Alexis Alquicires Mendivil';
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
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Iniciar sesión — MMPharma</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="../logos/MMPharma-Isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; height:100%; margin:0; overflow:hidden; }
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: { extend: { colors: {
            "surface-container-lowest":"#ffffff","primary-fixed-dim":"#abc7ff",
            "inverse-on-surface":"#e8f2ff","inverse-surface":"#1d3246",
            "on-tertiary":"#ffffff","surface":"#f7f9ff","on-background":"#051d30",
            "on-error-container":"#93000a","on-secondary":"#ffffff",
            "inverse-primary":"#abc7ff","error":"#ba1a1a",
            "on-surface-variant":"#43474f","surface-container-high":"#d9eaff",
            "on-secondary-container":"#004d77","on-primary-container":"#89a5dd",
            "on-tertiary-container":"#39bb6c","secondary":"#006397",
            "tertiary-container":"#004520","primary":"#003e79",
            "surface-variant":"#cfe5ff","tertiary-fixed":"#7efba4",
            "error-container":"#ffdad6","surface-container-highest":"#cfe5ff",
            "on-primary":"#ffffff","on-primary-fixed":"#001b3f",
            "surface-bright":"#f7f9ff","secondary-fixed-dim":"#92ccff",
            "primary-fixed":"#d7e2ff","surface-dim":"#c6dcf6",
            "on-secondary-fixed-variant":"#004b73","tertiary-fixed-dim":"#61de8a",
            "primary-container":"#1a3a6b","outline-variant":"#c4c6d0",
            "on-primary-fixed-variant":"#284678","surface-container":"#e3efff",
            "background":"#f7f9ff","secondary-container":"#71c0fe",
            "on-secondary-fixed":"#001d31","on-surface":"#051d30",
            "on-tertiary-fixed":"#00210c","on-tertiary-fixed-variant":"#005228",
            "tertiary":"#002c13","surface-container-low":"#edf4ff",
            "secondary-fixed":"#cce5ff","surface-tint":"#415e91",
            "outline":"#747780","on-error":"#ffffff"
        }}}
    }
</script>
</head>
<body class="bg-surface text-on-surface antialiased">

<div class="flex h-screen w-screen overflow-hidden">
    <!-- Panel izquierdo: Branding -->
    <div class="hidden lg:flex w-1/2 bg-[#003e79] flex-col justify-between p-16 relative overflow-hidden" data-aos="fade-right">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="../IMG/37.webp" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/95 via-primary/80 to-primary/60"></div>
        </div>

        <div class="relative z-10">
            <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-12 object-contain">
        </div>
        <div class="relative z-10">
            <h1 class="text-6xl font-black text-white leading-tight mb-6">Bienvenido a<br><span class="text-blue-300">MMPharma</span></h1>
            <p class="text-blue-100/90 text-lg leading-relaxed max-w-md">Accede al portal unificado para gestionar tu cuenta, ver el catálogo y administrar pedidos con la mayor precisión clínica.</p>
        </div>
        <div class="relative z-10 flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-xl">security</span>
            </div>
            <p class="text-blue-100/80 text-sm font-medium">Acceso seguro y encriptado</p>
        </div>
    </div>

    <!-- Panel derecho: Formulario -->
    <div class="flex-1 flex items-center justify-center p-8 bg-surface">
        <div class="w-full max-w-md" data-aos="fade-left">
            <div class="flex items-center gap-3 mb-10">
                <a href="../INDEX/index.php" class="w-10 h-10 flex items-center justify-center bg-surface-container hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-extrabold text-on-surface tracking-tight">Iniciar sesión</h2>
                    <p class="text-on-surface-variant text-sm mt-1">Ingresa para acceder a tu panel.</p>
                </div>
            </div>

            <?php if ($error_login): ?>
            <div class="mb-6 flex items-center gap-3 bg-error-container/40 border border-error/20 text-on-error-container px-4 py-3 rounded-xl text-sm font-semibold">
                <span class="material-symbols-outlined text-error text-lg">error</span>
                <?= htmlspecialchars($error_msg) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Correo electrónico</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-lg">mail</span>
                        <input type="email" name="email" required autocomplete="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="tu@correo.com"
                               class="w-full pl-11 pr-4 py-3 bg-surface-container-low border-none rounded-xl text-sm focus:ring-2 focus:ring-primary outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Contraseña</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-lg">lock</span>
                        <input type="password" name="password" id="passwordInput" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-11 pr-12 py-3 bg-surface-container-low border-none rounded-xl text-sm focus:ring-2 focus:ring-primary outline-none">
                        <button type="button" onclick="togglePass()" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors">
                            <span class="material-symbols-outlined text-lg" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>
                
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-secondary hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(0,0,0,0.1)] active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 mt-2">
                        <span class="material-symbols-outlined text-lg">login</span>
                        Entrar al portal
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-surface-container-highest text-center">
                <p class="text-sm text-on-surface-variant mb-4">¿Aún no tienes cuenta?</p>
                <a href="../SELECCIÓN_REGISTRO/selección_registro.php" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                    Solicitar acceso al portal <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <p class="text-center text-xs text-on-surface-variant mt-12 opacity-60">
                © <?= date('Y') ?> MMPharma. Todos los derechos reservados.
            </p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true });
    function togglePass() {
        const inp  = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (inp.type === 'password') { inp.type = 'text';     icon.textContent = 'visibility_off'; }
        else                          { inp.type = 'password'; icon.textContent = 'visibility'; }
    }
</script>
</body>
</html>
