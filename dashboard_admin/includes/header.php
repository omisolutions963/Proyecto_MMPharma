<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db.php';
    $pdo = getDB();
}

$notif_admin = [];

try {
    // 1. Solicitudes de Registro Pendientes
    $stmt_sol = $pdo->prepare("SELECT id, razon_social, created_at FROM clientes_solicitudes_registro WHERE estatus = 'PENDIENTE' ORDER BY created_at DESC LIMIT 5");
    $stmt_sol->execute();
    while ($row = $stmt_sol->fetch(PDO::FETCH_ASSOC)) {
        $notif_admin[] = [
            'tipo' => 'SOLICITUD',
            'mensaje' => 'Nueva solicitud de registro: ' . $row['razon_social'],
            'link' => '../s_registro/solicitudes.php',
            'created_at' => $row['created_at'],
            'icon' => 'person_add',
            'style' => 'bg-primary/10 text-primary',
            'icon_class' => 'text-primary'
        ];
    }

    // 2. Pedidos Pendientes
    $stmt_ped = $pdo->prepare("SELECT p.id, p.monto_total, p.created_at, c.razon_social FROM clientes_pedidos p JOIN clientes_usuarios c ON p.cliente_id = c.id WHERE p.estado_envio = 'PENDIENTE' ORDER BY p.created_at DESC LIMIT 5");
    $stmt_ped->execute();
    while ($row = $stmt_ped->fetch(PDO::FETCH_ASSOC)) {
        $notif_admin[] = [
            'tipo' => 'PEDIDO',
            'mensaje' => 'Nuevo pedido de ' . $row['razon_social'] . ' (Total: $' . number_format($row['monto_total'], 2) . ')',
            'link' => '../g_pedidos/pedidos.php',
            'created_at' => $row['created_at'],
            'icon' => 'shopping_cart',
            'style' => 'bg-secondary/10 text-secondary',
            'icon_class' => 'text-secondary'
        ];
    }

    // 3. Mensajes de Soporte No Leídos
    $stmt_msg = $pdo->prepare("SELECT id, nombre, created_at FROM clientes_contacto_mensajes WHERE leido = 0 ORDER BY created_at DESC LIMIT 5");
    $stmt_msg->execute();
    while ($row = $stmt_msg->fetch(PDO::FETCH_ASSOC)) {
        $notif_admin[] = [
            'tipo' => 'SOPORTE',
            'mensaje' => 'Mensaje de soporte de ' . $row['nombre'],
            'link' => '../g_soporte/mensajes.php',
            'created_at' => $row['created_at'],
            'icon' => 'chat',
            'style' => 'bg-tertiary/10 text-tertiary',
            'icon_class' => 'text-tertiary'
        ];
    }

    // 4. Documentos Pendientes de Validar
    $stmt_doc = $pdo->prepare("SELECT d.id, d.cliente_id, d.tipo_documento, d.fecha_subida, c.razon_social FROM clientes_documentos d JOIN clientes_usuarios c ON d.cliente_id = c.id WHERE d.estatus_validacion = 'PENDIENTE' ORDER BY d.fecha_subida DESC LIMIT 5");
    $stmt_doc->execute();
    while ($row = $stmt_doc->fetch(PDO::FETCH_ASSOC)) {
        $notif_admin[] = [
            'tipo' => 'DOCUMENTO',
            'mensaje' => 'Documento por validar (' . $row['tipo_documento'] . ') de ' . $row['razon_social'],
            'link' => '../g_clientes/ver_cliente.php?id=' . $row['cliente_id'],
            'created_at' => $row['fecha_subida'],
            'icon' => 'description',
            'style' => 'bg-primary/10 text-primary',
            'icon_class' => 'text-primary'
        ];
    }

    // Ordenar todas por created_at descending
    usort($notif_admin, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
} catch (Exception $e) {
    // Tolerancia a fallos si alguna tabla no está disponible o falla
}

$unread_count = count($notif_admin);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'MMPharma Portal' ?></title>
<link rel="icon" type="image/png" href="../../logos/mmpharma-isotipo.png">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Cropper.js para recorte de foto de perfil -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script id="tailwind-config">
 tailwind.config = {
 darkMode: "class",
 theme: { extend: {
 colors: {
 "surface-container-lowest": "#ffffff",
 "surface-container-low": "#f8fafc",
 "surface-container": "#f1f5f9",
 "surface-container-high": "#e2e8f0",
 "surface-container-highest":"#cbd5e1",
 "surface": "#ffffff",
 "surface-bright": "#ffffff",
 "surface-dim": "#eef4fc",
 "surface-variant": "#f1f5f9",
 "background": "#eef4fc",
 "on-surface": "#0f172a",
 "on-surface-variant": "#475569",
 "on-background": "#0f172a",
 "primary": "#003e79",
 "primary-container": "#e0f2fe",
 "on-primary": "#ffffff",
 "on-primary-container": "#0369a1",
 "on-primary-fixed": "#002111",
 "on-primary-fixed-variant": "#002a52",
 "primary-fixed": "#003e79",
 "primary-fixed-dim": "#1e60aa",
 "secondary": "#1e60aa",
 "secondary-container": "#dbeafe",
 "on-secondary": "#ffffff",
 "on-secondary-container": "#1d4ed8",
 "secondary-fixed": "#005234",
 "secondary-fixed-dim": "#1e60aa",
 "on-secondary-fixed": "#e1ffe7",
 "on-secondary-fixed-variant":"#006d48",
 "tertiary": "#2ca1b5",
 "tertiary-container": "#ecfeff",
 "on-tertiary": "#ffffff",
 "on-tertiary-container": "#0e7490",
 "tertiary-fixed": "#7efba4",
 "tertiary-fixed-dim": "#61de8a",
 "on-tertiary-fixed": "#00210c",
 "on-tertiary-fixed-variant":"#005228",
 "error": "#ef4444",
 "error-container": "#fee2e2",
 "on-error": "#ffffff",
 "on-error-container": "#b91c1c",
 "outline": "#cbd5e1",
 "outline-variant": "#cbd5e1",
 "inverse-surface": "#0f172a",
 "inverse-on-surface": "#f8fafc",
 "inverse-primary": "#e0f2fe",
 "surface-tint": "#003e79",
 },
 fontFamily: { headline: ["Inter"], body: ["Inter"], label: ["Inter"] },
 }}
 }
</script>
<style>
 * { font-family: 'Inter', sans-serif; }
 body { background: #eef4fc; }
 .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
 /* ══ SCROLLBAR GLOBAL AGRESIVA ══════════════════════════════════════════ */
 /* Forzamos el estilo en todos los elementos para evitar el comportamiento de Windows 11 */
 *::-webkit-scrollbar {
 width: 10px !important;
 height: 10px !important;
 }
 *::-webkit-scrollbar-track {
 background: rgba(0,0,0,0.05) !important;
 }
 *::-webkit-scrollbar-thumb {
 background: rgba(0,62,121,0.3) !important;
 border-radius: 10px !important;
 border: 2px solid transparent !important;
 background-clip: content-box !important;
 }
 *::-webkit-scrollbar-thumb:hover {
 background: rgba(0,62,121,0.6) !important;
 background-clip: content-box !important;
 }

 html { 
 scrollbar-gutter: stable !important; 
 scrollbar-width: auto !important; 
 overflow-y: scroll !important;
 }
 body {
 overflow-x: hidden !important;
 }
 html.swal2-shown, body.swal2-shown {
 overflow-y: scroll !important;
 }
 .glass-card { background: rgba(0,62,121,0.05); backdrop-filter: blur(20px); }
 .card-glow { box-shadow: 0 0 30px rgba(0,62,121,0.08); }

 /* --- ANIMACIONES NATIVAS --- */
 @keyframes revealUp {
 0% { opacity: 0; transform: translateY(5px); }
 100% { opacity: 1; transform: translateY(0); }
 }
 @keyframes scaleIn {
 0% { opacity: 0; transform: scale(0.98); }
 100% { opacity: 1; transform: scale(1); }
 }
 .animate-reveal, .animate-fade-up { animation: revealUp 0.6s ease-out forwards; opacity: 0; }
 .animate-scale-in { animation: scaleIn 0.6s ease-out forwards; opacity: 0; }
 
 .delay-100 { animation-delay: 0.1s; }
 .delay-200 { animation-delay: 0.2s; }
 .delay-300 { animation-delay: 0.3s; }
 .delay-400 { animation-delay: 0.4s; }
 .delay-500 { animation-delay: 0.5s; }

 /* Responsive Panel adjustments */
 @media (max-width: 1023px) {
   main.ml-64, main {
     margin-left: 0 !important;
     width: 100% !important;
     padding: 1.5rem 1rem !important;
   }
   header {
     padding-left: 1rem !important;
     padding-right: 1rem !important;
   }
 }
</style>

<script>
  function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    if (!sidebar || !overlay) return;
    
    if (sidebar.classList.contains('-translate-x-full')) {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      setTimeout(() => {
        overlay.classList.remove('opacity-0');
      }, 10);
    } else {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('opacity-0');
      setTimeout(() => {
        overlay.classList.add('hidden');
      }, 300);
    }
  }

  function mockAction(title, text = 'Acción procesada correctamente', icon = 'success') {
  Swal.fire({ title, text, icon, confirmButtonColor: '#003e79', confirmButtonText: 'Aceptar',
  background: '#ffffff', color: '#0f172a' });
  }
  function confirmAction(title, text, confirmText, callback) {
  Swal.fire({ title, text, icon: 'warning', showCancelButton: true,
  confirmButtonColor: '#f28b82', cancelButtonColor: '#284a3c',
  confirmButtonText: confirmText, cancelButtonText: 'Cancelar',
  background: '#ffffff', color: '#0f172a'
  }).then(r => { if (r.isConfirmed) callback(); });
  }

  function toggleNotificaciones() {
    const dropdown = document.getElementById('notificaciones-dropdown');
    if (dropdown) {
      dropdown.classList.toggle('opacity-0');
      dropdown.classList.toggle('invisible');
      dropdown.classList.toggle('translate-y-2');
    }
  }

  // Close dropdowns when clicking outside
  window.onclick = function(event) {
    if (!event.target.closest('#notif-btn') && !event.target.closest('#notificaciones-dropdown')) {
      const dropdown = document.getElementById('notificaciones-dropdown');
      if (dropdown && !dropdown.classList.contains('invisible')) {
        dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
      }
    }
  }
</script>
</head>
<body class="bg-background text-on-surface">

<!-- TopNavBar -->
<header style="background:#ffffff;border-bottom:1px solid rgba(0,62,121,0.15)"
 class="h-16 sticky top-0 z-40 flex items-center justify-between px-4 md:px-8 lg:ml-64 w-full lg:w-[calc(100%-16rem)] shadow-sm">
 
  <div class="flex items-center gap-2 md:gap-5 flex-1 min-w-0">
  <!-- Hamburger Menu Button -->
  <button onclick="toggleAdminSidebar()" class="lg:hidden text-slate-800 hover:bg-slate-100 rounded-xl p-2 flex items-center justify-center transition-colors mr-1 shrink-0">
    <span class="material-symbols-outlined text-2xl">menu</span>
  </button>
  <!-- Portal Label -->
  <div class="flex items-center gap-1.5 md:gap-3 min-w-0">
    <span class="text-sm sm:text-base md:text-lg lg:text-xl font-extrabold text-slate-800 tracking-tight whitespace-nowrap overflow-hidden text-ellipsis">Portal de administrador</span>
  </div>
  </div>

  <div class="flex items-center gap-5">
  
  <!-- Notifications -->
  <div class="relative">
  <button id="notif-btn" onclick="toggleNotificaciones()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-primary/10 border border-primary/20 text-primary hover:bg-primary/20 transition-all relative group">
  <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">notifications</span>
  <?php if ($unread_count > 0): ?>
  <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border border-background"></span>
  <?php endif; ?>
  </button>

  <!-- Notifications Dropdown -->
  <div id="notificaciones-dropdown" class="absolute right-0 top-[calc(100%+0.75rem)] w-80 bg-surface-container-low border border-outline-variant/50 rounded-2xl opacity-0 invisible translate-y-2 transition-all duration-200 z-50 overflow-hidden">
  <div class="p-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container/50">
  <h3 class="text-sm font-bold text-on-surface">Notificaciones</h3>
  <div class="flex flex-col items-end gap-1" id="notif-actions-header">
  <span class="text-[10px] font-black text-primary uppercase tracking-widest unread-count-text"><?= $unread_count ?> pendientes</span>
  </div>
  </div>
  <div class="max-h-96 overflow-y-auto" id="notif-items-list">
  <?php if (empty($notif_admin)): ?>
  <div class="p-8 text-center bg-surface">
  <span class="material-symbols-outlined text-outline text-[40px] mb-2">notifications_off</span>
  <p class="text-xs text-on-surface-variant">No hay tareas pendientes por ahora.</p>
  </div>
  <?php else: ?>
  <?php foreach($notif_admin as $n): ?>
  <a href="<?= $n['link'] ?>" class="block p-4 border-b border-outline-variant/10 hover:bg-white/5 bg-surface transition-colors cursor-pointer relative notification-item">
  <div class="flex items-start justify-between gap-3">
  <div class="flex gap-3 items-start min-w-0 flex-1">
  <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 <?= $n['style'] ?>">
  <span class="material-symbols-outlined text-[18px] <?= $n['icon_class'] ?>">
  <?= $n['icon'] ?>
  </span>
  </div>
  <div class="min-w-0 flex-1">
  <p class="text-xs font-bold text-on-surface mb-0.5 break-words leading-tight"><?= htmlspecialchars($n['mensaje']) ?></p>
  <p class="text-[10px] text-on-surface-variant"><?= date('d M, H:i', strtotime($n['created_at'])) ?></p>
  </div>
  </div>
  </div>
  </a>
  <?php endforeach; ?>
  <?php endif; ?>
  </div>
  </div>
  </div>

  <!-- Divider -->
  <div class="h-6 w-px bg-outline-variant/30 hidden md:block -mr-3"></div>

  <!-- User / Perfil Button -->
  <button onclick="abrirPerfil()"
  class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition-all hover:bg-slate-100 group">
  <?php
  $foto = $_SESSION['admin_foto'] ?? '';
  $nombre = $_SESSION['admin_nombre'] ?? 'Admin';
  ?>
  <?php if ($foto): ?>
  <img src="<?= htmlspecialchars($foto) ?>" id="headerProfileImg"
  class="w-8 h-8 rounded-lg object-cover border-2 border-sky-500/40"
  alt="Perfil">
  <?php else: ?>
  <div id="headerProfileImg"
  class="w-8 h-8 rounded-lg flex items-center justify-center text-sky-600 font-bold text-sm border-2 border-sky-500/30 group-hover:border-sky-500/60 transition-colors"
  style="background:rgba(0,62,121,0.1)">
  <?= strtoupper(substr($nombre, 0, 1)) ?>
  </div>
  <?php endif; ?>
  <div class="hidden lg:flex flex-col items-start leading-none">
  <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($nombre) ?></span>
  </div>
  <span class="material-symbols-outlined text-outline text-[16px] hidden lg:block group-hover:text-primary transition-colors">expand_more</span>
  </button>
  </div>
</header>
