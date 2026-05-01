<?php
use App\Config\Database;
$id = $_SESSION['user_id'];
$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM administradores WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

$avatarFallback = 'https://ui-avatars.com/api/?name=' . urlencode($admin['nombre']) . '&background=3b82f6&color=fff&size=128';
$imgSrc = $admin['foto_perfil'] ?: $avatarFallback;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil | Admin StarTraining</title>
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
                    <i class="fas fa-check-circle me-2"></i> Perfil y credenciales actualizados correctamente.
                </div>
            <?php endif; ?>
            
            <header class="mb-4">
                <h1 class="mb-1">Configuración de Cuenta</h1>
                <p class="text-muted small">Gestiona tus credenciales y preferencias de acceso administrativo.</p>
            </header>

            <form action="/admin/save-profile" method="POST" enctype="multipart/form-data">
                
                <!-- Avatar and basic info -->
                <div class="glass-card profile-header-card animate">
                    <div class="profile-avatar-wrap" onclick="document.getElementById('profile_img_input').click()">
                        <img id="profile_img_preview" src="<?= $imgSrc ?>" alt="Admin Profile">
                        <div class="upload-overlay"><i class="fas fa-camera fs-4"></i></div>
                        <input type="file" name="foto_perfil" id="profile_img_input" class="d-none" accept="image/*" onchange="previewImg(this)">
                    </div>
                    <div class="flex-1">
                        <h2 class="mb-1"><?= htmlspecialchars($admin['nombre']) ?></h2>
                        <span class="badge badge-primary mb-3">ID: #<?= $admin['id'] ?></span>
                        <div class="d-flex gap-4">
                            <div>
                                <p class="xsmall text-muted fw-800 mb-0">TIPO DE CUENTA</p>
                                <p class="fw-700 text-primary small">ADMINISTRADOR GLOBAL</p>
                            </div>
                            <div>
                                <p class="xsmall text-muted fw-800 mb-0">USUARIO (RENIEC / DNI)</p>
                                <p class="fw-700 small"><?= htmlspecialchars($admin['usuario']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row d-flex gap-4">
                    <!-- Left: Personal Data -->
                    <div class="col glass-card animate" style="animation-delay: 0.1s;">
                        <h3 class="section-title">Información Personal</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-input" value="<?= htmlspecialchars($admin['nombre']) ?>" required>
                        </div>
                        
                        <div class="form-group mb-0">
                            <label class="form-label">Usuario de Acceso</label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($admin['usuario']) ?>" readonly style="opacity: 0.6; cursor: not-allowed;">
                        </div>
                    </div>

                    <!-- Right: Security Data -->
                    <div class="col-5 glass-card animate" style="animation-delay: 0.2s;">
                        <h3 class="section-title">Seguridad</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="new_password" class="form-input" placeholder="••••••••">
                            <p class="xsmall text-muted mt-2">Dejar en blanco para mantener la contraseña actual.</p>
                        </div>

                        <div class="alert small text-muted p-3" style="background: rgba(239,68,68,0.05); border-radius: 12px; border: 1px solid rgba(239,68,68,0.1);">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                            Por seguridad, no compartas tus credenciales de administrador con nadie.
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
