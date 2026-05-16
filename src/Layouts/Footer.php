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
} catch (\Exception $e) {
    $totalVacantes = 0;
    $totalPostulaciones = 0;
}
?>
<!-- FontAwesome 6 CDN (Garantiza que los iconos fab funcionen) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<footer class="footer-modern mt-5 mb-4 animate"
    style="width: 100%; position:relative; overflow:hidden; border-radius: 35px; border: 1px solid var(--border-light); background: var(--glass-bg); padding: 3rem 4rem; box-shadow: var(--glass-shadow); backdrop-filter: blur(30px);">

    <div
        style="position:absolute; top:-100px; left:-100px; width:300px; height:300px; background:var(--primary); filter:blur(120px); opacity:0.1; border-radius:50%; z-index:0;">
    </div>
    <div
        style="position:absolute; bottom:-100px; right:10%; width:250px; height:250px; background:var(--secondary); filter:blur(110px); opacity:0.1; border-radius:50%; z-index:0;">
    </div>

    <div class="row position-relative g-4 align-items-start" style="z-index: 2;">
        <!-- Segmento 1: Branding -->
        <div class="col-4 footer-col">
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
        <div class="col-4 footer-col text-center">
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
        <div class="col-4 footer-col text-md-end">
            <h5 class="fw-900 mb-4 text-primary ls-2">REDES SOCIALES</h5>
            <div class="social-links-grid d-flex gap-3">
                <?php if ($fb && $fb !== '#'): ?>
                    <a href="<?= htmlspecialchars($fb) ?>" target="_blank" class="social-btn-box fb" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                <?php endif; ?>
                <?php if ($tw && $tw !== '#'): ?>
                    <a href="<?= htmlspecialchars($tw) ?>" target="_blank" class="social-btn-box tw" title="Twitter / X">
                        <i class="fab fa-twitter"></i>
                    </a>
                <?php endif; ?>
                <?php if ($in && $in !== '#'): ?>
                    <a href="<?= htmlspecialchars($in) ?>" target="_blank" class="social-btn-box in" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                <?php endif; ?>
                <?php if ($ig && $ig !== '#'): ?>
                    <a href="<?= htmlspecialchars($ig) ?>" target="_blank" class="social-btn-box ig" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                <?php endif; ?>

                <?php if (!$fb || $fb === '#' && !$ig || $ig === '#'): ?>
                    <!-- Fallback si todo está vacío para que no se vea el hueco -->
                    <span class="text-muted xsmall opacity-50">Enlaces pendientes</span>
                <?php endif; ?>
            </div>
            <p class="text-muted xsmall mt-5 mb-0 opacity-60 fw-700">© <?= date('Y') ?>
                <?= htmlspecialchars($siteName) ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<link rel="stylesheet" href="/assets/css/footer.css">