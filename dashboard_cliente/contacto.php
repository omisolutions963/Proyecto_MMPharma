<?php
if (session_status() === PHP_SESSION_NONE) {
 session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
 header("Location: ../login/login.php");
 exit;
}

require_once '../includes/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

$envio_exitoso = false;
$envio_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asunto'], $_POST['mensaje'])) {
    $categoria  = trim($_POST['categoria'] ?? 'General');
    $cotizacion = trim($_POST['cotizacion'] ?? '');
    $asunto     = trim($_POST['asunto'] ?? '');
    $mensaje    = trim($_POST['mensaje'] ?? '');
    
    if (!empty($asunto) && !empty($mensaje)) {
        try {
            $stmt_user = $pdo->prepare("SELECT persona_contacto, razon_social, email, telefono_celular, telefono_local FROM clientes_usuarios WHERE id = ?");
            $stmt_user->execute([$cliente_id]);
            $user_info = $stmt_user->fetch(PDO::FETCH_ASSOC);

            $nombre_contacto = !empty($user_info['persona_contacto']) ? $user_info['persona_contacto'] : 'Cliente';
            $email_contacto = !empty($user_info['email']) ? $user_info['email'] : '';
            $telefono_contacto = !empty($user_info['telefono_celular']) ? $user_info['telefono_celular'] : $user_info['telefono_local'];
            $empresa_contacto = !empty($user_info['razon_social']) ? $user_info['razon_social'] : '';

            $msg_formatted = "Categoría: " . $categoria . "\n";
            if (!empty($cotizacion)) {
                $msg_formatted .= "Cotización Relacionada: " . $cotizacion . "\n";
            }
            $msg_formatted .= "Asunto: " . $asunto . "\n\n";
            $msg_formatted .= "Cuerpo del Mensaje:\n" . $mensaje;

            $stmt_insert = $pdo->prepare("INSERT INTO clientes_contacto_mensajes (nombre, email, telefono, empresa, mensaje, ip_origen) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert->execute([
                $nombre_contacto,
                $email_contacto,
                $telefono_contacto,
                $empresa_contacto,
                $msg_formatted,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            $envio_exitoso = true;
        } catch (Exception $e) {
            $envio_error = true;
        }
    } else {
        $envio_error = true;
    }
}

// Get company config
$stmt = $pdo->query("SELECT clave, valor FROM admin_configuracion WHERE clave IN ('empresa_telefono', 'empresa_email')");
$configuracion = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
 $configuracion[$row['clave']] = $row['valor'];
}
$empresa_telefono = $configuracion['empresa_telefono'] ?? '+52 (55) 8922-1000';
$empresa_email = $configuracion['empresa_email'] ?? 'soporte@mmpharma.com';
if (empty($empresa_telefono)) $empresa_telefono = '+52 (55) 8922-1000'; // fallback
if (empty($empresa_email)) $empresa_email = 'soporte@mmpharma.com'; // fallback

