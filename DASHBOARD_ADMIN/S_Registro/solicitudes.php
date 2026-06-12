<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

function enviarCorreoBienvenidaLocal($email_cliente, $razon_social, $tipo_cliente) {
    $url_login = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/Proyecto_MMPharma/LOGIN/login.php';
    $asunto = "¡Bienvenido a MMPharma! Tu cuenta ha sido activada";
    $headers = implode("\r\n", [
        'From: MMPharma Portal <noreply@mmpharma.com>',
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
    ]);
    
    $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f7ff;padding:30px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,36,81,.15)">
  <div style="background:#002451;padding:24px 32px;text-align:center">
    <h1 style="margin:0;color:#fff;font-size:22px">🎉 ¡Tu cuenta ha sido activada!</h1>
    <p style="margin:6px 0 0;color:#8baed4;font-size:14px">Bienvenido a MMPharma Clinical Systems</p>
  </div>
  <div style="padding:32px;color:#333;line-height:1.6">
    <p style="font-size:16px;font-weight:bold;color:#002451;margin-top:0">Estimado(a) ' . htmlspecialchars($razon_social) . ',</p>
    <p>Nos complace informarte que tu solicitud de registro ha sido aprobada con éxito. A partir de este momento, ya tienes acceso completo a nuestro catálogo de productos con precios personalizados para tu nivel de cliente.</p>
    
    <div style="background:#f0f5ff;border-radius:8px;padding:20px;margin:24px 0">
      <table style="width:100%;border-collapse:collapse;font-size:14px">
        <tr>
          <td style="padding:6px 0;color:#666;width:120px;font-weight:bold">Usuario/Email:</td>
          <td style="color:#002451;font-weight:bold">' . htmlspecialchars($email_cliente) . '</td>
        </tr>
        <tr>
          <td style="padding:6px 0;color:#666;font-weight:bold">Nivel de Cliente:</td>
          <td style="color:#002451;font-weight:bold">' . htmlspecialchars($tipo_cliente) . '</td>
        </tr>
        <tr>
          <td style="padding:6px 0;color:#666;font-weight:bold">Contraseña Temporal:</td>
          <td style="color:#002451;font-weight:bold">cliente123</td>
        </tr>
      </table>
    </div>
    
    <p>Puedes iniciar sesión en el portal utilizando tus credenciales registradas haciendo clic en el siguiente botón:</p>
    
    <div style="text-align:center;margin:32px 0 16px">
      <a href="' . htmlspecialchars($url_login) . '"
         style="display:inline-block;background:#002451;color:#fff;padding:14px 36px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px;box-shadow:0 4px 10px rgba(0,36,81,0.2)">
        Iniciar Sesión en el Portal
      </a>
    </div>
    
    <p style="font-size:12px;color:#777;margin-top:40px;border-top:1px solid #eee;padding-top:20px">
      Si tienes alguna duda o requieres asistencia adicional, no dudes en ponerte en contacto con nuestro equipo de atención a clientes.
    </p>
  </div>
  <div style="background:#f0f5ff;padding:16px 32px;text-align:center;font-size:11px;color:#888">
    &copy; ' . date('Y') . ' MMPharma. Todos los derechos reservados.
  </div>
</div>
</body></html>';
    
    // En lugar de usar @mail() que falla en local, usamos PHPMailer
    require_once __DIR__ . '/../../INCLUDES/mailer.php';
    enviarCorreoPHPMailer($email_cliente, $asunto, $html);
}

