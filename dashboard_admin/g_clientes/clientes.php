<?php
require_once '../clinical_core/db.php';
$pdo = getDB();

function enviarCorreoBienvenida($email_cliente, $razon_social, $tipo_cliente) {
    $url_login = getAppURL() . '/login/login.php';
    $asunto = "¡Bienvenido a MMPharma! Tu cuenta ha sido activada";
    
    $html = '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f4f7ff;padding:30px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,62,121,.15)">
  <div style="background:#003e79;padding:24px 32px;text-align:center">
    <h1 style="margin:0;color:#fff;font-size:22px">¡Tu cuenta ha sido activada!</h1>
    <p style="margin:6px 0 0;color:#67e8f9;font-size:14px">Bienvenido a MMPharma</p>
  </div>
  <div style="padding:32px;color:#333;line-height:1.6">
    <p style="font-size:16px;font-weight:bold;color:#003e79;margin-top:0">Estimado(a) ' . htmlspecialchars($razon_social) . ',</p>
    <p>Nos complace informarte que tu solicitud de registro ha sido aprobada con éxito. A partir de este momento, ya tienes acceso completo a nuestro catálogo de productos con precios personalizados para tu nivel de cliente.</p>
    
    <div style="background:#f0f5ff;border-radius:8px;padding:20px;margin:24px 0">
      <table style="width:100%;border-collapse:collapse;font-size:14px">
        <tr>
          <td style="padding:6px 0;color:#666;width:120px;font-weight:bold">Usuario/email:</td>
          <td style="color:#003e79;font-weight:bold">' . htmlspecialchars($email_cliente) . '</td>
        </tr>
        <tr>
          <td style="padding:6px 0;color:#666;font-weight:bold">Nivel de cliente:</td>
          <td style="color:#003e79;font-weight:bold">' . htmlspecialchars($tipo_cliente) . '</td>
        </tr>
      </table>
    </div>
    
    <p>Puedes iniciar sesión en el portal utilizando tus credenciales registradas haciendo clic en el siguiente botón:</p>
    
    <div style="text-align:center;margin:32px 0 16px">
      <a href="' . htmlspecialchars($url_login) . '"
         style="display:inline-block;background:#003e79;color:#fff;padding:14px 36px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px;box-shadow:0 4px 10px rgba(0,62,121,0.2)">
        Iniciar sesión en el portal
      </a>
    </div>
    
    <p style="font-size:12px;color:#777;margin-top:40px;border-top:1px solid #eee;padding-top:20px">
      Si tienes alguna duda o requieres asistencia adicional, no dudes en ponerte en contacto con nuestro equipo de atención a clientes.
    </p>
  </div>
  <div style="background:#f0f5ff;padding:16px 32px;text-align:center;font-size:11px;color:#888">
    MMPharma &bull; Notificación automática
  </div>
</div></body></html>';

    require_once __DIR__ . '/../../includes/mailer.php';
    enviarCorreoPHPMailer($email_cliente, $asunto, $html);
}

