<?php
use App\Models\VacancyModel;
use App\Models\ConfigModel;

$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';
$logoSitio = $config['logo_sitio'] ?? '';

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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/home.css">
</head>

<body>

    <!-- =============================================
         NAV
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
            <a href="/login" class="nav-item m-0 fw-800"
                style="background:transparent;color:var(--text-primary);text-decoration:none;font-size:1.1rem;letter-spacing:1px;">
                LOGIN
            </a>
            <a href="/register-company" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold py-3 px-8 rounded-full shadow-[0_0_15px_rgba(0,242,254,0.5)] hover:shadow-[0_0_25px_rgba(0,242,254,0.8)] transform hover:-translate-y-1 transition-all duration-300">
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
    <header class="hero animate relative overflow-hidden" style="background: url('/assets/img/hero_bg.png') center/cover no-repeat; border-bottom-left-radius: 50px; border-bottom-right-radius: 50px; margin-bottom: 3rem; padding-bottom: 6rem;">
        <!-- Subtle overlay to make text pop -->
        <div class="absolute inset-0 bg-white/30 backdrop-blur-[2px] z-0"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/70 z-0"></div>
        
        <div class="relative z-10">
            <h1 class="fw-800" style="text-shadow: 0 4px 20px rgba(255,255,255,0.8);">
                <span class="text-gradient">El impulso que tu carrera</span><br>necesita está aquí.
            </h1>
            <p style="text-shadow: 0 2px 10px rgba(255,255,255,0.9); font-weight: 500; color: #334155;">Conectamos a estudiantes estrella con las empresas más innovadoras del Perú.</p>

            <form action="/" method="GET" class="search-hero shadow-2xl backdrop-blur-md bg-white/40 border-white/50" style="box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
                <input type="text" name="search" placeholder="Buscar por puesto o empresa..."
                    value="<?= htmlspecialchars($search) ?>" style="color: #0f172a; font-weight: 500;">
                <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold py-3 px-8 rounded-full shadow-[0_0_15px_rgba(0,242,254,0.4)] hover:shadow-[0_0_25px_rgba(0,242,254,0.7)] transform hover:-translate-y-1 transition-all duration-300 border-none outline-none">Buscar Ahora</button>
            </form>
        </div>
    </header>


    <!-- =============================================
         VACANCIES GRID
    ============================================= -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div class="animate-fade-in">
                <h2 class="text-4xl font-black text-slate-900 mb-2">Vacantes Recientes</h2>
                <p class="text-slate-500 text-lg">Explora las últimas oportunidades de prácticas pre-profesionales.</p>
            </div>
            <span class="bg-white/50 backdrop-blur-md text-slate-600 text-xs font-black tracking-widest px-6 py-3 rounded-full border border-white/80 shadow-sm animate-fade-in">
                <?= count($vacancies) ?> CONVOCATORIAS ENCONTRADAS
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php foreach ($vacancies as $v): ?>
                <div class="group relative bg-white/70 backdrop-blur-2xl border border-white/60 rounded-[3rem] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] hover:shadow-[0_30px_70px_rgba(59,130,246,0.15)] transition-all duration-500 hover:-translate-y-3 flex flex-col h-full animate-fade-up">
                    
                    <!-- Top Section: Logo & Status -->
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-20 h-20 rounded-[1.5rem] overflow-hidden shadow-2xl border-4 border-white transform group-hover:scale-110 transition-transform duration-500">
                            <img src="<?= $v['foto_perfil'] ?: 'https://placehold.co/100x100/06070a/00f2fe?text=LOGO' ?>"
                                alt="Logo" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="bg-cyan-50 text-cyan-600 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-xl border border-cyan-100 shadow-sm">
                                <?= htmlspecialchars($v['modalidad']) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Middle Section: Title & Company -->
                    <div class="flex-grow">
                        <h3 class="text-2xl font-extrabold text-slate-900 mb-3 leading-tight group-hover:text-cyan-600 transition-colors duration-300">
                            <?= htmlspecialchars($v['titulo_puesto']) ?>
                        </h3>
                        <p class="text-sm font-bold text-slate-400 mb-5 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full"></span>
                            <?= htmlspecialchars($v['nombre_comercial']) ?>
                        </p>
                        
                        <div class="flex flex-wrap gap-2 mb-6">
                            <?php 
                                $car_list = explode(', ', $v['carrera'] ?? '');
                                foreach($car_list as $c_name): if(empty($c_name)) continue;
                            ?>
                                <span class="bg-blue-50/50 text-blue-600 text-[11px] font-extrabold px-3 py-1 rounded-lg border border-blue-100/50">
                                    <?= htmlspecialchars($c_name) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>

                        <p class="text-slate-500 text-sm line-clamp-3 mb-8 leading-relaxed opacity-80 group-hover:opacity-100 transition-opacity">
                            <?= htmlspecialchars($v['descripcion_puesto']) ?>
                        </p>
                    </div>

                    <!-- Bottom Section: Location & CTA -->
                    <div class="pt-8 border-t border-slate-100 flex items-center justify-between mt-auto">
                        <div class="flex flex-col">
                            <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Ubicación</span>
                            <span class="text-slate-700 text-xs font-bold flex items-center gap-1.5">
                                <i class="fas fa-map-marker-alt text-cyan-500"></i>
                                <?= htmlspecialchars($v['ubicacion'] ?: 'Remoto / Perú') ?>
                            </span>
                        </div>
                        <a href="/vacante/<?= $v['id'] ?>" 
                           class="relative z-10 inline-flex items-center justify-center bg-slate-900 text-white text-[11px] font-black tracking-widest py-4 px-8 rounded-2xl hover:bg-cyan-600 hover:shadow-cyan-200 transition-all duration-300 shadow-xl shadow-slate-200">
                            DETALLES <i class="fas fa-arrow-right ml-2 text-[9px] transform group-hover:translateX-1 transition-transform"></i>
                        </a>
                    </div>

                    <!-- Transparent Global Link Overlay -->
                    <a href="/vacante/<?= $v['id'] ?>" class="absolute inset-0 z-0 rounded-[3rem]" aria-label="Ver detalles de <?= htmlspecialchars($v['titulo_puesto']) ?>"></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>


    <?php require_once __DIR__ . '/../Layouts/Footer.php'; ?>
    <?php require_once __DIR__ . '/../Layouts/ChatBot.php'; ?>


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