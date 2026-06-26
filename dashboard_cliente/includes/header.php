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
  "secondary": "#1e60aa",
  "secondary-container": "#dbeafe",
  "on-secondary": "#ffffff",
  "on-secondary-container": "#1d4ed8",
  "tertiary": "#2ca1b5",
  "tertiary-container": "#ecfeff",
  "on-tertiary": "#ffffff",
  "on-tertiary-container": "#0e7490",
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
 /* ══ ANTI-FOUC: Colores hardcoded antes de que Tailwind CDN procese ══ */
 html, body, main { background-color: #eef4fc !important; }
  aside { background: #003e79 !important; }
  body { color: #0f172a; }
 .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

  /* Asegurar que el texto blanco de botones o insignias reales con fondo de color permanezca blanco */
  main .bg-primary,
  main .bg-primary *,
  main .bg-secondary,
  main .bg-secondary *,
  main .bg-tertiary,
  main .bg-tertiary *,
  main .bg-error,
  main .bg-error *,
  main .bg-red-500,
  main .bg-red-500 *,
  main .bg-green-600,
  main .bg-green-600 *,
  main .bg-orange-600,
  main .bg-orange-600 *,
  main .bg-blue-600,
  main .bg-blue-600 *,
  main .bg-indigo-600,
  main .bg-indigo-600 *,
  main button.bg-primary,
  main button.bg-secondary,
  main button.bg-tertiary,
  main a.bg-primary,
  main a.bg-secondary,
  main a.bg-tertiary {
      color: #ffffff !important;
  }

  /* Excluir el texto dentro de la barra de alerta de error/validación de documentos para que tenga contraste */
  main .bg-red-50 .text-white,
  main .bg-red-100 .text-white,
  main .bg-yellow-50 .text-white,
  main .bg-yellow-100 .text-white {
      color: inherit !important;
  }

  /* Estilos específicos para la tarjeta Hero de Perfil */
  main .bg-gradient-to-r.from-surface-container-highest.to-surface-container {
      background: linear-gradient(135deg, #003e79 0%, #1e60aa 100%) !important;
      border: none !important;
  }
  main .bg-gradient-to-r.from-surface-container-highest.to-surface-container .text-white,
  main .bg-gradient-to-r.from-surface-container-highest.to-surface-container .text-on-surface-variant {
      color: #ffffff !important;
  }
  main .bg-gradient-to-r.from-surface-container-highest.to-surface-container .text-primary {
      color: #93c5fd !important;
  }

  /* ══ ESTILIZACIÓN DE ALERTAS E ICONOS DE ALTA VISIBILIDAD (FONDO CLARO + ICONO/TEXTO FUERTE) ══ */
  /* 1. Contenedores de iconos (Badges / Círculos / Recuadros) */
  main .bg-primary\/10 {
      background-color: rgba(0, 62, 121, 0.1) !important;
      color: #003e79 !important;
  }
  main .bg-primary\/10 * {
      color: #003e79 !important;
  }

  main .bg-secondary\/10 {
      background-color: rgba(30, 96, 170, 0.1) !important;
      color: #1e60aa !important;
  }
  main .bg-secondary\/10 * {
      color: #1e60aa !important;
  }

  main .bg-tertiary\/10,
  main .bg-tertiary\/20,
  main .bg-tertiary\/30 {
      background-color: rgba(44, 161, 181, 0.15) !important;
      color: #0e7490 !important;
  }
  main .bg-tertiary\/10 *,
  main .bg-tertiary\/20 *,
  main .bg-tertiary\/30 * {
      color: #0e7490 !important;
  }

  main .bg-error\/10,
  main .bg-error\/20,
  main .bg-error\/30 {
      background-color: rgba(239, 68, 68, 0.12) !important;
      color: #b91c1c !important;
  }
  main .bg-error\/10 *,
  main .bg-error\/20 *,
  main .bg-error\/30 * {
      color: #b91c1c !important;
  }

  main .bg-emerald-500\/10,
  main .bg-emerald-500\/20 {
      background-color: rgba(16, 185, 129, 0.15) !important;
      color: #15803d !important;
  }
  main .bg-emerald-500\/10 *,
  main .bg-emerald-500\/20 * {
      color: #15803d !important;
  }

  main .bg-sky-500\/10,
  main .bg-sky-500\/20 {
      background-color: rgba(14, 165, 233, 0.15) !important;
      color: #0369a1 !important;
  }
  main .bg-sky-500\/10 *,
  main .bg-sky-500\/20 * {
      color: #0369a1 !important;
  }

  main .bg-amber-500\/10,
  main .bg-amber-500\/20,
  main .bg-\[\#eab308\]\/20 {
      background-color: rgba(245, 158, 11, 0.15) !important;
      color: #b45309 !important;
  }
  main .bg-amber-500\/10 *,
  main .bg-amber-500\/20 *,
  main .bg-\[\#eab308\]\/20 * {
      color: #b45309 !important;
  }

  /* 2. Contenedores de Banners de Alerta Completos */
  /* Alerta Documentos en el Dashboard (bg-[#422c10] en HTML) */
  main .bg-\[\#422c10\] {
      background-color: #fef8e7 !important;
      border: 1px solid #fde68a !important;
  }
  main .bg-\[\#422c10\] h3 {
      color: #854d0e !important;
  }
  main .bg-\[\#422c10\] p {
      color: #a16207 !important;
  }
  main .bg-\[\#422c10\] a {
      color: #ca8a04 !important;
  }
  
  /* Alerta Documentos Aprobados/Activos (bg-tertiary-container/30 en HTML) */
  main .bg-tertiary-container\/30 {
      background-color: #f0fdf4 !important;
      border: 1px solid #dcfce7 !important;
  }
  main .bg-tertiary-container\/30 h3 {
      color: #166534 !important;
  }
  main .bg-tertiary-container\/30 p {
      color: #15803d !important;
  }
  main .bg-tertiary-container\/30 a {
      color: #16a34a !important;
  }

  /* Alertas Estándar de Tailwind (en documentos.php) */
  /* Aprobados (Verde) */
  main .bg-emerald-500\/10 {
      background-color: #f0fdf4 !important;
      border-color: #dcfce7 !important;
  }
  main .bg-emerald-500\/10 h3 {
      color: #166534 !important;
  }
  main .bg-emerald-500\/10 p {
      color: #15803d !important;
  }
  
  /* Atención requerida / Warning (Ámbar) */
  main .bg-amber-500\/10 {
      background-color: #fffbeb !important;
      border-color: #fde68a !important;
  }
  main .bg-amber-500\/10 h3 {
      color: #78350f !important;
  }
  main .bg-amber-500\/10 p {
      color: #b45309 !important;
  }

  /* Revisión (Celeste/Sky) */
  main .bg-sky-500\/10 {
      background-color: #f0f9ff !important;
      border-color: #bae6fd !important;
  }
  main .bg-sky-500\/10 h3 {
      color: #0c4a6e !important;
  }
  main .bg-sky-500\/10 p {
      color: #0369a1 !important;
  }

  /* Inputs, selects y textareas */
  main input:not([type="checkbox"]):not([type="radio"]), 
  main select, 
  main textarea {
      color: #0f172a !important;
      background-color: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
  }
  main input::placeholder, 
  main textarea::placeholder {
      color: #94a3b8 !important;
  }
  main input:focus, 
  main select:focus, 
  main textarea:focus {
      border-color: #003e79 !important;
      box-shadow: 0 0 0 1px #003e79 !important;
  }

  /* Forzar colores correctos de texto en Notificaciones del Header */
  #notificaciones-dropdown .text-white,
  .swal2-popup .text-white {
      color: #0f172a !important;
  }
  #notificaciones-dropdown .bg-surface-container\/50 {
      background-color: #f1f5f9 !important;
  }

  /* Ajustes generales de tablas basados en la imagen de referencia */
  main table {
      color: #0f172a !important;
  }
  main th {
      background-color: #003e79 !important;
      color: #ffffff !important;
      font-weight: 700 !important;
      font-size: 11px !important;
      text-transform: uppercase !important;
      letter-spacing: 0.1em !important;
      padding: 16px 20px !important;
      border: none !important;
      opacity: 1 !important;
  }
  main td {
      border-bottom: 1px solid #e2e8f0 !important;
      color: #334155 !important;
      background-color: #ffffff !important;
      padding: 16px 20px !important;
  }
  main tr:hover td {
      background-color: #f8fafc !important;
  }

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
  .card-glow { box-shadow: 0 0 30px rgba(74,144,217,0.08); }

  /* ══ ESTANDARIZACIÓN DE SOMBRAS GLOBALES ══ */
  .shadow-sm { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important; }
  .shadow-md { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important; }
  .shadow-lg { box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1) !important; }

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
  background: '#ffffff', color: '#1e293b' });
  }
  function confirmAction(title, text, confirmText, callback) {
  Swal.fire({ title, text, icon: 'warning', showCancelButton: true,
  confirmButtonColor: '#f28b82', cancelButtonColor: '#3a5a8a',
  confirmButtonText: confirmText, cancelButtonText: 'Cancelar',
  background: '#ffffff', color: '#1e293b'
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
                      background: '#ffffff',
                      color: '#1e293b'
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
                      const bgStyle = isUnread ? 'bg-primary/5' : '';

                      htmlList += `
                      <div class="p-3 rounded-xl border border-outline-variant/30 ${borderStyle} ${bgStyle} flex justify-between items-start gap-3 transition-all" id="modal-notif-item-${n.id}">
                          <div class="min-w-0 flex-1">
                              <div class="flex justify-between items-start gap-2">
                                  <h4 class="text-xs font-bold text-slate-800">${n.tipo || 'INFORMACIÓN'}</h4>
                                  <span class="text-[9px] text-slate-400 shrink-0">${dateFmt}</span>
                              </div>
                              <p class="text-xs text-slate-600 mt-1 break-words">${n.mensaje}</p>
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
                  background: '#ffffff',
                  color: '#1e293b'
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
<header style="background:#2ca1b5;border-bottom:1px solid rgba(255,255,255,0.15)"
 class="h-16 fixed top-0 z-40 flex items-center justify-between px-4 md:px-8 lg:ml-64 w-full lg:w-[calc(100%-16rem)] shadow-md">
 
  <div class="flex items-center gap-2 md:gap-5 flex-1 min-w-0">
  <!-- Hamburger Menu Button -->
  <button onclick="toggleUserSidebar()" class="lg:hidden text-white hover:bg-white/10 rounded-xl p-2 flex items-center justify-center transition-colors mr-1 shrink-0">
    <span class="material-symbols-outlined text-2xl">menu</span>
  </button>
  <!-- Portal Label -->
  <div class="flex items-center gap-1.5 md:gap-3 min-w-0">
  <div class="flex flex-col leading-none min-w-0">
  <span class="text-[10px] font-black uppercase tracking-[0.3em] text-white/80 mb-1">MMPharma</span>
  <span class="text-sm sm:text-base md:text-lg lg:text-xl font-extrabold text-white tracking-tight whitespace-nowrap overflow-hidden text-ellipsis">Portal cliente</span>
  </div>
  </div>
  </div>

 <div class="flex items-center gap-5">
 <!-- Search -->
 <div class="relative hidden md:block">
 <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-white/70 text-sm">search</span>
 <input class="w-72 pl-9 pr-4 py-2 rounded-xl border border-white/20
 bg-white/15 text-white text-sm placeholder:text-white/60
 focus:ring-1 focus:ring-white focus:outline-none focus:bg-white/25"
 placeholder="Buscar..." type="text"/>
 </div>

 <!-- Notifications -->
 <div class="relative">
 <button id="notif-btn" onclick="toggleNotificaciones()" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/15 border border-white/10 text-white hover:bg-white/25 transition-all relative group">
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
  <div class="h-6 w-px bg-white/20 hidden md:block"></div>

 <!-- User / Perfil Button -->
 <a href="perfil.php" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl transition-all hover:bg-white/10 group">
 <?php
 $foto = $_SESSION['cliente_foto'] ?? '';
 $nombre = $_SESSION['cliente_nombre'] ?? 'Cliente';
 ?>
 <?php if ($foto): ?>
 <img src="<?= htmlspecialchars($foto) ?>" id="headerProfileImg"
 class="w-8 h-8 rounded-lg object-cover border-2 border-white/50"
 alt="Perfil">
 <?php else: ?>
 <div id="headerProfileImg"
 class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm border-2 border-white/40 group-hover:border-white/70 transition-colors"
 style="background:rgba(255,255,255,0.2)">
 <?= strtoupper(substr($nombre, 0, 1)) ?>
 </div>
 <?php endif; ?>
 <div class="hidden lg:flex flex-col items-start leading-none">
 <span class="text-sm font-semibold text-white"><?= htmlspecialchars($nombre) ?></span>
 <span class="text-[10px] text-white/70">Mi cuenta</span>
 </div>
 <span class="material-symbols-outlined text-white/70 text-[16px] hidden lg:block group-hover:text-white transition-colors">expand_more</span>
 </a>
 </div>
</header>
