<?php
use App\Config\Database;
$db = Database::getConnection();
$stmtT = $db->prepare("SELECT valor FROM configuracion WHERE clave = 'terminos_condiciones'");
$stmtT->execute();
$terminos = $stmtT->fetchColumn() ?: 'Acepto los términos y condiciones.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresa | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/register-company.css">
</head>
<body class="animate">

    <div class="register-container">
        <div class="glass-card animate" style="padding: 3.5rem; border-radius: 30px;">
            <div class="text-center mb-5">
                <div class="sidebar-grid-icon mb-4 mx-auto" style="transform: scale(0.85);"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
                <h1 class="fw-800 mb-2" style="font-size: 1.75rem; letter-spacing: -1px;">Portal para Empresas</h1>
                <p class="text-secondary small">Crea tu cuenta institucional para publicar convocatorias de prácticas.</p>
                
                <div class="mt-4 p-3 d-inline-flex align-items-center gap-2" style="background: rgba(var(--primary-rgb), 0.1); border-radius: 12px; border: 1px solid rgba(var(--primary-rgb), 0.2);">
                    <i class="fas fa-shield-halved text-primary"></i>
                    <span class="xsmall fw-700 text-primary">REVISIÓN OBLIGATORIA POR ADMINISTRADOR</span>
                </div>
            </div>

            <div class="alert-premium mb-5 p-4 animate" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(6, 182, 212, 0.05)); border-left: 5px solid var(--primary); border-radius: 15px;">
                <div class="d-flex gap-3">
                    <div class="icon-circle bg-primary text-white" style="width: 45px; height: 45px; flex-shrink: 0;"><i class="fas fa-info"></i></div>
                    <div>
                        <h6 class="fw-800 mb-1">Nota importante sobre el registro</h6>
                        <p class="mb-0 xsmall text-muted" style="line-height: 1.6;">
                            Su solicitud de registro entrará en un proceso de validación. <strong>El administrador debe otorgar permisos manuales</strong> tras verificar su RUC, DNI y Biometría. Se le enviará un <strong>correo electrónico de confirmación</strong> una vez su cuenta sea activada.
                        </p>
                    </div>
                </div>
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
                        <!-- Honeypot field for security -->
                        <div style="display:none;">
                            <input type="text" name="website_url" value="">
                        </div>
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
                            <input type="text" id="sector" name="sector" class="form-input" required placeholder="Ej: Tecnología, Minería...">
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
                        <input type="text" id="direccion" name="direccion" class="form-input" required placeholder="Av. Los Pinos 123, Lima">
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

                    <div class="grid-half mb-4">
                        <div class="form-group">
                            <label class="form-label">Logo de Empresa (Imagen)</label>
                            <input type="file" name="foto_perfil" class="form-input" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ficha RUC (PDF obligatorio) <i class="fas fa-shield-alt text-primary" title="Para validación de seguridad"></i></label>
                            <input type="file" name="ficha_ruc" class="form-input" accept="application/pdf" required>
                        </div>
                    </div>

                    <div class="grid-half mb-4">
                        <div class="form-group">
                            <label class="form-label">DNI Frente (Imagen/PDF)</label>
                            <input type="file" name="dni_frente" class="form-input" accept="image/*,application/pdf" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">DNI Reverso (Imagen/PDF)</label>
                            <input type="file" name="dni_reverso" class="form-input" accept="image/*,application/pdf" required>
                        </div>
                    </div>

                    <!-- Paso: Selfie en tiempo real -->
                    <div class="form-group mb-5">
                        <label class="form-label mb-3">Paso 3: Foto Selfie (Verificación Biométrica)</label>
                        <div class="glass-card p-4 text-center" style="background: rgba(var(--primary-rgb), 0.02); border: 1px dashed rgba(var(--primary-rgb), 0.2); border-radius: 25px;">
                            <div id="cameraContainer" class="mb-3 mx-auto overflow-hidden shadow-lg" style="width: 320px; height: 240px; border-radius: 20px; background: #000; position: relative;">
                                <video id="video" width="320" height="240" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                                <img id="selfiePreview" style="display:none; width: 100%; height: 100%; object-fit: cover; position: absolute; top:0; left:0;">
                            </div>
                            
                            <input type="hidden" name="foto_selfie_base64" id="foto_selfie_base64" required>
                            
                            <div class="d-flex justify-content-center gap-3">
                                <button type="button" id="btnStartCamera" class="btn-ghost py-2 px-4 small">
                                    <i class="fas fa-camera me-2"></i> Iniciar Cámara
                                </button>
                                <button type="button" id="btnCapture" class="btn-futuristic py-2 px-4 small" style="display:none;">
                                    <i class="fas fa-circle-dot me-2"></i> Capturar Foto
                                </button>
                                <button type="button" id="btnRetake" class="btn-ghost py-2 px-4 small" style="display:none;">
                                    <i class="fas fa-undo me-2"></i> Repetir
                                </button>
                            </div>
                            <p class="xsmall text-muted mt-3">Mira a la cámara y asegúrate de tener buena iluminación.</p>
                        </div>
                    </div>

                    <div class="form-group mb-5">
                        <div class="glass-card p-4 d-flex align-items-center justify-content-between gap-3" style="border-radius: 20px; background: rgba(59, 130, 246, 0.03); border: 1px solid rgba(59, 130, 246, 0.1);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" name="accept_terms" id="accept_terms" required style="width: 22px; height: 22px; cursor: not-allowed;" onclick="return false;">
                                </div>
                                <label for="accept_terms" class="small text-muted mb-0 fw-600">
                                    He leído y acepto los <span class="text-primary cursor-pointer fw-800" onclick="TermsModal.show()">Términos y Condiciones</span>
                                </label>
                            </div>
                            <button type="button" class="btn-ghost xsmall py-2" onclick="TermsModal.show()">
                                <i class="fas fa-external-link-alt me-1"></i> Leer
                            </button>
                        </div>
                    </div>

                    <div class="p-4 mb-5" style="background: rgba(59, 130, 246, 0.04); border-radius: 20px; border: 1px dashed rgba(59, 130, 246, 0.2);">
                        <div class="d-flex gap-3">
                            <i class="fas fa-info-circle text-primary mt-1"></i>
                            <div>
                                <p class="mb-1 fw-700 small">Proceso de Verificación</p>
                                <p class="mb-0 xsmall text-muted" style="line-height: 1.5;">
                                    Para garantizar la seguridad de nuestra plataforma, todas las solicitudes de registro son verificadas manualmente por nuestro equipo administrativo. Recibirás un correo cuando tu cuenta sea activada.
                                </p>
                            </div>
                        </div>
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

    <!-- Terms Modal -->
    <div id="termsModalOverlay" class="modal-overlay" style="z-index: 1000;">
        <div class="modal-glass" style="max-width: 650px; text-align: left; padding: 3rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-900 mb-0 d-flex align-items-center gap-3">
                    <i class="fas fa-gavel text-primary"></i> Términos Legales
                </h3>
                <button onclick="TermsModal.hide()" class="btn-ghost p-2"><i class="fas fa-times"></i></button>
            </div>
            <div class="terms-content mb-5 p-4" style="max-height: 350px; overflow-y: auto; background: rgba(255,255,255,0.03); border-radius: 15px; border: 1px solid var(--border-glass); line-height: 1.8; font-size: 0.9rem; color: var(--text-secondary);">
                <?= nl2br(htmlspecialchars($terminos)) ?>
            </div>
            <div class="d-flex gap-3">
                <button onclick="TermsModal.hide()" class="btn-ghost flex-1 py-3">Cerrar</button>
                <button onclick="TermsModal.accept()" class="btn-futuristic flex-1 py-3">
                    <i class="fas fa-check-double me-2"></i> He leído y acepto
                </button>
            </div>
        </div>
    </div>
    
    <!-- Custom Alert Modal -->
    <div id="modalOverlay" class="modal-overlay" style="display: none; z-index: 2000;">
        <div class="modal-glass" style="max-width: 450px; text-align: center; padding: 3rem;">
            <div id="modalIcon" class="mb-4 fs-1 text-primary"></div>
            <h3 id="modalTitle" class="fw-900 mb-3"></h3>
            <p id="modalMsg" class="text-muted mb-5"></p>
            <button onclick="StarAlert.hide()" class="btn-futuristic w-100 py-3">Entendido</button>
        </div>
    </div>

    <script>
        const StarAlert = {
            show(title, msg, type = 'info', fieldId = null) {
                const overlay = document.getElementById('modalOverlay');
                const icon = document.getElementById('modalIcon');
                document.getElementById('modalTitle').innerText = title;
                document.getElementById('modalMsg').innerText = msg;
                const icons = { success: 'fa-check-circle', error: 'fa-exclamation-triangle', info: 'fa-info-circle' };
                icon.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i>`;
                overlay.style.display = 'flex';

                if (fieldId) {
                    const field = document.getElementById(fieldId) || document.querySelector(`input[name="${fieldId}"]`);
                    if (field) {
                        field.style.borderColor = 'var(--danger)';
                        field.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        field.focus();
                        
                        // Reset after user types
                        field.addEventListener('input', function() {
                            this.style.borderColor = '';
                            this.style.boxShadow = '';
                        }, { once: true });
                    }
                }
            },
            hide() { document.getElementById('modalOverlay').style.display = 'none'; }
        };

        const TermsModal = {
            show() { 
                const overlay = document.getElementById('termsModalOverlay');
                overlay.style.display = 'flex';
                overlay.style.opacity = '0';
                setTimeout(() => {
                    overlay.style.transition = 'opacity 0.3s ease';
                    overlay.style.opacity = '1';
                }, 10);
            },
            hide() { 
                const overlay = document.getElementById('termsModalOverlay');
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 300);
            },
            accept() {
                const check = document.getElementById('accept_terms');
                check.checked = true;
                check.style.cursor = 'pointer';
                check.onclick = null;
                this.hide();
                StarAlert.show('Términos Aceptados', 'Has confirmado la lectura y aceptación de los términos legales.', 'success');
            }
        };

        // Handle URL errors
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            const err = urlParams.get('error');
            const msgs = {
                ruc_exists: 'Este RUC ya se encuentra registrado.',
                email_exists: 'Este correo electrónico ya está en uso.',
                ruc_invalid_verification: 'No pudimos verificar la validez de este RUC.',
                ruc_inactive_or_nohabido: 'El RUC debe estar en estado ACTIVO y condición HABIDO para registrarse.',
                phone_invalid: 'El teléfono debe tener 9 dígitos numéricos.',
                pass_mismatch: 'Las contraseñas no coinciden.',
                ficha_required: 'La Ficha RUC en PDF es obligatoria.',
                dni_required: 'Las fotos del DNI (ambas caras) son obligatorias.',
                selfie_required: 'La foto selfie es obligatoria para la verificación biometríca.',
                terms_not_accepted: 'Debes aceptar los términos y condiciones.',
                upload_ficha_failed: 'Error al subir el documento.',
                logo_required: 'El logo de la empresa es obligatorio.',
                upload_logo_failed: 'Error al subir el logo.',
                upload_dni_failed: 'Error al subir fotos de DNI.',
                upload_selfie_failed: 'Error al subir la foto selfie.',
                bot_detected: 'Detección de seguridad: Solicitud rechazada.',
                db_fail: 'Error interno al guardar los datos.'
            };
            if (msgs[err]) StarAlert.show('Registro Fallido', msgs[err], 'error');
        }

        if (urlParams.has('reg') && urlParams.get('reg') === 'success') {
            StarAlert.show(
                '¡Solicitud Recibida!', 
                'Tu registro ha sido enviado con éxito. Espera unas 24 horas, tu cuenta se pondrá en revisión y nos pondremos en contacto con ustedes. ¡Gracias por elegir StarTraining!', 
                'success'
            );
        }

        document.getElementById('btnConsultar').addEventListener('click', async function() {
            const ruc = document.getElementById('rucInput').value;
            if (ruc.length !== 11) return StarAlert.show('RUC Inválido', 'El RUC debe tener exactamente 11 dígitos.', 'error', 'rucInput');
            
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            try {
                const res = await fetch(`/api/consultar-ruc?ruc=${ruc}`);
                const response = await res.json();

                if (response.success && response.data) {
                    const data = response.data;
                    document.getElementById('rucHidden').value = ruc;
                    document.getElementById('nombre_comercial').value = data.nombre_comercial || '';
                    document.getElementById('direccion').value = data.direccion || '';
                    document.getElementById('sector').value = data.sector || '';
                    document.getElementById('ruc_conf_text').innerText = `IDENTIFICADO: ${data.nombre_comercial}`;
                    document.getElementById('companyFields').style.display = 'block';
                    document.getElementById('companyFields').classList.add('animate');
                } else {
                    StarAlert.show('Consulta Fallida', 'No se pudo verificar el RUC: ' + (response.error || 'Número no encontrado.'), 'error', 'rucInput');
                }
            } catch (e) {
                StarAlert.show('Error', 'No se pudo conectar con el servicio de consulta RUC.', 'error');
            } finally {
                this.innerHTML = '<i class="fas fa-search me-2"></i> Consultar';
                this.disabled = false;
            }
        });

        // Camera Logic
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('selfiePreview');
        const inputBase64 = document.getElementById('foto_selfie_base64');
        const btnStart = document.getElementById('btnStartCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnRetake = document.getElementById('btnRetake');

        btnStart.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" }, audio: false });
                video.srcObject = stream;
                btnStart.style.display = 'none';
                btnCapture.style.display = 'inline-block';
            } catch (err) {
                StarAlert.show('Error de Cámara', 'No se pudo acceder a la cámara. Asegúrate de dar permisos.', 'error');
            }
        });

        btnCapture.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, 320, 240);
            const data = canvas.toDataURL('image/png');
            
            inputBase64.value = data;
            preview.src = data;
            preview.style.display = 'block';
            video.style.display = 'none';
            
            btnCapture.style.display = 'none';
            btnRetake.style.display = 'inline-block';
        });

        btnRetake.addEventListener('click', () => {
            inputBase64.value = '';
            preview.style.display = 'none';
            video.style.display = 'block';
            btnCapture.style.display = 'inline-block';
            btnRetake.style.display = 'none';
        });
        
        // Form Validation Enhancement
        document.getElementById('mainRegisterForm').addEventListener('submit', function(e) {
            const password = this.querySelector('input[name="password"]');
            const confirm = this.querySelector('input[name="password_confirm"]');
            
            // Password Restrictions
            if (password.value.length < 8) {
                e.preventDefault();
                StarAlert.show('Seguridad Insuficiente', 'La contraseña debe tener al menos 8 caracteres para proteger tu cuenta.', 'error', 'password');
                return false;
            }

            if (password.value !== confirm.value) {
                e.preventDefault();
                StarAlert.show('Contraseñas Diferentes', 'Las contraseñas ingresadas no coinciden. Por favor, verifica.', 'error', 'password_confirm');
                return false;
            }

            if (!inputBase64.value) {
                e.preventDefault();
                StarAlert.show('Falta Selfie', 'Por favor, captura tu foto selfie para la verificación biométrica obligatoria.', 'error');
                return false;
            }
        });

        // File Size & Type Restrictions
        const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
        const fileInputs = document.querySelectorAll('input[type="file"]');

        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    
                    if (file.size > MAX_FILE_SIZE) {
                        StarAlert.show('Archivo muy pesado', `El archivo supera el límite de 2MB. Por favor, sube una versión más ligera.`, 'error', this.name);
                        this.value = '';
                        return;
                    }

                    if (this.name === 'foto_perfil') {
                        if (!file.type.startsWith('image/')) {
                            StarAlert.show('Formato Inválido', 'El logo de la empresa debe ser una imagen (JPG, PNG, WEBP).', 'error', this.name);
                            this.value = '';
                        }
                    } else if (this.name === 'ficha_ruc') {
                        if (file.type !== 'application/pdf') {
                            StarAlert.show('Formato Inválido', 'La Ficha RUC debe ser un archivo PDF.', 'error', this.name);
                            this.value = '';
                        }
                    } else if (this.name === 'dni_frente' || this.name === 'dni_reverso') {
                        if (!file.type.startsWith('image/') && file.type !== 'application/pdf') {
                            StarAlert.show('Formato Inválido', 'Los documentos de identidad deben ser Imagen o PDF.', 'error', this.name);
                            this.value = '';
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