// ── FILTROS Y PAGINACIÓN ──────────────────────────────────────────────────────
$q = trim($_GET['q'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');
$pg = max(1, (int)($_GET['pg'] ?? 1));
$perPage = 15;
$offset = ($pg - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];
if ($q) {
 $where .= " AND (razon_social LIKE ? OR rfc LIKE ? OR email LIKE ?)";
 $l = "%$q%"; $params[]=$l; $params[]=$l; $params[]=$l;
}
if ($tipo) {
 $where .= " AND tipo = ?";
 $params[] = $tipo;
}

// Datos
$sql = "SELECT * FROM clientes_usuarios $where ORDER BY razon_social ASC LIMIT $perPage OFFSET $offset";
$st = $pdo->prepare($sql);
$st->execute($params);
$clientes = $st->fetchAll();

// ── RESPUESTA AJAX PARA INFINITE SCROLL ────────────────────────────────────────
if (isset($_GET['ajax'])) {
 if (empty($clientes)) die("");
 foreach ($clientes as $c): ?>
 <tr class="group hover:bg-surface-container-low/30 transition-colors">
 <td class="px-8 py-4 text-center">
 <div class="flex flex-col items-center">
 <span class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($c['razon_social']) ?></span>
 <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5"><?= $c['rfc'] ?: 'Sin RFC' ?></span>
 </div>
 </td>
 <td class="px-8 py-4 text-center">
 <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase bg-primary/10 text-primary">
 <?= $c['tipo'] ?>
 </span>
 </td>
 <td class="px-8 py-4 text-center">
 <div class="flex flex-col items-center">
 <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($c['persona_contacto'] ?: 'No asignado') ?></span>
 <span class="text-[10px] text-on-surface-variant"><?= $c['email'] ?></span>
 </div>
 </td>
 <td class="px-8 py-4 text-center">
 <?php
 $stColor = match($c['estatus']){
 'ACTIVO' => 'bg-tertiary-container/20 text-on-tertiary-container',
 'INACTIVO' => 'bg-error-container/20 text-error',
 default => 'bg-surface-container-high text-on-surface-variant'
 };
 ?>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $stColor ?>">
 <?= $c['estatus'] ?>
 </span>
 </td>
 <td class="px-8 py-4">
 <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
 <a href="ver_cliente.php?id=<?= $c['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-tertiary hover:bg-tertiary hover:text-white transition-all" title="Ver detalles">
 <span class="material-symbols-outlined text-[18px]">visibility</span>
 </a>
 <button onclick='abrirEditar(<?= json_encode($c) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
 <span class="material-symbols-outlined text-[18px]">edit</span>
 </button>
 <form method="POST" onsubmit="return confirm('¿Eliminar cliente?')">
 <input type="hidden" name="action" value="delete">
 <input type="hidden" name="id" value="<?= $c['id'] ?>">
 <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
 <span class="material-symbols-outlined text-[18px]">delete</span>
 </button>
 </form>
 </div>
 </td>
 </tr>
 <?php endforeach;
 exit;
}

// ── ACCIONES POST (UPSERT/DELETE) ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
 $action = $_POST['action'];
 $id = (int)($_POST['id'] ?? 0);
 if ($action === 'delete' && $id) {
  try {
  $pdo->prepare("DELETE FROM clientes_usuarios WHERE id = ?")->execute([$id]);
  header("Location: clientes.php?msg=deleted"); exit;
  } catch (PDOException $e) {
  header("Location: clientes.php?err=fk"); exit;
  }
 }

 if ($action === 'upsert') {
  $tipo = $_POST['tipo'] ?? 'FARMACIA';
  $razon_social = $_POST['razon_social'] ?? '';
  $nombre_comercial = $_POST['nombre_comercial'] ?? '';
  $rfc = $_POST['rfc'] ?? '';
  $regimen_fiscal = $_POST['regimen_fiscal'] ?? '';
  $domicilio_fiscal = $_POST['domicilio_fiscal'] ?? '';
  $colonia_fiscal = $_POST['colonia_fiscal'] ?? '';
  $cp_fiscal = $_POST['cp_fiscal'] ?? '';
  $ciudad_fiscal = $_POST['ciudad_fiscal'] ?? '';
  $estado_fiscal = $_POST['estado_fiscal'] ?? '';
  $representante_legal = $_POST['representante_legal'] ?? '';
  $giro = $_POST['giro'] ?? '';
  $persona_contacto = $_POST['persona_contacto'] ?? '';
  $volumen_mensual = (float)($_POST['volumen_mensual'] ?? 0.0);
  $telefono_local = $_POST['telefono_local'] ?? '';
  $telefono_celular = $_POST['telefono_celular'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $documento_tipo = $_POST['documento_tipo'] ?? 'FACTURA';
  $metodo_pago = $_POST['metodo_pago'] ?? 'TRANSFERENCIA';
  $uso_cfdi = $_POST['uso_cfdi'] ?? '';
  $domicilio_entrega = $_POST['domicilio_entrega'] ?? '';
  $colonia_entrega = $_POST['colonia_entrega'] ?? '';
  $cp_entrega = $_POST['cp_entrega'] ?? '';
  $ciudad_entrega = $_POST['ciudad_entrega'] ?? '';
  $municipio_entrega = $_POST['municipio_entrega'] ?? '';
  $estado_entrega = $_POST['estado_entrega'] ?? '';
  $receptor_entrega = $_POST['receptor_entrega'] ?? '';
  $horario_entrega = $_POST['horario_entrega'] ?? '';
  $estatus = $_POST['estatus'] ?? 'DOCS_PENDIENTES';
  $notas = $_POST['notas'] ?? '';

  if ($id > 0) {
    // Obtener estatus anterior para verificar si se activa
    $stmtOld = $pdo->prepare("SELECT estatus, email, razon_social, tipo FROM clientes_usuarios WHERE id = ?");
    $stmtOld->execute([$id]);
    $oldCli = $stmtOld->fetch(PDO::FETCH_ASSOC);

    $sql = "UPDATE clientes_usuarios SET 
        tipo = ?, razon_social = ?, nombre_comercial = ?, rfc = ?, regimen_fiscal = ?, 
        domicilio_fiscal = ?, colonia_fiscal = ?, cp_fiscal = ?, ciudad_fiscal = ?, estado_fiscal = ?, 
        representante_legal = ?, giro = ?, persona_contacto = ?, volumen_mensual = ?, 
        telefono_local = ?, telefono_celular = ?, email = ?, documento_tipo = ?, 
        metodo_pago = ?, uso_cfdi = ?, domicilio_entrega = ?, colonia_entrega = ?, 
        cp_entrega = ?, ciudad_entrega = ?, municipio_entrega = ?, estado_entrega = ?, 
        receptor_entrega = ?, horario_entrega = ?, estatus = ?, notas = ?";
    $params = [
     $tipo, $razon_social, $nombre_comercial, $rfc, $regimen_fiscal,
     $domicilio_fiscal, $colonia_fiscal, $cp_fiscal, $ciudad_fiscal, $estado_fiscal,
     $representante_legal, $giro, $persona_contacto, $volumen_mensual,
     $telefono_local, $telefono_celular, $email, $documento_tipo,
     $metodo_pago, $uso_cfdi, $domicilio_entrega, $colonia_entrega,
     $cp_entrega, $ciudad_entrega, $municipio_entrega, $estado_entrega,
     $receptor_entrega, $horario_entrega, $estatus, $notas
    ];

    if (!empty($password)) {
     $sql .= ", password_hash = ?";
     $params[] = password_hash($password, PASSWORD_DEFAULT);
    }
    $sql .= " WHERE id = ?";
    $params[] = $id;

    $pdo->prepare($sql)->execute($params);

    if ($oldCli && $oldCli['estatus'] !== 'ACTIVO' && $estatus === 'ACTIVO') {
        enviarCorreoBienvenida($oldCli['email'], $oldCli['razon_social'], $oldCli['tipo']);
    }
  } else {
    if (empty($password)) {
     $password = 'cliente123';
    }
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO clientes_usuarios (
        tipo, razon_social, nombre_comercial, rfc, regimen_fiscal, 
        domicilio_fiscal, colonia_fiscal, cp_fiscal, ciudad_fiscal, estado_fiscal, 
        representante_legal, giro, persona_contacto, volumen_mensual, 
        telefono_local, telefono_celular, email, password_hash, documento_tipo, 
        metodo_pago, uso_cfdi, domicilio_entrega, colonia_entrega, 
        cp_entrega, ciudad_entrega, municipio_entrega, estado_entrega, 
        receptor_entrega, horario_entrega, estatus, notas
      ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $params = [
     $tipo, $razon_social, $nombre_comercial, $rfc, $regimen_fiscal,
     $domicilio_fiscal, $colonia_fiscal, $cp_fiscal, $ciudad_fiscal, $estado_fiscal,
     $representante_legal, $giro, $persona_contacto, $volumen_mensual,
     $telefono_local, $telefono_celular, $email, $password_hash, $documento_tipo,
     $metodo_pago, $uso_cfdi, $domicilio_entrega, $colonia_entrega,
     $cp_entrega, $ciudad_entrega, $municipio_entrega, $estado_entrega,
     $receptor_entrega, $horario_entrega, $estatus, $notas
    ];

    $pdo->prepare($sql)->execute($params);

    if ($estatus === 'ACTIVO') {
        enviarCorreoBienvenida($email, $razon_social, $tipo);
    }
  }
  header("Location: clientes.php?msg=saved"); exit;
 }
}

$pageTitle = "MMPharma Portal - Gestión de clientes";
$activePage = "clientes";
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<main class="ml-64 p-8 min-h-screen bg-background text-on-surface">

<!-- Header -->
<div class="flex justify-between items-end mb-8 animate-reveal">
 <div>
 <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
 <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
 <span class="material-symbols-outlined text-[12px]">chevron_right</span>
 <span class="text-on-surface-variant">Clientes</span>
 </nav>
 <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Gestión de clientes</h2>
 <p class="text-on-surface-variant text-sm mt-1">Directorio de farmacias, distribuidoras y empresas.</p>
 </div>
 <button onclick="abrirModal()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold hover:opacity-90 transition-all">
 <span class="material-symbols-outlined text-[18px]">person_add</span> Nuevo cliente
 </button>
</div>

<!-- KPIs -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
 <?php
 $total_c = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios")->fetchColumn();
 $activos = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='ACTIVO'")->fetchColumn();
 $pendien = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='DOCS_PENDIENTES'")->fetchColumn();
 $inactiv = (int)$pdo->query("SELECT COUNT(*) FROM clientes_usuarios WHERE estatus='INACTIVO'")->fetchColumn();
 $kpis = [
 ['l'=>'Total clientes', 'v'=>$total_c, 'i'=>'group', 'b'=>'border-primary/40'],
 ['l'=>'Activos', 'v'=>$activos, 'i'=>'check_circle', 'b'=>'border-tertiary/40'],
 ['l'=>'Pendientes', 'v'=>$pendien, 'i'=>'pending', 'b'=>'border-secondary/40'],
 ['l'=>'Inactivos', 'v'=>$inactiv, 'i'=>'cancel', 'b'=>'border-error/40'],
 ];
 foreach($kpis as $index => $k): ?>
 <div class="bg-surface-container-lowest p-5 rounded-2xl border-l-4 <?= $k['b'] ?> animate-reveal" style="animation-delay: <?= $index * 0.1 ?>s">
 <div class="flex justify-between items-center mb-1">
 <span class="text-[10px] font-black uppercase tracking-widest text-on-surface-variant"><?= $k['l'] ?></span>
 <span class="material-symbols-outlined text-on-surface-variant/30 scale-75"><?= $k['i'] ?></span>
 </div>
 <h3 class="text-2xl font-black text-on-surface"><?= number_format($k['v']) ?></h3>
 </div>
 <?php endforeach; ?>
</div>

<!-- Filtros -->
<form method="GET" class="bg-surface-container-low p-4 rounded-2xl flex items-center gap-4 mb-8 animate-reveal" style="animation-delay: 0.35s">
 <div class="flex-1 relative">
 <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">search</span>
 <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar cliente, RFC o email..." class="w-full bg-white border-none rounded-xl py-3 pl-12 pr-4 text-sm text-slate-800 focus:ring-2 focus:ring-primary outline-none "/>
 </div>
 <select name="tipo" class="bg-white border-none rounded-xl py-3 px-4 text-sm text-slate-800 focus:ring-2 focus:ring-primary outline-none w-48 font-bold">
 <option value="">Todos los tipos</option>
 <option value="FARMACIA" <?= $tipo==='FARMACIA'?'selected':'' ?>>Farmacia</option>
 <option value="DISTRIBUIDORA" <?= $tipo==='DISTRIBUIDORA'?'selected':'' ?>>Distribuidora</option>
 <option value="EMPRESA" <?= $tipo==='EMPRESA'?'selected':'' ?>>Empresa</option>
 </select>
 <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:opacity-90 transition-opacity ">Filtrar</button>
</form>

<!-- Tabla Centrada -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/10 overflow-hidden animate-reveal" style="animation-delay: 0.4s">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
 <th class="px-8 py-4 text-center">Socio comercial</th>
 <th class="px-8 py-4 text-center">Tipo</th>
 <th class="px-8 py-4 text-center">Contacto</th>
 <th class="px-8 py-4 text-center">Estatus</th>
 <th class="px-8 py-4 text-center">Acciones</th>
 </tr>
 </thead>
 <tbody id="tableBody" class="divide-y divide-outline-variant/10">
 <?php if (empty($clientes)): ?>
 <tr><td colspan="5" class="px-8 py-20 text-center text-on-surface-variant text-sm font-medium italic animate-reveal">No se encontraron clientes.</td></tr>
 <?php else: ?>
 <?php foreach ($clientes as $c): ?>
 <tr class="group hover:bg-surface-container-low/30 transition-colors animate-fade-in">
 <td class="px-8 py-4 text-center">
 <div class="flex flex-col items-center">
 <span class="text-sm font-bold text-on-surface leading-tight"><?= htmlspecialchars($c['razon_social']) ?></span>
 <span class="text-[10px] text-on-surface-variant font-bold uppercase mt-0.5"><?= $c['rfc'] ?: 'Sin RFC' ?></span>
 </div>
 </td>
 <td class="px-8 py-4 text-center">
 <span class="inline-flex px-2 py-1 rounded text-[10px] font-black uppercase bg-primary/10 text-primary">
 <?= $c['tipo'] ?>
 </span>
 </td>
 <td class="px-8 py-4 text-center">
 <div class="flex flex-col items-center">
 <span class="text-xs font-bold text-on-surface"><?= htmlspecialchars($c['persona_contacto'] ?: 'No asignado') ?></span>
 <span class="text-[10px] text-on-surface-variant"><?= $c['email'] ?></span>
 </div>
 </td>
 <td class="px-8 py-4 text-center">
 <?php
 $stColor = match($c['estatus']){
 'ACTIVO' => 'bg-tertiary-container/20 text-on-tertiary-container',
 'INACTIVO' => 'bg-error-container/20 text-error',
 default => 'bg-surface-container-high text-on-surface-variant'
 };
 ?>
 <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase <?= $stColor ?>">
 <?= $c['estatus'] ?>
 </span>
 </td>
 <td class="px-8 py-4">
 <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
 <a href="ver_cliente.php?id=<?= $c['id'] ?>" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-tertiary hover:bg-tertiary hover:text-white transition-all" title="Ver detalles">
 <span class="material-symbols-outlined text-[18px]">visibility</span>
 </a>
 <button onclick='abrirEditar(<?= json_encode($c) ?>)' class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
 <span class="material-symbols-outlined text-[18px]">edit</span>
 </button>
 <form method="POST" onsubmit="return confirm('¿Eliminar cliente?')">
 <input type="hidden" name="action" value="delete">
 <input type="hidden" name="id" value="<?= $c['id'] ?>">
 <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
 <span class="material-symbols-outlined text-[18px]">delete</span>
 </button>
 </form>
 </div>
 </td>
 </tr>
 <?php endforeach; ?>
 <?php endif; ?>
 </tbody>
 </table>
  </div>
  <!-- Loading Indicator -->
  <div id="loading" class="hidden px-8 py-6 text-center">
    <div class="inline-block w-6 h-6 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
  </div>
 </div>

<!-- SweetAlert Notificaciones -->
<?php if (isset($_GET['msg'])): ?>
<script>
  Swal.fire({
    title: '¡Operación Exitosa!',
    text: '<?= $_GET['msg'] === 'deleted' ? 'El cliente ha sido eliminado.' : 'El cliente ha sido guardado correctamente.' ?>',
    icon: 'success',
    confirmButtonColor: '#003e79',
    background: '#ffffff',
    color: '#0f172a'
  });
</script>
<?php endif; ?>
<?php if (isset($_GET['err'])): ?>
<script>
  Swal.fire({
    title: 'Error',
    text: '<?= $_GET['err'] === 'fk' ? 'No se puede eliminar el cliente porque tiene registros relacionados.' : 'Ocurrió un error inesperado.' ?>',
    icon: 'error',
    confirmButtonColor: '#003e79',
    background: '#ffffff',
    color: '#0f172a'
  });
</script>
<?php endif; ?>

<!-- MODAL CLIENTE CON PESTAÑAS -->
<div id="modalCliente" class="fixed inset-0 z-[100] hidden">
  <div onclick="cerrarModal()" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
  <div id="modalPanel" class="absolute right-0 top-0 h-full w-full max-w-4xl bg-surface transition-transform duration-300 translate-x-full flex flex-col border-l border-outline-variant/30 shadow-2xl">
    <div class="px-8 py-6 border-b border-outline-variant/10 bg-primary/5">
      <h3 id="modalTitle" class="text-xl font-black text-on-surface tracking-tight">Nuevo cliente</h3>
      <p class="text-on-surface-variant text-xs mt-1">Registra o actualiza la información del socio comercial.</p>
    </div>
    <form method="POST" class="flex-1 overflow-y-auto p-8 space-y-6">
      <input type="hidden" name="action" value="upsert">
      <input type="hidden" name="id" id="cli_id">
 
      <!-- Navegación de Pestañas -->
      <div class="flex items-center gap-6 border-b border-outline-variant/20 mb-6 pb-2">
        <button type="button" id="tabBtn_generales" onclick="cambiarPestana('generales')" class="tab-btn pb-2 text-sm font-bold text-primary border-b-2 border-primary transition-all">Datos generales</button>
        <button type="button" id="tabBtn_facturacion" onclick="cambiarPestana('facturacion')" class="tab-btn pb-2 text-sm font-bold text-on-surface-variant/60 hover:text-white border-b-2 border-transparent transition-all">Facturación (CFDI)</button>
        <button type="button" id="tabBtn_entrega" onclick="cambiarPestana('entrega')" class="tab-btn pb-2 text-sm font-bold text-on-surface-variant/60 hover:text-white border-b-2 border-transparent transition-all">Entrega y notas</button>
      </div>

      <!-- TAB: GENERALES -->
      <div id="tab_generales" class="tab-content space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Razón social *</label>
            <input type="text" name="razon_social" id="cli_razon_social" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Nombre comercial</label>
            <input type="text" name="nombre_comercial" id="cli_nombre_comercial" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">RFC</label>
            <input type="text" name="rfc" id="cli_rfc" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tipo de cliente *</label>
            <select name="tipo" id="cli_tipo" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface font-bold">
              <option value="FARMACIA">Farmacia</option>
              <option value="DISTRIBUIDORA">Distribuidora</option>
              <option value="EMPRESA">Empresa</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Estatus *</label>
            <select name="estatus" id="cli_estatus" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface font-bold">
              <option value="ACTIVO">Activo</option>
              <option value="INACTIVO">Inactivo</option>
              <option value="DOCS_PENDIENTES">Docs Pendientes</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Volumen mensual ($)</label>
            <input type="number" step="0.01" name="volumen_mensual" id="cli_volumen_mensual" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface font-bold">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Giro comercial</label>
            <input type="text" name="giro" id="cli_giro" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Representante legal</label>
            <input type="text" name="representante_legal" id="cli_representante_legal" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Persona de contacto</label>
            <input type="text" name="persona_contacto" id="cli_persona_contacto" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Email profesional *</label>
            <input type="email" name="email" id="cli_email" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Teléfono local</label>
            <input type="text" name="telefono_local" id="cli_telefono_local" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Teléfono celular</label>
            <input type="text" name="telefono_celular" id="cli_telefono_celular" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div class="col-span-2 bg-primary/10 border border-primary/20 p-4 rounded-2xl">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-1">Contraseña de acceso</label>
            <span class="text-[9px] text-on-surface-variant/60 block mb-2 font-medium">Para clientes nuevos, si se deja en blanco se asignará 'cliente123'. Para clientes existentes, se actualiza solo si digita un nuevo valor.</span>
            <input type="password" name="password" id="cli_password" class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
        </div>
      </div>

      <!-- TAB: FACTURACION -->
      <div id="tab_facturacion" class="tab-content hidden space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Régimen fiscal</label>
            <input type="text" name="regimen_fiscal" id="cli_regimen_fiscal" placeholder="Ej. 601 - General de Ley Personas Morales" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Domicilio fiscal</label>
            <input type="text" name="domicilio_fiscal" id="cli_domicilio_fiscal" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Colonia fiscal</label>
            <input type="text" name="colonia_fiscal" id="cli_colonia_fiscal" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">CP fiscal</label>
            <input type="text" name="cp_fiscal" id="cli_cp_fiscal" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Ciudad fiscal</label>
            <input type="text" name="ciudad_fiscal" id="cli_ciudad_fiscal" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Estado fiscal</label>
            <input type="text" name="estado_fiscal" id="cli_estado_fiscal" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tipo de documento</label>
            <select name="documento_tipo" id="cli_documento_tipo" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface font-bold">
              <option value="FACTURA">Factura</option>
              <option value="NOTA">Nota</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Método de pago</label>
            <select name="metodo_pago" id="cli_metodo_pago" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface font-bold">
              <option value="TRANSFERENCIA">Transferencia</option>
              <option value="CHEQUE">Cheque</option>
              <option value="EFECTIVO">Efectivo</option>
            </select>
          </div>
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Uso de CFDI</label>
            <input type="text" name="uso_cfdi" id="cli_uso_cfdi" placeholder="Ej. G03 - Gastos en general" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
        </div>
      </div>

      <!-- TAB: ENTREGA -->
      <div id="tab_entrega" class="tab-content hidden space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Domicilio de entrega</label>
            <input type="text" name="domicilio_entrega" id="cli_domicilio_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Colonia de entrega</label>
            <input type="text" name="colonia_entrega" id="cli_colonia_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">CP de entrega</label>
            <input type="text" name="cp_entrega" id="cli_cp_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Ciudad de entrega</label>
            <input type="text" name="ciudad_entrega" id="cli_ciudad_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Municipio de entrega</label>
            <input type="text" name="municipio_entrega" id="cli_municipio_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Estado de entrega</label>
            <input type="text" name="estado_entrega" id="cli_estado_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Persona que recibe</label>
            <input type="text" name="receptor_entrega" id="cli_receptor_entrega" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Horario de entrega</label>
            <input type="text" name="horario_entrega" id="cli_horario_entrega" placeholder="Ej. Lunes a Viernes 09:00 - 18:00" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface">
          </div>
          <div class="col-span-2">
            <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Notas internas</label>
            <textarea name="notas" id="cli_notas" rows="3" class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none text-on-surface"></textarea>
          </div>
        </div>
      </div>

      <div class="flex gap-4 pt-4 sticky bottom-0 bg-surface">
        <button type="button" onclick="cerrarModal()" class="flex-1 py-4 text-xs font-bold text-on-surface-variant hover:bg-surface-container-low rounded-xl transition-all">Cancelar</button>
        <button type="submit" class="flex-1 py-4 bg-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-[18px]">save</span> Guardar
        </button>
      </div>
    </form>
  </div>
</div>

</main>

<script>
let currentPage = 1;
let loading = false;
let hasMore = true;

window.addEventListener('scroll', () => {
 if (loading || !hasMore) return;
 if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
 loadMore();
 }
});

async function loadMore() {
 loading = true;
 document.getElementById('loading').classList.remove('hidden');
 currentPage++;
 try {
 const response = await fetch(`clientes.php?ajax=1&pg=${currentPage}&q=<?= urlencode($q) ?>&tipo=<?= urlencode($tipo) ?>`);
 const html = await response.text();
 if (html.trim() === "") { hasMore = false; } 
 else { document.getElementById('tableBody').insertAdjacentHTML('beforeend', html); }
 } catch (e) { console.error("Error", e); } 
 finally {
 loading = false;
 document.getElementById('loading').classList.add('hidden');
 }
}

function abrirModal() {
  document.getElementById('modalTitle').textContent = "Nuevo cliente";
  document.getElementById('cli_id').value = "0";
  document.getElementById('cli_razon_social').value = "";
  document.getElementById('cli_nombre_comercial').value = "";
  document.getElementById('cli_rfc').value = "";
  document.getElementById('cli_tipo').value = "FARMACIA";
  document.getElementById('cli_estatus').value = "DOCS_PENDIENTES";
  document.getElementById('cli_volumen_mensual').value = "0";
  document.getElementById('cli_giro').value = "";
  document.getElementById('cli_representante_legal').value = "";
  document.getElementById('cli_persona_contacto').value = "";
  document.getElementById('cli_email').value = "";
  document.getElementById('cli_telefono_local').value = "";
  document.getElementById('cli_telefono_celular').value = "";
  document.getElementById('cli_password').value = "";
  document.getElementById('cli_regimen_fiscal').value = "";
  document.getElementById('cli_domicilio_fiscal').value = "";
  document.getElementById('cli_colonia_fiscal').value = "";
  document.getElementById('cli_cp_fiscal').value = "";
  document.getElementById('cli_ciudad_fiscal').value = "";
  document.getElementById('cli_estado_fiscal').value = "";
  document.getElementById('cli_documento_tipo').value = "FACTURA";
  document.getElementById('cli_metodo_pago').value = "TRANSFERENCIA";
  document.getElementById('cli_uso_cfdi').value = "";
  document.getElementById('cli_domicilio_entrega').value = "";
  document.getElementById('cli_colonia_entrega').value = "";
  document.getElementById('cli_cp_entrega').value = "";
  document.getElementById('cli_ciudad_entrega').value = "";
  document.getElementById('cli_municipio_entrega').value = "";
  document.getElementById('cli_estado_entrega').value = "";
  document.getElementById('cli_receptor_entrega').value = "";
  document.getElementById('cli_horario_entrega').value = "";
  document.getElementById('cli_notas').value = "";
  
  cambiarPestana('generales');
  document.getElementById('modalCliente').classList.remove('hidden');
  setTimeout(() => document.getElementById('modalPanel').classList.remove('translate-x-full'), 10);
}

function abrirEditar(c) {
  document.getElementById('modalTitle').textContent = "Editar cliente";
  document.getElementById('cli_id').value = c.id;
  document.getElementById('cli_razon_social').value = c.razon_social || '';
  document.getElementById('cli_nombre_comercial').value = c.nombre_comercial || '';
  document.getElementById('cli_rfc').value = c.rfc || '';
  document.getElementById('cli_tipo').value = c.tipo || 'FARMACIA';
  document.getElementById('cli_estatus').value = c.estatus || 'DOCS_PENDIENTES';
  document.getElementById('cli_volumen_mensual').value = c.volumen_mensual || '0';
  document.getElementById('cli_giro').value = c.giro || '';
  document.getElementById('cli_representante_legal').value = c.representante_legal || '';
  document.getElementById('cli_persona_contacto').value = c.persona_contacto || '';
  document.getElementById('cli_email').value = c.email || '';
  document.getElementById('cli_telefono_local').value = c.telefono_local || '';
  document.getElementById('cli_telefono_celular').value = c.telefono_celular || '';
  document.getElementById('cli_password').value = "";
  document.getElementById('cli_regimen_fiscal').value = c.regimen_fiscal || '';
  document.getElementById('cli_domicilio_fiscal').value = c.domicilio_fiscal || '';
  document.getElementById('cli_colonia_fiscal').value = c.colonia_fiscal || '';
  document.getElementById('cli_cp_fiscal').value = c.cp_fiscal || '';
  document.getElementById('cli_ciudad_fiscal').value = c.ciudad_fiscal || '';
  document.getElementById('cli_estado_fiscal').value = c.estado_fiscal || '';
  document.getElementById('cli_documento_tipo').value = c.documento_tipo || 'FACTURA';
  document.getElementById('cli_metodo_pago').value = c.metodo_pago || 'TRANSFERENCIA';
  document.getElementById('cli_uso_cfdi').value = c.uso_cfdi || '';
  document.getElementById('cli_domicilio_entrega').value = c.domicilio_entrega || '';
  document.getElementById('cli_colonia_entrega').value = c.colonia_entrega || '';
  document.getElementById('cli_cp_entrega').value = c.cp_entrega || '';
  document.getElementById('cli_ciudad_entrega').value = c.ciudad_entrega || '';
  document.getElementById('cli_municipio_entrega').value = c.municipio_entrega || '';
  document.getElementById('cli_estado_entrega').value = c.estado_entrega || '';
  document.getElementById('cli_receptor_entrega').value = c.receptor_entrega || '';
  document.getElementById('cli_horario_entrega').value = c.horario_entrega || '';
  document.getElementById('cli_notas').value = c.notas || '';
  
  cambiarPestana('generales');
  document.getElementById('modalCliente').classList.remove('hidden');
  setTimeout(() => document.getElementById('modalPanel').classList.remove('translate-x-full'), 10);
}

function cerrarModal() {
  document.getElementById('modalPanel').classList.add('translate-x-full');
  setTimeout(() => document.getElementById('modalCliente').classList.add('hidden'), 300);
}

function cambiarPestana(tabId) {
  document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.remove('text-primary', 'border-primary');
    btn.classList.add('text-on-surface-variant/60', 'border-transparent');
  });

  document.getElementById('tab_' + tabId).classList.remove('hidden');
  const activeBtn = document.getElementById('tabBtn_' + tabId);
  activeBtn.classList.remove('text-on-surface-variant/60', 'border-transparent');
  activeBtn.classList.add('text-primary', 'border-primary');
}
</script>


<?php include("../includes/footer.php"); ?>
