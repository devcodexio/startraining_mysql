<?php
use App\Config\Database;
$id = $matches[1] ?? 0;
$db = Database::getConnection();

// 1. Obtener datos de la empresa
$stmt = $db->prepare("SELECT * FROM empresas WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    header('Location: /admin/empresas');
    exit;
}

// 2. Obtener vacantes de esta empresa
$stmtV = $db->prepare("SELECT v.*, c.nombre as carrera FROM vacantes v 
                       LEFT JOIN carreras c ON v.carrera_id = c.id 
                       WHERE v.empresa_id = ? ORDER BY v.creado_en DESC");
$stmtV->execute([$id]);
$vacantes = $stmtV->fetchAll();

// 3. Obtener conteo de postulaciones totales
$stmtP = $db->prepare("SELECT COUNT(*) FROM postulaciones p 
                       JOIN vacantes v ON p.vacante_id = v.id 
                       WHERE v.empresa_id = ?");
$stmtP->execute([$id]);
$totalPostulaciones = $stmtP->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Corporativo: <?= htmlspecialchars($c['nombre_comercial']) ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin_company_detail.css">
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <header class="mb-5 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-4">
                <a href="/admin/empresas" class="btn-futuristic p-3" style="border-radius: 15px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid var(--border-glass);">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-gradient mb-0"><?= htmlspecialchars($c['nombre_comercial']) ?></h1>
                    <p class="text-muted small fw-bold">EXPEDIENTE CORPORATIVO #<?= $c['id'] ?></p>
                </div>
            </div>
            <div class="d-flex gap-3">
                <span class="badge" style="background: rgba(var(--primary-rgb),0.1); color: var(--primary); padding: 12px 25px; border-radius: 30px; font-weight: 800;">
                    <i class="fas fa-check-circle me-2"></i> ENTIDAD VERIFICADA
                </span>
            </div>
        </header>

        <div class="detail-grid">
            <!-- Columna Izquierda: Perfil y Datos Base -->
            <div class="animate" style="animation-delay: 0.1s;">
                <div class="glass-card text-center p-5 mb-4">
                    <div class="mx-auto mb-4" style="width: 140px; height: 140px; border-radius: 35px; border: 3px solid var(--primary); overflow: hidden; background: #000;">
                        <img src="<?= $c['foto_perfil'] ?: 'https://ui-avatars.com/api/?name='.urlencode($c['nombre_comercial']).'&background=00f2fe&color=000&size=200'; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4 class="fw-800 mb-1"><?= htmlspecialchars($c['nombre_comercial']) ?></h4>
                    <p class="text-muted small mb-4"><?= htmlspecialchars($c['sector'] ?: 'Sector No Definido') ?></p>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="p-3 bg-white bg-opacity-5 rounded-4">
                                <h5 class="mb-0 fw-800 text-primary"><?= count($vacantes) ?></h5>
                                <span class="xsmall text-muted fw-bold">VACANTES</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white bg-opacity-5 rounded-4">
                                <h5 class="mb-0 fw-800 text-primary"><?= $totalPostulaciones ?></h5>
                                <span class="xsmall text-muted fw-bold">POSTULADOS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-4">
                    <h5 class="fw-800 mb-4 ls-1">CONTACTO LEGAL</h5>
                    <div class="info-item">
                        <span class="info-label">RUC</span>
                        <span class="info-value text-primary"><?= $c['ruc'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">CORREO OFICIAL</span>
                        <span class="info-value"><?= htmlspecialchars($c['correo_contacto']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">TELÉFONO</span>
                        <span class="info-value"><?= $c['telefono'] ?: 'No registrado' ?></span>
                    </div>
                    <div class="info-item mb-0">
                        <span class="info-label">DIRECCIÓN FISCAL</span>
                        <span class="info-value" style="font-size: 0.9rem; opacity: 0.8;"><?= htmlspecialchars($c['direccion'] ?: 'Sin dirección') ?></span>
                    </div>

                    <div class="mt-5 p-4 rounded-4" style="background: rgba(var(--primary-rgb), 0.05); border: 1px dashed rgba(var(--primary-rgb), 0.3);">
                        <span class="info-label mb-3"><i class="fas fa-file-pdf text-danger me-2"></i> Documentación RUC</span>
                        <?php if(!empty($c['ficha_ruc'])): ?>
                            <a href="<?= $c['ficha_ruc'] ?>" target="_blank" class="btn-futuristic w-100 py-3 small">
                                <i class="fas fa-external-link-alt me-2"></i> Ver Ficha RUC PDF
                            </a>
                        <?php else: ?>
                            <p class="xsmall text-muted mb-0">No se adjuntó Ficha RUC durante el registro.</p>
                        <?php endif; ?>

                        <span class="info-label mb-3 mt-4"><i class="fas fa-id-card text-primary me-2"></i> Documentación DNI</span>
                        <div class="row g-2">
                            <div class="col-6">
                                <?php if(!empty($c['dni_frente'])): 
                                    $isPdfF = str_contains($c['dni_frente'], '.pdf');
                                ?>
                                    <a href="<?= $c['dni_frente'] ?>" target="_blank" class="d-block rounded-3 overflow-hidden border border-secondary border-opacity-20 text-center bg-white bg-opacity-5" style="height: 80px; display: flex !important; align-items: center; justify-content: center;">
                                        <?php if($isPdfF): ?>
                                            <i class="fas fa-file-pdf fs-2 text-danger"></i>
                                        <?php else: ?>
                                            <img src="<?= $c['dni_frente'] ?>" style="width:100%; height:100%; object-fit:cover;" title="DNI Frente">
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <div class="p-2 text-center xsmall opacity-50 border rounded-3" style="height: 80px; display: flex; align-items: center; justify-content: center;">Sin Frente</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-6">
                                <?php if(!empty($c['dni_reverso'])): 
                                    $isPdfR = str_contains($c['dni_reverso'], '.pdf');
                                ?>
                                    <a href="<?= $c['dni_reverso'] ?>" target="_blank" class="d-block rounded-3 overflow-hidden border border-secondary border-opacity-20 text-center bg-white bg-opacity-5" style="height: 80px; display: flex !important; align-items: center; justify-content: center;">
                                        <?php if($isPdfR): ?>
                                            <i class="fas fa-file-pdf fs-2 text-danger"></i>
                                        <?php else: ?>
                                            <img src="<?= $c['dni_reverso'] ?>" style="width:100%; height:100%; object-fit:cover;" title="DNI Reverso">
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <div class="p-2 text-center xsmall opacity-50 border rounded-3" style="height: 80px; display: flex; align-items: center; justify-content: center;">Sin Reverso</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <span class="info-label mb-3 mt-4"><i class="fas fa-camera text-primary me-2"></i> Verificación Biométrica (Selfie)</span>
                        <?php if(!empty($c['foto_selfie'])): ?>
                            <div class="rounded-4 overflow-hidden border border-primary border-opacity-20 shadow-sm" style="height: 200px; background: #000;">
                                <img src="<?= $c['foto_selfie'] ?>" style="width:100%; height:100%; object-fit:cover;" title="Selfie de Verificación">
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center xsmall opacity-50 border rounded-3 bg-white bg-opacity-5">No se capturó selfie</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Actividad en la Plataforma -->
            <div class="animate" style="animation-delay: 0.2s;">
                <div class="glass-card p-5 h-100">
                    <h4 class="fw-800 mb-5 d-flex align-items-center gap-3">
                        <i class="fas fa-briefcase text-primary"></i> Historial de Convocatorias
                    </h4>

                    <?php if(empty($vacantes)): ?>
                        <div class="text-center py-5 opacity-50">
                            <i class="fas fa-folder-open fs-1 mb-3"></i>
                            <p>Esta empresa no ha publicado vacantes aún.</p>
                        </div>
                    <?php else: ?>
                        <div class="v-list">
                            <?php foreach($vacantes as $v): ?>
                                <div class="v-list-card d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-800 mb-1"><?= htmlspecialchars($v['titulo_puesto']) ?></h6>
                                        <p class="xsmall text-muted mb-0">
                                            <?= htmlspecialchars($v['carrera']) ?> • 
                                            <?php 
                                                $today = date('Y-m-d');
                                                $isExpired = (!empty($v['fecha_limite']) && $v['fecha_limite'] < $today);
                                                $isClosed = ($v['estado'] === 'cerrada' || $isExpired);
                                                $displayStatus = $isClosed ? 'CERRADA' : 'ABIERTA';
                                                $colorClass = $isClosed ? 'text-danger' : 'text-success';
                                            ?>
                                            <span class="<?= $colorClass ?> fw-800">
                                                <?= $displayStatus ?>
                                                <?php if($isExpired && $v['estado'] === 'abierta'): ?>
                                                    <i class="fas fa-history ms-1" title="Vencida por fecha"></i>
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-end me-3">
                                            <span class="xsmall fw-bold d-block mb-1">LIMITE</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-white border-0"><?= $v['fecha_limite'] ?></span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="/admin/vacancies/toggle-status?id=<?= $v['id'] ?>" class="btn-ghost" style="width: 45px; height: 45px; border-radius: 12px; padding: 0;" title="<?= $isClosed ? 'Re-abrir' : 'Cerrar' ?> Vacante">
                                                <i class="fas <?= $isClosed ? 'fa-lock-open text-success' : 'fa-lock text-warning' ?>"></i>
                                            </a>
                                            <a href="/admin/vacantes/<?= $v['id'] ?>/postulantes" target="_blank" class="btn-futuristic" style="width: 45px; height: 45px; border-radius: 12px; padding: 0;" title="Ver Postulantes">
                                                <i class="fas fa-users"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-5 pt-4 border-top border-secondary border-opacity-10">
                        <p class="text-muted small">Nota: Los administradores tienen acceso de <strong>solo lectura</strong> a los expedientes corporativos para garantizar la integridad de los datos de la empresa.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
