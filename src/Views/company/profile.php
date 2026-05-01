<?php
use App\Config\Database;
$db = Database::getConnection();
$companyId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM empresas WHERE id = :id");
$stmt->execute([':id' => $companyId]);
$c = $stmt->fetch();

$avatarFallback = 'https://ui-avatars.com/api/?name=' . urlencode($c['nombre_comercial']) . '&background=3b82f6&color=fff&size=128';
$imgSrc = $c['foto_perfil'] ?: $avatarFallback;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Perfil | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .profile-container { max-width: 960px; margin: 0 auto; }
        .profile-header-card {
            display: flex; align-items: center; gap: 2rem;
            margin-bottom: 2rem; padding: 2.5rem;
        }
        .profile-avatar-wrap {
            width: 140px; height: 140px; border-radius: 30px;
            overflow: hidden; position: relative; border: 3px solid #fff;
            box-shadow: var(--glass-shadow); flex-shrink: 0;
            background: #fff;
        }
        .profile-avatar-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .upload-overlay {
            position: absolute; inset: 0; background: rgba(0,0,0,0.4);
            display: flex; align-items: center; justify-content: center;
            color: #fff; opacity: 0; transition: opacity 0.2s ease; cursor: pointer;
        }
        .profile-avatar-wrap:hover .upload-overlay { opacity: 1; }
        
        .section-title { font-size: 0.95rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-secondary); margin-bottom: 1.5rem; }
    </style>
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <div class="profile-container">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success p-3 rounded mb-4" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #22c55e;">
                    <i class="fas fa-check-circle me-2"></i> Perfil actualizado correctamente.
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'phone_invalid'): ?>
                <div class="alert alert-danger p-3 rounded mb-4" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444;">
                    <i class="fas fa-exclamation-triangle me-2"></i> El teléfono debe tener exactamente 9 dígitos.
                </div>
            <?php endif; ?>
            
            <header class="mb-4">
                <h1 class="mb-1">Configuración de Perfil</h1>
                <p class="text-muted small">Gestiona tu identidad corporativa y datos de contacto.</p>
            </header>

            <form action="/company/profile-update" method="POST" enctype="multipart/form-data">
                
                <!-- Avatar and basic info -->
                <div class="glass-card profile-header-card animate">
                    <div class="profile-avatar-wrap" onclick="document.getElementById('profile_img_input').click()">
                        <img id="profile_img_preview" src="<?= $imgSrc ?>" alt="Logo">
                        <div class="upload-overlay"><i class="fas fa-camera fs-4"></i></div>
                        <input type="file" name="foto_perfil" id="profile_img_input" class="d-none" accept="image/*" onchange="previewImg(this)">
                    </div>
                    <div class="flex-1">
                        <h2 class="mb-1"><?= htmlspecialchars($c['nombre_comercial']) ?></h2>
                        <span class="badge badge-primary mb-3">RUC: <?= $c['ruc'] ?></span>
                        <div class="d-flex gap-4">
                            <div>
                                <p class="xsmall text-muted fw-800 mb-0">ESTADO</p>
                                <p class="fw-700 text-success small">ACTIVO</p>
                            </div>
                            <div>
                                <p class="xsmall text-muted fw-800 mb-0">SECTOR</p>
                                <p class="fw-700 small"><?= htmlspecialchars($c['sector'] ?: 'General') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row d-flex gap-4">
                    <!-- Left: Contact Data -->
                    <div class="col glass-card animate" style="animation-delay: 0.1s;">
                        <h3 class="section-title">Datos de Contacto</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Correo Corporativo</label>
                            <input type="email" name="correo_contacto" class="form-input" value="<?= htmlspecialchars($c['correo_contacto'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Teléfono de Contacto</label>
                            <input type="text" name="telefono" class="form-input" value="<?= htmlspecialchars($c['telefono'] ?? '') ?>" placeholder="987654321" required maxlength="9" minlength="9" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/\D/g,'')">
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Dirección Fiscal</label>
                            <input type="text" name="direccion" class="form-input" value="<?= htmlspecialchars($c['direccion'] ?? '') ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
                        </div>
                    </div>

                    <!-- Right: Corporate Data -->
                    <div class="col-5 glass-card animate" style="animation-delay: 0.2s;">
                        <h3 class="section-title">Detalles de Empresa</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Razón Social / Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" class="form-input" value="<?= htmlspecialchars($c['nombre_comercial'] ?: '-') ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Rubro / Sector</label>
                            <input type="text" name="sector" class="form-input" value="<?= htmlspecialchars($c['sector']) ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="new_password" class="form-input" placeholder="••••••••" minlength="6">
                            <p class="xsmall text-muted mt-2">Dejar en blanco para mantener la contraseña actual.</p>
                        </div>

                        <div class="alert small text-muted p-3" style="background: rgba(59,130,246,0.05); border-radius: 12px; border: 1px solid rgba(59,130,246,0.1);">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Razón Social, Rubro y Dirección Fiscal son verificados y no pueden ser alterados.
                        </div>

                        <button type="submit" class="btn-futuristic w-100 mt-4 py-3">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImg(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile_img_preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
