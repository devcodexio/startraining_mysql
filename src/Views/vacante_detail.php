<?php
use App\Models\VacancyModel;
$id = $matches[1]; // From router
$model = new VacancyModel();
$v = $model->getById($id);
if (!$v) die("Vacante no encontrada");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular a <?= htmlspecialchars($v['titulo_puesto']) ?> | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="animate">
    <nav class="header glass" style="margin-left: 0; position: sticky; border-radius: 0;">
        <h2 class="fw-bold mb-0 text-primary">StarTraining</h2>
        <a href="/" class="text-white text-decoration-none fw-500">Volver al Inicio</a>
    </nav>

    <div class="main-content" style="margin-left: 0; padding: 4rem 2rem; max-width: 1200px; margin: 0 auto;">
        <div class="row d-flex gap-4">
            <!-- Detalles de la Vacante -->
            <div class="col flex-1">
                <div class="glass-card p-5 animate">
                    <div class="d-flex align-items-center mb-4 gap-4">
                        <div class="vacancy-logo">
                            <img src="<?= $v['foto_perfil'] ?: 'https://placehold.co/100x100/0a0b10/00f2fe?text=LOGO' ?>" alt="Logo">
                        </div>
                        <div>
                            <h1 class="fw-bold mb-1 text-primary"><?= htmlspecialchars($v['titulo_puesto']) ?></h1>
                            <p class="text-secondary fs-5 mb-0"><?= htmlspecialchars($v['nombre_comercial']) ?> | <span class="badge-modalidad"><?= $v['modalidad'] ?></span></p>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h3 class="fw-bold mb-3">Descripción de la Convocatoria</h3>
                        <p class="text-secondary fs-5"><?= nl2br(htmlspecialchars($v['descripcion_puesto'])) ?></p>
                        
                        <h3 class="fw-bold mb-3 mt-5">Requisitos</h3>
                        <p class="text-secondary fs-5"><?= nl2br(htmlspecialchars($v['requisitos_raw'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Formulario de Postulación -->
            <div class="col-4" style="width: 450px;">
                <div class="glass-card p-5 animate" style="animation-delay: 0.1s; border-color: var(--primary);">
                    <h3 class="fw-bold mb-4">Postular Ahora</h3>
                    <p class="text-secondary small mb-5">Ingresa tus datos para empezar el proceso.</p>

                    <form id="applyForm" action="/postular-proceso" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="vacante_id" value="<?= $v['id'] ?>">
                        
                        <div class="form-group mb-4">
                            <label class="mb-2 text-secondary small">DNI</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="dniInput" name="dni" class="form-input" placeholder="Ej: 71234567" maxlength="8" required>
                                <button type="button" id="btnDni" class="btn-premium px-3"><i class="fas fa-search"></i></button>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="mb-2 text-secondary small">Apellidos y Nombres</label>
                            <input type="text" id="nombreCompleto" name="nombre_completo" class="form-input" readonly placeholder="Se autocompletará con DNI">
                        </div>

                        <div class="form-group mb-4">
                            <label class="mb-2 text-secondary small">Correo Institucional (@edu.pe)</label>
                            <input type="email" id="correoEstudiante" name="correo_estudiante" class="form-input" placeholder="ejemplo@universidad.edu.pe" required>
                            <small id="emailWarning" class="text-danger d-none mt-1" style="font-size: 0.7rem;">Solo se aceptan correos @edu.pe</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="mb-2 text-secondary small">Celular</label>
                            <input type="text" name="celular" class="form-input" placeholder="987 654 321" required>
                        </div>

                        <div class="form-group mb-5">
                            <label class="mb-2 text-secondary small">Subir CV (PDF)</label>
                            <input type="file" name="cv_pdf" class="form-input" accept=".pdf" required>
                        </div>

                        <button type="submit" class="btn-premium w-100 py-3 fs-5">
                            <i class="fas fa-paper-plane me-2"></i> Enviar Postulación
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btnDni').addEventListener('click', function() {
            const dni = document.getElementById('dniInput').value;
            if (dni.length !== 8) return alert('DNI debe tener 8 dígitos');
            
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            fetch(`/api/consultar-dni?dni=${dni}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('nombreCompleto').value = data.nombres + ' ' + data.apellidos;
                    } else {
                        alert('No se encontró el DNI');
                    }
                })
                .finally(() => {
                    this.innerHTML = '<i class="fas fa-search"></i>';
                });
        });

        document.getElementById('correoEstudiante').addEventListener('blur', function() {
            const email = this.value;
            const warning = document.getElementById('emailWarning');
            if (email && !email.endsWith('@edu.pe')) {
                warning.classList.remove('d-none');
                this.classList.add('border-danger');
            } else {
                warning.classList.add('d-none');
                this.classList.remove('border-danger');
            }
        });

        document.getElementById('applyForm').addEventListener('submit', function(e) {
            const email = document.getElementById('correoEstudiante').value;
            if (!email.endsWith('@edu.pe')) {
                e.preventDefault();
                alert('Solo se permiten correos electrónicos universitarios (@edu.pe)');
            }
        });
    </script>
</body>
</html>
