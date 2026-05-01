<?php
use App\Models\VacancyModel;
$model = new VacancyModel();
$vacancies = $model->getAll(['empresa_id' => $_SESSION['user_id']]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Convocatorias | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>
    
    <main class="main-content">

        <header class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Mis Convocatorias</h1>
                <p class="text-muted small">Gestiona tus puestos publicados y el estado del reclutamiento.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/vacancies/create" class="btn-futuristic" style="padding: 0.75rem 1.5rem;">
                    <i class="fas fa-plus me-2"></i> Nueva Vacante
                </a>
            </div>
        </header>

        <!-- Search Bar -->
        <div class="glass-card mb-4 d-flex align-items-center gap-3 px-4 py-3" style="border-radius: 15px;">
            <i class="fas fa-search text-muted small"></i>
            <input type="text" id="filterInput" placeholder="Filtrar por título de puesto..." style="background: transparent; border: none; outline: none; color: var(--text-primary); font-family: var(--font); font-size: 0.92rem; flex: 1;">
        </div>

        <div class="row" id="vacancyGrid">
            <?php foreach ($vacancies as $v): ?>
                <div class="col-4 glass-card p-4 d-flex flex-column animate vacancy-card" style="min-width: 320px; transition: transform 0.3s ease;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge badge-primary xsmall" style="text-transform: uppercase; letter-spacing: 1px;">
                            <?= $v['modalidad'] ?>
                        </span>
                        <div class="dropdown">
                            <button class="btn-ghost p-1 px-2 small" style="border-radius: 8px;"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>
                    
                    <h3 class="fw-800 mb-2" style="font-size: 1.1rem; line-height: 1.3; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; min-height: 2.6rem;">
                        <?= htmlspecialchars($v['titulo_puesto']) ?>
                    </h3>
                    
                    <div class="d-flex align-items-center gap-4 text-muted xsmall fw-600 mb-4">
                        <span><i class="fas fa-graduation-cap text-primary me-1"></i> <?= htmlspecialchars($v['carrera']) ?></span>
                    </div>

                    <p class="text-secondary xsmall mb-4" style="line-height: 1.6; height: 3.2rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                        <?= htmlspecialchars($v['descripcion_puesto']) ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                        <div class="d-flex align-items-center gap-1 text-muted xsmall fw-bold">
                            <i class="far fa-calendar-alt" style="opacity: 0.5;"></i> 
                            <?= date('d/m/Y', strtotime($v['creado_en'])) ?>
                        </div>
                        <div class="d-flex gap-2">
                             <a href="/vacancies/toggle-status?id=<?= $v['id'] ?>" class="btn-ghost p-2 px-3 small" style="color: var(--danger);" title="Inhabilitar (Cerrar)Convocatoria"><i class="fas fa-power-off"></i></a>
                             <a href="/vacancies/edit/<?= $v['id'] ?>" class="btn-ghost p-2 px-3 small" title="Editar Convocatoria"><i class="fas fa-edit"></i></a>
                             <a href="/vacante/<?= $v['id'] ?>" target="_blank" class="btn-ghost p-2 px-3 small" title="Ver Publicación"><i class="fas fa-external-link-alt"></i></a>
                             <a href="/postulations?vacante_id=<?= $v['id'] ?>" class="btn-futuristic p-2 px-3 small" title="Ver Candidatos"><i class="fas fa-users"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($vacancies) === 0): ?>
                <div class="col-12 glass-card text-center py-5">
                    <div class="mb-4" style="font-size: 3rem; opacity: 0.2;"><i class="fas fa-folder-open"></i></div>
                    <p class="text-muted small">Aún no has publicado ninguna convocatoria. ¡Comienza ahora para atraer talento!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.getElementById('filterInput').addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.vacancy-card').forEach(card => {
                const title = card.querySelector('h3').innerText.toLowerCase();
                if (title.includes(val)) {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });
        });
    </script>
</body>
</html>
