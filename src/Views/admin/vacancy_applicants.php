<?php
use App\Config\Database;
$vacante_id = $matches[1] ?? 0;
$db = Database::getConnection();

// 1. Obtener datos de la vacante y empresa
$stmtV = $db->prepare("SELECT v.*, e.nombre_comercial, e.foto_perfil as empresa_logo 
                       FROM vacantes v 
                       JOIN empresas e ON v.empresa_id = e.id 
                       WHERE v.id = ?");
$stmtV->execute([$vacante_id]);
$v = $stmtV->fetch();

if (!$v) {
    echo "Vacante no encontrada.";
    exit;
}

// 2. Obtener postulantes
$stmtP = $db->prepare("SELECT * FROM postulaciones WHERE vacante_id = ? ORDER BY match_porcentaje DESC");
$stmtP->execute([$vacante_id]);
$postulantes = $stmtP->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postulantes: <?= htmlspecialchars($v['titulo_puesto']) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .applicant-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .applicant-card:hover {
            border-left-color: var(--primary);
            transform: translateX(5px);
        }
        .match-badge {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <div class="container-fluid">
            <header class="mb-5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-4">
                    <div style="width: 60px; height: 60px; border-radius: 15px; overflow: hidden; background: #000;">
                        <img src="<?= $v['empresa_logo'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <h1 class="text-gradient mb-0"><?= htmlspecialchars($v['titulo_puesto']) ?></h1>
                        <p class="text-muted small fw-bold mb-0">
                            <i class="fas fa-building me-1"></i> <?= htmlspecialchars($v['nombre_comercial']) ?> 
                            <span class="mx-2">•</span> 
                            <?php 
                                $today = date('Y-m-d');
                                $isClosed = ($v['estado'] === 'cerrada' || (!empty($v['fecha_limite']) && $v['fecha_limite'] < $today));
                            ?>
                            <span class="<?= $isClosed ? 'text-danger' : 'text-success' ?>">
                                <i class="fas fa-circle me-1" style="font-size: 0.6rem;"></i> <?= $isClosed ? 'CONVOCATORIA CERRADA' : 'CONVOCATORIA ABIERTA' ?>
                            </span>
                            <span class="mx-2">•</span> 
                            <i class="fas fa-users me-1"></i> <?= count($postulantes) ?> Postulantes
                        </p>
                    </div>
                </div>
                <button onclick="window.close()" class="btn-ghost">
                    <i class="fas fa-times me-2"></i> CERRAR PESTAÑA
                </button>
            </header>

            <?php if(empty($postulantes)): ?>
                <div class="glass-card text-center py-5">
                    <i class="fas fa-user-slash fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No hay postulaciones aún</h4>
                    <p class="small text-muted">Esta vacante todavía no ha recibido candidatos.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($postulantes as $p): 
                        $matchColor = $p['match_porcentaje'] >= 70 ? '#10b981' : ($p['match_porcentaje'] >= 40 ? '#f59e0b' : '#ef4444');
                        $matchBg = $p['match_porcentaje'] >= 70 ? 'rgba(16,185,129,0.1)' : ($p['match_porcentaje'] >= 40 ? 'rgba(245,158,11,0.1)' : 'rgba(239,68,68,0.1)');
                    ?>
                        <div class="col-12">
                            <div class="glass-card applicant-card p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="match-badge" style="background: <?= $matchBg ?>; color: <?= $matchColor ?>; border: 2px solid <?= $matchColor ?>;">
                                            <?= round($p['match_porcentaje']) ?>%
                                        </div>
                                        <div>
                                            <h5 class="fw-800 mb-1"><?= htmlspecialchars($p['nombre_completo']) ?></h5>
                                            <div class="d-flex gap-3 small text-muted">
                                                <span><i class="fas fa-id-card me-1"></i> <?= $p['dni'] ?></span>
                                                <span><i class="fas fa-envelope me-1"></i> <?= $p['correo_estudiante'] ?></span>
                                                <span><i class="fas fa-phone me-1"></i> <?= $p['celular'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-end me-4">
                                            <span class="badge <?= $p['estado_postulacion'] === 'Apto' ? 'badge-success' : 'badge-danger' ?> px-3 py-2 mb-1">
                                                <?= strtoupper($p['estado_postulacion']) ?>
                                            </span>
                                            <span class="d-block xsmall text-muted fw-bold"><?= date('d M, Y', strtotime($p['fecha_postulacion'])) ?></span>
                                        </div>
                                        <a href="<?= $p['url_cv_pdf'] ?>" target="_blank" class="btn-futuristic px-4">
                                            <i class="fas fa-file-pdf me-2"></i> VER CV
                                        </a>
                                    </div>
                                </div>
                                <?php if($p['ia_analisis_descripcion']): ?>
                                    <div class="mt-4 p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                        <p class="mb-0 xsmall text-muted" style="line-height: 1.6;">
                                            <strong class="text-primary"><i class="fas fa-robot me-1"></i> ANÁLISIS IA:</strong> 
                                            <?= htmlspecialchars($p['ia_analisis_descripcion']) ?>
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