// ── Acción: Aprobar / Rechazar ────────────────────────────────────────────────
$msgFlash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
  $id = (int)$_POST['id'];
  $action = $_POST['action'];
  if ($action === 'aprobar') {
      // Obtener solicitud
      $stmt = $pdo->prepare("SELECT * FROM clientes_solicitudes_registro WHERE id = ?");
      $stmt->execute([$id]);
      $sol = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($sol) {
          // Verificar duplicado de email
          $checkUser = $pdo->prepare("SELECT COUNT(*) FROM clientes_usuarios WHERE email = ?");
          $checkUser->execute([$sol['email']]);
          if ($checkUser->fetchColumn() == 0) {
              // Crear cuenta activa
              $password_hash = password_hash('cliente123', PASSWORD_DEFAULT);
              $stmtInsert = $pdo->prepare("
                  INSERT INTO clientes_usuarios (
                      tipo, razon_social, nombre_comercial, rfc, regimen_fiscal, 
                      domicilio_fiscal, colonia_fiscal, cp_fiscal, ciudad_fiscal, estado_fiscal, 
                      representante_legal, giro, persona_contacto, volumen_mensual, 
                      telefono_local, telefono_celular, email, password_hash, documento_tipo, 
                      metodo_pago, uso_cfdi, domicilio_entrega, colonia_entrega, 
                      cp_entrega, ciudad_entrega, municipio_entrega, estado_entrega, 
                      receptor_entrega, horario_entrega, estatus,
                      doc_constancia_fiscal, doc_licencia_sanitaria, doc_comprobante_domicilio,
                      doc_alta_hacienda, doc_identificacion_oficial, doc_acta_constitutiva
                  ) VALUES (
                      ?, ?, ?, ?, ?, 
                      ?, ?, ?, ?, ?, 
                      ?, ?, ?, ?, 
                      ?, ?, ?, ?, ?, 
                      ?, ?, ?, ?, 
                      ?, ?, ?, ?, 
                      ?, ?, 'ACTIVO',
                      ?, ?, ?, ?, ?, ?
                  )
              ");
              
              $stmtInsert->execute([
                  $sol['tipo_cliente'],
                  $sol['razon_social'],
                  $sol['nombre_comercial'],
                  $sol['rfc'],
                  $sol['regimen_fiscal'],
                  $sol['domicilio_fiscal'],
                  $sol['colonia'],
                  $sol['cp'],
                  $sol['ciudad'],
                  $sol['estado'],
                  $sol['representante'],
                  $sol['giro'],
                  $sol['persona_contacto'],
                  $sol['volumen_mensual'],
                  $sol['telefono_local'],
                  $sol['telefono_celular'],
                  $sol['email'],
                  $password_hash,
                  $sol['documento_tipo'],
                  $sol['metodo_pago'],
                  $sol['uso_cfdi'],
                  $sol['domicilio_entrega'],
                  $sol['colonia_entrega'],
                  $sol['cp_entrega'],
                  $sol['ciudad_entrega'],
                  $sol['municipio_entrega'],
                  $sol['estado_entrega'],
                  $sol['receptor_entrega'],
                  $sol['horario_entrega'],
                  $sol['doc_constancia_fiscal'],
                  $sol['doc_licencia_sanitaria'],
                  $sol['doc_comprobante_domicilio'],
                  $sol['doc_alta_hacienda'],
                  $sol['doc_identificacion_oficial'],
                  $sol['doc_acta_constitutiva']
              ]);
              
              // Correo de bienvenida
              enviarCorreoBienvenidaLocal($sol['email'], $sol['razon_social'], $sol['tipo_cliente']);
          }
          
          $pdo->prepare("UPDATE clientes_solicitudes_registro SET estatus='APROBADA' WHERE id=?")->execute([$id]);
          $msgFlash = 'aprobada';
      }
  } elseif ($action === 'rechazar') {
      $pdo->prepare("UPDATE clientes_solicitudes_registro SET estatus='RECHAZADA' WHERE id=?")->execute([$id]);
      $msgFlash = 'rechazada';
  }
  header("Location: solicitudes.php?msg=$msgFlash");
  exit;
}
$msgFlash = $_GET['msg'] ?? '';

// ── KPIs ──────────────────────────────────────────────────────────────────────
$pendientes = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='PENDIENTE'")->fetchColumn();
$aprobadas = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='APROBADA'")->fetchColumn();
$rechazadas = (int)$pdo->query("SELECT COUNT(*) FROM clientes_solicitudes_registro WHERE estatus='RECHAZADA'")->fetchColumn();

// ── Lista ─────────────────────────────────────────────────────────────────────
$filtro = $_GET['filtro'] ?? '';
$where = $filtro ? "WHERE estatus = " . $pdo->quote($filtro) : "";
$solicitudes = $pdo->query(
 "SELECT * FROM clientes_solicitudes_registro $where ORDER BY created_at DESC LIMIT 50"
)->fetchAll();

$pageTitle = 'MMPharma Portal - Solicitudes de registro';
$activePage = 'solicitudes';
include('../Includes/header.php');
include('../Includes/sidebar.php');
?>
<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<?php if ($msgFlash === 'aprobada'): ?>
<div class="mb-6 bg-tertiary/10 border border-on-tertiary-container/20 text-on-tertiary-container px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 animate-fade-in">
 <span class="material-symbols-outlined">check_circle</span> Solicitud aprobada correctamente.
