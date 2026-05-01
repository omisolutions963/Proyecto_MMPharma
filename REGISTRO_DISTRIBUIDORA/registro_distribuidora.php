<?php
$titulo = 'MMPharma | Registro Distribuidora';
$pagina_actual = 'inicio';
$base = '../';

$solicitud_ok    = false;
$solicitud_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect fields
    // Add logic to save to database and process files
    // ...
    header("Location: ../CONFIRMACION_REGISTRO/confirmacion.php");
    exit;
}
?>
<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $titulo ?></title>
    <!-- Include fonts and tailwind -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
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
            "fontFamily": {
                    "headline": ["Inter"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
          },
        },
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .drag-over {
            border-color: #003e79 !important;
            background-color: #e0f2ff !important;
        }
    </style>
</head>
<body class="bg-surface text-on-surface antialiased min-h-screen flex flex-col">
<!-- TopNavBar -->
<nav class="bg-surface/95 backdrop-blur-md font-['Inter'] tracking-tight antialiased w-full top-0 sticky z-50 border-b border-slate-200 shadow-sm">
    <div class="flex justify-between items-center w-full px-6 py-3 max-w-7xl mx-auto">
        <div class="flex-1 flex justify-start">
            <a class="flex items-center gap-3 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white text-slate-700 rounded-full transition-all duration-300 text-sm font-bold shadow-sm group border border-slate-200 hover:border-primary" href="../SELECCIÓN_REGISTRO/selección_registro.php">
                <div class="w-6 h-6 flex items-center justify-center rounded-full bg-white text-primary group-hover:bg-white/20 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[16px] font-bold group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                </div>
                Volver
            </a>
        </div>
    </div>
</nav>

