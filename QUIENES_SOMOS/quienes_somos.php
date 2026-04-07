<?php
$titulo = '¿Quiénes somos? | MMPharma';
$pagina_actual = 'nosotros';
$base = '../';
require_once '../includes/header.php';
?>

<!-- ── HERO ── -->
<section class="relative min-h-[369px] flex items-center overflow-hidden">
  <img src="../IMG/3.WEBP" class="absolute inset-0 w-full h-full object-cover" alt="MMPharma instalaciones">
  <div class="absolute inset-0 bg-[#002451] opacity-80"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-8 py-24 w-full">
    <h1 class="text-5xl md:text-6xl font-black tracking-tight leading-tight text-white mb-4">
      Quiénes somos
    </h1>
    <p class="text-lg text-blue-100/90 max-w-2xl leading-relaxed">
      Más de 10 años conectando a la industria farmacéutica con soluciones de distribución de alta precisión desde Zapopan, Jalisco.
    </p>
  </div>
</section>

<main>

<!-- ── NUESTRA HISTORIA ── -->
<section class="py-24 bg-surface">
  <div class="max-w-7xl mx-auto px-8">
    <div class="grid md:grid-cols-2 gap-16 items-center">

      <!-- Imagen -->
      <div class="relative">
        <div class="aspect-square rounded-2xl overflow-hidden clinical-shadow">
          <img class="w-full h-full object-cover"
               src="../IMG/1.WEBP"
               alt="Almacén MMPharma">
        </div>
        <div class="absolute -bottom-6 -right-6 bg-white p-8 rounded-2xl clinical-shadow hidden md:block border border-outline-variant/10">
          <div class="text-4xl font-black text-primary">+10</div>
          <div class="text-sm font-semibold text-on-surface-variant tracking-wide">años de trayectoria</div>
        </div>
      </div>

      <!-- Texto -->
      <div>
        <h2 class="text-3xl font-bold tracking-tight text-on-surface mb-6">Nuestra historia</h2>
        <div class="space-y-5 text-on-surface-variant text-lg leading-relaxed">
          <p>
            MMPharma nació de una necesidad real en el mercado mexicano: una distribución farmacéutica que entendiera que cada medicamento no es solo una unidad de inventario, sino una vida que depende de su integridad y puntualidad.
          </p>
          <p>
            Desde Zapopan, Jalisco, hemos construido una red de distribución B2B que atiende a farmacias, distribuidoras y empresas del sector salud con precios diferenciados, atención personalizada y manejo especializado de cadena de frío.
          </p>
          <p>
            Nuestro catálogo de más de 769 productos cubre desde medicamentos de patente hasta insumos médicos y soluciones de red fría, todo respaldado por un equipo comprometido con la precisión en cada entrega.
          </p>
        </div>

    </div>
  </div>
</section>

<!-- ── MISIÓN Y VISIÓN ── -->
<section class="py-24 bg-surface-container-low">
  <div class="max-w-7xl mx-auto px-8">
    <div class="text-center mb-16">
      <h2 class="text-3xl font-bold tracking-tight text-on-surface mb-4">Lo que nos mueve</h2>
      <p class="text-on-surface-variant max-w-2xl mx-auto">La base que sustenta cada una de nuestras operaciones.</p>
    </div>
    <div class="grid md:grid-cols-2 gap-8">
      <div class="bg-surface-container-lowest p-12 rounded-2xl clinical-shadow border-l-4 border-secondary">
        <div class="mb-6 inline-flex items-center justify-center w-12 h-12 bg-secondary-container/10 rounded-xl">
          <span class="material-symbols-outlined text-secondary text-2xl" style="font-variation-settings: 'FILL' 1">flag</span>
        </div>
        <h3 class="text-2xl font-bold text-on-surface mb-4">Misión</h3>
        <p class="text-on-surface-variant leading-relaxed">
          Facilitar el acceso a insumos médicos y farmacéuticos con los más altos estándares de calidad, asegurando que los productos lleguen íntegros, a tiempo y al precio correcto para cada tipo de cliente.
        </p>
      </div>
      <div class="bg-surface-container-lowest p-12 rounded-2xl clinical-shadow border-l-4 border-primary">
        <div class="mb-6 inline-flex items-center justify-center w-12 h-12 bg-primary-fixed/30 rounded-xl">
          <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1">visibility</span>
        </div>
        <h3 class="text-2xl font-bold text-on-surface mb-4">Visión</h3>
        <p class="text-on-surface-variant leading-relaxed">
          Ser la distribuidora de referencia en el occidente de México, reconocida por nuestra tecnología, transparencia en precios y compromiso inquebrantable con el sector salud.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ── VALORES ── -->
