<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cliente_logged_in']) || $_SESSION['cliente_logged_in'] !== true) {
    header("Location: ../LOGIN/login.php");
    exit;
}

require_once '../INCLUDES/db.php';
$pdo = getDB();
$cliente_id = $_SESSION['cliente_id'];

// 1. Cotizaciones este mes
$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(monto_total) as monto_total FROM clientes_pedidos WHERE cliente_id = ? AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stmt->execute([$cliente_id]);
$mes_stats = $stmt->fetch(PDO::FETCH_ASSOC);
$cotizaciones_mes = $mes_stats['total'] ?? 0;
$monto_mes = $mes_stats['monto_total'] ?? 0;

// 2. Cotizaciones aprobadas
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clientes_pedidos WHERE cliente_id = ? AND estado_envio NOT IN ('PENDIENTE', 'CANCELADO')");
$stmt->execute([$cliente_id]);
$aprobadas = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 3. Obtener cotizaciones
$stmt = $pdo->prepare("
    SELECT p.id, p.folio, p.monto_total, p.estado_envio, p.created_at, COUNT(pd.id) as total_items 
    FROM clientes_pedidos p
    LEFT JOIN clientes_pedidos_detalle pd ON p.id = pd.pedido_id
    WHERE p.cliente_id = ?
    GROUP BY p.id
    ORDER BY p.created_at DESC
");
$stmt->execute([$cliente_id]);
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle  = 'MMPharma Portal - Cotizaciones';
$activePage = 'cotizaciones';
include('Includes/header.php');
include('Includes/sidebar.php');
?>
<main class="ml-64 mt-16 p-8 min-h-screen w-[calc(100%-16rem)] bg-background text-on-surface">
    
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 animate-reveal">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
                <a href="Dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-on-surface-variant">Cotizaciones</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Historial de Cotizaciones</h1>
            <p class="text-on-surface-variant mt-1 text-sm">Gestiona y consulta el estatus de tus solicitudes de inventario.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="../CATALOGO/catalogo.php" class="px-5 py-2.5 bg-primary hover:bg-primary-fixed-dim text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span> Nueva Cotización
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-reveal delay-100">
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">calendar_month</span>
            </div>
            <div>
                <p class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Este mes</p>
                <h3 class="text-3xl font-extrabold text-white leading-none"><?= $cotizaciones_mes ?></h3>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <p class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Monto mensual</p>
                <h3 class="text-3xl font-extrabold text-white leading-none">$<?= number_format($monto_mes, 2) ?></h3>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-14 h-14 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <p class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-1">Aprobadas</p>
                <h3 class="text-3xl font-extrabold text-white leading-none"><?= $aprobadas ?></h3>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-4 mb-6 animate-reveal delay-200">
        <div class="relative flex-1 min-w-[250px]">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" id="searchInput" placeholder="Buscar por folio..." class="w-full bg-surface-container-lowest border border-outline-variant/50 text-white rounded-xl pl-12 pr-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" onkeyup="filterTable()">
        </div>
        <div class="relative w-48">
            <select id="statusFilter" class="w-full bg-surface-container-lowest border border-outline-variant/50 text-white rounded-xl pl-4 pr-10 py-3 text-sm appearance-none focus:border-primary outline-none" onchange="filterTable()">
                <option value="">Todos los estados</option>
                <option value="APROBADA">Aprobadas</option>
                <option value="PENDIENTE">Pendientes</option>
                <option value="CANCELADO">Canceladas</option>
            </select>
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl shadow-sm overflow-hidden animate-reveal delay-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap" id="cotizacionesTable">
                <thead class="bg-surface-container-low text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
                    <tr>
                        <th class="py-4 px-6">Folio</th>
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6 text-center">Cant.</th>
                        <th class="py-4 px-6 text-right">Subtotal</th>
                        <th class="py-4 px-6 text-right">IVA</th>
                        <th class="py-4 px-6 text-right">Total</th>
                        <th class="py-4 px-6 text-center">Estado</th>
                        <th class="py-4 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php if (empty($cotizaciones)): ?>
                    <tr>
                        <td colspan="8" class="py-12 text-center text-on-surface-variant text-sm">No tienes cotizaciones registradas.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($cotizaciones as $cot): 
                            $total = $cot['monto_total'];
                            $subtotal = $total / 1.16;
                            $iva = $total - $subtotal;
                        ?>
                        <tr class="hover:bg-surface-container/30 transition-colors group cotizacion-row animate-fade-in" data-estado="<?= htmlspecialchars($cot['estado_envio']) ?>">
                            <td class="py-4 px-6 text-sm font-bold text-primary folio-cell"><?= htmlspecialchars($cot['folio']) ?></td>
                            <td class="py-4 px-6 text-xs text-on-surface-variant"><?= date('d M Y', strtotime($cot['created_at'])) ?></td>
                            <td class="py-4 px-6 text-xs text-on-surface-variant text-center"><?= $cot['total_items'] ?></td>
                            <td class="py-4 px-6 text-sm text-on-surface text-right">$<?= number_format($subtotal, 2) ?></td>
                            <td class="py-4 px-6 text-sm text-on-surface text-right">$<?= number_format($iva, 2) ?></td>
                            <td class="py-4 px-6 text-sm font-bold text-white text-right">$<?= number_format($total, 2) ?></td>
                            <td class="py-4 px-6 text-center">
                                <?php if ($cot['estado_envio'] === 'PENDIENTE'): ?>
                                <span class="px-3 py-1 bg-secondary/20 text-secondary text-[10px] font-bold rounded-md uppercase tracking-wider">Pendiente</span>
                                <?php elseif ($cot['estado_envio'] === 'CANCELADO'): ?>
                                <span class="px-3 py-1 bg-error/20 text-error text-[10px] font-bold rounded-md uppercase tracking-wider">Cancelado</span>
                                <?php else: ?>
                                <span class="px-3 py-1 bg-tertiary/20 text-tertiary text-[10px] font-bold rounded-md uppercase tracking-wider"><?= htmlspecialchars($cot['estado_envio']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="Cotizacion-Detalle.php?id=<?= $cot['id'] ?>" class="text-on-surface-variant hover:text-white transition-colors" title="Ver detalle">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <button onclick="borrarCotizacion(<?= $cot['id'] ?>, '<?= $cot['folio'] ?>')" class="text-on-surface-variant hover:text-error transition-colors" title="Eliminar">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination info -->
        <div class="px-6 py-4 border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-between gap-4 bg-surface-container-low/50">
            <span class="text-xs text-on-surface-variant" id="tableInfo">Mostrando <?= count($cotizaciones) ?> cotizaciones</span>
        </div>
    </div>

    <!-- Export Actions -->
    <div class="flex justify-end gap-3 mt-6 animate-reveal delay-400">
        <button class="px-5 py-2.5 bg-secondary/10 hover:bg-secondary/20 text-secondary text-sm font-bold rounded-xl transition-colors flex items-center gap-2" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Exportación a Excel en desarrollo.', background:'#071628', color:'#fff'})">
            <span class="material-symbols-outlined text-[18px]">table_chart</span> Exportar Excel
        </button>
        <button class="px-5 py-2.5 bg-error/10 hover:bg-error/20 text-error text-sm font-bold rounded-xl transition-colors flex items-center gap-2" onclick="Swal.fire({icon:'info', title:'Próximamente', text:'Descarga en PDF en desarrollo.', background:'#071628', color:'#fff'})">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Descargar Reporte PDF
        </button>
    </div>

</main>
<?php include('Includes/footer.php'); ?>

<script>
function filterTable() {
    const searchVal = document.getElementById('searchInput').value.toLowerCase();
    const statusVal = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.cotizacion-row');
    let count = 0;
    
    rows.forEach(row => {
        const folio = row.querySelector('.folio-cell').textContent.toLowerCase();
        const estado = row.getAttribute('data-estado');
        
        let matchSearch = folio.includes(searchVal);
        let matchStatus = statusVal === '' || (statusVal === 'APROBADA' && estado !== 'PENDIENTE' && estado !== 'CANCELADO') || estado === statusVal;
        
        if (matchSearch && matchStatus) {
            row.style.display = '';
            count++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('tableInfo').textContent = 'Mostrando ' + count + ' cotizaciones filtradas';
}

async function borrarCotizacion(id, folio) {
    const result = await Swal.fire({
        title: '¿Eliminar cotización?',
        text: `Estás a punto de borrar permanentemente la cotización ${folio}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ba1a1a',
        cancelButtonColor: '#747780',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#071628',
        color: '#fff'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch('api_borrar_cotizacion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: 'Eliminado',
                    text: 'La cotización ha sido eliminada correctamente.',
                    icon: 'success',
                    background: '#071628',
                    color: '#fff'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'No se pudo eliminar la cotización.',
                    icon: 'error',
                    background: '#071628',
                    color: '#fff'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión con el servidor.',
                icon: 'error',
                background: '#071628',
                color: '#fff'
            });
        }
    }
}
</script>