</div>
<?php elseif ($msgFlash === 'rechazada'): ?>
<div class="mb-6 bg-error-container/40 border border-error/20 text-on-error-container px-5 py-3 rounded-xl text-sm font-semibold flex items-center gap-2 animate-fade-in">
 <span class="material-symbols-outlined">block</span> Solicitud rechazada.
</div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
 <div>
 <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
 <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
 <span class="material-symbols-outlined text-[12px]">chevron_right</span>
 <span class="text-on-surface-variant">Solicitudes</span>
 </nav>
 <h2 class="text-3xl font-extrabold tracking-tight text-on-surface animate-reveal">Solicitudes de registro</h2>
 <p class="text-on-surface-variant text-sm mt-1">Solicitudes enviadas desde el sitio público.</p>
 </div>
 <div class="flex gap-2 flex-wrap bg-surface-container-low p-1.5 rounded-2xl">
 <a href="?filtro=" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= !$filtro ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Todas</a>
 <a href="?filtro=PENDIENTE" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='PENDIENTE' ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Pendientes</a>
 <a href="?filtro=APROBADA" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='APROBADA' ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Aprobadas</a>
 <a href="?filtro=RECHAZADA" class="px-5 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all <?= $filtro==='RECHAZADA' ? 'bg-primary text-white ' : 'text-on-surface-variant hover:bg-surface-container-high' ?>">Rechazadas</a>
 </div>
</div>

<!-- KPIs Unificados -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
 <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-primary/40 animate-reveal">
 <div class="flex justify-between items-center mb-1">
 <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Pendientes</span>
 <span class="material-symbols-outlined text-primary/30 scale-75">pending</span>
 </div>
 <h3 class="text-2xl font-black text-on-surface"><?= $pendientes ?></h3>
 </div>
 <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-tertiary/40 animate-reveal" style="animation-delay: 0.1s">
 <div class="flex justify-between items-center mb-1">
 <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Aprobadas</span>
 <span class="material-symbols-outlined text-tertiary/30 scale-75">check_circle</span>
 </div>
 <h3 class="text-2xl font-black text-on-surface"><?= $aprobadas ?></h3>
 </div>
 <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 border-error/40 animate-reveal" style="animation-delay: 0.2s">
 <div class="flex justify-between items-center mb-1">
 <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant">Rechazadas</span>
 <span class="material-symbols-outlined text-error/30 scale-75">block</span>
 </div>
 <h3 class="text-2xl font-black text-on-surface"><?= $rechazadas ?></h3>
 </div>
</div>