<section class="py-24 bg-surface">
  <div class="max-w-7xl mx-auto px-8">
    <div class="text-center mb-16">
      <h2 class="text-3xl font-bold tracking-tight text-on-surface mb-4">Valores fundamentales</h2>
      <p class="text-on-surface-variant max-w-2xl mx-auto">Los principios que guían cada decisión dentro de MMPharma.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="group p-8 rounded-2xl bg-surface-container-low hover:bg-surface-container-lowest hover:shadow-xl transition-all duration-300 clinical-shadow">
        <span class="material-symbols-outlined text-4xl text-primary mb-6 block" style="font-variation-settings: 'FILL' 1">verified_user</span>
        <h4 class="text-xl font-bold text-on-surface mb-3">Integridad</h4>
        <p class="text-sm text-on-surface-variant leading-relaxed">Transparencia absoluta en precios, inventarios y condiciones de entrega.</p>
      </div>
      <div class="group p-8 rounded-2xl bg-surface-container-low hover:bg-surface-container-lowest hover:shadow-xl transition-all duration-300 clinical-shadow">
        <span class="material-symbols-outlined text-4xl text-secondary mb-6 block" style="font-variation-settings: 'FILL' 1">biotech</span>
        <h4 class="text-xl font-bold text-on-surface mb-3">Precisión</h4>
        <p class="text-sm text-on-surface-variant leading-relaxed">Manejo cuidadoso de cadena de frío y entrega exacta de inventarios especializados.</p>
      </div>
      <div class="group p-8 rounded-2xl bg-surface-container-low hover:bg-surface-container-lowest hover:shadow-xl transition-all duration-300 clinical-shadow">
        <span class="material-symbols-outlined text-4xl text-tertiary-container mb-6 block" style="font-variation-settings: 'FILL' 1">handshake</span>
        <h4 class="text-xl font-bold text-on-surface mb-3">Compromiso</h4>
        <p class="text-sm text-on-surface-variant leading-relaxed">Atención personalizada y lealtad total hacia nuestros socios comerciales.</p>
      </div>
      <div class="group p-8 rounded-2xl bg-surface-container-low hover:bg-surface-container-lowest hover:shadow-xl transition-all duration-300 clinical-shadow">
        <span class="material-symbols-outlined text-4xl text-on-primary-fixed-variant mb-6 block" style="font-variation-settings: 'FILL' 1">memory</span>
        <h4 class="text-xl font-bold text-on-surface mb-3">Innovación</h4>
        <p class="text-sm text-on-surface-variant leading-relaxed">Portal B2B propio con inventario en tiempo real y cotizaciones automatizadas.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── LO QUE OFRECEMOS (reemplaza "Presencia Nacional") ── -->
