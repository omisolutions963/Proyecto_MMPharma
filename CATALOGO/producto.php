<?php
$host   = 'localhost';
$port   = 3307;
$dbname = 'mm_pharma';
$user   = 'root';
$pass   = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

// Obtener ID del producto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header('Location: catalogo.php');
    exit;
}

// Buscar el producto
$stmt = $pdo->prepare("
    SELECT p.*, c.nombre as categoria_nombre 
    FROM catalogo_productos p 
    LEFT JOIN catalogo_categorias c ON p.categoria_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    header('Location: catalogo.php');
    exit;
}

// Lógica de visibilidad por tipo de cliente
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$is_cliente_check = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$cliente_tipo_check = $is_cliente_check ? $_SESSION['cliente_tipo'] : 'FARMACIA';

if ($cliente_tipo_check === 'EMPRESA' && $p['solo_empresa'] !== 'SI') {
    header('Location: catalogo.php');
    exit;
}

// Productos relacionados (misma sustancia, diferente id)
$where_rel = ["sustancia LIKE ?", "id != ?"];
$params_rel = ['%' . explode(' ', $p['sustancia'])[0] . '%', $id];

if ($cliente_tipo_check === 'EMPRESA') {
    $where_rel[] = "solo_empresa = 'SI'";
}

$rel_sql = "SELECT * FROM catalogo_productos WHERE " . implode(' AND ', $where_rel) . " LIMIT 4";
$rel = $pdo->prepare($rel_sql);
$rel->execute($params_rel);
$relacionados = $rel->fetchAll(PDO::FETCH_ASSOC);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_cliente = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
$is_admin   = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$is_logged_in = $is_cliente || $is_admin;
$cliente_tipo = $is_cliente ? $_SESSION['cliente_tipo'] : 'FARMACIA';

$precio_campo = 'precio_farmacia';
if ($cliente_tipo === 'DISTRIBUIDORA') $precio_campo = 'precio_distribuidor';
elseif ($cliente_tipo === 'EMPRESA') $precio_campo = 'precio_empresa';

$precio_mostrar = $p[$precio_campo];
?>

<?php
$titulo = htmlspecialchars($p['nombre']); 
$pagina_actual = 'catalogo'; // marca el link activo en el nav
$base = '../';               // si estás en subcarpeta como CATALOGO/
require_once '../includes/header.php';
?>

<!-- ═══ BREADCRUMB ═══ -->
<div class="max-w-[1600px] mx-auto px-12 py-8" data-aos="fade-down">
  <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-slate-400">
    <a href="../INDEX/index.php" class="hover:text-primary transition-colors">Inicio</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <a href="catalogo.php" class="hover:text-primary transition-colors">Catálogo</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-primary font-bold"><?= htmlspecialchars($p['nombre']) ?></span>
  </div>
</div>