// Get assigned agent
$stmt = $pdo->prepare("
 SELECT c.agente_id, a.nombre, a.foto_perfil, a.telefono, a.email 
 FROM clientes_usuarios c 
 LEFT JOIN admin_usuarios a ON c.agente_id = a.id 
 WHERE c.id = ?
");
$stmt->execute([$cliente_id]);
$agente_data = $stmt->fetch(PDO::FETCH_ASSOC);

$tiene_agente = !empty($agente_data['agente_id']);
$agente_nombre = $tiene_agente ? $agente_data['nombre'] : 'No asignado';
$agente_foto = ($tiene_agente && !empty($agente_data['foto_perfil'])) ? $agente_data['foto_perfil'] : 'https://picsum.photos/seed/default/100/100';

$pageTitle = 'MMPharma Portal - Contacto';
$activePage = 'contacto';
include('includes/header.php');
include('includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)]" style="background:#ffffff">

  <!-- Header -->
  <div class="mb-10 animate-reveal">
  <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Centro de soporte</h1>
  <p class="text-slate-500 text-sm max-w-2xl">Bienvenido a nuestro canal de atención directa. Si tienes dudas sobre un pedido o necesitas asistencia técnica, nuestro equipo especializado está listo para apoyarte.</p>
  </div>

  <!-- Main Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-reveal delay-100">
  
  <!-- Form Column (Left) -->
  <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-8 ">
  <h2 class="text-lg font-bold text-white flex items-center gap-2 mb-8">
  <span class="material-symbols-outlined text-primary">send</span> Enviar un mensaje
  </h2>
  
  <form action="" method="POST" class="space-y-6">
  <div>
  <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Categoría</label>
  <div class="relative">
  <select name="categoria" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none bg-none focus:border-primary outline-none transition-all">
  <option value="Soporte Técnico">Soporte técnico</option>
  <option value="Dudas de Facturación">Dudas de facturación</option>
  <option value="Estatus de Pedido">Estatus de pedido</option>
  <option value="Otro">Otro</option>
  </select>
  <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
  </div>
  </div>

  <div>
  <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Asunto</label>
  <input type="text" name="asunto" placeholder="Escribe el título de tu consulta" class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all" required>
  </div>

  <div>
  <label class="block text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2 ml-1">Mensaje</label>
  <textarea name="mensaje" rows="5" placeholder="Describe tu situación con el mayor detalle posible..." class="w-full bg-surface-container-low border border-outline-variant/50 text-white rounded-xl px-4 py-3 text-sm focus:border-primary outline-none transition-all resize-none" required></textarea>
  </div>

  <div>
  <button type="submit" class="px-6 py-3 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-bold rounded-xl transition-all flex items-center gap-2">
  Enviar solicitud <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
  </button>
  </div>
  </form>
  </div>

  <!-- Info Column (Right) -->
  <div class="space-y-6">
  
    <!-- Contacto Directo -->
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 relative">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-base font-bold text-white">Contacto directo</h3>
        <span class="px-2.5 py-1 bg-primary/10 text-primary text-[9px] font-black rounded-lg uppercase tracking-wider flex items-center gap-1">
          <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span> Soporte de Ventas
        </span>
      </div>
      
      <div class="space-y-6">
        <!-- WhatsApp -->
        <a href="https://wa.me/523322207506" target="_blank" rel="noopener noreferrer" class="flex gap-4 p-3 rounded-xl border border-transparent hover:border-emerald-500/20 hover:bg-emerald-500/5 transition-all group">
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
            <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.964 9.964 0 0 0 1.333 4.993L2 22l5.233-1.371a9.936 9.936 0 0 0 4.777 1.21h.005c5.505 0 9.988-4.478 9.99-9.984 0-2.669-1.038-5.176-2.924-7.062C17.197 2.906 14.685 2.001 12.012 2zm5.727 14.06c-.313.882-1.528 1.621-2.122 1.708-.594.087-1.18.324-3.79-.714-3.14-1.251-5.143-4.436-5.3-4.647-.156-.212-1.272-1.69-1.272-3.226 0-1.536.804-2.292 1.089-2.597.285-.304.624-.381.832-.381.208 0 .416.002.597.01.187.008.437-.072.686.529.25.602.854 2.08.927 2.229.073.149.122.323.023.522-.099.2-.149.323-.298.497-.149.174-.312.387-.446.519-.149.147-.304.307-.13.606.174.299.773 1.275 1.658 2.06.136.12.261.23.364.316.593.504 1.134.75 1.554.925.321.133.626.088.847-.149.27-.291 1.077-1.251 1.365-1.678.287-.427.575-.357.969-.211 1.01.373 2.522.929 2.622.978.1.049.167.243.117.551z"/>
            </svg>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-0.5">WhatsApp</p>
            <p class="text-sm font-bold text-white mb-0.5">33 2220 7506</p>
            <p class="text-[10px] text-emerald-400 font-medium flex items-center gap-1">
              Enviar mensaje directo <span class="material-symbols-outlined text-[12px] group-hover:translate-x-1 transition-transform">open_in_new</span>
            </p>
          </div>
        </a>

        <!-- Teléfonos Fijos -->
        <div class="flex gap-4 p-3 rounded-xl border border-transparent">
          <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-[20px]">call</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-1.5">Líneas fijas</p>
            <div class="space-y-2.5">
              <a href="tel:3343480581" class="group/phone flex items-center justify-between text-sm font-bold text-white hover:text-primary transition-colors">
                <span>33 4348 0581</span>
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant/40 group-hover/phone:text-primary transition-colors">call</span>
              </a>
              <a href="tel:3343480582" class="group/phone flex items-center justify-between text-sm font-bold text-white hover:text-primary transition-colors">
                <span>33 4348 0582</span>
                <span class="material-symbols-outlined text-[16px] text-on-surface-variant/40 group-hover/phone:text-primary transition-colors">call</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Correo Electrónico -->
        <a href="mailto:<?= htmlspecialchars($empresa_email) ?>" class="flex gap-4 p-3 rounded-xl border border-transparent hover:border-secondary/20 hover:bg-secondary/5 transition-all group">
          <div class="w-10 h-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
            <span class="material-symbols-outlined text-[20px]">mail</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-0.5">Correo electrónico</p>
            <p class="text-sm font-bold text-white mb-0.5"><?= htmlspecialchars($empresa_email) ?></p>
            <p class="text-[10px] text-secondary font-medium flex items-center gap-1">
              Enviar correo directo <span class="material-symbols-outlined text-[12px] group-hover:translate-x-1 transition-transform">open_in_new</span>
            </p>
          </div>
        </a>
      </div>
    </div>
  </div>

  </div>
  </div>

</main>

<?php if ($envio_exitoso): ?>
 <script>
     Swal.fire({
         icon: 'success',
         title: '¡Mensaje enviado!',
         text: 'Tu solicitud de soporte ha sido recibida con éxito. Te responderemos a la brevedad.',
         background: '#ffffff',
         color: '#1e293b',
         confirmButtonColor: '#4a90d9',
         confirmButtonText: 'Aceptar'
     });
 </script>
 <?php elseif ($envio_error): ?>
 <script>
     Swal.fire({
         icon: 'error',
         title: 'Error de envío',
         text: 'Ocurrió un error al procesar tu solicitud. Por favor intenta de nuevo más tarde.',
         background: '#ffffff',
         color: '#1e293b',
         confirmButtonColor: '#f28b82',
         confirmButtonText: 'Cerrar'
     });
 </script>
 <?php endif; ?>

<?php include('includes/footer.php'); ?>
