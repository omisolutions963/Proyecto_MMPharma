<?php
$host   = 'localhost';
$port   = 3306;
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
?>

<?php
$titulo = htmlspecialchars($p['nombre']); 
$pagina_actual = 'catalogo'; // marca el link activo en el nav
$base = '../';               // si estás en subcarpeta como CATALOGO/
require_once '../includes/header.php';
?>

<!-- ═══ BREADCRUMB ═══ -->
<div class="max-w-7xl mx-auto px-8 py-4">
  <div class="flex items-center gap-2 text-xs text-on-surface-variant">
    <a href="../INDEX/index.php" class="hover:text-primary transition-colors">Inicio</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <a href="catalogo.php" class="hover:text-primary transition-colors">Catálogo</a>
    <span class="material-symbols-outlined text-sm">chevron_right</span>
    <span class="text-on-surface font-medium truncate max-w-xs"><?= htmlspecialchars($p['nombre']) ?></span>
  </div>
</div>

<!-- ═══ CONTENIDO PRINCIPAL ═══ -->
<main class="max-w-7xl mx-auto px-8 pb-16">
  <div class="grid md:grid-cols-2 gap-10 mb-16">

    <!-- Imagen del producto -->
    <div class="bg-surface-container-lowest rounded-2xl clinical-shadow flex items-center justify-center min-h-[320px] p-10 relative">
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
    <div class="flex flex-col justify-between">

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
        <div class="mb-6">
          <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-3">Precios por nivel de cliente</p>
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-surface-container-low p-4 rounded-xl text-center clinical-shadow">
              <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Empresa</p>
              <p class="text-xl font-black text-primary">$<?= number_format($p['precio_empresa'], 2) ?></p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-xl text-center clinical-shadow">
              <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Farmacia</p>
              <p class="text-xl font-black text-primary">$<?= number_format($p['precio_farmacia'], 2) ?></p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-xl text-center clinical-shadow">
              <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Distribuidor</p>
              <p class="text-xl font-black text-primary">$<?= number_format($p['precio_distribuidor'], 2) ?></p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-xl text-center clinical-shadow">
              <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Red Fría</p>
              <p class="text-xl font-black text-primary">$<?= number_format($p['precio_red_fria'], 2) ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Acciones -->
      <div class="space-y-3">
        <div class="bg-surface-container-low rounded-xl p-4 flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary">lock</span>
          <p class="text-sm text-on-surface-variant">
            <strong class="text-on-surface">¿Quieres cotizar este producto?</strong><br>
            Inicia sesión o solicita acceso para generar cotizaciones.
          </p>
        </div>
        <div class="flex gap-3">
          <a href="../LOGIN/login.php"
             class="flex-1 py-3.5 bg-gradient-to-br from-primary to-primary-container text-white font-bold rounded-xl text-center hover:opacity-90 active:scale-95 transition-all text-sm">
            Iniciar sesión para cotizar
          </a>
          <a href="catalogo.php"
             class="px-4 py-3.5 border border-outline-variant/30 text-on-surface-variant font-medium rounded-xl hover:bg-surface-container-low transition-all text-sm text-center">
            ← Volver
          </a>
        </div>
      </div>

    </div>
  </div>

  <!-- ═══ PRODUCTOS RELACIONADOS ═══ -->
  <?php if (!empty($relacionados)): ?>
  <div>
    <h2 class="text-lg font-bold text-on-surface mb-6">Productos con sustancia similar</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ($relacionados as $r): ?>
      <a href="producto.php?id=<?= $r['id'] ?>"
         class="bg-surface-container-lowest rounded-xl p-5 clinical-shadow hover:shadow-xl transition-all border border-transparent hover:border-outline-variant/20 group">
        <div class="w-12 h-12 bg-surface-container-low rounded-lg flex items-center justify-center mb-3">
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
        <p class="text-sm font-black text-primary mt-auto">$<?= number_format($r['precio_farmacia'], 2) ?></p>
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