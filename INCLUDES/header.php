<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?= $titulo ?? 'MMPharma | Distribuidora Farmacéutica' ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link rel="icon" type="image/png" href="<?= $base ?? '' ?>logos/MMPharma-Isotipo.png">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<!-- SweetAlert2 para alertas animadas -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "primary": "#003e79",
                    "secondary": "#1e60aa",
                    "tertiary": "#32b4ca",
                    "primary-container": "#e0f2ff",
                    "secondary-container": "#cfe5ff",
                    "tertiary-container": "#d1e4ff",
                    "on-surface": "#001d35",
                    "on-surface-variant": "#003e79",
                    "background": "#f0f7ff",
                    "surface": "#ffffff",
                    "surface-container-low": "#e1f0ff",
                    "surface-container": "#cfe5ff",
                    "surface-container-high": "#abc7ff",
                },
                fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] },
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
    .clinical-shadow { box-shadow: 0 10px 40px -10px rgba(0, 62, 121, 0.1); }
</style>
</head>
<body class="bg-[#f0f7ff] font-body text-on-surface antialiased">

<header class="sticky top-0 z-50 bg-white shadow-[0_4px_30px_rgba(0,0,0,0.03)]">
<nav class="flex justify-between items-center w-full px-12 py-4 max-w-[1600px] mx-auto font-['Inter'] font-medium text-base antialiased">
  <div class="flex-1 flex items-center">
    <a href="<?= $base ?? '' ?>INDEX/index.php">
      <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal.png" alt="MMPharma" class="h-8 w-auto">
    </a>
  </div>

  <div class="hidden md:flex gap-12 text-base">
    <a class="<?= ($pagina_actual ?? '') === 'inicio' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>INDEX/index.php">Inicio</a>
    <a class="<?= ($pagina_actual ?? '') === 'nosotros' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php">¿Quiénes somos?</a>
    <a class="<?= ($pagina_actual ?? '') === 'catalogo' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>CATALOGO/catalogo.php">Catálogo</a>
    <a class="<?= ($pagina_actual ?? '') === 'contacto' ? 'text-primary font-black border-b-2 border-tertiary pb-1' : 'text-slate-600 hover:text-primary transition-colors duration-200 font-bold' ?>" href="<?= $base ?? '' ?>CONTACTO/contacto.php">Contacto</a>
  </div>

  <div class="flex-1 flex items-center justify-end gap-4">
    <?php if (isset($_SESSION['cliente_logged_in']) && $_SESSION['cliente_logged_in'] === true): ?>
        <!-- Icono de carrito (Solo para clientes logueados) -->
        <button id="cart-icon-btn" onclick="toggleCartDrawer()" class="relative w-10 h-10 flex items-center justify-center text-slate-600 hover:text-primary hover:bg-slate-100 rounded-xl transition-all mr-2" aria-label="Carrito de compras">
          <span class="material-symbols-outlined text-2xl">shopping_cart</span>
          <span id="cart-badge" class="hidden absolute -top-1 -right-1 bg-secondary text-white text-[10px] font-black w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">0</span>
        </button>
        
        <!-- Perfil de cliente -->
        <div class="flex items-center gap-3">
          <div class="hidden md:flex flex-col text-right">
            <span class="text-xs font-bold text-slate-900"><?= htmlspecialchars($_SESSION['cliente_nombre'] ?? 'Cliente') ?></span>
            <span class="text-[10px] font-bold text-primary uppercase tracking-widest"><?= htmlspecialchars($_SESSION['cliente_tipo'] ?? 'FARMACIA') ?></span>
          </div>
          <a href="<?= $base ?? '' ?>LOGIN/logout.php" title="Cerrar Sesión">
            <button class="w-10 h-10 flex items-center justify-center text-[#ba1a1a] hover:bg-[#ffdad6] rounded-xl transition-all group" aria-label="Cerrar Sesión">
              <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">logout</span>
            </button>
          </a>
        </div>
    <?php elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <a href="<?= $base ?? '' ?>Admin-mmpharma/dashboard/dashboard.php">
          <button class="px-6 py-2 bg-gradient-to-br from-[#005132] to-[#008151] text-white font-bold rounded-xl shadow-lg hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(0,129,81,0.2)] active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">dashboard</span> Panel de Administrador
          </button>
        </a>
        <a href="<?= $base ?? '' ?>LOGIN/logout.php" title="Cerrar Sesión">
          <button class="w-10 h-10 flex items-center justify-center text-[#ba1a1a] hover:bg-[#ffdad6] rounded-xl transition-all group" aria-label="Cerrar Sesión">
            <span class="material-symbols-outlined text-2xl group-hover:scale-110 transition-transform">logout</span>
          </button>
        </a>
    <?php else: ?>
        <a href="<?= $base ?? '' ?>LOGIN/login_cliente.php">
          <button class="px-4 py-2 text-[#1A3A6B] font-bold hover:bg-[#edf4ff] rounded-xl transition-all">Acceso Clientes</button>
        </a>
        <a href="<?= $base ?? '' ?>SELECCIÓN_REGISTRO/selección_registro.php">
          <button class="px-6 py-2 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-0.5 hover:shadow-[0_0_20px_rgba(0,0,0,0.1)] active:scale-95 transition-all">Solicitar acceso</button>
        </a>
    <?php endif; ?>
  </div>
