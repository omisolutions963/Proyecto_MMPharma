<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMPharma - Catálogo</title>
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
            <a href="Catalogo.php" class="nav-item active">
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
        <header class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Catálogo de Productos</h2>
                <p class="text-text-secondary mt-1">Explore nuestra gama completa de insumos farmacéuticos.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-card-bg border border-border-subtle px-4 py-2 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span class="text-sm font-semibold">Filtros</span>
                </div>
            </div>
        </header>

        <div class="mb-10">
            <div class="relative max-w-2xl">
                <input type="text" placeholder="Buscar por nombre, sustancia activa o código..." class="w-full bg-card-bg border border-border-subtle rounded-xl py-3 pl-12 pr-4 text-sm focus:border-accent-blue outline-none transition-all shadow-sm">
                <svg class="absolute left-4 top-3.5 text-text-secondary" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Product Card -->
            <div class="card p-0 overflow-hidden group">
                <div class="aspect-square bg-bg-deep flex items-center justify-center p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary opacity-20 group-hover:scale-110 transition-transform"><path d="M12 2v10"/><path d="M16 12a4 4 0 0 1-8 0"/><path d="M3 12h18"/><path d="M3 21h18"/></svg>
                </div>
                <div class="p-5">
                    <p class="text-[10px] font-bold text-accent-blue uppercase tracking-widest mb-1">Antibióticos</p>
                    <h3 class="font-semibold text-text-primary mb-3">Amoxicilina 500mg</h3>
                    <div class="flex items-center justify-between mb-5">
                        <span class="text-lg font-bold text-text-primary">$245.00</span>
                        <span class="text-[10px] font-bold uppercase py-1 px-2 bg-emerald-500/10 text-emerald-500 rounded-full">En Stock</span>
                    </div>
                    <button class="w-full py-2 bg-accent-blue/5 border border-accent-blue/20 text-accent-blue rounded-lg text-xs font-bold hover:bg-accent-blue hover:text-white transition-all">
                        Añadir a Cotización
                    </button>
                </div>
            </div>

            <div class="card p-0 overflow-hidden group">
                <div class="aspect-square bg-bg-deep flex items-center justify-center p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary opacity-20 group-hover:scale-110 transition-transform"><path d="M12 2v10"/><path d="M16 12a4 4 0 0 1-8 0"/><path d="M3 12h18"/><path d="M3 21h18"/></svg>
                </div>
                <div class="p-5">
                    <p class="text-[10px] font-bold text-accent-blue uppercase tracking-widest mb-1">Analgésicos</p>
                    <h3 class="font-semibold text-text-primary mb-3">Paracetamol 1g</h3>
                    <div class="flex items-center justify-between mb-5">
                        <span class="text-lg font-bold text-text-primary">$180.00</span>
                        <span class="text-[10px] font-bold uppercase py-1 px-2 bg-emerald-500/10 text-emerald-500 rounded-full">En Stock</span>
                    </div>
                    <button class="w-full py-2 bg-accent-blue/5 border border-accent-blue/20 text-accent-blue rounded-lg text-xs font-bold hover:bg-accent-blue hover:text-white transition-all">
                        Añadir a Cotización
                    </button>
                </div>
            </div>

            <div class="card p-0 overflow-hidden group">
                <div class="aspect-square bg-bg-deep flex items-center justify-center p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary opacity-20 group-hover:scale-110 transition-transform"><path d="M12 2v10"/><path d="M16 12a4 4 0 0 1-8 0"/><path d="M3 12h18"/><path d="M3 21h18"/></svg>
                </div>
                <div class="p-5">
                    <p class="text-[10px] font-bold text-accent-blue uppercase tracking-widest mb-1">Gastrointestinal</p>
                    <h3 class="font-semibold text-text-primary mb-3">Omeprazol 20mg</h3>
                    <div class="flex items-center justify-between mb-5">
                        <span class="text-lg font-bold text-text-primary">$320.00</span>
                        <span class="text-[10px] font-bold uppercase py-1 px-2 bg-amber-500/10 text-amber-500 rounded-full">Bajo Stock</span>
                    </div>
                    <button class="w-full py-2 bg-accent-blue/5 border border-accent-blue/20 text-accent-blue rounded-lg text-xs font-bold hover:bg-accent-blue hover:text-white transition-all">
                        Añadir a Cotización
                    </button>
                </div>
            </div>
            
            <div class="card p-0 overflow-hidden group">
                <div class="aspect-square bg-bg-deep flex items-center justify-center p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary opacity-20 group-hover:scale-110 transition-transform"><path d="M12 2v10"/><path d="M16 12a4 4 0 0 1-8 0"/><path d="M3 12h18"/><path d="M3 21h18"/></svg>
                </div>
                <div class="p-5">
                    <p class="text-[10px] font-bold text-accent-blue uppercase tracking-widest mb-1">Respiratorio</p>
                    <h3 class="font-semibold text-text-primary mb-3">Salbutamol Spray</h3>
                    <div class="flex items-center justify-between mb-5">
                        <span class="text-lg font-bold text-text-primary">$540.00</span>
                        <span class="text-[10px] font-bold uppercase py-1 px-2 bg-red-500/10 text-red-500 rounded-full">Agotado</span>
                    </div>
                    <button class="w-full py-2 bg-accent-blue/5 border border-accent-blue/20 text-accent-blue rounded-lg text-xs font-bold opacity-50 cursor-not-allowed">
                        Agotado
                    </button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