<section class="py-24 bg-gradient-to-br from-primary to-primary-container">
  <div class="max-w-7xl mx-auto px-8">
    <div class="grid md:grid-cols-2 gap-16 items-center">

      <div>
        <span class="inline-block py-1 px-3 mb-4 rounded-full bg-white/10 text-white font-semibold text-xs tracking-widest uppercase">Portal B2B</span>
        <h2 class="text-3xl font-bold text-white mb-6">Tu operación, simplificada</h2>
        <p class="text-primary-fixed-dim text-lg mb-8 leading-relaxed">
          Nuestro portal digital te da acceso a precios diferenciados por nivel de cliente, cotizaciones instantáneas y un catálogo de 769 productos siempre actualizado.
        </p>
        <div class="space-y-4 mb-10">
          <div class="flex items-center gap-4 text-white font-medium">
            <div class="w-8 h-8 bg-tertiary-container rounded-full flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-tertiary-fixed text-sm" style="font-variation-settings: 'FILL' 1">check</span>
            </div>
            4 niveles de precio: Empresa, Farmacia, Distribuidor y Red Fría
          </div>
          <div class="flex items-center gap-4 text-white font-medium">
            <div class="w-8 h-8 bg-tertiary-container rounded-full flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-tertiary-fixed text-sm" style="font-variation-settings: 'FILL' 1">check</span>
            </div>
            Cotizaciones en PDF y Excel con validez de 5 días
          </div>
          <div class="flex items-center gap-4 text-white font-medium">
            <div class="w-8 h-8 bg-tertiary-container rounded-full flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-tertiary-fixed text-sm" style="font-variation-settings: 'FILL' 1">check</span>
            </div>
            Manejo especializado de cadena de frío (27 productos)
          </div>
          <div class="flex items-center gap-4 text-white font-medium">
            <div class="w-8 h-8 bg-tertiary-container rounded-full flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-tertiary-fixed text-sm" style="font-variation-settings: 'FILL' 1">check</span>
            </div>
            Envíos a todo el país con umbrales de flete automáticos
          </div>
        </div>
        <div class="flex gap-4">
          <a href="../SELECCIÓN_REGISTRO/selección_registro.html"
             class="px-8 py-4 bg-white text-primary font-bold rounded-xl hover:bg-blue-50 transition-all">
            Solicitar acceso
          </a>
          <a href="../CATALOGO/catalogo.php"
             class="px-8 py-4 border-2 border-white/30 hover:border-white/60 text-white font-bold rounded-xl transition-all">
            Ver catálogo
          </a>
        </div>
      </div>

      <!-- Card de métricas -->
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center">
          <p class="text-5xl font-black text-white mb-2">+700</p>
          <p class="text-primary-fixed-dim text-sm uppercase tracking-wider">Productos en catálogo</p>
        </div>
        <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center">
          <p class="text-5xl font-black text-white mb-2">+300</p>
          <p class="text-primary-fixed-dim text-sm uppercase tracking-wider">Clientes activos</p>
        </div>
        <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl border border-white/20 text-center">
          <p class="text-5xl font-black text-white mb-2">+20</p>
          <p class="text-primary-fixed-dim text-sm uppercase tracking-wider">Productos red fría</p>
        </div>
        <div class="bg-tertiary-container/80 backdrop-blur-sm p-8 rounded-2xl border border-tertiary-fixed/30 text-center">
          <p class="text-5xl font-black text-white mb-2">4</p>
          <p class="text-tertiary-fixed/80 text-sm uppercase tracking-wider">Niveles de precio</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ── CTA FINAL ── -->
<section class="py-20 bg-surface">
  <div class="max-w-5xl mx-auto px-8">
    <div class="bg-surface-container-lowest rounded-3xl p-12 md:p-16 text-center clinical-shadow border border-outline-variant/10">
      <h2 class="text-3xl md:text-4xl font-bold text-on-surface mb-4">¿Listo para trabajar juntos?</h2>
      <p class="text-on-surface-variant text-lg mb-10 max-w-2xl mx-auto">
        Solicita tu acceso al portal B2B o contáctanos directamente. Nuestro equipo te asigna tu nivel de cliente y te da acceso al catálogo completo.
      </p>
      <div class="flex flex-wrap gap-4 justify-center">
        <a href="../SELECCIÓN_REGISTRO/selección_registro.html"
           class="px-10 py-4 bg-gradient-to-br from-primary to-primary-container text-white font-bold rounded-xl hover:opacity-90 active:scale-95 transition-all shadow-lg shadow-primary/20">
          Solicitar acceso al portal
        </a>
        <a href="../CONTACTO/contacto.php"
           class="px-10 py-4 border-2 border-outline-variant/30 text-on-surface font-bold rounded-xl hover:bg-surface-container-low transition-all">
          Contactar a ventas
        </a>
      </div>
    </div>
  </div>
</section>

</main>

<?php require_once '../includes/footer.php'; ?>