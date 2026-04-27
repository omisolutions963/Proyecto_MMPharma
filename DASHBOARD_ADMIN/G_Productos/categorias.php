<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../../LOGIN/login.php");
    exit;
}
require_once '../clinical_core/db.php';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'upsert') {
        $id = (int)$_POST['id'];
        $nombre = $_POST['nombre'];
        if ($id > 0) {
            $pdo->prepare("UPDATE catalogo_categorias SET nombre = ? WHERE id = ?")->execute([$nombre, $id]);
        } else {
            $pdo->prepare("INSERT INTO catalogo_categorias (nombre) VALUES (?)")->execute([$nombre]);
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM catalogo_categorias WHERE id = ?")->execute([$id]);
    }
    header("Location: categorias.php");
    exit;
}

$categorias = $pdo->query("SELECT * FROM catalogo_categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Gestión de Categorías | MMPharma Admin";
$activePage = "productos";
include("../Includes/header.php");
include("../Includes/sidebar.php");
?>
<main class="ml-64 p-8 min-h-screen" style="background:#051a10">
    <div class="flex justify-between items-end mb-8 animate-reveal">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[10px] font-black uppercase tracking-widest text-on-surface-variant/60">
                <a href="../dashboard/dashboard.php" class="hover:text-primary transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <a href="productos.php" class="hover:text-primary transition-colors">Inventario</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-on-surface-variant">Categorías</span>
            </nav>
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">Categorías de Productos</h2>
        </div>
        <button onclick="abrirModalCat()" class="bg-primary text-white px-6 py-3 rounded-xl flex items-center gap-2 font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined">add</span> Nueva Categoría
        </button>
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden max-w-2xl animate-reveal" style="animation-delay: 0.15s">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase tracking-widest">
                    <th class="px-8 py-4">Nombre</th>
                    <th class="px-8 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach($categorias as $c): ?>
                <tr class="group hover:bg-white/5 transition-colors animate-fade-in">
                    <td class="px-8 py-4 text-sm font-bold text-on-surface"><?= htmlspecialchars($c['nombre']) ?></td>
                    <td class="px-8 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick='abrirEditarCat(<?= json_encode($c) ?>)' class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-primary hover:bg-primary hover:text-white transition-all">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta categoría? Los productos asociados se quedarán sin categoría.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-surface-container-high text-error hover:bg-error hover:text-white transition-all">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal Categoría -->
<div id="modalCat" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-surface w-full max-w-sm rounded-3xl overflow-hidden shadow-2xl border border-outline-variant/10">
        <form method="POST" class="p-8 space-y-6">
            <input type="hidden" name="action" value="upsert">
            <input type="hidden" name="id" id="cat_id">
            <h3 id="cat_title" class="text-xl font-black text-white">Nueva Categoría</h3>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-on-surface-variant mb-2">Nombre</label>
                <input type="text" name="nombre" id="cat_nombre" required class="w-full bg-surface-container-low border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="cerrarModalCat()" class="flex-1 py-3 text-xs font-bold text-on-surface-variant hover:bg-surface-container-low rounded-xl">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-primary text-white text-xs font-bold rounded-xl shadow-lg">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCat() {
    document.getElementById('cat_title').textContent = "Nueva Categoría";
    document.getElementById('cat_id').value = "0";
    document.getElementById('cat_nombre').value = "";
    document.getElementById('modalCat').classList.remove('hidden');
}
function abrirEditarCat(c) {
    document.getElementById('cat_title').textContent = "Editar Categoría";
    document.getElementById('cat_id').value = c.id;
    document.getElementById('cat_nombre').value = c.nombre;
    document.getElementById('modalCat').classList.remove('hidden');
}
function cerrarModalCat() {
    document.getElementById('modalCat').classList.add('hidden');
}
</script>

<?php include("../Includes/footer.php"); ?>
