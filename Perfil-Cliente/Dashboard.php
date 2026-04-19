<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMPharma - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #05070a;
            --sidebar-bg: #0d1117;
            --card-bg: #161b22;
            --accent-blue: #3b82f6;
            --text-primary: #e6edf3;
            --text-secondary: #8b949e;
            --border-subtle: rgba(255, 255, 255, 0.05);
            --danger-bg: rgba(248, 81, 73, 0.1);
            --danger-text: #f85149;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-deep);
            color: var(--text-primary);
            margin: 0;
            display: flex;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-subtle);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 50;
        }

        .main-content {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            padding: 2rem;
        }

        .nav-item {
            display: flex;
            items-center: center;
            gap: 12px;
            padding: 12px 24px;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            color: var(--text-primary);
            background-color: rgba(255, 255, 255, 0.03);
            border-left-color: var(--accent-blue);
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 24px;
        }

        .btn-primary {
            background-color: var(--accent-blue);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
        }

        .btn-outline {
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-outline:hover {
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }

        .btn-danger-transparent {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
        }

        .badge-status {
            padding: 2px 8px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .bg-success-subtle { background-color: rgba(35, 134, 54, 0.15); color: #3fb950; }
        .bg-pending-subtle { background-color: rgba(187, 128, 9, 0.15); color: #d29922; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="p-8">
            <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma Logo" class="h-10 w-auto object-contain">
        </div>

        <nav class="flex-1">
            <a href="Dashboard.php" class="nav-item active">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                Dashboard
            </a>
            <a href="Catalogo.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                Catálogo
            </a>
            <a href="Cotizaciones.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                Cotizaciones
            </a>
            <a href="Perfil.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Mi Perfil
            </a>
            <a href="Documentos.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                Documentos
            </a>
            <a href="Direcciones.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                Direcciones
            </a>
            <a href="Contacto.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l4-4H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Contacto
            </a>
        </nav>

        <div class="p-6 border-t border-border-subtle space-y-3">
            <a href="http://localhost:8080/proyecto_mmpharma/INDEX/index.php" class="w-full btn-outline flex items-center justify-center gap-2">
                Ir al sitio Público
            </a>
            <a href="http://localhost:8080/proyecto_mmpharma/LOGIN/logout.php" class="w-full btn-danger-transparent flex items-center justify-center gap-2">
                Cerrar sesión
            </a>
            
            <div class="pt-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/10 overflow-hidden flex items-center justify-center border border-border-subtle">
                    <img src="https://picsum.photos/seed/company/100/100" alt="Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-xs font-bold text-white">MMPharma</p>
                    <p class="text-[10px] text-text-secondary">Portal Cliente</p>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-bold tracking-tight mb-1">¡Hola, Farmacia San Jorge!</h2>
                <p class="text-text-secondary">Viernes, 19 de Abril 2026</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text" placeholder="Buscar..." class="bg-card-bg border border-border-subtle rounded-lg py-2 pl-10 pr-4 text-sm focus:border-accent-blue outline-none transition-all">
                    <svg class="absolute left-3 top-2.5 text-text-secondary" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <button class="btn-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Nueva Cotización
                </button>
            </div>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="card">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-600/10 flex items-center justify-center text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-text-secondary uppercase tracking-widest">Últimos 30 días</span>
                </div>
                <h3 class="text-2xl font-bold mb-1">12</h3>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Cotizaciones Realizadas</p>
            </div>
            <div class="card">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600/10 flex items-center justify-center text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.91 8.11 16.13 3.33a.87.87 0 0 0-1.23 0l-4.78 4.78a.87.87 0 0 0 0 1.23l4.78 4.78a.87.87 0 0 0 1.23 0l4.78-4.78a.87.87 0 0 0 0-1.23ZM6.11 20.91l4.78-4.78a.87.87 0 0 0 0-1.23l-4.78-4.78a.87.87 0 0 0-1.23 0L.11 14.9a.87.87 0 0 0 0 1.23l4.78 4.78a.87.87 0 0 0 1.23 0Z"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold mb-1">Aprobado</h3>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Estatus de Crédito</p>
            </div>
            <div class="card">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-lg bg-purple-600/10 flex items-center justify-center text-purple-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold mb-1">24</h3>
                <p class="text-xs text-text-secondary font-medium uppercase tracking-wider">Productos en Cotización</p>
            </div>
        </section>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold">Actividad Reciente</h3>
                <button class="text-accent-blue text-xs font-bold hover:underline">Ver todo</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-xs font-bold text-text-secondary uppercase tracking-widest border-b border-border-subtle">
                        <tr>
                            <th class="pb-4 px-2">Folio</th>
                            <th class="pb-4 px-2">Fecha</th>
                            <th class="pb-4 px-2">Cliente</th>
                            <th class="pb-4 px-2">Total</th>
                            <th class="pb-4 px-2">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        <tr class="group hover:bg-white/2 transition-colors">
                            <td class="py-4 px-2 text-sm font-semibold">COT-2023-142</td>
                            <td class="py-4 px-2 text-sm text-text-secondary">18 Abr 2026</td>
                            <td class="py-4 px-2 text-sm text-text-secondary">Sucursal Lomas</td>
                            <td class="py-4 px-2 text-sm font-bold">$12,450.00</td>
                            <td class="py-4 px-2">
                                <span class="badge-status bg-success-subtle">Aprobada</span>
                            </td>
                        </tr>
                        <tr class="group hover:bg-white/2 transition-colors">
                            <td class="py-4 px-2 text-sm font-semibold">COT-2023-141</td>
                            <td class="py-4 px-2 text-sm text-text-secondary">17 Abr 2026</td>
                            <td class="py-4 px-2 text-sm text-text-secondary">Matriz Centro</td>
                            <td class="py-4 px-2 text-sm font-bold">$8,920.00</td>
                            <td class="py-4 px-2">
                                <span class="badge-status bg-pending-subtle">Pendiente</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Animaciones simples o lógica de JS aquí
        console.log("MMPharma Dashboard Loaded");
    </script>
</body>
</html>