<!-- Tabla Centrada -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.3s">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
 <th class="px-8 py-4 text-center">Solicitante / RFC</th>
 <th class="px-8 py-4 text-center">Tipo cliente</th>
 <th class="px-8 py-4 text-center">Contacto</th>
 <th class="px-8 py-4 text-center">Fecha envío</th>
 <th class="px-8 py-4 text-center">Estatus</th>
 <th class="px-8 py-4 text-center">Acciones</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-outline-variant/10">
 <?php if (empty($solicitudes)): ?>
 <tr><td colspan="6" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-fade-in">
 No hay solicitudes registradas con este criterio.
 </td></tr>
 <?php else: ?>
 <?php foreach ($solicitudes as $s):
 $badgeClass = match($s['estatus']) {
 'APROBADA' => 'bg-tertiary-container/20 text-on-tertiary-container',
 'RECHAZADA' => 'bg-error-container/20 text-error',
 default => 'bg-primary/10 text-primary border border-primary/20',
 };
 ?>
 <tr class="hover:bg-surface-container-low/30 transition-colors group animate-fade-in">
 <td class="px-8 py-5 text-center">
 <p class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($s['razon_social']) ?></p>
 <p class="text-[10px] text-on-surface-variant font-black uppercase mt-0.5"><?= htmlspecialchars($s['rfc'] ?: 'SIN RFC') ?></p>
 </td>
 <td class="px-8 py-5 text-center">
 <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant text-[10px] font-black rounded-lg uppercase tracking-wider">
 <?= htmlspecialchars($s['tipo_cliente']) ?>
 </span>
 </td>
 <td class="px-8 py-5 text-center">
 <div class="flex flex-col items-center">
 <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($s['persona_contacto'] ?: '—') ?></span>
 <span class="text-[10px] text-on-surface-variant"><?= htmlspecialchars($s['email']) ?></span>
 </div>
 </td>
 <td class="px-8 py-5 text-center text-[11px] font-bold text-on-surface-variant"><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
 <td class="px-8 py-5 text-center">
 <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $badgeClass ?>">
 <?= $s['estatus'] ?>
 </span>
 </td>
 <td class="px-8 py-5">
 <div class="flex justify-center gap-2">
 <button type="button" title="Ver Detalles" onclick='openDetailsModal(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-secondary/10 text-secondary hover:bg-secondary hover:text-white transition-all">
 <span class="material-symbols-outlined text-[18px]">info</span>
 </button>
 <button type="button" title="Ver Documentos" onclick='openDocsModal(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all">
 <span class="material-symbols-outlined text-[18px]">visibility</span>
 </button>

 <?php if ($s['estatus'] === 'PENDIENTE'): ?>
 <form method="POST">
 <input type="hidden" name="id" value="<?= $s['id'] ?>">
 <input type="hidden" name="action" value="aprobar">
 <button title="Aprobar" class="w-9 h-9 flex items-center justify-center rounded-lg bg-tertiary/10 text-on-tertiary-container hover:bg-tertiary hover:text-white transition-all ">
 <span class="material-symbols-outlined text-[18px]">done_all</span>
 </button>
 </form>
 <form method="POST">
 <input type="hidden" name="id" value="<?= $s['id'] ?>">
 <input type="hidden" name="action" value="rechazar">
 <button title="Rechazar" class="w-9 h-9 flex items-center justify-center rounded-lg bg-error-container/20 text-error hover:bg-error hover:text-white transition-all ">
 <span class="material-symbols-outlined text-[18px]">close</span>
 </button>
 </form>
 <?php endif; ?>
 </div>
 </td>
 </tr>
 <?php endforeach; ?>
 <?php endif; ?>
 </tbody>
 </table>
 </div>
 <div class="px-8 py-4 bg-surface-container-low text-[10px] text-on-surface-variant font-black uppercase tracking-widest border-t border-outline-variant/10">
 Total solicitudes: <span class="text-on-surface"><?= count($solicitudes) ?></span>
 </div>
</div>
</main>

<!-- Modal Detalles -->
<div id="detailsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-surface-container-lowest w-full max-w-4xl rounded-3xl shadow-2xl border border-outline-variant/10 transform scale-95 transition-transform duration-300 overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low">
            <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">assignment</span> 
                Detalles de Solicitud
            </h3>
            <button onclick="closeDetailsModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high text-on-surface-variant transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6 overflow-y-auto" id="detailsContent">
            <!-- Se llena por JS -->
        </div>
        <div class="px-6 py-4 border-t border-outline-variant/10 bg-surface-container-low flex justify-end">
            <button onclick="closeDetailsModal()" class="px-6 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-sm rounded-xl transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal Documentos -->
<div id="docsModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-surface-container-lowest w-full max-w-2xl rounded-3xl shadow-2xl border border-outline-variant/10 transform scale-95 transition-transform duration-300 overflow-hidden">
        <div class="px-6 py-5 border-b border-outline-variant/10 flex justify-between items-center bg-surface-container-low">
            <h3 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">folder_open</span> 
                Documentos de Registro
            </h3>
            <button onclick="closeDocsModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high text-on-surface-variant transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="p-6">
            <div id="docsList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Se llena por JS -->
            </div>
            <div id="noDocsMsg" class="hidden text-center py-8 text-on-surface-variant">
                <span class="material-symbols-outlined text-4xl mb-2 opacity-50">description</span>
                <p class="text-sm font-medium">No se encontraron documentos adjuntos para esta solicitud.</p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant/10 bg-surface-container-low flex justify-end">
            <button onclick="closeDocsModal()" class="px-6 py-2 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-bold text-sm rounded-xl transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
