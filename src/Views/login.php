<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 font-['Outfit']">
    <div class="max-w-5xl w-full bg-white rounded-[2.5rem] overflow-hidden shadow-[0_40px_100px_rgba(6,182,212,0.22)] flex flex-col md:flex-row min-h-[520px] animate-fade-in border border-slate-100">
        
        <!-- Left Side: Branding & Image (Bold Luxe Wide) -->
        <div class="md:w-[45%] relative overflow-hidden hidden md:block group">
            <img src="/assets/img/login_bg.png" alt="Recruitment" class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900/90 to-cyan-500/40 backdrop-blur-[1px]"></div>
            
            <div class="relative h-full flex flex-col justify-between p-12 text-white">
                <a href="/" class="flex items-center gap-4 no-underline group/logo">
                    <div class="bg-cyan-500 p-3 rounded-2xl shadow-[0_0_25px_rgba(6,182,212,0.5)] group-hover/logo:scale-110 transition-all duration-500">
                        <i class="fas fa-rocket text-2xl text-white"></i>
                    </div>
                    <span class="text-3xl font-black tracking-tighter">StarTraining</span>
                </a>
                
                <div class="animate-fade-up">
                    <div class="w-16 h-2 bg-cyan-400 rounded-full mb-6 shadow-[0_0_20px_rgba(34,211,238,0.8)]"></div>
                    <h2 class="text-4xl font-black mb-2 leading-tight tracking-tight">Acceso Premium.</h2>
                    <p class="text-cyan-100 text-sm font-black uppercase tracking-[0.3em]">Recruitment Hub</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form (Refined Alignment) -->
        <div class="md:w-[55%] p-12 md:p-18 flex flex-col justify-center bg-white">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-5">
                <div>
                    <div class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-cyan-500 text-white text-[11px] font-black uppercase tracking-widest mb-4 shadow-xl shadow-cyan-100">
                        <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        CONECTADO
                    </div>
                    <h1 class="text-5xl font-black text-slate-950 mb-0 tracking-tighter leading-none">Bienvenido</h1>
                </div>
                <p class="text-cyan-600 font-black text-xs uppercase tracking-[0.2em] pb-1.5 border-b-4 border-cyan-100">IDENTIFICACIÓN</p>
            </div>

            <form action="/login-process" method="POST" class="space-y-8">
                <div class="space-y-3">
                    <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 ml-2">E-mail Corporativo</label>
                    <div class="relative group">
                        <i class="fas fa-envelope-open absolute left-6 top-1/2 -translate-y-1/2 text-cyan-500 text-xl transition-all duration-300 z-20"></i>
                        <input type="text" name="login_user" 
                            class="relative z-10 w-full bg-slate-50 border-2 border-slate-50 rounded-[1.5rem] py-5 pl-16 pr-8 text-slate-900 font-bold text-[16px] focus:border-cyan-500 focus:bg-white transition-all duration-300 outline-none shadow-sm"
                            placeholder="admin@startraining.com" required>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 ml-2">Contraseña</label>
                    <div class="relative group">
                        <i class="fas fa-key absolute left-6 top-1/2 -translate-y-1/2 text-cyan-500 text-xl transition-all duration-300 z-20"></i>
                        <input type="password" name="login_pass" 
                            class="relative z-10 w-full bg-slate-50 border-2 border-slate-50 rounded-[1.5rem] py-5 pl-16 pr-8 text-slate-900 font-bold text-[16px] focus:border-cyan-500 focus:bg-white transition-all duration-300 outline-none shadow-sm"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="group relative w-full overflow-hidden bg-gradient-to-r from-cyan-600 via-cyan-500 to-blue-600 text-white font-black text-sm tracking-[0.4em] py-6 rounded-[1.5rem] shadow-[0_25px_50px_rgba(6,182,212,0.35)] hover:shadow-cyan-400/60 transition-all duration-500 transform hover:-translate-y-1.5 active:scale-[0.98]">
                        <span class="relative z-10 flex items-center justify-center gap-4 text-xs">
                            INGRESAR AL PANEL <i class="fas fa-arrow-right text-xs transform group-hover:translate-x-2 transition-transform"></i>
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-12 pt-10 border-t-2 border-slate-50 flex flex-col sm:flex-row justify-between items-center gap-6">
                <p class="text-slate-400 text-xs font-black uppercase tracking-widest opacity-60">¿Necesitas ayuda?</p>
                <div class="flex gap-10">
                    <a href="/register-company" class="text-cyan-600 text-xs font-black uppercase tracking-[0.3em] no-underline hover:text-blue-800 transition-all hover:scale-105">Registrar</a>
                    <a href="/" class="text-slate-300 text-xs font-black uppercase tracking-[0.3em] no-underline hover:text-slate-950 transition-all hover:scale-105">Volver</a>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
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

            const reg = p.get('reg');
            if (reg === 'success') {
                StarAlert.show(
                    '¡Solicitud Recibida!', 
                    'Tu registro ha sido enviado con éxito. Espera unas 24 horas, tu cuenta se pondrá en revisión y nos pondremos en contacto con ustedes. ¡Gracias por elegir StarTraining!', 
                    'success'
                );
            }
        });
    </script>
</body>
</html>
