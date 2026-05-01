<?php
use App\Models\VacancyModel;
$id = $matches[1] ?? 0;
$model = new VacancyModel();
$v = $model->getById($id);
if (!$v) { header('Location: /'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postular a <?= htmlspecialchars($v['titulo_puesto']) ?> | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body { background: var(--bg-main); }
        
        .landing-nav {
            position: fixed; top: 1.5rem; left: 1.5rem; right: 1.5rem;
            height: 75px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 3rem;
            z-index: 100;
            background: rgba(7,10,15,0.85);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            backdrop-filter: blur(20px);
        }
        
        .page-wrap {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 120px 2rem 4rem;
        }
        
        .apply-card {
            width: 100%; max-width: 860px;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 36px;
            padding: 4rem;
            box-shadow: 0 40px 80px rgba(0,0,0,0.3);
        }
        
        .file-drop-zone {
            background: rgba(var(--primary-rgb), 0.03);
            border: 2px dashed rgba(var(--primary-rgb), 0.25);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-drop-zone:hover, .file-drop-zone.drag-over {
            background: rgba(var(--primary-rgb), 0.06);
            border-color: var(--primary);
            box-shadow: var(--shadow-neon);
        }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } .apply-card { padding: 2.5rem 1.5rem; } }
        
        .info-strip {
            display: flex; align-items: center; gap: 1rem;
            background: rgba(var(--primary-rgb), 0.04);
            border: 1px solid rgba(var(--primary-rgb), 0.12);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 2.5rem;
        }
    </style>
</head>
<body>
    <nav class="landing-nav">
        <span class="logo-text" style="font-size: 1.4rem;">StarTraining</span>
        <a href="/vacante/<?= $id ?>" class="btn-ghost py-2 px-4" style="font-size: 0.8rem;">
            <i class="fas fa-arrow-left me-2"></i> Volver a la Vacante
        </a>
    </nav>

    <div class="page-wrap">
        <div class="apply-card animate">
            <!-- Header -->
            <div class="text-center mb-5">
                <span class="badge badge-primary mb-3" style="font-size: 0.7rem; padding: 0.4rem 1rem;">POSTULACIÓN IA ACTIVADA</span>
                <h1 class="fw-900 text-gradient mb-2" style="font-size: 2.2rem; letter-spacing: -1px;">Envía tu Perfil</h1>
                <p class="text-muted">Puesto: <strong class="text-primary"><?= htmlspecialchars($v['titulo_puesto']) ?></strong> — <?= htmlspecialchars($v['nombre_comercial']) ?></p>
            </div>

            <!-- AI Banner -->
            <div class="info-strip">
                <i class="fas fa-robot text-primary" style="font-size: 1.5rem; flex-shrink:0;"></i>
                <div>
                    <p class="fw-800 mb-0" style="font-size: 0.9rem;">Sistema de IA Activo</p>
                    <p class="text-muted xsmall mb-0">Tu CV será analizado y recibirás una puntuación de compatibilidad al instante.</p>
                </div>
            </div>

            <form action="/vacancies/postular-process" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="vacante_id" value="<?= $id ?>">
                
                <!-- DNI Row -->
                <div class="form-group">
                    <label class="form-label">Número de DNI</label>
                    <div class="d-flex gap-2">
                        <input type="text" name="dni" id="dniInput" class="form-input" placeholder="Escribe 8 dígitos" required maxlength="8" pattern="[0-9]{8}" oninput="this.value=this.value.replace(/\D/g,'')">
                        <button type="button" id="dniBtn" class="btn-futuristic" style="padding: 1rem 1.5rem; white-space: nowrap; flex-shrink: 0;" onclick="validateDNI()">
                            <i class="fas fa-search me-1"></i> Validar
                        </button>
                    </div>
                    <p id="dniStatus" class="xsmall mt-1 text-muted"></p>
                </div>
                
                <!-- Name & Phone -->
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="nombreCompleto" class="form-input" placeholder="Se cargará desde RENIEC" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Celular</label>
                        <input type="tel" name="celular" class="form-input" placeholder="987654321" required maxlength="9" minlength="9" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/\D/g,'')">
                    </div>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label class="form-label">Correo Institucional (.edu.pe)</label>
                    <input type="email" name="correo_estudiante" class="form-input" placeholder="usuario@universidad.edu.pe" required pattern=".+@.+\.edu\.pe$">
                </div>

                <!-- CV Upload -->
                <div class="form-group">
                    <label class="form-label">Currículum Vitae (PDF)</label>
                    <div class="file-drop-zone" id="dropZone" onclick="document.getElementById('cvInput').click()">
                        <i class="fas fa-file-pdf text-primary" style="font-size: 2.5rem; margin-bottom: 1rem; display:block;"></i>
                        <p class="fw-800 mb-1" id="fileNameDisp">Clic para seleccionar o arrastra aquí</p>
                        <p class="text-muted xsmall mb-0">Solo archivos PDF. Máx. 5MB.</p>
                        <input type="file" name="url_cv_pdf" id="cvInput" accept=".pdf" required class="d-none" onchange="handleFile(this)">
                    </div>
                </div>

                <button type="submit" class="btn-futuristic w-100 mt-3" style="padding: 1.2rem; font-size: 1rem;">
                    <i class="fas fa-bolt me-2"></i> ENVIAR POSTULACIÓN
                </button>
            </form>
        </div>
    </div>

    <script>
        // Drag & Drop
        const dropZone = document.getElementById('dropZone');
        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
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
                document.getElementById('fileNameDisp').style.color = 'var(--primary)';
                dropZone.style.borderColor = 'var(--primary)';
            }
        }

        async function validateDNI() {
            const dni = document.getElementById('dniInput').value.trim();
            const status = document.getElementById('dniStatus');
            const btn = document.getElementById('dniBtn');

            if (dni.length !== 8) {
                status.innerText = '✗ Ingresa exactamente 8 dígitos.';
                status.style.color = 'var(--danger)';
                return;
            }

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            status.innerText = 'Consultando RENIEC...';
            status.style.color = 'var(--text-secondary)';

            try {
                const res = await fetch(`/api/dni?dni=${dni}`);
                const data = await res.json();

                if (data.success && data.data) {
                    const nombre = data.data.nombre_completo || 
                                   ((data.data.nombres || '') + ' ' + (data.data.apellido_paterno || '') + ' ' + (data.data.apellido_materno || '')).trim();
                    document.getElementById('nombreCompleto').value = nombre;
                    status.innerText = '✓ Identidad verificada con RENIEC';
                    status.style.color = 'var(--success)';
                } else {
                    status.innerText = '✗ DNI no encontrado. Ingresa tu nombre manualmente.';
                    status.style.color = 'var(--warning)';
                }
            } catch (e) {
                status.innerText = '✗ Error de conexión. Ingresa tu nombre manualmente.';
                status.style.color = 'var(--danger)';
            } finally {
                btn.innerHTML = '<i class="fas fa-search me-1"></i> Validar';
                btn.disabled = false;
            }
        }

        // Auto-validate on 8 digits
        document.getElementById('dniInput').addEventListener('input', function() {
            if (this.value.length === 8) validateDNI();
        });
    </script>
</body>
</html>
