<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../LOGIN/login.php");
    exit;
}

require_once '../clinical_core/db.php';
$pdo = getDB();

$activePage = 'marketing';
$pageTitle = "Marketing & Comunicación | MMPharma Admin";

// 1. Fetch Banners
$stmt = $pdo->query("SELECT * FROM admin_banners_promocionales ORDER BY orden ASC");
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch Clientes for notifications
$stmt = $pdo->query("SELECT id, razon_social FROM clientes_usuarios WHERE estatus = 'ACTIVO' ORDER BY razon_social ASC");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Recent Notifications
$stmt = $pdo->query("SELECT n.*, c.razon_social FROM admin_alertas_notificaciones n JOIN clientes_usuarios c ON n.cliente_id = c.id ORDER BY n.created_at DESC LIMIT 10");
$notif_recientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include("../Includes/header.php");
include("../Includes/sidebar.php");
?>

<main class="ml-64 flex-1 p-8 min-h-screen bg-background text-on-surface">
    
    <!-- Header -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
                <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-on-surface-variant">Marketing</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-on-surface tracking-tight animate-reveal">Marketing & Comunicación</h1>
            <p class="text-on-surface-variant text-sm mt-1 animate-reveal delay-100">Gestiona banners promocionales y notificaciones directas a clientes.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        <!-- SECCIÓN BANNERS -->
        <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-8 shadow-sm animate-reveal" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">ads_click</span>
                    Banners del Dashboard
                </h2>
                <button onclick="abrirModalBanner()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span> Nuevo banner
                </button>
            </div>

            <div class="space-y-4">
                <?php if(empty($banners)): ?>
                    <p class="text-center py-10 text-white/20 italic">No hay banners activos.</p>
                <?php else: ?>
                    <?php foreach($banners as $b): ?>
                    <div class="flex items-center gap-4 p-4 bg-surface-container-high/20 border border-outline-variant/10 rounded-2xl group hover:bg-surface-container-high/40 transition-all animate-scale-in">
                        <div class="w-24 h-14 rounded-lg overflow-hidden border border-outline-variant/20">
                            <img src="../../<?= htmlspecialchars($b['ruta_imagen']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-on-surface"><?= htmlspecialchars($b['titulo']) ?></h3>
                            <p class="text-[10px] text-on-surface-variant truncate w-40"><?= htmlspecialchars($b['enlace_url'] ?: 'Sin link') ?></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="eliminarBanner(<?= $b['id'] ?>)" class="w-9 h-9 flex items-center justify-center text-error hover:bg-error/20 rounded-xl transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- SECCIÓN NOTIFICACIONES -->
        <div class="bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-8 shadow-sm animate-reveal" style="animation-delay: 0.3s">
            <h2 class="text-xl font-bold text-on-surface flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-primary">send</span>
                Enviar Notificación
            </h2>

            <form id="formNotif" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Seleccionar Cliente</label>
                    <select name="cliente_id" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-primary outline-none text-white">
                        <option value="0">--- Seleccionar Cliente ---</option>
                        <?php foreach($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['razon_social']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Tipo de Alerta</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="INFO" checked class="hidden peer">
                            <div class="p-3 border border-outline-variant/10 rounded-xl text-center text-[10px] font-bold text-on-surface-variant peer-checked:bg-primary peer-checked:text-white transition-all">INFO</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="SUCCESS" class="hidden peer">
                            <div class="p-3 border border-outline-variant/10 rounded-xl text-center text-[10px] font-bold text-on-surface-variant peer-checked:bg-tertiary peer-checked:text-white transition-all">ÉXITO</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="WARNING" class="hidden peer">
                            <div class="p-3 border border-outline-variant/10 rounded-xl text-center text-[10px] font-bold text-on-surface-variant peer-checked:bg-error peer-checked:text-white transition-all">ALERTA</div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Mensaje</label>
                    <textarea name="mensaje" rows="3" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-primary outline-none text-white placeholder:text-white/20" placeholder="Escribe el mensaje para el cliente..."></textarea>
                </div>

                <button type="button" onclick="enviarNotificacion()" class="w-full py-4 bg-primary hover:opacity-90 text-white font-bold rounded-xl shadow-lg shadow-primary/20 transition-all active:scale-[0.98]">
                    Enviar notificación directa
                </button>
            </form>
        </div>

        <!-- HISTORIAL RECIENTE -->
        <div class="xl:col-span-2 bg-surface-container-lowest border border-outline-variant/10 rounded-2xl p-8 mt-8 shadow-sm animate-reveal" style="animation-delay: 0.4s">
            <h2 class="text-xl font-bold text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history</span>
                Historial Reciente
            </h2>
            <div class="overflow-hidden rounded-2xl border border-outline-variant/10">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-[10px] font-black text-on-surface-variant uppercase tracking-widest">
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4">Mensaje</th>
                            <th class="px-6 py-4 text-center">Tipo</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-outline-variant/10">
                        <?php foreach($notif_recientes as $nr): ?>
                        <tr class="group hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-white"><?= htmlspecialchars($nr['razon_social']) ?></td>
                            <td class="px-6 py-4 text-on-surface-variant"><?= htmlspecialchars($nr['mensaje']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $nr['tipo'] === 'SUCCESS' ? 'bg-tertiary/20 text-tertiary' : ($nr['tipo'] === 'WARNING' ? 'bg-error/20 text-error' : 'bg-primary/20 text-primary') ?>">
                                    <?= $nr['tipo'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[11px] text-on-surface-variant/60"><?= date('d/m/Y H:i', strtotime($nr['created_at'])) ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php if($nr['leida']): ?>
                                    <span class="material-symbols-outlined text-tertiary text-lg" title="Leída">done_all</span>
                                <?php else: ?>
                                    <span class="material-symbols-outlined text-on-surface-variant/30 text-lg" title="Pendiente">mail</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</main>

<script>
    async function enviarNotificacion() {
        const form = document.getElementById('formNotif');
        const data = new FormData(form);
        data.append('action', 'send_notif');

        if (data.get('cliente_id') == '0' || !data.get('mensaje')) {
            return Swal.fire({title:'Error', text:'Completa los campos', icon:'error', background: '#102245', color: '#fff'});
        }

        const res = await fetch('procesar_marketing.php', { method: 'POST', body: data });
        const json = await res.json();

        if (json.status === 'success') {
            Swal.fire({title:'¡Enviado!', text:'La notificación llegará al dashboard del cliente.', icon:'success', background: '#102245', color: '#fff'}).then(() => location.reload());
        } else {
            Swal.fire({title:'Error', text:json.message, icon:'error', background: '#102245', color: '#fff'});
        }
    }

    function abrirModalBanner() {
        Swal.fire({
            title: 'Nuevo Banner',
            html: `
                <div class="space-y-4 text-left p-2">
                    <div>
                        <label class="text-[10px] font-bold text-primary uppercase">Título</label>
                        <input type="text" id="b_titulo" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary" placeholder="Ej: Oferta de Verano">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-primary uppercase">URL (Opcional)</label>
                        <input type="text" id="b_url" class="w-full bg-surface border border-outline-variant/30 rounded-xl px-4 py-3 mt-1 text-white outline-none focus:ring-2 focus:ring-primary" placeholder="https://...">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-primary uppercase">Imagen del Banner</label>
                        <input type="file" id="b_file" class="w-full mt-2 text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/20 file:text-primary hover:file:bg-primary/30">
                    </div>
                </div>
            `,
            confirmButtonText: 'Publicar Banner',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            background: '#0d1f3c',
            color: '#fff',
            customClass: { confirmButton: 'bg-primary px-8 py-3 rounded-xl font-bold', cancelButton: 'text-on-surface-variant font-bold' },
            preConfirm: () => {
                const titulo = document.getElementById('b_titulo').value;
                const url = document.getElementById('b_url').value;
                const file = document.getElementById('b_file').files[0];
                if (!titulo || !file) {
                    Swal.showValidationMessage('Título e imagen son requeridos');
                    return false;
                }
                return { titulo, url, file };
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                const data = new FormData();
                data.append('action', 'add_banner');
                data.append('titulo', result.value.titulo);
                data.append('url', result.value.url);
                data.append('banner_img', result.value.file);

                const res = await fetch('procesar_marketing.php', { method: 'POST', body: data });
                const json = await res.json();
                if (json.status === 'success') {
                    Swal.fire({title:'¡Éxito!', text:'Banner publicado correctamente.', icon:'success', background: '#0d1f3c', color: '#fff'}).then(() => location.reload());
                } else {
                    Swal.fire({title:'Error', text:json.message, icon:'error', background: '#0d1f3c', color: '#fff'});
                }
            }
        });
    }

    async function eliminarBanner(id) {
        confirmAction('¿Eliminar banner?', 'Esta acción no se puede deshacer.', 'Sí, eliminar', async () => {
            const data = new FormData();
            data.append('action', 'del_banner');
            data.append('id', id);
            await fetch('procesar_marketing.php', { method: 'POST', body: data });
            location.reload();
        });
    }
</script>

<?php include("../Includes/footer.php"); ?>
