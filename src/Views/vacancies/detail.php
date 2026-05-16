<?php
use App\Models\VacancyModel;
use App\Models\ConfigModel;

$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';
$logoSitio = $config['logo_sitio'] ?? '';

$id = $matches[1] ?? 0;
$model = new VacancyModel();
$v = $model->getById($id);

if (!$v) {
    header('Location: /');
    exit;
}

$companyLogo = $v['foto_perfil'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($v['nombre_comercial']) . '&background=3b82f6&color=fff&size=128';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($v['titulo_puesto']) ?> | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/vacancies_detail.css">
</head>

<body class="bg-slate-50">

    <!-- =============================================
         NAV (IDENTICAL TO HOME)
    ============================================= -->
    <nav class="nav-main glass">
        <a href="/" class="nav-logo flex items-center gap-3" style="text-decoration:none;">
            <?php if ($logoSitio): ?>
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-full blur opacity-50 group-hover:opacity-100 transition duration-300"></div>
                    <img src="<?= htmlspecialchars($logoSitio) ?>" alt="Logo" class="relative w-12 h-12 object-cover rounded-[1rem] shadow-lg border border-white/20 transform hover:scale-105 transition-all duration-300">
                </div>
                <h2 class="logo-text m-0 text-xl font-black tracking-tight" style="background: linear-gradient(to right, #00f2fe, #4facfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; drop-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <?= htmlspecialchars($siteName) ?>
                </h2>
            <?php else: ?>
                <h2 class="logo-text m-0" style="font-size: 1.8rem;"><?= htmlspecialchars($siteName) ?></h2>
            <?php endif; ?>
        </a>

        <!-- Desktop links -->
        <div class="nav-links" style="gap: 1.5rem;">
            <a href="/" class="nav-item m-0 fw-800"
                style="background:transparent;color:var(--text-primary);text-decoration:none;font-size:1.1rem;letter-spacing:1px;">
                INICIO
            </a>
            <a href="/login" class="nav-item m-0 fw-800"
                style="background:transparent;color:var(--text-primary);text-decoration:none;font-size:1.1rem;letter-spacing:1px;">
                LOGIN
            </a>
            <a href="/register-company" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold py-3 px-8 rounded-full shadow-[0_0_15px_rgba(0,242,254,0.5)] hover:shadow-[0_0_25px_rgba(0,242,254,0.8)] transform hover:-translate-y-1 transition-all duration-300 no-underline">
                Registrarse &rarr;
            </a>
        </div>

        <!-- Hamburger button (mobile only) -->
        <button class="nav-hamburger" id="hamburgerBtn" aria-label="Abrir menú" aria-expanded="false"
            aria-controls="navDrawer">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <!-- Mobile drawer -->
    <div class="nav-drawer" id="navDrawer" role="dialog" aria-label="Menú de navegación">
        <a href="/" class="nav-item-mobile">Inicio</a>
        <a href="/login" class="nav-item-mobile">Login</a>
        <a href="/register-company" class="nav-item-mobile">Registrarse &rarr;</a>
    </div>

    <div class="animate">
    <header class="vacancy-hero">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 1.5rem;">
            <div class="row d-flex gap-5">

                <div class="col-8">
                    <div class="company-badge-large animate">
                        <img src="<?= $companyLogo ?>" alt="Logo">
                    </div>
                    <h1 class="fw-800 mb-2" style="font-size: 2.2rem; letter-spacing: -1px;">
                        <?= htmlspecialchars($v['titulo_puesto']) ?>
                    </h1>
                    <div class="d-flex gap-4 mb-5 text-muted small fw-600">
                        <span><i class="fas fa-building text-primary me-2"></i>
                            <?= htmlspecialchars($v['nombre_comercial']) ?></span>
                        <span><i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <?= htmlspecialchars($v['ubicacion'] ?: 'Remoto / Perú') ?></span>
                        <span><i class="fas fa-tag text-primary me-2"></i> <?= htmlspecialchars($v['carrera']) ?></span>
                    </div>

                    <div class="glass-card mb-5 animate" style="padding: 2.5rem; border-radius: 25px;">
                        <h3 class="mb-4 fw-800"
                            style="font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Descripción</h3>
                        <p class="text-secondary lh-lg mb-5" style="white-space: pre-line;">
                            <?= htmlspecialchars($v['descripcion_puesto']) ?>
                        </p>

                        <h3 class="mb-4 fw-800"
                            style="font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">Requisitos</h3>
                        <p class="text-secondary lh-lg" style="white-space: pre-line;">
                            <?= htmlspecialchars($v['requisitos']) ?>
                        </p>
                    </div>
                </div>

                <div class="col">
                    <div class="d-flex flex-column gap-4" style="position: sticky; top: 100px;">

                        <!-- Apply Card -->
                        <div class="glass-card animate"
                            style="padding: 2.5rem; border-radius: 25px; border-top: 5px solid var(--primary);">
                            <h2 class="fw-800 mb-3" style="font-size: 1.25rem;">¿Deseas postular?</h2>
                            <p class="text-muted small mb-4">Envía tu CV y nuestra IA evaluará tu perfil al instante
                                para la empresa.</p>

                            <a href="/vacante/<?= $id ?>/postular"
                                class="relative group overflow-hidden bg-slate-900 text-white font-black text-[10px] tracking-[0.2em] py-5 px-10 rounded-2xl transition-all duration-500 hover:shadow-[0_20px_50px_rgba(0,0,0,0.2)] hover:-translate-y-1 flex items-center justify-center gap-4 w-full mb-6 no-underline">
                                <div class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <span class="relative z-10 flex items-center gap-3">
                                    <i class="fas fa-paper-plane"></i> POSTULAR AHORA
                                </span>
                            </a>

                            <div class="d-flex align-items-center gap-3 p-3"
                                style="background: rgba(59,130,246,0.06); border-radius: 12px;">
                                <i class="fas fa-robot text-primary fs-4"></i>
                                <span class="xsmall fw-700 text-primary">Análisis IA optimizado activo</span>
                            </div>
                        </div>

                        <!-- Check Result Card -->
                        <div class="glass-card animate" style="padding: 2rem; border-radius: 25px;">
                            <h3 class="fw-800 mb-2" style="font-size: 1rem;">Consulta tu Resultado</h3>
                            <p class="text-muted xsmall mb-3">Si ya postulaste, ingresa tu DNI para ver tu puntaje de
                                match con esta vacante.</p>

                            <div class="d-flex gap-2">
                                <input type="text" id="dniConsulta" class="form-input" placeholder="Ingresar DNI"
                                    maxlength="8" style="padding: 0.6rem 1rem; font-size: 0.85rem;">
                                <button onclick="consultarResultado()" id="btnConsultarRes"
                                    class="bg-slate-900 hover:bg-cyan-600 text-white rounded-xl px-6 transition-all duration-300 shadow-lg shadow-slate-100 border-none outline-none cursor-pointer flex items-center justify-center">
                                    <i class="fas fa-search text-xs"></i>
                                </button>
                            </div>

                            <!-- Result Display (Hidden initially) -->
                            <div id="resultadoDniWrap" style="display: none;" class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="xsmall fw-800 text-muted">TU PUNTAJE IA</span>
                                    <span id="resMatchPct" class="fw-900 text-primary"
                                        style="font-size: 1.2rem;">0%</span>
                                </div>
                                <div
                                    style="height: 6px; background: rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
                                    <div id="resMatchBar"
                                        style="height: 100%; width: 0%; background: var(--primary); transition: width 0.6s ease;">
                                    </div>
                                </div>
                                <p id="resStatusTxt" class="xsmall fw-700 mb-2"></p>
                                <p id="resAnalisisMsg" class="text-muted xsmall" style="line-height: 1.4;"></p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </header>

    <?php require_once __DIR__ . '/../../Layouts/Footer.php'; ?>

    <!-- Global Modal Alert -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-glass">
            <span id="modalIcon" class="modal-icon text-primary"></span>
            <h2 id="modalTitle" class="mb-2 fw-800" style="font-size: 1.25rem;"></h2>
            <p id="modalMsg" class="text-muted mb-4 small" style="line-height: 1.6;"></p>
            <button onclick="StarAlert.hide()" class="btn-futuristic px-5">Aceptar</button>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('hamburgerBtn');
            const drawer = document.getElementById('navDrawer');
            if (!btn || !drawer) return;
            function openMenu() {
                drawer.classList.add('is-open'); btn.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true'); document.body.style.overflow = 'hidden';
            }
            function closeMenu() {
                drawer.classList.remove('is-open'); btn.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false'); document.body.style.overflow = '';
            }
            btn.addEventListener('click', function () { drawer.classList.contains('is-open') ? closeMenu() : openMenu(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });
            drawer.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', closeMenu); });
            window.addEventListener('resize', function () { if (window.innerWidth > 1024) closeMenu(); });
        })();

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

        async function consultarResultado() {
            const dni = document.getElementById('dniConsulta').value;
            if (dni.length < 8) return StarAlert.show('DNI Incompleto', 'Ingresa un DNI válido de 8 dígitos.', 'error');

            const btn = document.getElementById('btnConsultarRes');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const res = await fetch(`/api/postulacion/resultado?dni=${dni}&vacante_id=<?= $id ?>`);
                const data = await res.json();

                if (data.success) {
                    const pct = Math.round(data.match);
                    const color = pct >= 85 ? '#10b981' : pct >= 70 ? '#f59e0b' : '#ef4444';

                    const htmlResult = `
                        <div class="text-start mt-4 px-3">
                            <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background: rgba(59,130,246,0.06); border-radius: 16px; border: 1px solid rgba(59,130,246,0.1);">
                                <i class="fas fa-id-card text-primary" style="font-size: 1.5rem;"></i>
                                <div style="overflow:hidden;">
                                    <p class="xsmall text-muted fw-800 mb-0 uppercase" style="letter-spacing:1px;">Candidato(a)</p>
                                    <p class="fw-900 mb-0 text-truncate" style="font-size: 1rem; color: #fff;">${data.nombre}</p>
                                </div>
                            </div>
                            
                            <div class="text-center mb-4">
                                <p class="xsmall text-muted fw-800 mb-2 uppercase" style="letter-spacing:1.5px;">Puntuación de Compatibilidad (IA)</p>
                                <span class="fw-900" style="font-size: 3.5rem; color: ${color}; letter-spacing: -3px; display: block; line-height: 1;">${pct}%</span>
                            </div>

                            <div style="height: 12px; background: rgba(255,255,255,0.05); border-radius: 20px; overflow: hidden; margin-bottom: 2rem;">
                                <div style="height: 100%; width: ${pct}%; background: linear-gradient(to right, ${color}, #fff); opacity: 0.85; transition: width 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);"></div>
                            </div>

                            <div style="padding: 1.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); border-radius: 20px;">
                                <p class="fw-800 xsmall mb-2" style="color: ${color};"><i class="fas fa-robot me-2"></i>FEEDBACK DEL SISTEMA</p>
                                <p class="text-muted small lh-lg" style="font-style: italic;">"${data.analisis || 'Tu perfil ya ha sido analizado por nuestra inteligencia artificial. La empresa revisará tu puntaje pronto para contactarte si cumples con el perfil.'}"</p>
                            </div>
                        </div>
                    `;

                    StarAlert.show('Evaluación del Candidato', '', 'info');
                    document.getElementById('modalMsg').innerHTML = htmlResult;
                }
                else {
                    StarAlert.show('Aviso', data.error, 'info');
                }
            } catch (e) {
                StarAlert.show('Error', 'No se pudo conectar con el servidor.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-search"></i>';
            }
        }

        window.addEventListener('load', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('postulado') === 'success') {
                StarAlert.show('¡Postulación Recibida!', 'Hemos recibido tus datos con éxito. Tu CV está siendo analizado. La empresa recibirá tu puntuación pronto.', 'success');
            } else if (params.get('postulado') === 'error') {
                const msg = params.get('msg') || 'Hubo un error al procesar tu postulación.';
                StarAlert.show('Error en Postulación', msg, 'error');
            }
        });
    </script>
    </div>
</body>

</html>