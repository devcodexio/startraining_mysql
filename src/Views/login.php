<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="animate" style="display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div class="glass-card p-5" style="width: 450px;">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary">Acceso Unificado</h1>
            <p class="text-secondary small">Administradores | Empresas</p>
        </div>
        
        <form action="/login-process" method="POST">
            <div class="form-group mb-4">
                <label class="mb-2 text-secondary small fw-bold">Correo Electrónico</label>
                <input type="text" name="login_user" class="form-input" placeholder="empresa@ejemplo.com o usuario admin" required>
            </div>

            <div class="form-group mb-5">
                <label class="mb-2 text-secondary small">Contraseña</label>
                <input type="password" name="login_pass" class="form-input" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-premium w-100 py-3 fs-5">
                <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
            </button>
            
            <div class="mt-5 text-center d-flex justify-content-center gap-4 border-top border-secondary border-opacity-10 pt-4">
                <a href="/register-company" class="text-secondary small hover-primary text-decoration-none transition fw-bold">Registro de Empresa</a>
                <span class="text-secondary small opacity-20">|</span>
                <a href="/" class="text-secondary small hover-primary text-decoration-none transition">Volver</a>
            </div>
        </form>
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
            hide() { 
                document.getElementById('modalOverlay').style.display = 'none'; 
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        };

        window.addEventListener('load', () => {
            const p = new URLSearchParams(window.location.search);
            const err = p.get('error');
            if (err === 'account_pending') {
                StarAlert.show('Cuenta Pendiente', 'Tu registro fue recibido correctamente. Actualmente está siendo revisado por un administrador. Recibirás un correo cuando seas aprobado.', 'info');
            } else if (err === 'account_blocked') {
                StarAlert.show('Cuenta Bloqueada', 'Tu acceso ha sido restringido por infringir las políticas de la plataforma.', 'error');
            } else if (err === 'invalid_credentials') {
                StarAlert.show('Error de Acceso', 'El correo o la contraseña son incorrectos.', 'error');
            }
        });
    </script>
</body>
</html>
