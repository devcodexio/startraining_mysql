<?php
use App\Models\ConfigModel;
use App\Config\Database;

$configModel = new ConfigModel();
$config = $configModel->getSettings();

// Datos básicos de configuración
$siteName = $config['nombre_sitio'] ?? 'StarTraining';
$footerDesc = $config['footer_descripcion'] ?? 'Plataforma líder en reclutamiento de talento profesional.';
$siteMision = $config['mision_sitio'] ?? 'Nuestra misión es empoderar a la próxima generación de profesionales peruanos.';
$email = $config['email_contacto'] ?? 'contacto@startraining.com';
$phone = $config['telefono_contacto'] ?? '+51 987 654 321';

// Mapeo dinámico de redes para soportar múltiples nombres de claves (facebook vs facebook_url)
$fb = $config['facebook_url'] ?? $config['facebook'] ?? '#';
$tw = $config['twitter_url'] ?? $config['twitter'] ?? $config['x_url'] ?? '#';
$in = $config['linkedin_url'] ?? $config['linkedin'] ?? '#';
$ig = $config['instagram_url'] ?? $config['instagram'] ?? '#';

// ESTADÍSTICAS REALES DE LA BD
try {
    $db = Database::getConnection();
    $totalVacantes = $db->query("SELECT COUNT(*) FROM vacantes WHERE estado = 'abierta'")->fetchColumn() ?: 0;
    $totalPostulaciones = $db->query("SELECT COUNT(*) FROM postulaciones")->fetchColumn() ?: 0;
} catch (\Exception $e) { $totalVacantes = 0; $totalPostulaciones = 0; }
?>
<!-- FontAwesome 6 CDN (Garantiza que los iconos fab funcionen) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<footer class="footer-modern mt-5 mb-4 mx-auto animate" 
    style="max-width: 1200px; position:relative; overflow:hidden; border-radius: 35px; border: 1px solid var(--border-light); background: var(--glass-bg); padding: 3rem 4rem; box-shadow: var(--glass-shadow); backdrop-filter: blur(30px);">
    
    <div style="position:absolute; top:-100px; left:-100px; width:300px; height:300px; background:var(--primary); filter:blur(120px); opacity:0.1; border-radius:50%; z-index:0;"></div>
    <div style="position:absolute; bottom:-100px; right:10%; width:250px; height:250px; background:var(--secondary); filter:blur(110px); opacity:0.1; border-radius:50%; z-index:0;"></div>

    <div class="row position-relative g-4 align-items-start" style="z-index: 2;">
        <!-- Segmento 1: Branding -->
        <div class="col-md-4">
            <h3 class="logo-text d-flex align-items-center gap-3 mb-3" style="font-size: 1.85rem;">
                <i class="fas fa-meteor text-primary shadow-icon"></i> <?= htmlspecialchars($siteName) ?>
            </h3>
            <p class="text-secondary mb-3 xsmall fw-700" style="line-height: 1.5; opacity: 0.9;">
                <?= htmlspecialchars($footerDesc) ?>
            </p>
            <div class="d-flex gap-4 mt-3 pt-2">
                <div class="stat-mini">
                    <h4 class="mb-0 fw-900 text-primary" style="font-size: 1.3rem;"><?= $totalVacantes ?></h4>
                    <p class="xsmall text-muted fw-800 mb-0">VACANTES</p>
                </div>
                <div class="stat-mini">
                    <h4 class="mb-0 fw-900 text-primary" style="font-size: 1.3rem;"><?= $totalPostulaciones ?></h4>
                    <p class="xsmall text-muted fw-800 mb-0">POSTULANTES</p>
                </div>
            </div>
        </div>

        <!-- Segmento 2: Soporte -->
        <div class="col-md-4 text-center">
            <h5 class="fw-900 mb-4 text-primary ls-2">SOPORTE TÉCNICO</h5>
            <div class="d-flex flex-column gap-3 align-items-center">
                <div class="contact-pill hover-scale">
                    <i class="fas fa-envelope text-primary me-2"></i>
                    <span><?= htmlspecialchars($email) ?></span>
                </div>
                <div class="contact-pill hover-scale">
                    <i class="fas fa-phone-alt text-primary me-2"></i>
                    <span><?= htmlspecialchars($phone) ?></span>
                </div>
                <p class="xsmall text-muted mt-2 fw-600 ls-1">Centro de Ayuda 24/7</p>
            </div>
        </div>

        <!-- Segmento 3: Redes Sociales (Corregido y reforzado) -->
        <div class="col-md-4 text-md-end">
            <h5 class="fw-900 mb-4 text-primary ls-2">REDES SOCIALES</h5>
            <div class="social-links-grid d-flex gap-3 justify-content-md-end justify-content-center">
                <?php if($fb && $fb !== '#'): ?>
                    <a href="<?= htmlspecialchars($fb) ?>" target="_blank" class="social-btn-box fb" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                <?php endif; ?>
                <?php if($tw && $tw !== '#'): ?>
                    <a href="<?= htmlspecialchars($tw) ?>" target="_blank" class="social-btn-box tw" title="Twitter / X">
                        <i class="fab fa-twitter"></i>
                    </a>
                <?php endif; ?>
                <?php if($in && $in !== '#'): ?>
                    <a href="<?= htmlspecialchars($in) ?>" target="_blank" class="social-btn-box in" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                <?php endif; ?>
                <?php if($ig && $ig !== '#'): ?>
                    <a href="<?= htmlspecialchars($ig) ?>" target="_blank" class="social-btn-box ig" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                <?php endif; ?>
                
                <?php if(!$fb || $fb==='#' && !$ig || $ig==='#'): ?>
                    <!-- Fallback si todo está vacío para que no se vea el hueco -->
                    <span class="text-muted xsmall opacity-50">Enlaces pendientes</span>
                <?php endif; ?>
            </div>
            <p class="text-muted xsmall mt-5 mb-0 opacity-60 fw-700">© <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
    .ls-1 { letter-spacing: 1px; }
    .ls-2 { letter-spacing: 2px; text-transform: uppercase; font-size: 0.85rem; }
    .shadow-icon { filter: drop-shadow(0 0 5px rgba(var(--primary-rgb), 0.3)); }
    
    .contact-pill {
        background: rgba(var(--primary-rgb), 0.05);
        padding: 10px 22px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-secondary);
        border: 1px solid var(--border-glass);
        transition: all 0.3s ease;
    }
    .hover-scale:hover { transform: scale(1.05); background: rgba(var(--primary-rgb), 0.1); }

    .social-btn-box {
        width: 48px; height: 48px;
        background: rgba(255,255,255,0.08);
        border: 1px solid var(--border-glass);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-primary);
        font-size: 1.2rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none !important;
    }
    .social-btn-box:hover { transform: translateY(-8px) rotate(5deg); color: white !important; }

    .social-btn-box.fb:hover { background: #1877F2 !important; border-color: #1877F2 !important; }
    .social-btn-box.tw:hover { background: #000 !important; border-color: #333 !important; }
    .social-btn-box.in:hover { background: #0A66C2 !important; border-color: #0A66C2 !important; }
    .social-btn-box.ig:hover { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%) !important; border-color: transparent !important; }

    .stat-mini h4 { line-height: 1; }
    .stat-mini p { font-size: 0.55rem; letter-spacing: 1px; opacity: 0.7; }
</style>