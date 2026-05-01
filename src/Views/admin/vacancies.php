<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitoreo de Convocatorias | Admin StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .filter-btn {
            background: rgba(255,255,255,0.05); border: 1px solid var(--border-glass);
            border-radius: 12px; padding: 0.6rem 1.2rem; cursor: pointer;
            transition: all 0.3s ease; font-weight: 600; font-size: 0.85rem;
        }
        .filter-btn.active { background: var(--primary); border-color: var(--primary); color: #fff; }
    </style>
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <div class="glass-card animate" style="padding: 2.5rem; border-radius: 25px;">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="fw-900 mb-1" style="font-size: 2rem; letter-spacing: -1px;">Convocatorias Activas</h1>
                    <p class="text-muted small">Monitoreo y gestión de las postulaciones publicadas por todas las empresas.</p>
                </div>
                <div class="badge badge-success p-2 px-3"><?= count($vacancies) ?> Publicadas</div>
            </div>

            <div class="table-responsive">
                <table class="table-cyber">
                    <thead>
                        <tr>
                            <th class="ps-4">Vacante</th>
                            <th>Carrera</th>
                            <th>Empresa</th>
                            <th>Fecha Límite</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones (Admin)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vacancies)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay convocatorias activas en este momento.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($vacancies as $v): ?>
                            <tr>
                                <td class="ps-4">
                                    <p class="mb-0 fw-700" style="font-size: 0.9rem;"><?= htmlspecialchars($v['titulo_puesto']) ?></p>
                                    <span class="xsmall text-muted">ID: #<?= $v['id'] ?> | <?= $v['modalidad'] ?></span>
                                </td>
                                <td><span class="badge badge-primary" style="font-size: 0.65rem;"><?= $v['carrera'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 24px; height: 24px; border-radius: 6px; overflow: hidden;">
                                            <img src="<?= $v['foto_perfil'] ?: 'https://ui-avatars.com/api/?name='.urlencode($v['nombre_comercial']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <span class="small fw-600"><?= htmlspecialchars($v['nombre_comercial']) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= date('d M, Y', strtotime($v['fecha_limite'])) ?></td>
                                <td class="text-center">
                                    <span class="badge badge-success">ABIERTA</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/admin/vacancies/toggle-status?id=<?= $v['id'] ?>" class="btn-ghost p-2" style="color: var(--danger);" title="Cerrar Convocatoria Forzosamente"><i class="fas fa-power-off"></i></a>
                                        <a href="/vacante/<?= $v['id'] ?>" target="_blank" class="btn-ghost p-2" title="Ver Publicación"><i class="fas fa-external-link-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
