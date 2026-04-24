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
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    header('Location: catalogo.php');
    exit;
}

// Productos relacionados (misma sustancia, diferente id)
$rel = $pdo->prepare("SELECT * FROM productos WHERE sustancia LIKE ? AND id != ? LIMIT 4");
$rel->execute(['%' . explode(' ', $p['sustancia'])[0] . '%', $id]);
$relacionados = $rel->fetchAll(PDO::FETCH_ASSOC);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_cliente = isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true;
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
<main class="max-w-[1600px] mx-auto px-12 pb-24">
  <div class="grid md:grid-cols-2 gap-10 mb-16">

    <!-- Imagen del producto -->
    <div class="bg-white rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.03)] border border-blue-50 flex items-center justify-center min-h-[400px] p-12 relative" data-aos="fade-right">
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
        <div class="bg-tertiary-container/10 border border-tertiary-container/20 rounded-xl p-4 mb-6 flex gap-3">
          <span class="material-symbols-outlined text-tertiary-container text-xl flex-shrink-0">ac_unit</span>
          <div>
            <p class="text-sm font-bold text-tertiary-container mb-1">Producto de Red Fría</p>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Este producto requiere refrigeración. El cliente debe gestionar su propio transporte — ya sea enviando guía prepagada o mandando su transportista al almacén de MM Pharma.
            </p>
          </div>
        </div>
        <?php endif; ?>

        <!-- Precios por nivel de cliente -->
        <?php if ($is_cliente): ?>
        <div class="mb-10">
          <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-4">Tu precio especial (<?= htmlspecialchars($cliente_tipo) ?>)</p>
          <div class="bg-white p-6 rounded-2xl border-2 border-secondary/20 shadow-[0_10px_30px_rgba(0,0,0,0.05)] flex items-center justify-between">
            <p class="text-4xl font-black text-primary">$<?= number_format($precio_mostrar, 2) ?></p>
            <span class="material-symbols-outlined text-secondary text-4xl opacity-20">verified</span>
          </div>
        </div>
        
        <!-- Controles de Carrito -->
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
            
            <button type="button" onclick="anadirMultipleAlCarrito()" class="flex-1 h-[58px] bg-secondary text-white font-bold rounded-xl shadow-lg hover:bg-primary hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(30,96,170,0.3)] active:scale-95 transition-all text-base flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
              Añadir al Carrito
            </button>
          </div>
          <a href="catalogo.php" class="w-full h-[50px] bg-white text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm flex items-center justify-center gap-2 border border-slate-200">
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
        
        <?php else: ?>
        <!-- Acciones (Usuario no logueado) -->
        <div class="space-y-4">
          <div class="bg-blue-50/50 rounded-xl p-5 flex items-center gap-4 border border-blue-100">
            <span class="material-symbols-outlined text-primary text-2xl">lock</span>
            <p class="text-sm text-slate-600 leading-relaxed">
              <strong class="text-primary block mb-1">¿Quieres ver precios y cotizar este producto?</strong>
              Inicia sesión como cliente o solicita acceso para visualizar nuestro catálogo con precios.
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="../LOGIN/login_cliente.php"
               class="flex-1 h-[58px] bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-1 hover:shadow-[0_10px_30px_rgba(0,62,121,0.2)] active:scale-95 transition-all text-base flex items-center justify-center">
              Iniciar sesión para acceder
            </a>
            <a href="catalogo.php"
               class="px-8 h-[58px] bg-white text-slate-600 font-bold rounded-xl hover:bg-slate-50 hover:-translate-y-1 transition-all text-base flex items-center justify-center gap-2 border border-slate-100 shadow-sm">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up">
      <?php foreach ($relacionados as $r): ?>
      <a href="producto.php?id=<?= $r['id'] ?>"
         class="bg-white rounded-2xl p-6 shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)] transition-all border border-blue-50 group flex flex-col">
        <div class="w-14 h-14 bg-blue-50/50 rounded-xl flex items-center justify-center mb-4">
          <?php if (!empty($r['imagen']) && $r['imagen'] !== 'PENDIENTE'): ?>
            <img src="imagenes/productos/<?= htmlspecialchars($r['imagen']) ?>"
                 class="w-full h-full object-contain rounded-lg">
          <?php else: ?>
            <span class="material-symbols-outlined text-outline text-lg">medication</span>
          <?php endif; ?>
        </div>
        <p class="text-sm font-medium text-on-surface leading-tight mb-2 line-clamp-2 group-hover:text-primary transition-colors">
          <?= htmlspecialchars($r['nombre']) ?>
        </p>
        <?php if ($r['tipo'] === 'RED FRIA'): ?>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-tertiary-container text-white text-xs font-bold rounded-full mb-2">
          <span class="material-symbols-outlined text-xs">ac_unit</span> Red Fría
        </span>
        <?php endif; ?>
        <?php if ($is_cliente): ?>
          <p class="text-sm font-black text-primary mt-auto">$<?= number_format($r[$precio_campo] ?? $r['precio_farmacia'], 2) ?></p>
        <?php else: ?>
          <p class="text-[10px] font-bold text-slate-400 mt-auto">Inicia sesión para ver precio</p>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</main>

<!-- ═══ FOOTER ═══ -->
<?php require_once '../includes/footer.php'; ?>

</body>
</html>