<?php
use App\Models\ConfigModel;
$configModel = new ConfigModel();
$config = $configModel->getSettings();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración Global | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <header class="mb-5 d-flex justify-content-between align-items-end">
            <div>
                <h1 class="text-gradient mb-1">Configuración Maestro</h1>
                <p class="text-muted">Gestiona el entorno, apariencia y redes sociales de tu plataforma.</p>
            </div>
            <?php if(isset($_GET['success'])): ?>
                <div class="px-4 py-2 small bg-success text-white mb-2" style="border-radius: 12px; animation: slideUpFade 0.3s ease;">
                    <i class="fas fa-check-circle me-2"></i> Cambios guardados correctamente
                </div>
            <?php endif; ?>
        </header>

        <form action="/admin/save-config" method="POST" id="mainConfigForm">
            <div class="row g-4">
                <div class="col-8">
                    <!-- Sección: General -->
                    <div class="glass-card mb-4 animate shadow-sm" style="padding: 3rem;">
                        <h3 class="fw-800 mb-5 d-flex align-items-center gap-3">
                            <i class="fas fa-desktop text-primary"></i> Apariencia & Marca
                        </h3>
                        <div class="row">
                            <div class="col-6 form-group mb-5">
                                <label class="mb-2 text-muted xsmall fw-800 ls-2">NOMBRE DEL SITIO</label>
                                <input type="text" name="nombre_sitio" class="form-input" value="<?= htmlspecialchars($config['nombre_sitio'] ?? 'StarTraining') ?>" required>
                            </div>
                            <div class="col-6 form-group mb-5">
                                <label class="mb-2 text-muted xsmall fw-800 ls-2">ESLOGAN / DESCRIPCIÓN FOOTER</label>
                                <input type="text" name="footer_descripcion" class="form-input" value="<?= htmlspecialchars($config['footer_descripcion'] ?? 'Plataforma líder en reclutamiento.') ?>">
                            </div>
                        </div>

                        <div class="form-group mb-5">
                            <label class="mb-2 text-muted xsmall fw-800 ls-2">MISIÓN DE LA PLATAFORMA (APARECE EN EL FOOTER)</label>
                            <textarea name="mision_sitio" class="form-input" rows="3" style="border-radius: 15px; resize: none;"><?= htmlspecialchars($config['mision_sitio'] ?? 'Nuestra misión es empoderar a la próxima generación de profesionales peruanos.') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-6 form-group">
                                <label class="mb-2 text-muted xsmall fw-800 ls-2">EMAIL PÚBLICO</label>
                                <input type="email" name="email_contacto" class="form-input" value="<?= htmlspecialchars($config['email_contacto'] ?? 'contacto@startraining.com') ?>">
                            </div>
                            <div class="col-6 form-group">
                                <label class="mb-2 text-muted xsmall fw-800 ls-2">TELÉFONO CONTACTO</label>
                                <input type="text" name="telefono_contacto" class="form-input" value="<?= htmlspecialchars($config['telefono_contacto'] ?? '+51 987 654 321') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Redes Sociales (Modal Trigger) -->
                    <div class="glass-card mb-4 animate p-4 d-flex align-items-center justify-content-between" style="border: 2px dashed rgba(var(--primary-rgb), 0.2);">
                        <div class="d-flex align-items-center gap-4">
                            <div class="icon-circle bg-primary text-white" style="width: 50px; height: 50px;"><i class="fas fa-share-alt"></i></div>
                            <div>
                                <h5 class="fw-800 mb-1">Redes Sociales Activas</h5>
                                <p class="text-muted small mb-0">Gestiona los enlaces a tus perfiles oficiales.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-ghost px-4" onclick="openSocialModal()">
                            <i class="fas fa-edit me-2"></i> Editar Redes
                        </button>
                    </div>

                    <!-- Sección: Seguridad y Mantenimiento -->
                    <div class="glass-card animate shadow-sm" style="padding: 3rem;">
                        <h3 class="fw-800 mb-5 d-flex align-items-center gap-3">
                            <i class="fas fa-shield-virus text-primary"></i> Control de Acceso
                        </h3>

                        <input type="hidden" name="modo_mantenimiento" id="maintInput" value="<?= $config['modo_mantenimiento'] ?? 'off' ?>">
                        <div class="form-group mb-5">
                            <div class="d-flex align-items-center gap-4 p-4" style="background: rgba(var(--primary-rgb),0.03); border-radius: 20px; border: 1px solid var(--border-glass); cursor: pointer;" onclick="toggleMaint()">
                                <div class="flex-1">
                                    <p class="fw-800 mb-1">Modo Mantenimiento</p>
                                    <p class="text-muted small mb-0">Desactiva el acceso a todos los usuarios excepto administradores.</p>
                                </div>
                                <div id="maintVisualBtn" class="theme-switch m-0 <?= ($config['modo_mantenimiento'] ?? 'off') === 'on' ? 'active' : '' ?>" style="width: 70px; height: 35px; position: relative; border-radius: 25px; background: rgba(0,0,0,0.05); transition: 0.3s; border: 2px solid rgba(255,255,255,0.1);">
                                     <div id="maintCircle" style="width: 25px; height: 25px; background: #fff; border-radius: 50%; position: absolute; top: 3px; left: <?= ($config['modo_mantenimiento'] ?? 'off') === 'on' ? '38px' : '3px' ?>; transition: 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-2 text-muted xsmall fw-800 ls-2">MENSAJE PARA USUARIOS</label>
                            <input type="text" name="mantenimiento_msg" class="form-input" placeholder="Ejm: Estamos mejorando para ti." value="<?= htmlspecialchars($config['mantenimiento_msg'] ?? 'Estamos en mantenimiento. Volveremos pronto.') ?>">
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="glass-card sticky-top animate shadow-sm" style="top: 100px; padding: 2.5rem; border: 1px solid var(--primary);">
                        <h4 class="fw-800 mb-4">Acciones Maestro</h4>
                        <p class="text-muted small mb-5">Asegúrate de revisar todos los cambios antes de confirmar. Estos afectarán a todos los visitantes del sitio.</p>
                        
                        <button type="submit" class="btn-futuristic w-100 py-3 fs-6 mb-3" style="box-shadow: 0 10px 30px rgba(var(--primary-rgb), 0.3);">
                            <i class="fas fa-save me-2"></i> Guardar Todo
                        </button>
                        
                        <a href="/admin/config" class="btn-ghost w-100 py-3 text-center text-decoration-none" style="font-size: 0.85rem;">
                            <i class="fas fa-undo me-2"></i> Descartar Cambios
                        </a>

                        <div class="mt-5 pt-4 border-top border-secondary border-opacity-10">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">Último Cambio</span>
                                <span class="fw-bold small"><?= date('d/m H:i') ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Versión FW</span>
                                <span class="badge" style="background: rgba(var(--primary-rgb), 0.1); color: var(--primary);">v3.0.5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Redes Sociales -->
            <div class="modal-overlay" id="socialModal">
                <div class="modal-glass" style="max-width: 550px; text-align: left;">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <h4 class="fw-900 mb-0">Configura tus Redes</h4>
                        <button type="button" onclick="closeSocialModal()" class="btn-ghost py-2 px-3"><i class="fas fa-times"></i></button>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label">FACEBOOK URL</label>
                            <input type="text" name="facebook_url" class="form-input" placeholder="https://facebook.com/..." value="<?= htmlspecialchars($config['facebook_url'] ?? '#') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">INSTAGRAM URL</label>
                            <input type="text" name="instagram_url" class="form-input" placeholder="https://instagram.com/..." value="<?= htmlspecialchars($config['instagram_url'] ?? '#') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">LINKEDIN URL</label>
                            <input type="text" name="linkedin_url" class="form-input" placeholder="https://linkedin.com/..." value="<?= htmlspecialchars($config['linkedin_url'] ?? '#') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">TWITTER / X URL</label>
                            <input type="text" name="twitter_url" class="form-input" placeholder="https://x.com/..." value="<?= htmlspecialchars($config['twitter_url'] ?? '#') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5 pt-3">
                        <button type="button" onclick="closeSocialModal()" class="btn-futuristic py-3 px-5">Listo</button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <script>
        function openSocialModal() { document.getElementById('socialModal').style.display = 'flex'; }
        function closeSocialModal() { document.getElementById('socialModal').style.display = 'none'; }

        function toggleMaint() {
            const input = document.getElementById('maintInput');
            const circle = document.getElementById('maintCircle');
            const btn = document.getElementById('maintVisualBtn');
            if (input.value === 'on') {
                input.value = 'off';
                circle.style.left = '3px';
                btn.style.background = 'rgba(0,0,0,0.05)';
            } else {
                input.value = 'on';
                circle.style.left = '38px';
                btn.style.background = 'rgba(var(--primary-rgb), 0.2)';
            }
        }
    </script>
</body>
</html>

