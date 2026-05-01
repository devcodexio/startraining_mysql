<?php
$userType = $_SESSION['user_type'] ?? 'empresa';
if ($userType !== 'admin') { header('Location: /dashboard'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Carreras | Admin StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .carrera-item {
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass);
            padding: 1.25rem 2rem; border-radius: 18px; margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .carrera-item:hover {
            background: rgba(59,130,246,0.06); transform: translateX(8px);
            border-color: rgba(59,130,246,0.3);
        }
        .carrera-icon-box {
            width: 45px; height: 45px; border-radius: 12px;
            background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(168,85,247,0.1));
            display: flex; align-items: center; justify-content: center;
            color: var(--primary); font-size: 1.1rem;
        }
        .add-card {
            position: sticky; top: 100px;
            background: linear-gradient(135deg, rgba(59,130,246,0.05) 0%, rgba(168,85,247,0.05) 100%);
            border: 1px solid var(--border-glass); border-radius: 28px;
            padding: 2.5rem; backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <div class="row d-flex gap-5" style="max-width: 1100px; margin: 0 auto;">
            
            <!-- List Section -->
            <div class="col">
                <div class="mb-5">
                    <h1 class="fw-900 mb-1" style="font-size: 2.2rem; letter-spacing: -1.5px; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Carreras Profesionales</h1>
                    <p class="text-muted small">Especialidades académicas disponibles para el filtro de la IA.</p>
                </div>

                <div class="carreras-list">
                    <?php foreach ($carreras as $c): ?>
                        <div class="carrera-item animate">
                            <div class="d-flex align-items-center gap-4">
                                <div class="carrera-icon-box">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h3 class="fw-700 mb-0" style="font-size: 1rem;"><?= htmlspecialchars($c['nombre']) ?></h3>
                                    <span class="xsmall text-muted fw-500">ID: #<?= $c['id'] ?></span>
                                </div>
                            </div>
                            <button class="btn-ghost p-3" style="color: var(--danger); border-radius: 50%;" title="Eliminar" onclick="confirmDelete(<?= $c['id'] ?>, '<?= addslashes($c['nombre']) ?>')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Side Form -->
            <div class="col-4">
                <div class="add-card animate">
                    <div class="mb-4">
                        <div class="sidebar-grid-icon mb-3" style="transform: scale(0.8);"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
                        <h2 class="fw-900" style="font-size: 1.25rem;">Nueva Especialidad</h2>
                        <p class="text-muted xsmall">Añade carreras para que las empresas puedan vincular sus convocatorias.</p>
                    </div>

                    <form action="/admin/carreras/store" method="POST">
                        <div class="form-group mb-4">
                            <label class="form-label" style="font-size: 0.75rem;">NOMBRE DE LA CARRERA</label>
                            <input type="text" name="nombre" class="form-input" style="background: rgba(0,0,0,0.1); border-color: rgba(255,255,255,0.05);" placeholder="Ej: Negocios Internacionales" required autofocus>
                        </div>
                        <button type="submit" class="btn-futuristic w-100 py-3 mb-3">
                            <i class="fas fa-plus-circle me-2"></i> REGISTRAR CARRERA
                        </button>
                        <p class="text-center xsmall text-muted"><i class="fas fa-shield-alt me-1"></i> No duplicados permitidos</p>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                title: '¿Eliminar Carrera?',
                text: `Al borrar "${name}" ya no podrá seleccionarse en nuevas vacantes.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar',
                background: 'rgba(15,23,42,0.95)',
                color: '#fff',
                backdrop: `rgba(0,0,0,0.6) blur(4px)`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/admin/carreras/delete?id=${id}`;
                }
            });
        }

        window.addEventListener('load', () => {
            const params = new URLSearchParams(window.location.search);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            if (params.get('reg') === 'success') Toast.fire('¡Registrada!', 'La carrera se agregó con éxito.', 'success');
            if (params.get('del') === 'success') Toast.fire('Eliminada', 'Carrera borrada del sistema.', 'info');
            if (params.get('err') === 'in_use') Toast.fire('Error', 'Esta carrera tiene vacantes activas y no puede borrarse.', 'error');
            if (params.get('err') === 'exists') Toast.fire('Error', 'Esa carrera ya existe.', 'error');
        });
    </script>
</body>
</html>
