<?php
use App\Models\CarreraModel;
$carreraModel = new CarreraModel();
$carreras = $carreraModel->getAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Vacante | StarTraining</title>
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
                    <h1 class="mb-1" style="font-size: 1.75rem; letter-spacing: -1px;">Publicar Nueva Vacante</h1>
                    <p class="text-muted small">Define los requisitos para que nuestra IA pueda filtrar a los mejores candidatos.</p>
                </div>
            </div>

            <form action="/vacancies/store" method="POST">
                
                <div class="row d-flex gap-4 mb-4">
                    <div class="col">
                        <div class="form-group mb-4">
                            <label class="form-label">Título del Puesto</label>
                            <input type="text" name="titulo_puesto" class="form-input" required placeholder="Ej: Practicante de Desarrollo Web">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Carrera Requerida</label>
                            <select name="carrera_id" class="form-input" required>
                                <option value="">Selecciona una carrera</option>
                                <?php foreach ($carreras as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group mb-4">
                            <label class="form-label">Modalidad</label>
                            <select name="modalidad" class="form-input" required>
                                <option value="Remoto">Remoto</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Híbrido">Híbrido</option>
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-input" placeholder="Ej: Lima, Perú">
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">Fecha Límite</label>
                            <input type="date" name="fecha_limite" class="form-input" required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label">Descripción del Puesto</label>
                    <textarea name="descripcion_puesto" class="form-input" rows="4" required placeholder="Describe las funciones y objetivos del puesto..."></textarea>
                </div>

                <div class="form-group mb-5">
                    <label class="form-label">Requisitos (Para Análisis IA)</label>
                    <textarea name="requisitos_raw" class="form-input" rows="6" required placeholder="Ingresa los requisitos técnicos, habilidades y conocimientos específicos. Sé detallado para un mejor análisis de la IA..."></textarea>
                    <p class="xsmall text-muted mt-2">
                        <i class="fas fa-robot text-primary me-1"></i>
                        A mayor detalle en los requisitos, más preciso será el puntaje de match de los candidatos.
                    </p>
                </div>

                <div class="d-flex justify-content-end pt-4 border-top">
                    <button type="submit" class="btn-futuristic px-5 py-3">
                        <i class="fas fa-rocket me-2"></i> PUBLICAR CONVOCATORIA
                    </button>
                </div>
            </form>
        </div>

    </main>

</body>
</html>
