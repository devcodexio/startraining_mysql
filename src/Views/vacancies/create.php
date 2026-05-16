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
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Premium Dark Luxe Theme */
        .ts-control { 
            background: rgba(30, 41, 59, 0.8) !important; 
            border: 1.5px solid rgba(255, 255, 255, 0.1) !important; 
            color: #f1f5f9 !important; 
            border-radius: 16px !important; 
            padding: 0.8rem 1.2rem !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }
        .ts-wrapper.focus .ts-control {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
        }
        /* BRUTE FORCE OPAQUE BACKGROUND */
        div.ts-dropdown, 
        div.ts-dropdown-content, 
        div.ts-dropdown .option {
            background-color: #000000 !important;
            background-image: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            color: #ffffff !important;
        }
        div.ts-dropdown { 
            border: 1px solid #ffffff !important; 
            border-radius: 4px !important;
            box-shadow: 0 20px 50px rgba(0,0,0,1) !important;
            margin-top: 2px !important;
            z-index: 1000000 !important;
        }
        div.ts-dropdown .option {
            padding: 12px 15px !important;
            font-weight: 600 !important;
        }
        div.ts-dropdown .option.active, 
        div.ts-dropdown .option:hover { 
            background-color: #2563eb !important; 
            color: #ffffff !important;
        }
        .ts-control .item { 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important; 
            color: white !important; 
            border-radius: 8px !important; 
            padding: 3px 10px !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            border: none !important;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3) !important;
        }
        .ts-control .item .remove {
            border-left: 1px solid rgba(255,255,255,0.2) !important;
            margin-left: 8px !important;
        }
        .ts-wrapper.multi .ts-control > div {
            margin: 0 5px 5px 0 !important;
        }
        .ts-control input::placeholder {
            color: #64748b !important;
            font-weight: 500 !important;
        }
    </style>
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
                        <div class="form-group mb-4" id="carrera_select_container" style="position: relative;">
                            <label class="form-label">Carreras Requeridas (Puedes seleccionar varias)</label>
                            <select name="carrera_ids[]" id="carrera_select" class="form-input" multiple required placeholder="Selecciona una o más carreras...">
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

    <script>
        new TomSelect("#carrera_select", {
            plugins: ['remove_button'],
            maxItems: 5,
            persist: false,
            create: false,
            dropdownParent: '#carrera_select_container',
            onDropdownOpen: function() {
                this.dropdown.style.backgroundColor = '#000000';
                this.dropdown.style.opacity = '1';
                this.dropdown.style.backgroundImage = 'none';
                
                const content = this.dropdown_content;
                if(content) {
                    content.style.backgroundColor = '#000000';
                    content.style.opacity = '1';
                }
            }
        });
    </script>

    <style>
        /* FINAL BRUTE FORCE ATTEMPT */
        #carrera_select_container .ts-dropdown {
            background-color: #05070a !important;
            background: #05070a !important;
            opacity: 1 !important;
            display: block;
            border: 1px solid #ffffff !important;
            z-index: 99999 !important;
            position: absolute !important;
        }
        #carrera_select_container .ts-dropdown-content {
            background-color: #05070a !important;
            background: #05070a !important;
            opacity: 1 !important;
        }
        #carrera_select_container .ts-dropdown .option {
            background-color: #05070a !important;
            background: #05070a !important;
            opacity: 1 !important;
            color: #ffffff !important;
            padding: 12px 15px !important;
        }
        #carrera_select_container .ts-dropdown .option.active {
            background-color: #2563eb !important;
            background: #2563eb !important;
        }

        /* Tom Select Control Styles */
        .ts-control { 
            background: rgba(30, 41, 59, 0.8) !important; 
            border: 1.5px solid rgba(255, 255, 255, 0.1) !important; 
            color: #f1f5f9 !important; 
            border-radius: 16px !important; 
            padding: 0.8rem 1.2rem !important;
        }
        .ts-control .item { 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark)) !important; 
            color: white !important; 
            border-radius: 8px !important; 
            padding: 3px 10px !important;
            font-weight: 700 !important;
        }
    </style>
</body>
</html>
