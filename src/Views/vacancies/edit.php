<?php
use App\Models\VacancyModel;
use App\Models\CarreraModel;

$id = $matches[1] ?? 0;
$vacModel = new VacancyModel();
$v = $vacModel->getById($id);

if (!$v || $v['empresa_id'] != $_SESSION['user_id']) {
    header('Location: /vacancies');
    exit;
}

$carModel = new CarreraModel();
$carreras = $carModel->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vacante | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <div class="glass-card animate" style="max-width: 900px; margin: 0 auto; padding: 4rem; border-radius: 30px;">
            <div class="mb-5 d-flex align-items-center gap-4">
                <a href="/vacancies" class="btn-ghost p-3 small" style="border-radius: 12px;"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <h1 class="mb-1" style="font-size: 1.75rem; letter-spacing: -1px;">Editar Convocatoria</h1>
                    <p class="text-muted small">Modifica los detalles de "<?= htmlspecialchars($v['titulo_puesto']) ?>"</p>
                </div>
            </div>

            <form action="/vacancies/update" method="POST">
                <input type="hidden" name="id" value="<?= $v['id'] ?>">
                
                <div class="row d-flex gap-4 mb-4">
                    <div class="col">
                        <div class="form-group mb-4">
                            <label class="form-label">Título del Puesto</label>
                            <input type="text" name="titulo_puesto" class="form-input" required value="<?= htmlspecialchars($v['titulo_puesto']) ?>">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Carrera Requerida</label>
                            <select name="carrera_id" class="form-input" required>
                                <?php foreach ($carreras as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $v['carrera_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-4">
                            <label class="form-label">Modalidad</label>
                            <select name="modalidad" class="form-input" required>
                                <option value="Remoto" <?= $v['modalidad'] == 'Remoto' ? 'selected' : '' ?>>Remoto</option>
                                <option value="Presencial" <?= $v['modalidad'] == 'Presencial' ? 'selected' : '' ?>>Presencial</option>
                                <option value="Híbrido" <?= $v['modalidad'] == 'Híbrido' ? 'selected' : '' ?>>Híbrido</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-input" placeholder="Ej: Lima, Perú" value="<?= htmlspecialchars($v['ubicacion'] ?? '') ?>">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Fecha Límite</label>
                            <input type="date" name="fecha_limite" class="form-input" required min="<?= date('Y-m-d') ?>" value="<?= $v['fecha_limite'] ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Descripción del Puesto</label>
                    <textarea name="descripcion_puesto" class="form-input" rows="4" required><?= htmlspecialchars($v['descripcion_puesto']) ?></textarea>
                </div>

                <div class="form-group mb-5">
                    <label class="form-label">Requisitos (Para Análisis IA)</label>
                    <textarea name="requisitos_raw" class="form-input" rows="6" required><?= htmlspecialchars($v['requisitos_raw']) ?></textarea>
                    <p class="xsmall text-muted mt-2">
                        <i class="fas fa-robot text-primary me-1"></i>
                        Si cambias los requisitos, puedes volver a analizar los candidatos desde el pipeline para actualizar su match.
                    </p>
                </div>

                <div class="d-flex justify-content-between pt-4 border-top">
                    <button type="button" class="btn-ghost" style="color: var(--danger);" onclick="if(confirm('¿Seguro que deseas eliminar esta convocatoria?')) window.location='/vacancies/delete?id=<?= $v['id'] ?>'">
                        <i class="fas fa-trash me-2"></i> Eliminar
                    </button>
                    <button type="submit" class="btn-futuristic px-5 py-3">
                        <i class="fas fa-save me-2"></i> GUARDAR CAMBIOS
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
