<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMPharma - Contacto</title>
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
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
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

        .btn-danger-transparent {
            background-color: var(--danger-bg);
            color: var(--danger-text);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
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

        .form-input, .form-textarea {
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

        .form-input:focus, .form-textarea:focus {
            border-color: var(--accent-blue);
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
            <a href="Contacto.php" class="nav-item active">
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
            <h2 class="text-2xl font-bold tracking-tight mb-2">Centro de Soporte</h2>
            <p class="text-text-secondary">Atención directa para gestiones farmacéuticas y logística.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-7">
                <div class="card">
                    <h3 class="text-lg font-bold mb-8 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-accent-blue"><path d="M21 15a2 2 0 0 1-2 2H7l4-4H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Enviar un mensaje
                    </h3>
                    
                    <form action="#" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                            <div class="input-group">
                                <label class="input-label">Asunto</label>
                                <select class="form-input">
                                    <option>Soporte Técnico</option>
                                    <option>Facturación</option>
                                    <option>Logística</option>
                                    <option>Quejas y Sugerencias</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label class="input-label">Referencia</label>
                                <input type="text" class="form-input" placeholder="# COT-2023-XXX">
                            </div>
                        </div>
                        
                        <div class="input-group">
                            <label class="input-label">Mensaje</label>
                            <textarea rows="6" class="form-textarea" placeholder="Describa su solicitud detalladamente..."></textarea>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="btn-primary">
                                Enviar Mensaje
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div class="card p-8 bg-gradient-to-br from-[#161b22] to-[#0d1117] group">
                    <h3 class="font-bold text-lg mb-8 uppercase tracking-tight">Canales Directos</h3>
                    <div class="space-y-8">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest">Línea Nacional</p>
                                <p class="text-base font-bold text-text-primary mt-0.5">+52 55 8922 1000</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 rounded-xl bg-blue-600/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-text-secondary uppercase tracking-widest">Correo Electrónico</p>
                                <p class="text-base font-bold text-text-primary mt-0.5">soporte@mmpharma.mx</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
