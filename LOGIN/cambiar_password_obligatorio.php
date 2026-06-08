<?php
session_start();

// Validar que el cliente esté logueado y tenga la bandera activa
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true || !isset($_SESSION['debe_cambiar_password']) || $_SESSION['debe_cambiar_password'] != 1) {
    header("Location: login.php");
    exit;
}

require_once '../INCLUDES/db.php';
$error = false;
$error_msg = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (strlen($password) < 8) {
        $error = true;
        $error_msg = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $confirm_password) {
        $error = true;
        $error_msg = "Las contraseñas no coinciden.";
    } else {
        try {
            $pdo = getDB();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Actualizar contraseña y quitar flag
            $stmt = $pdo->prepare("UPDATE clientes_usuarios SET password_hash = ?, debe_cambiar_password = 0 WHERE id = ?");
            $stmt->execute([$hash, $_SESSION['cliente_id']]);

            // Actualizar la sesión
            $_SESSION['debe_cambiar_password'] = 0;
            $success = true;
        } catch (Exception $e) {
            $error = true;
            $error_msg = "Error de conexión o de base de datos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Cambio obligatorio de contraseña — MMPharma</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="icon" type="image/png" href="../logos/MMPharma-Isotipo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="../IMG/33.webp" class="absolute inset-0 w-full h-full object-cover object-[center_60%] transform scale-125">
            <div class="absolute inset-0 bg-background/80"></div>
        </div>

        <div class="relative z-10">
            <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-10 object-contain">
        </div>
        <div class="relative z-10 mt-20">
            <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight mb-6 tracking-tight">Actualiza tu<br><span class="text-tertiary">Contraseña</span></h1>
            <p class="text-slate-300 text-lg leading-relaxed max-w-lg font-medium">
                Por motivos de seguridad y privacidad, requerimos que cambies la contraseña temporal asignada en tu primer ingreso al portal.
            </p>
        </div>
        
        <div class="relative z-10 flex flex-col gap-6 mt-auto">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/20 flex items-center justify-center border border-primary/30">
                    <span class="material-symbols-outlined text-tertiary text-2xl">security</span>
                </div>
                <p class="text-slate-300 text-sm font-bold tracking-wide">Política de seguridad MMPharma</p>
            </div>
        </div>
    </div>

    <!-- Panel derecho: Formulario -->
    <div class="flex-1 flex flex-col items-center justify-start lg:justify-center px-6 pt-12 pb-12 sm:p-8 bg-background relative overflow-y-auto w-full min-h-screen lg:min-h-0">
        <!-- Logo para Móvil (Oculto en Desktop) -->
        <div class="lg:hidden mb-8 mt-2 z-10" data-aos="fade-down">
            <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-10 object-contain">
        </div>

        <div class="w-full max-w-md relative z-10 bg-tertiary px-6 py-8 sm:p-10 rounded-3xl" data-aos="fade-left">
            <div class="flex items-center gap-4 mb-6 sm:mb-10">
                <div class="w-12 h-12 flex items-center justify-center bg-white/20 text-white rounded-2xl">
                    <span class="material-symbols-outlined">lock_reset</span>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-white tracking-tight">Nueva contraseña</h2>
                    <p class="text-white text-xs mt-1 font-medium">Establece tus nuevas credenciales.</p>
                </div>
            </div>

            <?php if ($error): ?>
            <div class="mb-6 flex items-center gap-3 bg-white border border-red-200 text-red-600 px-5 py-4 rounded-2xl text-sm font-bold ">
                <span class="material-symbols-outlined text-red-500 text-xl">error</span>
                <?= htmlspecialchars($error_msg) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5 sm:space-y-6">
                <div>
                    <label class="block text-xs font-black text-white uppercase tracking-widest mb-3">Nueva contraseña</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-surface/50 group-focus-within:text-primary transition-colors">lock</span>
                        <input type="password" name="password" id="passwordInput" required minlength="8"
                               placeholder="Mínimo 8 caracteres"
                               class="w-full pl-14 pr-14 py-4 bg-white border border-transparent rounded-2xl text-surface text-base focus:ring-2 focus:ring-surface outline-none transition-all placeholder:text-surface/40 ">
                        <button type="button" onclick="togglePass('passwordInput', 'eyeIcon1')" class="absolute right-5 top-1/2 -translate-y-1/2 text-surface/50 hover:text-primary transition-colors focus:outline-none">
                            <span class="material-symbols-outlined text-xl" id="eyeIcon1">visibility</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-black text-white uppercase tracking-widest mb-3">Confirmar nueva contraseña</label>
                    <div class="relative group">
                        <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-surface/50 group-focus-within:text-primary transition-colors">lock</span>
                        <input type="password" name="confirm_password" id="confirmPasswordInput" required minlength="8"
                               placeholder="Confirma tu contraseña"
                               class="w-full pl-14 pr-14 py-4 bg-white border border-transparent rounded-2xl text-surface text-base focus:ring-2 focus:ring-surface outline-none transition-all placeholder:text-surface/40 ">
                        <button type="button" onclick="togglePass('confirmPasswordInput', 'eyeIcon2')" class="absolute right-5 top-1/2 -translate-y-1/2 text-surface/50 hover:text-primary transition-colors focus:outline-none">
                            <span class="material-symbols-outlined text-xl" id="eyeIcon2">visibility</span>
                        </button>
                    </div>
                </div>
                
                <div class="pt-4 sm:pt-6">
                    <button type="submit"
                            class="w-full h-14 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 hover:-translate-y-1 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 group">
                        <span class="text-lg">Actualizar contraseña</span>
                        <span class="material-symbols-outlined text-[22px] group-hover:translate-x-1 transition-transform">save</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-6 sm:mt-10 sm:pt-8 border-t border-white/20 text-center">
                <a href="logout.php" class="inline-flex items-center gap-2 text-white font-bold hover:text-white/80 transition-colors group">
                    Cerrar sesión <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">logout</span>
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
    function togglePass(inputId, iconId) {
        const inp = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (inp.type === 'password') { 
            inp.type = 'text'; 
            icon.textContent = 'visibility_off'; 
        } else { 
            inp.type = 'password'; 
            icon.textContent = 'visibility'; 
        }
    }
</script>

<?php if ($success): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Contraseña actualizada!',
        text: 'Tu contraseña ha sido cambiada exitosamente. Serás redirigido al portal.',
        background: '#112240',
        color: '#ffffff',
        confirmButtonColor: '#2ca1b5',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        allowOutsideClick: false
    }).then(() => {
        window.location.href = '../DASHBOARD_CLIENTE/Dashboard.php';
    });
</script>
<?php endif; ?>
</body>
</html>
