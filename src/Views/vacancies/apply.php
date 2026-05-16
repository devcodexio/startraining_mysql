<?php
use App\Models\VacancyModel;
use App\Models\ConfigModel;

$id = $matches[1] ?? 0;
$model = new VacancyModel();
$v = $model->getById($id);
if (!$v) { header('Location: /'); exit; }

$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';
$logoSitio = $config['logo_sitio'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular a <?= htmlspecialchars($v['titulo_puesto']) ?> | StarTraining</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .text-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .animate-fade-up {
            animation: fadeUp 0.8s ease-out forwards;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Fix for absolute icons in inputs */
        .input-group i {
            z-index: 20;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">
    <!-- =============================================
         NAV (Matched with Home)
    ============================================= -->
    <nav class="nav-main glass">
        <a href="/" class="nav-logo flex items-center gap-3" style="text-decoration:none;">
            <?php if ($logoSitio): ?>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full blur opacity-50 group-hover:opacity-100 transition duration-300"></div>
                    <img src="<?= htmlspecialchars($logoSitio) ?>" alt="Logo" class="relative w-10 h-10 md:w-12 md:h-12 object-cover rounded-[1rem] shadow-lg border border-white/20 transform hover:scale-105 transition-all duration-300">
                </div>
                <h2 class="logo-text m-0 text-lg md:text-xl font-black tracking-tight" style="background: linear-gradient(to right, #00f2fe, #4facfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    <?= htmlspecialchars($siteName) ?>
                </h2>
            <?php else: ?>
                <h2 class="logo-text m-0 text-xl font-black tracking-tight"><?= htmlspecialchars($siteName) ?></h2>
            <?php endif; ?>
        </a>

        <div class="nav-links" style="gap: 1.5rem;">
            <a href="/vacante/<?= $id ?>" class="hidden md:flex items-center gap-2 text-slate-400 font-bold text-sm hover:text-slate-900 transition-colors no-underline">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="/login" class="nav-item m-0 fw-800 no-underline" style="color:var(--text-primary);font-size:0.9rem;letter-spacing:1px;">
                LOGIN
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-40 pb-20 px-6">
        <div class="max-w-3xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-10 animate-fade-up">
                <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-cyan-500 text-white text-[10px] font-black uppercase tracking-widest mb-6 shadow-xl shadow-cyan-100">
                    <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                    SISTEMA DE POSTULACIÓN IA
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-950 mb-4 tracking-tighter leading-tight">Envía tu Perfil</h1>
                <p class="text-slate-500 font-bold text-lg">
                    Aplicando para: <span class="text-cyan-600"><?= htmlspecialchars($v['titulo_puesto']) ?></span>
                </p>
                <div class="flex flex-wrap justify-center gap-2 mt-4">
                    <?php 
                        $car_list = explode(', ', $v['carrera'] ?? '');
                        foreach($car_list as $c_name): if(empty($c_name)) continue;
                    ?>
                        <span class="bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full border border-slate-300">
                            <?= htmlspecialchars($c_name) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-[2.5rem] shadow-[0_40px_100px_rgba(15,23,42,0.1)] overflow-hidden border border-slate-100 animate-fade-up" style="animation-delay: 0.1s;">
                <!-- AI Info Strip -->
                <div class="bg-slate-950 p-6 flex items-center gap-6 border-b border-white/5">
                    <div class="bg-cyan-500/10 p-3.5 rounded-2xl border border-cyan-500/20">
                        <i class="fas fa-robot text-2xl text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-white font-black text-sm uppercase tracking-widest mb-1">Análisis Inteligente Activo</p>
                        <p class="text-slate-400 text-xs font-bold leading-relaxed max-w-md">Tu CV será procesado por nuestra IA para medir tu compatibilidad con el puesto de forma inmediata.</p>
                    </div>
                </div>

                <form action="/vacancies/postular-process" method="POST" enctype="multipart/form-data" class="p-8 md:p-14 space-y-10">
                    <input type="hidden" name="vacante_id" value="<?= $id ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- DNI Field -->
                        <div class="md:col-span-2 space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400 ml-2">Documento de Identidad (DNI)</label>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="relative flex-grow group input-group">
                                    <i class="fas fa-id-card absolute left-6 top-1/2 -translate-y-1/2 text-cyan-500 text-xl"></i>
                                    <input type="text" name="dni" id="dniInput" 
                                        class="w-full bg-slate-50 border-2 border-slate-50 rounded-[1.5rem] py-5 pl-16 pr-8 text-slate-900 font-bold text-base focus:border-cyan-500 focus:bg-white transition-all outline-none shadow-sm"
                                        placeholder="Ingresa 8 dígitos" required maxlength="8" pattern="[0-9]{8}" oninput="this.value=this.value.replace(/\D/g,'')">
                                </div>
                                <button type="button" id="dniBtn" onclick="validateDNI()"
                                    class="bg-slate-950 text-white font-black text-[11px] tracking-[0.2em] px-10 py-5 rounded-[1.5rem] hover:bg-cyan-600 transition-all shadow-lg hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 shrink-0">
                                    <i class="fas fa-search"></i> VALIDAR DNI
                                </button>
                            </div>
                            <p id="dniStatus" class="text-[10px] font-black uppercase tracking-[0.2em] ml-6 text-slate-300 min-h-[1rem]"></p>
                        </div>

                        <!-- Full Name -->
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400 ml-2">Nombre Completo</label>
                            <div class="relative group input-group">
                                <i class="fas fa-user absolute left-6 top-1/2 -translate-y-1/2 text-cyan-500 text-xl"></i>
                                <input type="text" name="nombre_completo" id="nombreCompleto" 
                                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-[1.5rem] py-5 pl-16 pr-8 text-slate-900 font-bold text-base focus:border-cyan-500 focus:bg-white transition-all outline-none shadow-sm"
                                    placeholder="Consultado por DNI" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400 ml-2">Número Celular</label>
                            <div class="relative group input-group">
                                <i class="fas fa-phone absolute left-6 top-1/2 -translate-y-1/2 text-cyan-500 text-xl"></i>
                                <input type="tel" name="celular" 
                                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-[1.5rem] py-5 pl-16 pr-8 text-slate-900 font-bold text-base focus:border-cyan-500 focus:bg-white transition-all outline-none shadow-sm"
                                    placeholder="Ej: 987654321" required maxlength="9" minlength="9" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/\D/g,'')">
                            </div>
                        </div>

                        <!-- Institutional Email -->
                        <div class="md:col-span-2 space-y-3">
                            <label class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400 ml-2">Correo Institucional (.edu.pe)</label>
                            <div class="relative group input-group">
                                <i class="fas fa-graduation-cap absolute left-6 top-1/2 -translate-y-1/2 text-cyan-500 text-xl"></i>
                                <input type="email" name="correo_estudiante" 
                                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-[1.5rem] py-5 pl-16 pr-8 text-slate-900 font-bold text-base focus:border-cyan-500 focus:bg-white transition-all outline-none shadow-sm"
                                    placeholder="usuario@universidad.edu.pe" required pattern=".+@.+\.edu\.pe$">
                            </div>
                        </div>
                    </div>

                    <!-- CV Upload Section -->
                    <div class="space-y-3 pt-4">
                        <label class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400 ml-2">Currículum Vitae (PDF)</label>
                        <div id="dropZone" onclick="document.getElementById('cvInput').click()" 
                            class="relative group cursor-pointer border-3 border-dashed border-slate-100 bg-slate-50 rounded-[2.5rem] p-12 text-center transition-all hover:border-cyan-500 hover:bg-white hover:shadow-2xl hover:shadow-cyan-100">
                            <div class="bg-white w-24 h-24 rounded-[2rem] shadow-2xl mx-auto mb-8 flex items-center justify-center group-hover:scale-110 transition-transform duration-700 border border-slate-100">
                                <i class="fas fa-file-pdf text-4xl text-cyan-500"></i>
                            </div>
                            <h3 class="text-slate-950 font-black text-xl mb-3 tracking-tight" id="fileNameDisp">Selecciona o arrastra tu CV</h3>
                            <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.3em]">Solo archivos PDF — Máximo 5MB</p>
                            <input type="file" name="url_cv_pdf" id="cvInput" accept=".pdf" required class="hidden" onchange="handleFile(this)">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" class="group relative w-full overflow-hidden bg-slate-950 text-white font-black text-[11px] tracking-[0.3em] py-7 rounded-[1.5rem] shadow-[0_30px_60px_rgba(6,182,212,0.3)] hover:shadow-cyan-400/50 transition-all duration-500 transform hover:-translate-y-2 active:scale-[0.98] uppercase">
                            <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-700 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <span class="relative z-10 flex items-center justify-center gap-6">
                                COMPLETAR POSTULACIÓN <i class="fas fa-bolt text-cyan-400 text-lg transform group-hover:translate-x-3 transition-transform"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer Integrated (From Home) -->
    <?php require_once __DIR__ . '/../../Layouts/Footer.php'; ?>

    <script>
        // Drag & Drop
        const dropZone = document.getElementById('dropZone');
        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-cyan-500', 'bg-white'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-cyan-500', 'bg-white'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-cyan-500', 'bg-white');
            const file = e.dataTransfer.files[0];
            if (file && file.type === 'application/pdf') {
                document.getElementById('cvInput').files = e.dataTransfer.files;
                handleFile(document.getElementById('cvInput'));
            }
        });

        function handleFile(input) {
            if (input.files.length > 0) {
                const name = input.files[0].name;
                document.getElementById('fileNameDisp').innerText = name;
                document.getElementById('fileNameDisp').classList.add('text-cyan-600');
            }
        }

        async function validateDNI() {
            const dni = document.getElementById('dniInput').value.trim();
            const status = document.getElementById('dniStatus');
            const btn = document.getElementById('dniBtn');

            if (dni.length !== 8) {
                status.innerText = '✗ Ingresa exactamente 8 dígitos.';
                status.className = 'text-[10px] font-black uppercase tracking-widest ml-6 text-red-500';
                return;
            }

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            status.innerText = 'Consultando RENIEC...';
            status.className = 'text-[10px] font-black uppercase tracking-widest ml-6 text-slate-400';

            try {
                const res = await fetch(`/api/dni?dni=${dni}`);
                const data = await res.json();

                if (data.success && data.data) {
                    const nombre = data.data.nombre_completo || 
                                   ((data.data.nombres || '') + ' ' + (data.data.apellido_paterno || '') + ' ' + (data.data.apellido_materno || '')).trim();
                    document.getElementById('nombreCompleto').value = nombre;
                    status.innerText = '✓ Identidad verificada';
                    status.className = 'text-[10px] font-black uppercase tracking-widest ml-6 text-emerald-500';
                } else {
                    status.innerText = '✗ DNI no encontrado';
                    status.className = 'text-[10px] font-black uppercase tracking-widest ml-6 text-amber-500';
                }
            } catch (e) {
                status.innerText = '✗ Error de conexión';
                status.className = 'text-[10px] font-black uppercase tracking-widest ml-6 text-red-500';
            } finally {
                btn.innerHTML = '<i class="fas fa-search"></i> VALIDAR DNI';
                btn.disabled = false;
            }
        }

        document.getElementById('dniInput').addEventListener('input', function() {
            if (this.value.length === 8) validateDNI();
        });
    </script>
</body>
</html>
ript>
</body>
</html>