function openDocsModal(data) {
    const modal = document.getElementById('docsModal');
    const docsList = document.getElementById('docsList');
    const noDocsMsg = document.getElementById('noDocsMsg');
    
    docsList.innerHTML = '';
    let hasDocs = false;
    
    const docs = [
        { key: 'doc_constancia_fiscal', label: 'Constancia de Situación Fiscal', icon: 'receipt_long' },
        { key: 'doc_licencia_sanitaria', label: 'Licencia Sanitaria / Aviso', icon: 'medical_services' },
        { key: 'doc_comprobante_domicilio', label: 'Comprobante de Domicilio', icon: 'home_pin' },
        { key: 'doc_alta_hacienda', label: 'Alta de Hacienda', icon: 'account_balance' },
        { key: 'doc_identificacion_oficial', label: 'Identificación Oficial', icon: 'badge' },
        { key: 'doc_acta_constitutiva', label: 'Acta Constitutiva', icon: 'gavel' }
    ];
    
    const baseUrl = '../../uploads/documentos_registro/';
    
    docs.forEach(doc => {
        if (data[doc.key]) {
            hasDocs = true;
            docsList.innerHTML += `
                <a href="${baseUrl}${data[doc.key]}" target="_blank" class="flex items-center gap-3 p-4 rounded-xl border border-outline-variant/20 hover:border-primary/50 hover:bg-primary/5 transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface-variant group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">${doc.icon}</span>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-bold text-on-surface truncate">${doc.label}</p>
                        <p class="text-[10px] text-on-surface-variant uppercase tracking-wider mt-0.5 font-medium">Ver documento</p>
                    </div>
                    <span class="material-symbols-outlined text-on-surface-variant opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                </a>
            `;
        }
    });
    
    if (hasDocs) {
        docsList.classList.remove('hidden');
        noDocsMsg.classList.add('hidden');
    } else {
        docsList.classList.add('hidden');
        noDocsMsg.classList.remove('hidden');
    }
    
    modal.classList.remove('hidden');
    // Pequeño delay para la animación
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeDocsModal() {
    const modal = document.getElementById('docsModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function openDetailsModal(data) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('detailsContent');
    
    // Función helper para mostrar campos vacíos como 'N/A'
    const v = (val) => val ? val : '<span class="text-on-surface-variant/50 italic">N/A</span>';

    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Datos Generales -->
            <div class="lg:col-span-3 pb-2 border-b border-outline-variant/10">
                <h4 class="text-sm font-black text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">domain</span> Datos Generales
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Razón Social</span>
                        <p class="text-sm font-medium text-white">${v(data.razon_social)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Nombre Comercial</span>
                        <p class="text-sm font-medium text-white">${v(data.nombre_comercial)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">RFC</span>
                        <p class="text-sm font-medium text-white">${v(data.rfc)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Régimen Fiscal</span>
                        <p class="text-sm font-medium text-white">${v(data.regimen_fiscal)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Representante Legal</span>
                        <p class="text-sm font-medium text-white">${v(data.representante)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Giro</span>
                        <p class="text-sm font-medium text-white">${v(data.giro)}</p>
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div class="pb-2 border-b md:border-b-0 border-outline-variant/10">
                <h4 class="text-sm font-black text-tertiary uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">contact_page</span> Contacto
                </h4>
                <div class="space-y-4">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Persona de Contacto</span>
                        <p class="text-sm font-medium text-white">${v(data.persona_contacto)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Email</span>
                        <p class="text-sm font-medium text-white">${v(data.email)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Teléfono Local</span>
                        <p class="text-sm font-medium text-white">${v(data.telefono_local)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Celular</span>
                        <p class="text-sm font-medium text-white">${v(data.telefono_celular)}</p>
                    </div>
                </div>
            </div>

            <!-- Fiscal y Domicilio -->
            <div class="lg:col-span-2 pb-2 border-outline-variant/10">
                <h4 class="text-sm font-black text-secondary uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">home_pin</span> Fiscal y Logística
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Domicilio Fiscal</span>
                        <p class="text-sm font-medium text-white leading-relaxed">
                            ${v(data.domicilio_fiscal)} ${v(data.colonia)}<br>
                            ${v(data.ciudad)}, ${v(data.estado)} C.P. ${v(data.cp)}
                        </p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Método de Pago Preferente</span>
                        <p class="text-sm font-medium text-white">${v(data.metodo_pago)}</p>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">Uso de CFDI</span>
                        <p class="text-sm font-medium text-white">${v(data.uso_cfdi)}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}
</script>

<?php include('../Includes/footer.php'); ?>
