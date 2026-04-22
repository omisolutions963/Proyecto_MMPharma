<?php
session_start();
require_once '../INCLUDES/db.php';

$error_login = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        try {
            $pdo  = getDB();
            $stmt = $pdo->prepare(
                "SELECT id, razon_social, password_hash, estatus, tipo FROM clientes WHERE email = ? LIMIT 1"
            );
            $stmt->execute([$email]);
            $cliente = $stmt->fetch();

            if ($cliente && $cliente['estatus'] === 'ACTIVO' && password_verify($password, $cliente['password_hash'])) {
                $_SESSION['cliente_logged_in'] = true;
                $_SESSION['cliente_id']        = $cliente['id'];
                $_SESSION['cliente_email']     = $email;
                $_SESSION['cliente_nombre']    = $cliente['razon_social'];
                $_SESSION['cliente_tipo']      = $cliente['tipo'];
                
                header("Location: ../CATALOGO/catalogo.php");
                exit;
            } else {
                $error_login = true;
            }
        } catch (Exception $e) {
            $error_login = true;
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
<title>Acceso a Clientes — MMPharma</title>
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
    <div class="hidden lg:flex w-1/2 bg-[#003e79] flex-col justify-between p-12 relative overflow-hidden" data-aos="fade-right">
        <div class="absolute inset-0 bg-gradient-to-br from-[#002451] via-[#003e79] to-[#006397]"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-300/10 rounded-full blur-3xl -translate-x-1/2 translate-y-1/2"></div>
        <div class="relative z-10">
            <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.svg" alt="MMPharma" class="h-10 object-contain">
        </div>
        <div class="relative z-10">
            <h1 class="text-4xl font-black text-white leading-tight mb-4">Bienvenido a<br><span class="text-blue-300">MMPharma</span></h1>
            <p class="text-blue-100/80 text-sm leading-relaxed max-w-sm">Tu socio estratégico en distribución farmacéutica. Accede al catálogo, cotiza en línea y gestiona tus pedidos desde cualquier lugar.</p>
        </div>
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-sm">inventory_2</span>
            </div>
            <p class="text-blue-100/70 text-xs">Más de 500 productos disponibles</p>
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
                    <h2 class="text-2xl font-extrabold text-on-surface tracking-tight">Acceso a Clientes</h2>
                    <p class="text-on-surface-variant text-sm mt-1">Ingresa para empezar a cotizar.</p>
                </div>
            </div>

            <?php if ($error_login): ?>
            <div class="mb-6 flex items-center gap-3 bg-error-container/40 border border-error/20 text-on-error-container px-4 py-3 rounded-xl text-sm font-semibold">
                <span class="material-symbols-outlined text-error text-lg">error</span>
                Correo o contraseña incorrectos, o cuenta inactiva.
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">Correo Electrónico</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-lg">mail</span>
                        <input type="email" name="email" required autocomplete="email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               placeholder="cliente@ejemplo.com"
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
                            class="w-full py-3.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 mt-2">
                        <span class="material-symbols-outlined text-lg">login</span>
                        Iniciar Sesión
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
