<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true) {
    if (isset($_SESSION['debe_cambiar_password']) && $_SESSION['debe_cambiar_password'] == 1) {
        $current_script = basename($_SERVER['PHP_SELF']);
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        if ($current_script !== 'cambiar_password_obligatorio.php' && $current_script !== 'logout.php') {
            $redirect_url = ($current_dir === 'login') ? 'cambiar_password_obligatorio.php' : '../login/cambiar_password_obligatorio.php';
            header("Location: " . $redirect_url);
            exit;
        }
    }
}

// Fetch Notifications if database connection is available
$notificaciones = [];
$unread_count = 0;
if (isset($pdo) && isset($_SESSION['cliente_id'])) {
 $stmt_notif = $pdo->prepare("SELECT * FROM admin_alertas_notificaciones WHERE cliente_id = ? ORDER BY created_at DESC LIMIT 5");
 $stmt_notif->execute([$_SESSION['cliente_id']]);
 $notificaciones = $stmt_notif->fetchAll(PDO::FETCH_ASSOC);
 
 $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM admin_alertas_notificaciones WHERE cliente_id = ? AND leida = 0");
 $stmt_unread->execute([$_SESSION['cliente_id']]);
 $unread_count = $stmt_unread->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $pageTitle ?? 'MMPharma Portal Cliente' ?></title>
<link rel="icon" type="image/png" href="../logos/mmpharma-isotipo.png">
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script id="tailwind-config">
 tailwind.config = {
 darkMode: "class",
 theme: { extend: {
 colors: {
 "surface-container-lowest": "#0d1f3c",
 "surface-container-low": "#102245",
 "surface-container": "#152a52",
 "surface-container-high": "#1a3260",
 "surface-container-highest":"#1e3a6e",
 "surface": "#0a1929",
 "surface-bright": "#112038",
 "surface-dim": "#061422",
 "surface-variant": "#1a3260",
 "background": "#071628",
 "on-surface": "#e8f0ff",
 "on-surface-variant": "#8aaad4",
 "on-background": "#e8f0ff",
 "primary": "#4a90d9",
 "primary-container": "#1a3a6b",
 "on-primary": "#ffffff",
 "on-primary-container": "#abc7ff",
 "on-primary-fixed": "#001b3f",
 "on-primary-fixed-variant": "#284678",
 "primary-fixed": "#1d3a70",
 "primary-fixed-dim": "#3a7abf",
 "secondary": "#5bb8f5",
 "secondary-container": "#0a3a5c",
 "on-secondary": "#ffffff",
 "on-secondary-container": "#9dd4f7",
 "secondary-fixed": "#1a4a72",
 "secondary-fixed-dim": "#4a9fd4",
 "on-secondary-fixed": "#d0eaff",
 "on-secondary-fixed-variant":"#7bbfe0",
 "tertiary": "#34c47a",
 "tertiary-container": "#0a3d20",
 "on-tertiary": "#ffffff",
 "on-tertiary-container": "#52e098",
 "tertiary-fixed": "#7efba4",
 "tertiary-fixed-dim": "#61de8a",
 "on-tertiary-fixed": "#00210c",
 "on-tertiary-fixed-variant":"#005228",
 "error": "#f28b82",
 "error-container": "#5c1010",
 "on-error": "#ffffff",
 "on-error-container": "#ffb4ab",
 "outline": "#3a5a8a",
 "outline-variant": "#1e3a6e",
 "inverse-surface": "#e8f0ff",
 "inverse-on-surface": "#071628",
 "inverse-primary": "#002451",
 "surface-tint": "#4a90d9",
 },
 fontFamily: { headline: ["Inter"], body: ["Inter"], label: ["Inter"] },
 }}
 }
</script>
<style>
 * { font-family: 'Inter', sans-serif; }
 /* ══ ANTI-FOUC: Colores hardcoded antes de que Tailwind CDN procese ══ */
 html, body, main, aside { background-color: #071628 !important; }
 body { color: #e8f0ff; }
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
 background: rgba(74,144,217,0.3) !important;
 border-radius: 10px !important;
 border: 2px solid transparent !important;
 background-clip: content-box !important;
 }
 *::-webkit-scrollbar-thumb:hover {
 background: rgba(74,144,217,0.6) !important;
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
 .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); }
 .card-glow { box-: 0 0 30px rgba(74,144,217,0.08); }

 /* ══ ESTANDARIZACIÓN DE SOMBRAS GLOBALES ══ */
 . { box-: 0 2px 8px rgba(0, 0, 0, 0.1) !important; }
 . { box-: 0 4px 12px rgba(0, 0, 0, 0.15) !important; }
 . { box-: 0 6px 16px rgba(0, 0, 0, 0.2) !important; }
 . { box-: 0 10px 25px rgba(0, 0, 0, 0.25) !important; }
 . { box-: 0 15px 35px rgba(0, 0, 0, 0.3) !important; }
 . { box-: 0 20px 50px rgba(0, 0, 0, 0.4) !important; }

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
  function toggleUserSidebar() {
    const sidebar = document.getElementById('userSidebar');
    const overlay = document.getElementById('userSidebarOverlay');
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
  Swal.fire({ title, text, icon, confirmButtonColor: '#4a90d9', confirmButtonText: 'Aceptar',
  background: '#102245', color: '#e8f0ff' });
  }
  function confirmAction(title, text, confirmText, callback) {
  Swal.fire({ title, text, icon: 'warning', showCancelButton: true,
  confirmButtonColor: '#f28b82', cancelButtonColor: '#3a5a8a',
  confirmButtonText: confirmText, cancelButtonText: 'Cancelar',
  background: '#102245', color: '#e8f0ff'
  }).then(r => { if (r.isConfirmed) callback(); });
  }
  function toggleNotificaciones() {
 const dropdown = document.getElementById('notificaciones-dropdown');
 dropdown.classList.toggle('opacity-0');
 dropdown.classList.toggle('invisible');
 dropdown.classList.toggle('translate-y-2');
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

  async function marcarNotificacionLeida(element, id) {
      const indicator = element.querySelector('.unread-indicator');
      if (!indicator) return;

      try {
          const response = await fetch('api_notificaciones.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'marcar_leida', id: id })
          });
          const data = await response.json();
          if (data.success) {
              indicator.remove();
              const badge = document.querySelector('#notif-btn .bg-error');
              const unreadText = document.querySelector('.unread-count-text');
              let currentCount = parseInt(unreadText.textContent) || 0;
              if (currentCount > 0) {
                  currentCount--;
                  unreadText.textContent = currentCount + ' sin leer';
                  if (currentCount === 0) {
                      if (badge) badge.remove();
                      const btnMarcarTodas = document.getElementById('btn-marcar-todas');
                      if (btnMarcarTodas) btnMarcarTodas.remove();
                      const divider = document.getElementById('notif-header-divider');
                      if (divider) divider.remove();
                  }
              }
          }
      } catch (error) {
          console.error('Error al marcar notificación como leída:', error);
      }
  }

  async function marcarTodasLeidas() {
      try {
          const response = await fetch('api_notificaciones.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'marcar_todas_leidas' })
          });
          const data = await response.json();
          if (data.success) {
              document.querySelectorAll('.unread-indicator').forEach(el => el.remove());
              const badge = document.querySelector('#notif-btn .bg-error');
              if (badge) badge.remove();
              const unreadText = document.querySelector('.unread-count-text');
              if (unreadText) {
                  unreadText.textContent = '0 sin leer';
              }
              const btnMarcarTodas = document.getElementById('btn-marcar-todas');
              if (btnMarcarTodas) btnMarcarTodas.remove();
              const divider = document.getElementById('notif-header-divider');
              if (divider) divider.remove();
          }
      } catch (error) {
          console.error('Error al marcar todas las notificaciones como leídas:', error);
      }
  }

  async function eliminarNotificacion(event, id) {
      if (event) event.stopPropagation();
      
      try {
          const response = await fetch('api_notificaciones.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'eliminar', id: id })
          });
          const data = await response.json();
          if (data.success) {
              let wasUnread = false;

              // Check dropdown item
              const dropdownItem = document.getElementById(`notif-item-${id}`);
              if (dropdownItem) {
                  if (dropdownItem.querySelector('.unread-indicator')) {
                      wasUnread = true;
                  }
                  dropdownItem.remove();
              }

              // Check modal item
              const modalItem = document.getElementById(`modal-notif-item-${id}`);
              if (modalItem) {
                  if (modalItem.className.includes('border-primary')) {
                      wasUnread = true;
                  }
                  modalItem.remove();
              }

              // Update badge & counter if it was unread
              if (wasUnread) {
                  const badge = document.querySelector('#notif-btn .bg-error');
                  const unreadText = document.querySelector('.unread-count-text');
                  if (unreadText) {
                      let currentCount = parseInt(unreadText.textContent) || 0;
                      if (currentCount > 0) {
                          currentCount--;
                          unreadText.textContent = currentCount + ' sin leer';
                          if (currentCount === 0) {
                              if (badge) badge.remove();
                              const btnMarcarTodas = document.getElementById('btn-marcar-todas');
                              if (btnMarcarTodas) btnMarcarTodas.remove();
                              const divider = document.getElementById('notif-header-divider');
                              if (divider) divider.remove();
                          }
                      }
                  }
              }

              // Check dropdown list container
              const listContainer = document.getElementById('notif-items-list');
              if (listContainer) {
                  const items = listContainer.querySelectorAll('.notification-item');
                  if (items.length === 0) {
                      listContainer.innerHTML = `
                          <div class="p-8 text-center">
                              <span class="material-symbols-outlined text-outline text-[40px] mb-2">notifications_off</span>
                              <p class="text-xs text-on-surface-variant">No tienes notificaciones por ahora.</p>
                          </div>
                      `;
                      const actionHeader = document.getElementById('notif-actions-header');
                      if (actionHeader) {
                          actionHeader.innerHTML = '<span class="text-[10px] font-black text-primary uppercase tracking-widest unread-count-text">0 sin leer</span>';
                      }
                  }
              }

              // Check modal container
              const modalList = document.querySelector('.swal2-html-container .space-y-3');
              if (modalList && modalList.querySelectorAll('[id^="modal-notif-item-"]').length === 0) {
                  modalList.innerHTML = '<p class="text-sm text-center text-slate-400">No tienes notificaciones.</p>';
                  const headerDeleteAll = document.querySelector('.swal2-html-container .flex.justify-between');
                  if (headerDeleteAll) {
                      headerDeleteAll.remove();
                  }
              }
          }
      } catch (error) {
          console.error('Error al eliminar la notificación:', error);
      }
  }

  async function eliminarTodasNotificaciones(event) {
      if (event) event.stopPropagation();
      
      confirmAction('¿Eliminar todas?', 'Esta acción borrará todo tu historial de notificaciones permanentemente.', 'Sí, eliminar', async () => {
          try {
              const response = await fetch('api_notificaciones.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ action: 'eliminar_todas' })
              });
              const data = await response.json();
              if (data.success) {
                  // Clear dropdown list container
                  const listContainer = document.getElementById('notif-items-list');
                  if (listContainer) {
                      listContainer.innerHTML = `
                          <div class="p-8 text-center">
                              <span class="material-symbols-outlined text-outline text-[40px] mb-2">notifications_off</span>
                              <p class="text-xs text-on-surface-variant">No tienes notificaciones por ahora.</p>
                          </div>
                      `;
                  }

                  // Clear badge & counter
                  const badge = document.querySelector('#notif-btn .bg-error');
                  if (badge) badge.remove();
                  const unreadText = document.querySelector('.unread-count-text');
                  if (unreadText) {
                      unreadText.textContent = '0 sin leer';
                  }
                  
                  const actionHeader = document.getElementById('notif-actions-header');
                  if (actionHeader) {
                      actionHeader.innerHTML = '<span class="text-[10px] font-black text-primary uppercase tracking-widest unread-count-text">0 sin leer</span>';
                  }

                  // Close SweetAlert modal if open
                  if (Swal.isVisible()) {
                      Swal.close();
                  }
                  
                  // Show success toast
                  Swal.fire({
                      toast: true,
                      position: 'top-end',
                      icon: 'success',
                      title: 'Notificaciones eliminadas',
                      showConfirmButton: false,
                      timer: 2000,
                      background: '#102245',
                      color: '#e8f0ff'
                  });
              }
          } catch (error) {
              console.error('Error al eliminar todas las notificaciones:', error);
          }
      });
  }

  async function verTodasNotificaciones() {
      try {
          const response = await fetch('api_notificaciones.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ action: 'listar' })
          });
          const data = await response.json();
          if (data.success && data.notificaciones) {
              let htmlList = '';
              if (data.notificaciones.length === 0) {
                  htmlList += '<div class="space-y-4 max-h-96 overflow-y-auto pr-2"><p class="text-sm text-center text-slate-400">No tienes notificaciones.</p></div>';
              } else {
                  htmlList += `<div class="flex justify-between items-center mb-4 pb-2 border-b border-outline-variant/30">
                      <span class="text-xs text-slate-400 font-medium">${data.notificaciones.length} notificaciones</span>
                      <button onclick="eliminarTodasNotificaciones(event)" class="text-[10px] text-error hover:underline font-bold uppercase tracking-wider flex items-center gap-1">
                          <span class="material-symbols-outlined text-[14px]">delete_sweep</span> Eliminar todas
                      </button>
                  </div>`;
                  
                  htmlList += '<div class="text-left space-y-3 max-h-96 overflow-y-auto pr-2">';
                  data.notificaciones.forEach(n => {
                      const dateFmt = new Date(n.created_at).toLocaleString('es-MX', {day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'});
                      const isUnread = !parseInt(n.leida);
                      const borderStyle = isUnread ? 'border-l-4 border-primary pl-3' : 'pl-3';
                      const bgStyle = isUnread ? 'bg-surface-container-high/40' : '';
                      
                      htmlList += `
                      <div class="p-3 rounded-xl border border-outline-variant/30 ${borderStyle} ${bgStyle} flex justify-between items-start gap-3 transition-all" id="modal-notif-item-${n.id}">
                          <div class="min-w-0 flex-1">
                              <div class="flex justify-between items-start gap-2">
                                  <h4 class="text-xs font-bold text-white">${n.tipo || 'INFORMACIÓN'}</h4>
                                  <span class="text-[9px] text-slate-400 shrink-0">${dateFmt}</span>
                              </div>
                              <p class="text-xs text-slate-300 mt-1 break-words">${n.mensaje}</p>
                          </div>
                          <button onclick="eliminarNotificacion(event, ${n.id})" class="text-slate-400 hover:text-error transition-colors p-1 rounded-lg hover:bg-white/5 shrink-0" title="Eliminar">
                              <span class="material-symbols-outlined text-[16px]">delete</span>
                          </button>
                      </div>`;
                  });
                  htmlList += '</div>';
              }

              Swal.fire({
                  title: 'Historial de Notificaciones',
                  html: htmlList,
                  width: '500px',
                  confirmButtonColor: '#4a90d9',
                  confirmButtonText: 'Cerrar',
                  background: '#102245',
                  color: '#e8f0ff'
              });

              marcarTodasLeidas();
          }
      } catch (error) {
          console.error('Error al listar notificaciones:', error);
      }
  }
</script>
</head>
<body class="bg-background text-on-surface">

<!-- TopNavBar -->
<header style="background:rgba(7,22,40,0.97);border-bottom:1px solid rgba(74,144,217,0.15)"
 class="h-16 fixed top-0 z-40 backdrop-blur-xl flex items-center justify-between px-4 md:px-8 lg:ml-64 w-full lg:w-[calc(100%-16rem)]">
 
  <div class="flex items-center gap-2 md:gap-5 flex-1 min-w-0">
  <!-- Hamburger Menu Button -->
  <button onclick="toggleUserSidebar()" class="lg:hidden text-white hover:bg-white/10 rounded-xl p-2 flex items-center justify-center transition-colors mr-1 shrink-0">
    <span class="material-symbols-outlined text-2xl">menu</span>
  </button>
  <!-- Portal Label -->
  <div class="flex items-center gap-1.5 md:gap-3 min-w-0">
  <div class="flex flex-col leading-none min-w-0">
  <span class="text-[10px] font-black uppercase tracking-[0.3em] text-primary/70 mb-1">MMPharma</span>
  <span class="text-sm sm:text-base md:text-lg lg:text-xl font-extrabold text-white tracking-tight whitespace-nowrap overflow-hidden text-ellipsis">Portal cliente</span>
  </div>
  </div>
  </div>

 <div class="flex items-center gap-5">
 <!-- Search -->
 <div class="relative hidden md:block">
 <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
 <input class="w-72 pl-9 pr-4 py-2 rounded-xl border border-outline-variant/50
 bg-surface-container-low/60 text-on-surface text-sm placeholder:text-outline
 focus:ring-1 focus:ring-primary focus:outline-none"
 placeholder="Buscar..." type="text"/>
 </div>

 <!-- Notifications -->
 <div class="relative">
 <button id="notif-btn" onclick="toggleNotificaciones()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-low/60 border border-outline-variant/30 text-on-surface-variant hover:text-primary hover:border-primary/50 transition-all relative group">
 <span class="material-symbols-outlined text-[22px] group-hover:scale-110 transition-transform">notifications</span>
 <?php if ($unread_count > 0): ?>
 <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border border-background"></span>
 <?php endif; ?>
 </button>

 <!-- Notifications Dropdown -->
 <div id="notificaciones-dropdown" class="absolute right-0 top-[calc(100%+0.75rem)] w-80 bg-surface-container-low border border-outline-variant/50 rounded-2xl opacity-0 invisible translate-y-2 transition-all duration-200 z-50 overflow-hidden">
 <div class="p-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container/50">
 <h3 class="text-sm font-bold text-white">Notificaciones</h3>
 <div class="flex flex-col items-end gap-1" id="notif-actions-header">
 <span class="text-[10px] font-black text-primary uppercase tracking-widest unread-count-text"><?= $unread_count ?> sin leer</span>
 <?php if (!empty($notificaciones)): ?>
 <div class="flex items-center gap-1.5">
 <?php if ($unread_count > 0): ?>
 <button id="btn-marcar-todas" onclick="marcarTodasLeidas(); event.stopPropagation();" class="text-[9px] text-secondary hover:underline font-bold uppercase tracking-wider">Marcar leídas</button>
 <span class="text-[9px] text-outline-variant/60" id="notif-header-divider">|</span>
 <?php endif; ?>
 <button onclick="eliminarTodasNotificaciones(event);" class="text-[9px] text-error hover:underline font-bold uppercase tracking-wider">Eliminar todas</button>
 </div>
 <?php endif; ?>
 </div>
 </div>
 <div class="max-h-96 overflow-y-auto" id="notif-items-list">
 <?php if (empty($notificaciones)): ?>
 <div class="p-8 text-center">
 <span class="material-symbols-outlined text-outline text-[40px] mb-2">notifications_off</span>
 <p class="text-xs text-on-surface-variant">No tienes notificaciones por ahora.</p>
 </div>
 <?php else: ?>
 <?php foreach($notificaciones as $n): ?>
 <div onclick="marcarNotificacionLeida(this, <?= $n['id'] ?>)" class="p-4 border-b border-outline-variant/10 hover:bg-white/5 transition-colors cursor-pointer relative notification-item" id="notif-item-<?= $n['id'] ?>">
 <?php if(!$n['leida']): ?>
 <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary unread-indicator"></div>
 <?php endif; ?>
 <div class="flex items-start justify-between gap-3">
 <div class="flex gap-3 items-start min-w-0 flex-1">
 <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 
 <?= $n['tipo'] === 'SUCCESS' ? 'bg-tertiary/10 text-tertiary' : 
 ($n['tipo'] === 'WARNING' ? 'bg-error/10 text-error' : 'bg-primary/10 text-primary') ?>">
 <span class="material-symbols-outlined text-[18px]">
 <?= $n['tipo'] === 'SUCCESS' ? 'check_circle' : 
 ($n['tipo'] === 'WARNING' ? 'warning' : 'info') ?>
 </span>
 </div>
 <div class="min-w-0 flex-1">
 <p class="text-xs font-bold text-white mb-0.5 break-words leading-tight"><?= htmlspecialchars($n['mensaje']) ?></p>
 <p class="text-[10px] text-on-surface-variant"><?= date('d M, H:i', strtotime($n['created_at'])) ?></p>
 </div>
 </div>
 <button onclick="eliminarNotificacion(event, <?= $n['id'] ?>)" class="text-on-surface-variant hover:text-error transition-colors p-1 rounded-lg hover:bg-white/5 shrink-0" title="Eliminar">
 <span class="material-symbols-outlined text-[18px]">delete</span>
 </button>
 </div>
 </div>
 <?php endforeach; ?>
 <?php endif; ?>
 </div>
 <a href="#" onclick="verTodasNotificaciones(); event.stopPropagation(); event.preventDefault();" class="block p-3 text-center text-[11px] font-bold text-primary hover:bg-primary/5 transition-colors">
 Ver todas las notificaciones
 </a>
 </div>
 </div>

 
  <!-- Divider -->
  <div class="h-6 w-px bg-outline-variant/30 hidden md:block"></div>

 <!-- User / Perfil Button -->
 <a href="perfil.php" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition-all hover:bg-white/5 group">
 <?php
 $foto = $_SESSION['cliente_foto'] ?? '';
 $nombre = $_SESSION['cliente_nombre'] ?? 'Cliente';
 ?>
 <?php if ($foto): ?>
 <img src="<?= htmlspecialchars($foto) ?>" id="headerProfileImg"
 class="w-8 h-8 rounded-lg object-cover border-2 border-primary/40"
 alt="Perfil">
 <?php else: ?>
 <div id="headerProfileImg"
 class="w-8 h-8 rounded-lg flex items-center justify-center text-primary font-bold text-sm border-2 border-primary/30 group-hover:border-primary/60 transition-colors"
 style="background:rgba(74,144,217,0.2)">
 <?= strtoupper(substr($nombre, 0, 1)) ?>
 </div>
 <?php endif; ?>
 <div class="hidden lg:flex flex-col items-start leading-none">
 <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($nombre) ?></span>
 <span class="text-[10px] text-on-surface-variant">Mi cuenta</span>
 </div>
 <span class="material-symbols-outlined text-outline text-[16px] hidden lg:block group-hover:text-primary transition-colors">expand_more</span>
 </a>
 </div>
</header>
