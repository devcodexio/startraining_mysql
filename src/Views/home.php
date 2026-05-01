<?php
use App\Models\VacancyModel;
use App\Models\ConfigModel;

$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';

$vacancyModel = new VacancyModel();
$search = $_GET['search'] ?? '';
$carrera = $_GET['carrera'] ?? '';
$modalidad = $_GET['modalidad'] ?? '';
$vacancies = $vacancyModel->getAll(['search' => $search, 'carrera' => $carrera, 'modalidad' => $modalidad]);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarTraining | Encuentra tus Prácticas</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* =============================================
           RESET & BASE
        ============================================= */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* =============================================
           NAV — DESKTOP (fixed, glass pill)
        ============================================= */
        .nav-main {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            right: 1.5rem;
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 4rem;
            z-index: 2000;
            border-radius: 30px;
            border: 1px solid var(--border-glass);
        }

        .nav-logo {
            font-size: 2rem;
            margin: 0;
            flex-shrink: 0;
        }

        /* Desktop links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
        }

        /* Hamburger button — hidden on desktop */
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

        /* Animated X state */
        .nav-hamburger.is-open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .nav-hamburger.is-open span:nth-child(2) {
            opacity: 0;
        }

        .nav-hamburger.is-open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Mobile drawer — full screen overlay */
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

        /* Links dentro del drawer */
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

        .nav-drawer .nav-item-mobile:hover {
            color: var(--primary, #00f2fe);
        }

        .nav-drawer .btn-futuristic {
            margin-top: 2rem;
            width: 100%;
            max-width: 340px;
            text-align: center;
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        /* =============================================
           HERO
        ============================================= */
        .hero {
            padding: 10rem 1.5rem 4rem;
            text-align: center;
        }

        .hero h1 {
            font-size: clamp(2.2rem, 6vw, 5rem);
            margin-bottom: 1.5rem;
            line-height: 1.15;
        }

        .hero p {
            font-size: clamp(1rem, 2.5vw, 1.5rem);
            color: var(--text-muted);
            max-width: 800px;
            margin: 0 auto 3rem;
        }

        /* Search bar */
        .search-hero {
            max-width: 800px;
            margin: 0 auto;
            padding: 0.5rem;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .search-hero input {
            background: transparent;
            border: none;
            padding: 1rem 1.5rem;
            color: #fff;
            flex: 1 1 180px;
            min-width: 0;
            outline: none;
            font-size: 1rem;
        }

        .search-hero button {
            border-radius: 40px;
            flex-shrink: 0;
            padding: 0.85rem 2rem;
            font-size: 0.9rem;
            width: auto;
        }

        /* =============================================
           VACANCIES SECTION
        ============================================= */
        .vacancies-container {
            max-width: 1100px;
            margin: 0 auto 10rem;
            padding: 0 1.5rem;
        }

        .vacancies-header {
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .vacancies-header h2 {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
        }

        .vacancies-header p {
            color: var(--text-muted);
            margin: 0.25rem 0 0;
        }

        /* Vacancy card */
        .vacancy-card {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            border-radius: 30px;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .vacancy-logo {
            width: 80px;
            height: 80px;
            min-width: 80px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.02);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            flex-shrink: 0;
        }

        .vacancy-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vacancy-body {
            flex: 1 1 0;
            min-width: 0;
        }

        .vacancy-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }

        .vacancy-title-row h3 {
            font-size: clamp(1.1rem, 3vw, 1.6rem);
            margin: 0;
        }

        .vacancy-badge {
            background: rgba(0, 242, 254, 0.1);
            color: var(--primary);
            font-size: 0.8rem;
            padding: 6px 15px;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .vacancy-meta {
            color: var(--text-muted);
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            margin-bottom: 1rem;
        }

        .vacancy-description {
            color: var(--text-muted);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            font-size: clamp(0.85rem, 2vw, 1rem);
        }

        .vacancy-footer {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .vacancy-location {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .vacancy-location .fa-map-marker-alt {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        /* =============================================
           RESPONSIVE BREAKPOINTS
        ============================================= */

        /* ---- Tablet & Mobile (≤ 1024px) ---- */
        @media (max-width: 1024px) {
            .nav-main {
                top: 1rem;
                left: 1rem;
                right: 1rem;
                height: 70px;
                padding: 0 1.5rem;
                border-radius: 20px;
            }

            .nav-logo {
                font-size: 1.5rem;
            }

            .nav-links {
                display: none;
            }

            .nav-hamburger {
                display: flex;
            }

            .hero {
                padding: 8rem 1.25rem 3rem;
            }

            .vacancy-card {
                padding: 1.5rem;
                gap: 1.25rem;
            }

            .search-hero {
                border-radius: 20px;
                padding: 0.75rem;
            }

            .search-hero input {
                padding: 0.75rem 1rem;
            }

            .search-hero button {
                width: 100%;
            }
        }

        /* ---- Mobile (≤ 480px) ---- */
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .vacancy-card {
                flex-direction: column;
            }

            .vacancy-logo {
                width: 64px;
                height: 64px;
                min-width: 64px;
                border-radius: 16px;
            }

            .vacancy-footer {
                flex-direction: column;
                align-items: flex-start;
            }

            .vacancy-footer a {
                width: 100%;
                text-align: center;
            }

            .vacancies-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body class="animate">

    <!-- =============================================
         NAV
    ============================================= -->
    <nav class="nav-main glass animate">
        <h2 class="nav-logo logo-text"><?= htmlspecialchars($siteName) ?></h2>

        <!-- Desktop links -->
        <div class="nav-links" style="gap: 1.5rem;">
            <a href="/login" class="nav-item m-0 fw-800"
                style="background:transparent;color:var(--text-primary);text-decoration:none;font-size:1.1rem;letter-spacing:1px;">
                LOGIN
            </a>
            <a href="/register-company" class="btn-futuristic" style="padding:0.8rem 2rem;font-size:0.9rem;">
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


    <!-- =============================================
         HERO
    ============================================= -->
    <header class="hero animate">
        <h1 class="fw-800">
            <span class="text-gradient">El impulso que tu carrera</span><br>necesita está aquí.
        </h1>
        <p>Conectamos a estudiantes estrella con las empresas más innovadoras del Perú.</p>

        <form action="/" method="GET" class="search-hero">
            <input type="text" name="search" placeholder="Buscar por puesto o empresa..."
                value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-premium">Buscar Ahora</button>
        </form>
    </header>


    <!-- =============================================
         VACANCIES
    ============================================= -->
    <div class="vacancies-container">
        <div class="vacancies-header">
            <div>
                <h2>Vacantes Recientes</h2>
                <p>Explora las últimas oportunidades de prácticas pre-profesionales.</p>
            </div>
            <span class="text-muted small fw-600"><?= count($vacancies) ?> Convocatorias encontradas</span>
        </div>

        <?php foreach ($vacancies as $v): ?>
            <div class="glass-card vacancy-card animate">

                <div class="vacancy-logo">
                    <img src="<?= $v['foto_perfil'] ?: 'https://placehold.co/100x100/06070a/00f2fe?text=LOGO' ?>"
                        alt="Logo de <?= htmlspecialchars($v['nombre_comercial']) ?>">
                </div>

                <div class="vacancy-body">
                    <div class="vacancy-title-row">
                        <h3><?= htmlspecialchars($v['titulo_puesto']) ?></h3>
                        <span class="vacancy-badge"><?= htmlspecialchars($v['modalidad']) ?></span>
                    </div>

                    <p class="vacancy-meta">
                        <?= htmlspecialchars($v['nombre_comercial']) ?>
                        • <span class="text-primary"><?= htmlspecialchars($v['carrera']) ?></span>
                    </p>

                    <p class="vacancy-description"><?= htmlspecialchars($v['descripcion_puesto']) ?></p>

                    <div class="vacancy-footer">
                        <span class="vacancy-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($v['ubicacion'] ?: 'Remoto / Perú') ?>
                        </span>
                        <a href="/vacante/<?= $v['id'] ?>" class="btn-premium px-5">Ver Detalles &rarr;</a>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>


    <?php require_once __DIR__ . '/../Layouts/Footer.php'; ?>


    <!-- =============================================
         JS — Hamburger toggle
    ============================================= -->
    <script>
        (function () {
            const btn = document.getElementById('hamburgerBtn');
            const drawer = document.getElementById('navDrawer');

            if (!btn || !drawer) return;

            function openMenu() {
                drawer.classList.add('is-open');
                btn.classList.add('is-open');
                btn.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                drawer.classList.remove('is-open');
                btn.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            btn.addEventListener('click', function () {
                drawer.classList.contains('is-open') ? closeMenu() : openMenu();
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeMenu();
            });

            drawer.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeMenu);
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) closeMenu();
            });
        })();
    </script>

</body>

</html>