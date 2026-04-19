<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMPharma - Perfil</title>
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
            padding: 32px;
        }

        .btn-primary {
            background-color: var(--accent-blue);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
        }

        .btn-outline {
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .input-group {
            margin-bottom: 24px;
        }

        .input-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-secondary);
            margin-bottom: 8px;
            margin-left: 4px;
        }

        .form-input {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 12px 16px;
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--accent-blue);
        }

        .btn-danger-transparent {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            padding: 10px 24px;
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
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/></svg>
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
            <a href="Perfil.php" class="nav-item active">
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
        <header class="mb-10">
            <h2 class="text-2xl font-bold tracking-tight mb-2">Mi Perfil</h2>
            <p class="text-text-secondary">Gestione los detalles de su establecimiento y la configuración de su cuenta.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="card">
                    <h3 class="text-lg font-bold mb-8 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-blue"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Información del Establecimiento
                    </h3>
                    
                    <form action="#" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                            <div class="input-group">
                                <label class="input-label">Razón Social</label>
                                <input type="text" class="form-input" value="Farmacias del Centro S.A. de C.V.">
                            </div>
                            <div class="input-group">
                                <label class="input-label">RFC</label>
                                <input type="text" class="form-input" value="FDC941012HG5">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Email de Contacto</label>
                                <input type="email" class="form-input" value="administracion@fsanjorge.mx">
                            </div>
                            <div class="input-group">
                                <label class="input-label">Teléfono</label>
                                <input type="tel" class="form-input" value="+52 55 1234 5678">
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="btn-primary">Actualizar Información</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-[11px] font-bold text-text-secondary uppercase tracking-widest mb-6">Seguridad</h3>
                    <div class="space-y-4">
                        <button class="w-full flex items-center justify-between p-4 bg-white/2 rounded-lg border border-transparent hover:border-accent-blue/30 transition-all group">
                            <span class="text-sm font-medium text-text-primary group-hover:text-accent-blue">Cambiar Contraseña</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary group-hover:text-accent-blue"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </button>
                        <button class="w-full flex items-center justify-between p-4 bg-white/2 rounded-lg border border-transparent hover:border-accent-blue/30 transition-all group">
                            <span class="text-sm font-medium text-text-primary group-hover:text-accent-blue">Configurar Notificaciones</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-text-secondary group-hover:text-accent-blue"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        </button>
                    </div>
                </div>

                <div class="card p-6 border-red-500/20">
                    <h3 class="text-[11px] font-bold text-red-500 uppercase tracking-widest mb-4">Zona de Peligro</h3>
                    <p class="text-xs text-text-secondary mb-6 leading-relaxed">Una vez eliminada la cuenta, no hay marcha atrás. Por favor, esté seguro.</p>
                    <button class="w-full btn-danger-transparent bg-red-500/5 hover:bg-red-500/10 transition-colors">Solicitar Baja de Portal</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