</nav>
</header>

<!-- ═══ CART DRAWER ═══ -->
<div id="cart-overlay" class="fixed inset-0 bg-slate-900/40 z-[60] opacity-0 pointer-events-none transition-opacity duration-300" onclick="toggleCartDrawer()"></div>
<div id="cart-drawer" class="fixed top-0 right-0 h-full w-full sm:w-[400px] bg-white z-[70] translate-x-full transition-transform duration-300 shadow-[-20px_0_40px_rgba(0,0,0,0.1)] flex flex-col">
  
  <!-- Header -->
  <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-primary/5">
    <div class="flex items-center gap-3 text-primary">
      <span class="material-symbols-outlined text-2xl">shopping_cart</span>
      <h2 class="text-lg font-black tracking-tight">Mi Carrito</h2>
    </div>
    <button onclick="toggleCartDrawer()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>

  <!-- Items -->
  <div id="cart-items-container" class="flex-1 overflow-y-auto p-6 space-y-4">
    <!-- El contenido se genera con JS -->
  </div>

  <!-- Footer -->
  <div class="p-6 border-t border-slate-100 bg-slate-50">
    <div class="flex justify-between items-end mb-4">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">Subtotal</p>
      <p id="cart-subtotal" class="text-2xl font-black text-primary">$0.00</p>
    </div>
    <p class="text-xs text-slate-500 mb-6 leading-relaxed">
      *Los precios mostrados son de lista. Si eres distribuidor o empresa, el precio final se ajustará al generar la cotización formal.
    </p>
    <button onclick="confirmarPedido()" id="btn-confirmar-pedido" class="w-full h-[52px] bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-0.5 hover:shadow-[0_10px_20px_rgba(0,62,121,0.2)] active:scale-95 transition-all text-sm flex items-center justify-center gap-2">
      <span class="material-symbols-outlined text-lg">receipt_long</span>
      Confirmar Pedido
    </button>
  </div>
</div>

<script>
let carrito = [];
try {
    const parsed = JSON.parse(localStorage.getItem('mm_carrito'));
    carrito = Array.isArray(parsed) ? parsed : [];
} catch (e) {
    carrito = [];
    localStorage.removeItem('mm_carrito');
}

function guardarCarrito() {
    localStorage.setItem('mm_carrito', JSON.stringify(carrito));
    actualizarBadge();
}

