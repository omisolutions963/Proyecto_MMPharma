<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMPharma - Documentos</title>
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
            align-items: center;
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
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .bg-success-subtle { background-color: rgba(35, 134, 54, 0.1); color: #3fb950; }
        .bg-warning-subtle { background-color: rgba(187, 128, 9, 0.1); color: #d29922; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="p-8">
            <img src="../logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma Logo" class="h-10 w-auto object-contain">
        </div>

        <nav class="flex-1">
            <a href="Dashboard.php" class="nav-item">
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
            <a href="Documentos.php" class="nav-item active">
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
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="text-2xl font-bold tracking-tight mb-2">Expediente Digital</h2>
                <p class="text-text-secondary">Documentación legal y regulatoria obligatoria de su establecimiento.</p>
            </div>
            <button class="bg-accent-blue text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 hover:opacity-90 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Subir Nuevo Documento
            </button>
        </header>

        <div class="bg-emerald-500/5 border border-emerald-500/10 p-6 rounded-xl flex items-center justify-between shadow-sm mb-10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-text-primary">Expediente Validado</h4>
                    <p class="text-[11px] text-text-secondary mt-0.5">Su cuenta tiene permisos completos para compras controladas.</p>
                </div>
            </div>
            <button class="text-accent-blue text-xs font-bold hover:underline flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Descargar Todo
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card flex flex-col group hover:border-accent-blue/40 transition-all">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-10 h-10 bg-white/2 rounded-lg flex items-center justify-center text-text-secondary group-hover:text-accent-blue transition-all border border-border-subtle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                    </div>
                    <span class="badge-status bg-success-subtle">Vigente</span>
                </div>
                <h3 class="font-bold text-text-primary text-sm mb-1 uppercase tracking-tight">Licencia Sanitaria</h3>
                <p class="text-[11px] text-text-secondary mb-6 italic">Expira: 12 Oct 2025</p>
                <div class="mt-auto flex gap-2">
                    <button class="flex-1 py-2 rounded-lg bg-bg-deep border border-border-subtle text-text-primary text-[10px] font-bold uppercase tracking-widest hover:bg-white/5 transition-colors">Ver</button>
                    <button class="px-3 py-2 rounded-lg bg-bg-deep border border-border-subtle text-text-secondary hover:text-accent-blue transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </button>
                </div>
            </div>

            <div class="card flex flex-col group hover:border-accent-blue/40 transition-all">
                <div class="flex justify-between items-start mb-6">
                    <div class="w-10 h-10 bg-white/2 rounded-lg flex items-center justify-center text-text-secondary group-hover:text-accent-blue transition-all border border-border-subtle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l4-4H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <span class="badge-status bg-warning-subtle">Cerca de Vencer</span>
                </div>
                <h3 class="font-bold text-text-primary text-sm mb-1 uppercase tracking-tight">Cofepris: Aviso Funcionamiento</h3>
                <p class="text-[11px] text-text-secondary mb-6 italic">Expira: 28 Jul 2024</p>
                <div class="mt-auto flex gap-2">
                    <button class="flex-1 py-2 rounded-lg bg-bg-deep border border-border-subtle text-text-primary text-[10px] font-bold uppercase tracking-widest hover:bg-white/5 transition-colors">Ver</button>
                    <button class="px-3 py-2 rounded-lg bg-bg-deep border border-border-subtle text-text-secondary hover:text-accent-blue transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