<!-- ═══ CONTENIDO PRINCIPAL ═══ -->
<main class="max-w-[1600px] mx-auto px-12 pb-24 relative">
  <div class="<?= !$is_logged_in ? 'filter blur-[10px] opacity-40 select-none pointer-events-none' : '' ?>">
  <div class="grid md:grid-cols-2 gap-10 mb-16">

    <!-- Imagen del producto -->
    <div class="bg-white rounded-[2rem]   flex items-center justify-center min-h-[400px] p-12 relative" data-aos="fade-right">
      <?php if ($p['tipo'] === 'RED FRIA'): ?>
      <span class="absolute top-4 left-4 inline-flex items-center gap-1 px-3 py-1.5 bg-tertiary-container text-white text-xs font-bold rounded-full">
        <span class="material-symbols-outlined text-sm">ac_unit</span>
        Requiere Red Fría
      </span>
      <?php endif; ?>

      <?php if (!empty($p['imagen']) && $p['imagen'] !== 'PENDIENTE'): ?>
        <img src="imagenes/productos/<?= htmlspecialchars($p['imagen']) ?>"
             alt="<?= htmlspecialchars($p['nombre']) ?>"
             class="max-h-64 object-contain">
      <?php else: ?>
        <div class="text-center">
          <span class="material-symbols-outlined text-8xl text-outline/30">medication</span>
          <p class="text-xs text-on-surface-variant mt-3">Imagen próximamente</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Información del producto -->
    <div class="flex flex-col justify-between" data-aos="fade-left">

      <div>
        <!-- Código -->
        <?php if (!empty($p['codigo'])): ?>
        <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-2">
          Código: <?= htmlspecialchars($p['codigo']) ?>
        </p>
        <?php endif; ?>

        <!-- Nombre -->
        <h1 class="text-2xl font-extrabold text-on-surface tracking-tight mb-3 leading-tight">
          <?= htmlspecialchars($p['nombre']) ?>
        </h1>

        <!-- Sustancia activa -->
        <div class="flex items-start gap-2 mb-6">
          <span class="material-symbols-outlined text-secondary text-lg mt-0.5">science</span>
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Sustancia activa</p>
            <p class="text-sm text-on-surface"><?= htmlspecialchars($p['sustancia'] ?? 'No especificada') ?></p>
          </div>
        </div>

        <!-- Aviso Red Fría -->
        <?php if ($p['tipo'] === 'RED FRIA'): ?>
        <div class="bg-tertiary-container/10  rounded-xl p-4 mb-6 flex gap-3">
          <span class="material-symbols-outlined text-tertiary-container text-xl flex-shrink-0">ac_unit</span>
          <div>
            <p class="text-sm font-bold text-tertiary-container mb-1">Producto de Red Fría</p>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Este producto requiere refrigeración. El cliente debe gestionar su propio transporte — ya sea enviando guía prepagada o mandando su transportista al almacén de MM Pharma.
            </p>
          </div>
        </div>
        <?php endif; ?>

        <!-- Precios por nivel de cliente y Controles -->
        <?php if ($is_admin || $is_cliente): ?>
            <?php if ($is_admin): ?>
            <div class="mb-10 animate-reveal">
              <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">visibility</span> Vista de Administrador (Todos los niveles)
              </p>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-primary p-5 rounded-2xl    transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1 opacity-80">Farmacia</p>
                <p class="text-2xl font-black text-white">$<?= number_format($p['precio_farmacia'], 2) ?></p>
            </div>
            <div class="bg-secondary p-5 rounded-2xl    transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1 opacity-80">Distribuidor</p>
                <p class="text-2xl font-black text-white">$<?= number_format($p['precio_distribuidor'], 2) ?></p>
            </div>
            <div class="bg-tertiary p-5 rounded-2xl    transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1 opacity-80">Empresa</p>
                <p class="text-2xl font-black text-white">$<?= number_format($p['precio_empresa'], 2) ?></p>
            </div>
          </div>
            </div>
            <?php else: ?>
            <div class="mb-10 animate-reveal">
              <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-4">Tu precio especial (<?= htmlspecialchars($cliente_tipo) ?>)</p>
              <?php 
                $box_class = 'bg-primary ';
                if ($cliente_tipo === 'DISTRIBUIDORA') $box_class = 'bg-secondary ';
                elseif ($cliente_tipo === 'EMPRESA') $box_class = 'bg-tertiary ';
              ?>
              <div class="<?= $box_class ?> p-6 rounded-2xl   flex items-center justify-between transition-transform hover:-translate-y-1">
                <div>
                    <p class="text-[10px] font-black text-white uppercase tracking-widest mb-1 opacity-80"><?= htmlspecialchars($cliente_tipo) ?></p>
                    <p class="text-4xl font-black text-white">$<?= number_format($precio_mostrar, 2) ?></p>
                </div>
                <span class="material-symbols-outlined text-white text-4xl opacity-30">verified</span>
              </div>
            </div>
            <?php endif; ?>

        <!-- Controles de Carrito (Solo para Clientes) -->
        <?php if ($is_cliente): ?>
        <div class="space-y-4">
          <div class="flex items-end gap-4">
            <div class="flex flex-col">
              <label class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Cantidad</label>
              <div class="flex items-center bg-white border-2 border-slate-200 rounded-xl h-[58px] px-2 w-32">
                <button type="button" onclick="const qty=document.getElementById('qty'); qty.value=Math.max(1, parseInt(qty.value)-1);" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary transition-colors hover:bg-slate-50 rounded-lg">
                  <span class="material-symbols-outlined">remove</span>
                </button>
                <input type="number" id="qty" value="1" min="1" class="w-full text-center font-bold text-slate-700 bg-transparent border-none focus:ring-0 p-0 text-lg">
                <button type="button" onclick="const qty=document.getElementById('qty'); qty.value=parseInt(qty.value)+1;" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-primary transition-colors hover:bg-slate-50 rounded-lg">
                  <span class="material-symbols-outlined">add</span>
                </button>
              </div>
            </div>
            
            <button type="button" onclick="anadirMultipleAlCarrito()" class="flex-1 h-[58px] bg-secondary text-white font-bold rounded-xl  hover:bg-primary hover:-translate-y-1 hover: active:scale-95 transition-all text-base flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
              Añadir al Carrito
            </button>
          </div>
          <a href="catalogo.php" class="w-full h-[50px] bg-white text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm flex items-center justify-center gap-2 ">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al catálogo
          </a>
        </div>
        
        <script>
        function anadirMultipleAlCarrito() {
            try {
                const qty = parseInt(document.getElementById('qty').value) || 1;
                for(let i=0; i<qty; i++) {
                    agregarAlCarrito(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre'])) ?>', <?= (float)$precio_mostrar ?>, '<?= htmlspecialchars(addslashes($p['imagen'] ?? '')) ?>');
                }
            } catch(e) {
                console.error("Error al añadir:", e);
                alert("Error técnico: " + e.message + "\n\nPor favor envíame este mensaje.");
            }
        }
        </script>
        <?php elseif ($is_admin): ?>
        <div class="space-y-4">
          <div class="bg-primary/5 rounded-xl p-5 flex items-center gap-4 ">
            <span class="material-symbols-outlined text-primary text-2xl">admin_panel_settings</span>
            <p class="text-sm text-slate-600 leading-relaxed">
              <strong class="text-primary block mb-1">Modo Administrador</strong>
              Estás visualizando el catálogo como administrador. Las funciones de cotización y carrito están deshabilitadas para este rol.
            </p>
          </div>
          <a href="catalogo.php" class="w-full h-[50px] bg-white text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm flex items-center justify-center gap-2 ">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Volver al catálogo
          </a>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <!-- Acciones (Usuario no logueado) -->
        <div class="space-y-4">
          <div class="bg-blue-50/50 rounded-xl p-5 flex items-center gap-4 ">
            <span class="material-symbols-outlined text-primary text-2xl">lock</span>
            <p class="text-sm text-slate-600 leading-relaxed">
              <strong class="text-primary block mb-1">¿Quieres ver precios y cotizar este producto?</strong>
              Inicia sesión como cliente o solicita acceso para visualizar nuestro catálogo con precios.
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="../LOGIN/login.php"
               class="flex-1 h-[58px] bg-primary text-white font-bold rounded-xl  hover:bg-secondary hover:-translate-y-1 hover: active:scale-95 transition-all text-base flex items-center justify-center">
              Iniciar sesión para acceder
            </a>
            <a href="catalogo.php"
               class="px-8 h-[58px] bg-white text-slate-600 font-bold rounded-xl hover:bg-slate-50 hover:-translate-y-1 transition-all text-base flex items-center justify-center gap-2  ">
              <span class="material-symbols-outlined text-lg">arrow_back</span>
              Volver
            </a>
          </div>
        </div>
        <?php endif; ?>

    </div>
  </div>

  <!-- ═══ PRODUCTOS RELACIONADOS ═══ -->
  <?php if (!empty($relacionados)): ?>
  <div>
    <h2 class="text-lg font-bold text-on-surface mb-6">Productos con sustancia similar</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6" data-aos="fade-up">
      <?php foreach ($relacionados as $r): ?>
      <a href="producto.php?id=<?= $r['id'] ?>"
         class="bg-white rounded-[3rem] transition-all duration-300  p-8 flex flex-col group animate-fade-up min-h-[420px] hover:-translate-y-2 ">
        
        <!-- Contenedor de Imagen -->
        <div class="w-full aspect-square bg-white flex items-center justify-center mb-6 relative group-hover:scale-105 transition-transform duration-500">
          <?php if (!empty($r['imagen']) && $r['imagen'] !== 'PENDIENTE'): ?>
            <img src="imagenes/productos/<?= htmlspecialchars($r['imagen']) ?>"
                 class="w-full h-full object-contain">
          <?php else: ?>
            <span class="material-symbols-outlined text-slate-200 text-7xl">medication</span>
          <?php endif; ?>
          <?php if ($r['tipo'] === 'RED FRIA'): ?>
            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-10 h-10 bg-secondary text-white rounded-full ">
              <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1">ac_unit</span>
            </span>
          <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="flex-1">
          <p class="text-sm font-black text-primary leading-tight mb-2 group-hover:text-secondary transition-colors uppercase tracking-tight">
            <?= htmlspecialchars($r['nombre']) ?>
          </p>
          <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-6">
            <?= htmlspecialchars($r['sustancia'] ?? '') ?>
          </p>
        </div>

        <!-- Precio y Carrito -->
        <div class="flex items-center justify-between">
          <p class="text-xl font-black text-primary">$<?= number_format($r[$precio_campo] ?? $r['precio_farmacia'], 2) ?></p>
          
          <?php if ($is_cliente): ?>
          <button type="button" 
                  onclick="event.preventDefault(); event.stopPropagation(); agregarAlCarrito(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['nombre'])) ?>', <?= (float)($r[$precio_campo] ?? $r['precio_farmacia']) ?>, '<?= htmlspecialchars(addslashes($r['imagen'] ?? '')) ?>')"
                  class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 hover:bg-primary hover:text-white transition-all flex items-center justify-center ">
            <span class="material-symbols-outlined text-2xl">shopping_cart</span>
          </button>
          <?php elseif ($is_admin): ?>
          <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center" title="Modo Administrador">
            <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
          </div>
          <?php else: ?>
          <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">lock</span>
          </div>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  </div>

  <?php if (!$is_logged_in): ?>
  <!-- Overlay CTA para usuarios no registrados -->
  <div class="absolute inset-0 z-40 flex items-center justify-center bg-background/20 backdrop-blur-[2px]">
    <div class="max-w-md w-full mx-4 bg-white p-10 rounded-[2.5rem]   text-center animate-reveal">
      <div class="w-20 h-20 bg-primary/10 rounded-3xl flex items-center justify-center mx-auto mb-6">
        <span class="material-symbols-outlined text-primary text-4xl">lock</span>
      </div>
      <h2 class="text-3xl font-black text-primary tracking-tight mb-4">Detalles exclusivos</h2>
      <p class="text-on-surface-variant font-medium mb-8 leading-relaxed">
        Para ver precios detallados, existencias y poder cotizar este producto, es necesario contar con una cuenta aprobada.
      </p>
      <div class="flex flex-col gap-3">
        <a href="../INDEX/SELECCIÓN_REGISTRO/selección_registro.php" class="w-full py-4 bg-primary text-white font-bold rounded-xl   hover:bg-secondary transition-all">
          Solicitar acceso
        </a>
        <a href="../LOGIN/login.php" class="w-full py-4 bg-transparent text-primary font-bold rounded-xl  hover:bg-primary/5 transition-all">
          Iniciar sesión
        </a>
      </div>
      <a href="catalogo.php" class="inline-block mt-6 text-slate-400 font-bold text-sm hover:text-primary transition-colors">
        Volver al catálogo
      </a>
    </div>
  </div>
  <?php endif; ?>
</main>

<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

</body>
</html>