function actualizarBadge() {
    const badge = document.getElementById('cart-badge');
    const icon = document.getElementById('cart-icon-btn'); // Necesitamos darle un id al boton
    if (!badge) return;
    const totalItems = carrito.reduce((sum, item) => sum + item.cantidad, 0);
    
    if (totalItems > 0) {
        badge.textContent = totalItems;
        badge.classList.remove('hidden');
        // Animacion
        badge.classList.remove('animate-bounce');
        void badge.offsetWidth; // trigger reflow
        badge.classList.add('animate-bounce');
        setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
        
        if(icon) {
            icon.classList.add('scale-110', 'text-primary');
            setTimeout(() => icon.classList.remove('scale-110', 'text-primary'), 300);
        }
    } else {
        badge.classList.add('hidden');
    }
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function renderCartItems() {
    const container = document.getElementById('cart-items-container');
    const subtotalEl = document.getElementById('cart-subtotal');
    if (!container) return;
    
    if (carrito.length === 0) {
        container.innerHTML = `
          <div class="h-full flex flex-col items-center justify-center text-center text-slate-400 opacity-60">
            <span class="material-symbols-outlined text-6xl mb-4">remove_shopping_cart</span>
            <p class="text-sm font-bold">Tu carrito está vacío</p>
          </div>
        `;
        subtotalEl.textContent = '$0.00';
        return;
    }

    let html = '';
    let subtotal = 0;

    carrito.forEach((item, index) => {
        const totalLinea = item.precio * item.cantidad;
        subtotal += totalLinea;
        
        let imagenHtml = '';
        if (item.imagen && item.imagen !== 'PENDIENTE' && item.imagen !== '') {
            imagenHtml = `<img src="<?= $base ?? '' ?>CATALOGO/imagenes/productos/${item.imagen}" class="w-full h-full object-contain p-1">`;
        } else {
            imagenHtml = `<span class="material-symbols-outlined text-slate-300 text-2xl">medication</span>`;
        }

        html += `
        <div class="flex gap-4 p-4 bg-white border border-slate-100 rounded-2xl shadow-sm relative group">
          <div class="w-16 h-16 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0">
            ${imagenHtml}
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="text-xs font-bold text-primary leading-tight mb-1 truncate pr-6" title="${item.nombre}">${item.nombre}</h4>
            <p class="text-sm font-black text-secondary mb-2">${formatCurrency(item.precio)}</p>
            
            <div class="flex items-center gap-3">
              <div class="flex items-center bg-slate-50 rounded-lg border border-slate-200">
                <button onclick="cambiarCantidad(${index}, -1)" class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[16px]">remove</span></button>
                <span class="w-6 text-center text-xs font-bold text-slate-700">${item.cantidad}</span>
                <button onclick="cambiarCantidad(${index}, 1)" class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[16px]">add</span></button>
              </div>
            </div>
          </div>
          <button onclick="eliminarDelCarrito(${index})" class="absolute top-3 right-3 text-slate-300 hover:text-red-500 transition-colors w-6 h-6 flex items-center justify-center rounded-md hover:bg-red-50">
            <span class="material-symbols-outlined text-[18px]">delete</span>
          </button>
        </div>
        `;
    });

    container.innerHTML = html;
    subtotalEl.textContent = formatCurrency(subtotal);
}

function agregarAlCarrito(id, nombre, precio, imagen) {
    console.log("agregarAlCarrito llamado con:", {id, nombre, precio, imagen});
    const itemIndex = carrito.findIndex(item => item.id == id);
    
    if (itemIndex > -1) {
        carrito[itemIndex].cantidad += 1;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: parseFloat(precio),
            imagen: imagen,
            cantidad: 1
        });
    }
    
    guardarCarrito();
    renderCartItems();
    
    // Mostramos una alerta visual (toast) en su lugar para indicar éxito sin interrumpir
    Swal.fire({
        toast: true,
        position: 'bottom-end',
        icon: 'success',
        title: 'Producto añadido',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
}

function cambiarCantidad(index, delta) {
    if (carrito[index]) {
        carrito[index].cantidad += delta;
        if (carrito[index].cantidad <= 0) {
            eliminarDelCarrito(index);
        } else {
            guardarCarrito();
            renderCartItems();
        }
    }
}

function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    guardarCarrito();
    renderCartItems();
}

function toggleCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    const overlay = document.getElementById('cart-overlay');
    if (!drawer || !overlay) return;
    
    if (drawer.classList.contains('translate-x-full')) {
        // Abrir
        drawer.classList.remove('translate-x-full');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        renderCartItems(); // Renderizar al abrir para estar seguros
    } else {
        // Cerrar
        drawer.classList.add('translate-x-full');
        overlay.classList.add('opacity-0', 'pointer-events-none');
    }
}

// Inicializar el badge al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    actualizarBadge();
});
function confirmarPedido() {
    if (carrito.length === 0) {
        Swal.fire('Carrito vacío', 'Añade productos antes de confirmar el pedido', 'warning');
        return;
    }
    
    const btn = document.getElementById('btn-confirmar-pedido');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<div class="inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div> Procesando...';
    btn.disabled = true;

    fetch('<?= $base ?? '' ?>CATALOGO/procesar_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ carrito: carrito })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            carrito = [];
            guardarCarrito();
            renderCartItems();
            toggleCartDrawer();
            
            let folioText = data.folio ? `Folio: ${data.folio}` : '';
            
            Swal.fire({
                html: `
                    <div class="flex flex-col items-center justify-center p-2">
                        <div class="relative w-32 h-32 mb-8 mt-4">
                            <!-- Círculos de fondo con animación -->
                            <div class="absolute inset-0 bg-blue-100 rounded-full animate-ping opacity-70" style="animation-duration: 3s;"></div>
                            <div class="absolute inset-2 bg-blue-200 rounded-full animate-pulse"></div>
                            <!-- Contenedor del ícono -->
                            <div class="absolute inset-4 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center shadow-xl z-10 border-4 border-white">
                                <span class="material-symbols-outlined text-white text-4xl transform -rotate-12 animate-[bounce_2s_infinite]">send</span>
                            </div>
                            <!-- Pequeño reloj indicador -->
                            <div class="absolute -bottom-2 -right-2 bg-white rounded-full p-1 shadow-lg z-20">
                                <div class="bg-amber-400 w-8 h-8 rounded-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">schedule</span>
                                </div>
                            </div>
                        </div>
                        
                        <h2 class="text-3xl font-black text-on-surface tracking-tight mb-2">¡Pedido Enviado!</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">${folioText}</p>
                        
                        <div class="bg-amber-50 border border-amber-100/50 rounded-2xl p-5 w-full text-left relative overflow-hidden group">
                            <div class="absolute top-0 left-0 w-1 h-full bg-amber-400"></div>
                            <div class="flex items-center gap-3 text-amber-700 mb-2">
                                <span class="material-symbols-outlined text-xl">pending_actions</span>
                                <span class="font-bold text-sm uppercase tracking-wider">En espera de autorización</span>
                            </div>
                            <p class="text-xs text-amber-900/70 leading-relaxed font-medium">
                                Tu pedido ha sido recibido exitosamente y se encuentra en revisión. Te notificaremos en cuanto nuestro equipo autorice la solicitud para proceder.
                            </p>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Entendido, volver al catálogo',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[2rem] p-2 border border-slate-100 shadow-[0_20px_60px_rgba(0,0,0,0.1)]',
                    confirmButton: 'w-full h-[52px] bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-secondary hover:-translate-y-0.5 transition-all text-sm mt-4'
                },
                width: '32em',
                allowOutsideClick: false,
                backdrop: `rgba(0, 29, 53, 0.4)`
            }).then(() => {
                window.location.href = '<?= $base ?? '' ?>CATALOGO/catalogo.php';
            });
        } else {
            Swal.fire('Error', 'Hubo un error al procesar tu pedido: ' + (data.message || 'Error desconocido'), 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error de conexión', 'No se pudo procesar el pedido. Verifica tu conexión a internet.', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>