<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresa | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; padding: 2rem;
            background: radial-gradient(circle at top left, rgba(59,130,246,0.1), transparent 400px),
                        radial-gradient(circle at bottom right, rgba(6,182,212,0.1), transparent 400px);
        }
        .register-container { width: 100%; max-width: 800px; }
        .grid-half { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .glass-card { padding: 2rem !important; }
            .grid-half { grid-template-columns: 1fr; gap: 1rem; }
            .form-group.mb-5 .d-flex { flex-direction: column; }
            .form-group.mb-5 .btn-futuristic { width: 100%; margin-top: 0.5rem; }
        }
        
        @media (max-width: 480px) {
            .glass-card { padding: 1.5rem !important; border-radius: 20px !important; }
            .register-container h1 { font-size: 1.4rem !important; }
        }
    </style>
</head>
<body class="animate">

    <div class="register-container">
        <div class="glass-card animate" style="padding: 3.5rem; border-radius: 30px;">
            <div class="text-center mb-5">
                <div class="sidebar-grid-icon mb-4 mx-auto" style="transform: scale(0.85);"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
                <h1 class="fw-800 mb-2" style="font-size: 1.75rem; letter-spacing: -1px;">Portal para Empresas</h1>
                <p class="text-secondary small">Crea tu cuenta institucional para publicar convocatorias de prácticas.</p>
            </div>

            <form id="mainRegisterForm" action="/register-company-process" method="POST" enctype="multipart/form-data">
                
                <!-- 1. RUC Lookup -->
                <div class="form-group mb-5">
                    <label class="form-label mb-2">Paso 1: Identificación RUC</label>
                    <div class="d-flex gap-2" id="rucLookupContainer">
                        <div class="input-with-icon flex-1">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="rucInput" class="form-input" placeholder="Ingresa los 11 dígitos del RUC" maxlength="11">
                        </div>
                        <input type="hidden" id="rucHidden" name="ruc">
                        <button type="button" id="btnConsultar" class="btn-futuristic py-3">
                             <i class="fas fa-search me-2"></i> Consultar
                        </button>
                    </div>
                </div>

                <div id="companyFields" style="display: none;" class="animate">
                    
                    <div class="mb-4 d-flex align-items-center gap-3 p-3" style="background: rgba(59, 130, 246, 0.04); border-radius: 15px; border: 1px solid rgba(59, 130, 246, 0.1);">
                        <i class="fas fa-check-circle text-primary fs-3"></i>
                        <div>
                            <p class="mb-0 fw-800 small" id="ruc_conf_text"></p>
                            <p class="mb-0 xsmall text-muted">Datos de SUNAT/RENIEC verificados correctamente.</p>
                        </div>
                    </div>

                    <div class="grid-half mb-4">
                        <div class="form-group">
                            <label class="form-label">Nombre Comercial</label>
                            <input type="text" id="nombre_comercial" name="nombre_comercial" class="form-input" required placeholder="Nombre de la marca">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sector / Actividad</label>
                            <input type="text" id="sector" name="sector" class="form-input" placeholder="Ej: Tecnología, Minería...">
                        </div>
                    </div>

                    <div class="grid-half mb-4">
                        <div class="form-group">
                            <label class="form-label">Correo de Contacto</label>
                            <input type="email" id="correo_contacto" name="correo_contacto" class="form-input" required placeholder="ejemplo@empresa.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-input" required maxlength="9" minlength="9" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/\D/g,'')" placeholder="987654321">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label">Dirección Fiscal / Oficina</label>
                        <input type="text" id="direccion" name="direccion" class="form-input" placeholder="Av. Los Pinos 123, Lima">
                    </div>

                    <div class="grid-half mb-4">
                        <div class="form-group">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-input" required placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Repetir Contraseña</label>
                            <input type="password" name="password_confirm" class="form-input" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <label class="form-label">Subir Logo (Archivo Imagen)</label>
                        <input type="file" name="foto_perfil" class="form-input" accept="image/*">
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn-futuristic w-100 py-3 fs-5">
                            <i class="fas fa-building me-2"></i> Confirmar Registro de Empresa
                        </button>
                        <p class="mt-4 text-center xsmall text-muted">
                            ¿Ya tienes cuenta corporativa? <a href="/login" class="text-primary text-decoration-none fw-800">Inicia sesión aquí</a>
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Modal -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-glass">
            <span id="modalIcon" class="modal-icon text-primary"></span>
            <h2 id="modalTitle" class="mb-2 fw-800" style="font-size: 1.25rem;"></h2>
            <p id="modalMsg" class="text-muted mb-4 small" style="line-height: 1.6;"></p>
            <button onclick="StarAlert.hide()" class="btn-futuristic px-5">Aceptar</button>
        </div>
    </div>

    <script>
        const StarAlert = {
            show(title, msg, type = 'info') {
                const overlay = document.getElementById('modalOverlay');
                const icon = document.getElementById('modalIcon');
                document.getElementById('modalTitle').innerText = title;
                document.getElementById('modalMsg').innerText = msg;
                const icons = { success: 'fa-check-circle', error: 'fa-exclamation-triangle', info: 'fa-info-circle' };
                icon.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i>`;
                overlay.style.display = 'flex';
            },
            hide() { document.getElementById('modalOverlay').style.display = 'none'; }
        };

        document.getElementById('btnConsultar').addEventListener('click', async function() {
            const ruc = document.getElementById('rucInput').value;
            if (ruc.length !== 11) return StarAlert.show('RUC Inválido', 'El RUC debe tener exactamente 11 dígitos.', 'error');
            
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            try {
                const res = await fetch(`/api/consultar-ruc?ruc=${ruc}`);
                const response = await res.json();

                if (response.success && response.data) {
                    const data = response.data; // Note: accessing nested 'data' from Service/Controller
                    document.getElementById('rucHidden').value = ruc;
                    document.getElementById('nombre_comercial').value = data.nombre_comercial || '';
                    document.getElementById('direccion').value = data.direccion || '';
                    document.getElementById('sector').value = data.sector || '';
                    document.getElementById('ruc_conf_text').innerText = `IDENTIFICADO: ${data.nombre_comercial}`;
                    document.getElementById('companyFields').style.display = 'block';
                    
                    // Smooth show
                    document.getElementById('companyFields').classList.add('animate');
                } else {
                    StarAlert.show('Consulta Fallida', 'No se pudo verificar el RUC: ' + (response.error || 'Número no encontrado.'), 'error');
                }
            } catch (e) {
                StarAlert.show('Error', 'No se pudo conectar con el servicio de consulta RUC.', 'error');
            } finally {
                this.innerHTML = '<i class="fas fa-search me-2"></i> Consultar';
                this.disabled = false;
            }
        });
    </script>
</body>
</html>
