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
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= htmlspecialchars($p['nombre']) ?> | MMPharma</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="logos/MMPharma-Isotipo.png">
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "on-tertiary-container": "#39bb6c",
                    "surface-bright": "#f7f9ff",
                    "secondary-container": "#71c0fe",
                    "surface-container-lowest": "#ffffff",
                    "secondary-fixed-dim": "#92ccff",
                    "tertiary-fixed": "#7efba4",
                    "outline-variant": "#c4c6d0",
                    "primary-container": "#1a3a6b",
                    "inverse-primary": "#abc7ff",
                    "surface-container-low": "#edf4ff",
                    "on-secondary-fixed-variant": "#004b73",
                    "surface-dim": "#c6dcf6",
                    "surface-container-highest": "#cfe5ff",
                    "secondary-fixed": "#cce5ff",
                    "secondary": "#006397",
                    "tertiary-container": "#004520",
                    "primary-fixed-dim": "#abc7ff",
                    "on-background": "#051d30",
                    "surface-container": "#e3efff",
                    "on-surface": "#051d30",
                    "on-primary-fixed": "#001b3f",
                    "error-container": "#ffdad6",
                    "surface": "#f7f9ff",
                    "on-secondary": "#ffffff",
                    "on-primary-container": "#89a5dd",
                    "on-secondary-fixed": "#001d31",
                    "surface-container-high": "#d9eaff",
                    "on-secondary-container": "#004d77",
                    "inverse-surface": "#1d3246",
                    "surface-tint": "#415e91",
                    "on-primary-fixed-variant": "#284678",
                    "error": "#ba1a1a",
                    "tertiary": "#002c13",
                    "on-tertiary-fixed": "#00210c",
                    "on-error-container": "#93000a",
                    "primary-fixed": "#d7e2ff",
                    "on-error": "#ffffff",
                    "on-tertiary-fixed-variant": "#005228",
                    "on-tertiary": "#ffffff",
                    "surface-variant": "#cfe5ff",
                    "inverse-on-surface": "#e8f2ff",
                    "primary": "#002451",
                    "tertiary-fixed-dim": "#61de8a",
                    "background": "#f7f9ff",
                    "outline": "#747780",
                    "on-surface-variant": "#43474f",
                    "on-primary": "#ffffff"
                },
                fontFamily: {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"]
                },
                borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
            },
        },
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .clinical-shadow { box-shadow: 0 10px 40px -10px rgba(0, 36, 81, 0.08); }
</style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<!-- ═══ HEADER ═══ -->
<header class="sticky top-0 z-50 bg-[#f7f9ff] shadow-sm">
<nav class="flex justify-between items-center w-full px-8 py-4 max-w-7xl mx-auto font-['Inter'] font-medium text-sm antialiased">
  <div class="flex items-center gap-12">
    <img src="../logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
    <div class="hidden md:flex gap-8">
      <a class="text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200" href="../index.html">Inicio</a>
      <a class="text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200" href="../QUIENES_SOMOS/quienes_somos.html">¿Quiénes somos?</a>
      <a class="text-[#002451] font-semibold border-b-2 border-[#002451] pb-1" href="../CATALOGO/catalogo.php">Catálogo</a>
      <a class="text-[#43474f] hover:text-[#1A3A6B] transition-colors duration-200" href="../CONTACTO/Contacto.html">Contacto</a>
    </div>
  </div>
  <div class="flex items-center gap-4">
    <a href="../LOGIN/login.html"><button class="px-4 py-2 text-[#1A3A6B] font-medium hover:bg-[#edf4ff] rounded-lg transition-all">Iniciar sesión</button></a>
    <a href="../SELECCIÓN_REGISTRO/selección_registro.html"><button class="px-6 py-2 bg-primary text-white font-semibold rounded-lg shadow-sm hover:opacity-90 active:scale-95 transition-all">Solicitar acceso</button></a>
  </div>
</nav>
</header>

<!-- ═══ BREADCRUMB ═══ -->
<div class="max-w-7xl mx-auto px-8 py-4">
  <div class="flex items-center gap-2 text-xs text-on-surface-variant">
    <a href="index.html" class="hover:text-primary transition-colors">Inicio</a>
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
          <a href="LOGIN/login.html"
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
<footer class="bg-[#0D2137] text-white pt-20 pb-10">
  <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
    <div class="space-y-6">
      <img src="logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
      <p class="text-blue-100/60 text-sm leading-relaxed">Distribución farmacéutica con estándares de excelencia. Innovación y compromiso en cada entrega.</p>
      <div class="flex gap-4">
        <a class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-colors" href="#">
          <span class="material-symbols-outlined text-xl">share</span>
        </a>
        <a class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-colors" href="#">
          <span class="material-symbols-outlined text-xl">mail</span>
        </a>
      </div>
    </div>
    <div>
      <h4 class="font-bold text-lg mb-6">Enlaces</h4>
      <ul class="space-y-4 text-blue-100/60 text-sm">
        <li><a class="hover:text-white transition-colors" href="index.html">Inicio</a></li>
        <li><a class="hover:text-white transition-colors" href="QUIENES_SOMOS/quienes_somos.html">Nosotros</a></li>
        <li><a class="hover:text-white transition-colors" href="catalogo.php">Catálogo</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Contacto</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-bold text-lg mb-6">Legal</h4>
      <ul class="space-y-4 text-blue-100/60 text-sm">
        <li><a class="hover:text-white transition-colors" href="#">Aviso de Privacidad</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Términos y Condiciones</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Cumplimiento COFEPRIS</a></li>
        <li><a class="hover:text-white transition-colors" href="#">Política de Devoluciones</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-bold text-lg mb-6">Contacto</h4>
      <ul class="space-y-4 text-blue-100/60 text-sm">
        <li class="flex items-start gap-3">
          <span class="material-symbols-outlined text-secondary-container">location_on</span>
          Av Obsidiana 3644 Residencial Loma Bonita, Zapopan, Jalisco.
        </li>
        <li class="flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary-container">call</span>
          33 4348 0581 / 33 4348 0582
        </li>
        <li class="flex items-center gap-3">
          <span class="material-symbols-outlined text-secondary-container">support_agent</span>
          Soporte en horario de oficina
        </li>
      </ul>
    </div>
  </div>
  <div class="max-w-7xl mx-auto px-8 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100/40">
    <p>© 2026 MMPharma. Todos los derechos reservados.</p>
    <div class="flex gap-8">
      <a class="hover:text-white transition-colors" href="#">Facebook</a>
      <a class="hover:text-white transition-colors" href="#">LinkedIn</a>
      <a class="hover:text-white transition-colors" href="#">Twitter</a>
    </div>
  </div>
</footer>

</body>
</html>