<main class="max-w-4xl mx-auto px-4 py-8 md:py-12 flex-grow flex flex-col items-center w-full">
    <div class="w-full bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-primary"></div>
        <div class="p-8 md:p-12">
            <header class="mb-10 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 text-primary">
                    <span class="material-symbols-outlined text-4xl">local_shipping</span>
                </div>
                <h1 class="text-3xl font-extrabold text-on-surface tracking-tight mb-3">Registro de Distribuidora</h1>
                <p class="text-on-surface-variant text-base">Completa tus datos y sube tus documentos para iniciar el proceso de alta.</p>
            </header>

            <form action="" method="POST" class="space-y-12" id="registroForm" enctype="multipart/form-data">
                <!-- Sección 1: Datos Generales -->
                <section>
                    <h2 class="flex items-center gap-2 text-sm font-bold text-primary uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">
                        <span class="material-symbols-outlined">badge</span> 1. Datos Generales
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nombre o Razón Social <span class="text-red-500">*</span></label>
                            <input name="razon_social" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="Ej. Distribuidora Médica S.A. de C.V." type="text" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nombre Comercial <span class="text-red-500">*</span></label>
                            <input name="nombre_comercial" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="Ej. DistriMed" type="text" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">RFC <span class="text-red-500">*</span></label>
                            <input name="rfc" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="Ej. DME010101ABC" type="text" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Régimen Fiscal <span class="text-red-500">*</span></label>
                            <select id="regimen_fiscal" name="regimen_fiscal" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" required>
                                <option value="" disabled selected>Selecciona tu régimen fiscal</option>
                                <option value="moral">General de Ley Personas Morales</option>
                                <option value="fisica">Personas Físicas con Actividades Empresariales</option>
                                <option value="resico">Régimen Simplificado de Confianza</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Representante Legal <span class="text-red-500">*</span></label>
                            <input name="representante_legal" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="Nombre completo del representante legal" type="text" required>
                        </div>
                    </div>
                </section>

                <!-- Sección 2: Dirección y Contacto -->
                <section>
                    <h2 class="flex items-center gap-2 text-sm font-bold text-primary uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">
                        <span class="material-symbols-outlined">location_on</span> 2. Dirección y Contacto
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Domicilio Fiscal (Validación) <span class="text-red-500">*</span></label>
                            <input name="domicilio_fiscal" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="Calle, número exterior e interior" type="text" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Colonia <span class="text-red-500">*</span></label>
                            <input name="colonia" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" type="text" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">C.P. <span class="text-red-500">*</span></label>
                            <input name="cp" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" type="text" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Ciudad/Municipio <span class="text-red-500">*</span></label>
                            <input name="ciudad" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" type="text" required>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Estado <span class="text-red-500">*</span></label>
                            <select name="estado" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" required>
                                <option value="" disabled selected>Selecciona tu estado</option>
                                <option value="Aguascalientes">Aguascalientes</option>
                                <option value="Baja California">Baja California</option>
                                <option value="Baja California Sur">Baja California Sur</option>
                                <option value="Campeche">Campeche</option>
                                <option value="Chiapas">Chiapas</option>
                                <option value="Chihuahua">Chihuahua</option>
                                <option value="Ciudad de México">Ciudad de México</option>
                                <option value="Coahuila">Coahuila</option>
                                <option value="Colima">Colima</option>
                                <option value="Durango">Durango</option>
                                <option value="Estado de México">Estado de México</option>
                                <option value="Guanajuato">Guanajuato</option>
                                <option value="Guerrero">Guerrero</option>
                                <option value="Hidalgo">Hidalgo</option>
                                <option value="Jalisco">Jalisco</option>
                                <option value="Michoacán">Michoacán</option>
                                <option value="Morelos">Morelos</option>
                                <option value="Nayarit">Nayarit</option>
                                <option value="Nuevo León">Nuevo León</option>
                                <option value="Oaxaca">Oaxaca</option>
                                <option value="Puebla">Puebla</option>
                                <option value="Querétaro">Querétaro</option>
                                <option value="Quintana Roo">Quintana Roo</option>
                                <option value="San Luis Potosí">San Luis Potosí</option>
                                <option value="Sinaloa">Sinaloa</option>
                                <option value="Sonora">Sonora</option>
                                <option value="Tabasco">Tabasco</option>
                                <option value="Tamaulipas">Tamaulipas</option>
                                <option value="Tlaxcala">Tlaxcala</option>
                                <option value="Veracruz">Veracruz</option>
                                <option value="Yucatán">Yucatán</option>
                                <option value="Zacatecas">Zacatecas</option>
                            </select>
                        </div>
                        
                        <!-- Contacto Principal -->
                        <div class="md:col-span-3 mt-4 border-t border-slate-100 pt-6">
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wide mb-4">Contacto Principal</h3>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Persona de contacto <span class="text-red-500">*</span></label>
                            <input name="persona_contacto" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="Nombre de quien gestiona" type="text" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Teléfono (Principal) <span class="text-red-500">*</span></label>
                            <input name="telefono" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="10 dígitos" type="tel" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Teléfono Celular <span class="text-red-500">*</span></label>
                            <input name="telefono_celular" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="10 dígitos" type="tel" required>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Correo Electrónico de Contacto (Será tu usuario) <span class="text-red-500">*</span></label>
                            <input name="email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-colors" placeholder="ejemplo@empresa.com" type="email" required>
                        </div>
                    </div>
                </section>

                <!-- Sección 3: Documentación -->
                <section>
                    <h2 class="flex items-center gap-2 text-sm font-bold text-primary uppercase tracking-widest mb-6 border-b border-slate-100 pb-3">
                        <span class="material-symbols-outlined">folder</span> 3. Documentación Requerida
                    </h2>
                    <p class="text-xs text-slate-500 mb-6">Sube los documentos escaneados. Formatos permitidos: PDF, JPG, PNG. Tamaño máximo por archivo: 15MB.</p>

                    <div class="space-y-4">
                        <?php
                        $documentos = [
                            ['id' => 'licencia_sanitaria', 'titulo' => 'Aviso de funcionamiento o Licencia Sanitaria'],
                            ['id' => 'comprobante_domicilio', 'titulo' => 'Comprobante de Domicilio'],
                            ['id' => 'alta_hacienda', 'titulo' => 'Alta de Hacienda'],
                            ['id' => 'identificacion_oficial', 'titulo' => 'Identificación oficial del representante legal o propietario']
                        ];
                        foreach($documentos as $doc): ?>
                            <div class="file-upload-wrapper bg-slate-50 border border-slate-200 border-dashed rounded-xl p-4 transition-all duration-300 relative">
                                <input type="file" name="<?= $doc['id'] ?>" id="<?= $doc['id'] ?>" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="flex items-center justify-between pointer-events-none">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary transition-colors icon-container">
                                            <span class="material-symbols-outlined file-icon">upload_file</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-700"><?= $doc['titulo'] ?> <span class="text-red-500">*</span></p>
                                            <p class="text-xs text-slate-500 file-name-display">Arrastra y suelta tu archivo o haz clic para explorar</p>
                                        </div>
                                    </div>
                                    <div class="text-xs font-semibold text-primary px-3 py-1 bg-white border border-slate-200 rounded-md shadow-sm">Examinar</div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Acta Constitutiva (Condicional) -->
                        <div id="wrapper_acta_constitutiva" class="file-upload-wrapper bg-slate-50 border border-slate-200 border-dashed rounded-xl p-4 transition-all duration-300 relative hidden">
                            <input type="file" name="acta_constitutiva" id="acta_constitutiva" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 file-input" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="flex items-center justify-between pointer-events-none">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary transition-colors icon-container">
                                        <span class="material-symbols-outlined file-icon">upload_file</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">Copia del alta constitutiva <span class="text-red-500">*</span></p>
                                        <p class="text-xs text-slate-500 file-name-display">Arrastra y suelta tu archivo o haz clic para explorar</p>
                                    </div>
                                </div>
                                <div class="text-xs font-semibold text-primary px-3 py-1 bg-white border border-slate-200 rounded-md shadow-sm">Examinar</div>
                            </div>
                        </div>

                    </div>
                </section>

                <!-- Validación Legal -->
                <section class="bg-slate-50 p-6 rounded-xl border border-slate-100">
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <input class="mt-1 w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary/20" type="checkbox" required>
                        <span class="text-sm text-slate-600 group-hover:text-primary transition-colors">He leído y acepto el <a href="#" class="font-bold underline text-primary">Aviso de Privacidad</a> de MMPharma y confirmo que los datos proporcionados son verídicos.</span>
                    </label>
                </section>

                <div class="pt-4">
                    <button class="w-full py-4 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:bg-secondary active:scale-[0.98] transition-all flex items-center justify-center gap-2 uppercase tracking-wide" type="submit">
                        <span class="material-symbols-outlined text-xl">send</span> Enviar Solicitud de Registro
                    </button>
                    <p class="text-center text-xs text-slate-500 mt-4 font-semibold uppercase tracking-widest opacity-80">Proceso 100% Seguro</p>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    // Logic for Persona Moral selection
    const regimenSelect = document.getElementById('regimen_fiscal');
    const actaWrapper = document.getElementById('wrapper_acta_constitutiva');
    const actaInput = document.getElementById('acta_constitutiva');

    regimenSelect.addEventListener('change', (e) => {
        if(e.target.value === 'moral') {
            actaWrapper.classList.remove('hidden');
            actaInput.setAttribute('required', 'required');
        } else {
            actaWrapper.classList.add('hidden');
            actaInput.removeAttribute('required');
        }
    });

    // File Upload Logic (Drag and Drop, Size, Preview)
    const maxFileSize = 15 * 1024 * 1024; // 15MB

    document.querySelectorAll('.file-input').forEach(input => {
        const wrapper = input.closest('.file-upload-wrapper');
        const nameDisplay = wrapper.querySelector('.file-name-display');
        const icon = wrapper.querySelector('.file-icon');
        const iconContainer = wrapper.querySelector('.icon-container');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => wrapper.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => wrapper.classList.remove('drag-over'), false);
        });

        wrapper.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if(files.length > 0) {
                input.files = files; 
                validateAndDisplay(input.files[0], nameDisplay, icon, iconContainer, input);
            }
        });

        input.addEventListener('change', (e) => {
            if(input.files.length > 0) {
                validateAndDisplay(input.files[0], nameDisplay, icon, iconContainer, input);
            }
        });
    });

    function validateAndDisplay(file, displayElement, iconElement, iconContainer, inputElement) {
        if (file.size > maxFileSize) {
            alert('El archivo "' + file.name + '" supera el límite de 15MB. Por favor, selecciona un archivo más pequeño.');
            inputElement.value = ''; 
            displayElement.textContent = "Arrastra y suelta tu archivo o haz clic para explorar";
            displayElement.classList.remove('text-green-600', 'font-medium');
            iconElement.textContent = "upload_file";
            iconContainer.classList.remove('bg-green-100', 'text-green-600');
            iconContainer.classList.add('bg-primary/10', 'text-primary');
            return;
        }

        displayElement.textContent = file.name;
        displayElement.classList.add('text-green-600', 'font-medium');
        iconElement.textContent = "check_circle";
        iconContainer.classList.remove('bg-primary/10', 'text-primary');
        iconContainer.classList.add('bg-green-100', 'text-green-600');
    }
</script>

<?php require_once '../INCLUDES/footer.php'; ?>
</body>
</html>
