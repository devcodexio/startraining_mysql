<?php
use App\Models\DashboardModel;

$model    = new DashboardModel();
$userType = $_SESSION['user_type'] ?? 'empresa';
$userId   = $_SESSION['user_id'];
$userName = $_SESSION['user_nombre'] ?? 'Usuario';
$stats    = ($userType === 'admin') ? $model->getAdminStats() : $model->getCompanyStats($userId);
$firstName = explode(' ', $userName)[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../Layouts/Header.php'; ?>

    <main class="main-content">

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4 animate">
            <div>
                <h1 class="mb-1">Dashboard Overview</h1>
                <p class="text-muted" style="font-size:0.85rem;">Un vistazo general de toda tu actividad en la plataforma.</p>
            </div>
            <div class="d-flex gap-2">
                <?php if ($userType === 'empresa'): ?>
                <a href="/vacancies/create" class="btn-futuristic">
                    <i class="fas fa-plus"></i> Nueva Vacante
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row mb-4 animate" style="animation-delay:0.08s;">
            <?php if ($userType === 'admin'): ?>
                <div class="glass-card stat-card col">
                    <div class="stat-icon"><i class="fas fa-building"></i></div>
                    <div class="stat-value"><?= $stats['total_empresas'] ?? 0 ?></div>
                    <div class="stat-label">Empresas Registradas</div>
                </div>
                <div class="glass-card stat-card col">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-value" style="color: var(--secondary);"><?= $stats['total_vacantes'] ?? 0 ?></div>
                    <div class="stat-label">Convocatorias Activas</div>
                </div>
                <div class="glass-card stat-card col">
                    <div class="stat-icon" style="color: var(--success);"><i class="fas fa-users"></i></div>
                    <div class="stat-value" style="color: var(--success);"><?= $stats['total_postulaciones'] ?? 0 ?></div>
                    <div class="stat-label">Total Postulaciones</div>
                </div>
            <?php else: ?>
                <div class="glass-card stat-card col">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-value"><?= $stats['total_vacantes'] ?? 0 ?></div>
                    <div class="stat-label">Mis Convocatorias</div>
                </div>
                <div class="glass-card stat-card col">
                    <div class="stat-icon" style="color: var(--secondary);"><i class="fas fa-users"></i></div>
                    <div class="stat-value" style="color: var(--secondary);"><?= $stats['total_postulaciones'] ?? 0 ?></div>
                    <div class="stat-label">Postulantes Totales</div>
                </div>
                <div class="glass-card stat-card col">
                    <div class="stat-icon" style="color: var(--warning);"><i class="fas fa-clock"></i></div>
                    <div class="stat-value" style="color: var(--warning);"><?= $stats['pendientes'] ?? 0 ?></div>
                    <div class="stat-label">Pendientes de Revisión</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Content Grid — like Devoryn's 2-panel layout -->
        <div class="row animate" style="animation-delay:0.16s;">

            <!-- Activity Panel -->
            <div class="glass-card col-8" style="padding: 2rem;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-700 mb-0" style="font-size:1rem;">Actividad Reciente</h3>
                    <button class="btn-ghost" style="padding:0.35rem 0.8rem; font-size:0.72rem; border-radius:8px;">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>

                <!-- Activity Items -->
                <?php if (!empty($stats['recent'])): ?>
                    <?php foreach ($stats['recent'] as $item): ?>
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div style="width:36px; height:36px; background:rgba(59,130,246,0.1); border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--primary); flex-shrink:0;">
                            <i class="fas fa-file-alt" style="font-size:0.85rem;"></i>
                        </div>
                        <div class="flex-1">
                            <p class="mb-0 fw-600" style="font-size:0.85rem;"><?= htmlspecialchars($item['descripcion'] ?? 'Actividad') ?></p>
                            <span class="text-muted xsmall"><?= $item['fecha'] ?? '' ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size:2rem; opacity:0.3; display:block; margin-bottom:0.75rem;"></i>
                    <p class="text-muted" style="font-size:0.85rem;">Sin actividad reciente para mostrar.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="col d-flex flex-column gap-3">

                <!-- Quick Links Card -->
                <div class="glass-card" style="padding: 1.75rem;">
                    <h3 class="fw-700 mb-3" style="font-size:0.95rem;">Accesos Rápidos</h3>
                    <div class="d-flex flex-column gap-2">
                        <?php if ($userType === 'empresa'): ?>
                            <a href="/vacancies/create" class="btn-futuristic w-100" style="justify-content:flex-start; padding:0.7rem 1rem;">
                                <i class="fas fa-plus-circle"></i> Publicar Vacante
                            </a>
                            <a href="/postulations" class="btn-ghost w-100" style="justify-content:flex-start; padding:0.7rem 1rem;">
                                <i class="fas fa-users"></i> Ver Candidatos
                            </a>
                            <a href="/vacancies" class="btn-ghost w-100" style="justify-content:flex-start; padding:0.7rem 1rem;">
                                <i class="fas fa-list"></i> Mis Convocatorias
                            </a>
                        <?php else: ?>
                            <a href="/admin/empresas" class="btn-futuristic w-100" style="justify-content:flex-start; padding:0.7rem 1rem;">
                                <i class="fas fa-building"></i> Gestionar Empresas
                            </a>
                            <a href="/admin/config" class="btn-ghost w-100" style="justify-content:flex-start; padding:0.7rem 1rem;">
                                <i class="fas fa-shield-alt"></i> Configuración
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mini info card -->
                <div class="glass-card" style="padding: 1.5rem; background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(6,182,212,0.05));">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <i class="fas fa-robot text-primary" style="font-size:1.4rem;"></i>
                        <div>
                            <p class="fw-700 mb-0" style="font-size:0.88rem;">IA de Reclutamiento</p>
                            <p class="text-muted xsmall mb-0">Análisis de CV con n8n activo</p>
                        </div>
                    </div>
                    <div style="height:4px; background:rgba(59,130,246,0.12); border-radius:10px; overflow:hidden; margin-top:0.75rem;">
                        <div style="height:100%; width:78%; background:linear-gradient(90deg,var(--primary),var(--secondary)); border-radius:10px;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span class="text-muted xsmall">Precisión del modelo</span>
                        <span class="xsmall fw-700" style="color:var(--primary);">78%</span>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once __DIR__ . '/../Layouts/Footer.php'; ?>
    </main>
</body>
</html>
