<?php
use App\Models\VacancyModel;
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .vacancy-hero {
            padding: 8rem 0 4rem;
            background: linear-gradient(180deg, rgba(59, 130, 246, 0.05) 0%, transparent 100%);
        }

        .company-badge-large {
            width: 110px;
            height: 110px;
            border-radius: 28px;
            background: #fff;
            border: 4px solid #fff;
            box-shadow: var(--glass-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .company-badge-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .result-search-box {
            background: rgba(59, 130, 246, 0.04);
            border: 1px solid rgba(59, 130, 246, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        /* Hamburger button */
        .nav-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 6px;
            z-index: 2000;
            position: relative;
        }

        .nav-hamburger span {
            display: block;
            width: 26px;
            height: 2px;
            background: #fff;
            border-radius: 2px;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .nav-hamburger.is-open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .nav-hamburger.is-open span:nth-child(2) {
            opacity: 0;
        }

        .nav-hamburger.is-open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Drawer */
        .nav-drawer {
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1500;
            background: rgba(6, 7, 10, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: block;
            padding-top: 25vh;
            overflow-y: auto;
        }

        .nav-drawer.is-open {
            visibility: visible;
            opacity: 1;
            pointer-events: all;
        }

        .nav-drawer .nav-item-mobile {
            display: block;
            width: 100%;
            max-width: 340px;
            margin: 0 auto;
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #fff;
            text-decoration: none;
            padding: 1.1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            transition: color 0.2s;
        }

        @media (max-width: 1024px) {
            .nav-main {
                top: 1rem !important;
                left: 1rem !important;
                right: 1rem !important;
                height: 70px !important;
                padding: 0 1.5rem !important;
                border-radius: 20px !important;
            }

            .nav-logo {
                font-size: 1.2rem !important;
            }

            .nav-links {
                display: none !important;
            }

            .nav-hamburger {
                display: flex !important;
            }

            .vacancy-hero {
                padding-top: 7rem;
            }

            .row.d-flex {
                flex-direction: column !important;
                gap: 2rem !important;
            }

            .col-8,
            .col {
                width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
            }

            .d-flex.gap-4 {
                flex-wrap: wrap;
                gap: 1rem !important;
            }

            .col .d-flex.flex-column {
                position: static !important;
            }
        }
    </style>
</head>

<body class="animate">

    <!-- Responsive Navbar -->
    <nav class="nav-main glass animate"
        style="position: fixed; top: 1.5rem; left: 1.5rem; right: 1.5rem; height: 85px; display: flex; align-items: center; justify-content: space-between; padding: 0 4rem; z-index: 2000; border-radius: 30px; border: 1px solid var(--border-glass);">
        <div class="d-flex align-items-center gap-2">
            <div class="sidebar-grid-icon" style="transform: scale(0.6);">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <a href="/" class="nav-logo logo-text m-0"
                style="text-decoration: none; font-size: 1.5rem; flex-shrink: 0;">StarTraining</a>
        </div>

        <!-- Desktop Links -->
        <div class="nav-links" style="display: flex; align-items: center; gap: 2.5rem;">
            <a href="/" class="nav-item m-0 fw-800"
                style="background:transparent;color:var(--text-primary);text-decoration:none;font-size:1.1rem;letter-spacing:1px;">INICIO</a>
            <a href="/login" class="btn-futuristic" style="padding: 0.8rem 2rem; font-size: 0.9rem;">Login</a>
            <a href="/register-company" class="btn-futuristic" style="padding:0.8rem 2rem;font-size:0.9rem;">Registrarse
                &rarr;</a>
        </div>

        <!-- Hamburger button -->
        <button class="nav-hamburger" id="hamburgerBtn" aria-label="Abrir menú" aria-expanded="false"
            aria-controls="navDrawer">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <!-- Mobile drawer -->
    <div class="nav-drawer" id="navDrawer" role="dialog" aria-label="Menú de navegación">
        <a href="/" class="nav-item-mobile">Inicio</a>
        <a href="/login" class="nav-item-mobile">Login</a>
        <a href="/register-company" class="nav-item-mobile">Registrarse &rarr;</a>
    </div>

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
                                class="btn-futuristic w-100 py-3 mb-3 text-decoration-none">
                                <i class="fas fa-paper-plane me-2"></i> Postular Ahora
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
                                <button onclick="consultarResultado()" id="btnConsultarRes" class="btn-futuristic"
                                    style="padding: 0 1rem;">
                                    <i class="fas fa-search"></i>
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
</body>

</html>