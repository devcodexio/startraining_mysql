<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Convocatorias Finalizadas | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <div class="glass-card animate" style="padding: 2.5rem; border-radius: 25px;">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h1 class="fw-900 mb-1" style="font-size: 2rem; letter-spacing: -1px;">Convocatorias Cerradas</h1>
                    <p class="text-muted small">Listado de vacantes que ya expiraron o fueron cerradas manualmente.</p>
                </div>
                <div class="badge badge-danger p-2 px-3"><?= count($vacancies) ?> Finalizadas</div>
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
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vacancies)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay convocatorias terminadas.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($vacancies as $v): ?>
                            <tr>
                                <td class="ps-4">
                                    <p class="mb-0 fw-700" style="font-size: 0.9rem;"><?= htmlspecialchars($v['titulo_puesto']) ?></p>
                                    <span class="xsmall text-muted">ID: #<?= $v['id'] ?></span>
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
                                    <span class="badge badge-danger">CERRADA</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if ($_SESSION['user_type'] === 'admin'): ?>
                                            <a href="/admin/vacancies/toggle-status?id=<?= $v['id'] ?>" class="btn-ghost p-2" style="color: var(--success);" title="Habilitar (Reabrir) Convocatoria"><i class="fas fa-play"></i></a>
                                        <?php else: ?>
                                            <a href="/vacancies/toggle-status?id=<?= $v['id'] ?>" class="btn-ghost p-2" style="color: var(--success);" title="Habilitar (Reabrir) Convocatoria"><i class="fas fa-play"></i></a>
                                        <?php endif; ?>
                                        <a href="/vacante/<?= $v['id'] ?>" class="btn-ghost p-2" title="Ver Detalles"><i class="fas fa-eye"></i></a>
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
