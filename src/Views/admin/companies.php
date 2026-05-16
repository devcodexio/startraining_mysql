<?php
use App\Config\Database;
$db = Database::getConnection();
$companies = $db->query("SELECT * FROM empresas ORDER BY ruc ASC")->fetchAll();
$totalCompanies = count($companies);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empresas | StarTraining</title>
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
                <h1 class="mb-1">Gestión de Empresas</h1>
                <p class="text-muted small">Supervisa y bloquea entidades registradas si es necesario.</p>
            </div>
            <div class="glass-card d-flex align-items-center gap-3 px-4 py-2" style="border-radius: 12px;">
                <span class="fw-800 text-primary" style="font-size: 1.25rem;"><?= $totalCompanies ?></span>
                <span class="text-muted small fw-bold" style="line-height: 1;">EMPRESAS<br>TOTALES</span>
            </div>
        </header>

        <div class="glass-card p-0 overflow-hidden">
            <table class="table-cyber">
                <thead>
                    <tr>
                        <th class="ps-5">Logo / Razón Social</th>
                        <th>RUC</th>
                        <th>Sector</th>
                        <th>Estado</th>
                        <th class="pe-5 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($companies as $c): 
                        $isBlocked = ($c['estado'] === 'bloqueado');
                    ?>
                        <tr id="row-<?= $c['id'] ?>">
                            <td class="ps-5">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 44px; height: 44px; border-radius: 10px; border: 1px solid var(--border-glass); background: rgba(59,130,246,0.05); overflow: hidden; flex-shrink: 0;">
                                        <img src="<?= $c['foto_perfil'] ?: 'https://ui-avatars.com/api/?name='.urlencode($c['nombre_comercial']).'&background=3b82f6&color=fff'; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-700" style="font-size: 0.9rem;"><?= htmlspecialchars($c['nombre_comercial'] ?: 'Sin nombre') ?></p>
                                        <p class="mb-0 xsmall text-muted">ID: #<?= $c['id'] ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-primary"><?= $c['ruc'] ?></span></td>
                            <td class="text-muted small"><?= htmlspecialchars($c['sector'] ?: 'General') ?></td>
                            <td id="status-cell-<?= $c['id'] ?>">
                                <?php if ($c['estado'] === 'bloqueado'): ?>
                                    <span class="badge badge-danger">BLOQUEADO</span>
                                <?php elseif ($c['estado'] === 'pendiente'): ?>
                                    <span class="badge badge-warning">PENDIENTE</span>
                                <?php else: ?>
                                    <span class="badge badge-success">ACTIVO</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-5 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="/admin/empresas/detalle/<?= $c['id'] ?>" class="btn-ghost py-2 px-3" style="font-size: 0.75rem; border-radius: 10px;" title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button class="btn-ghost py-2 px-3" style="font-size: 0.75rem; border-radius: 10px;" 
                                            onclick="openEmailModal('<?= $c['correo_contacto'] ?>', '<?= htmlspecialchars($c['nombre_comercial']) ?>')" title="Enviar Correo">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </button>
                                    
                                    <?php if ($c['estado'] === 'pendiente'): ?>
                                        <button id="toggle-btn-<?= $c['id'] ?>" 
                                                class="btn-futuristic py-2 px-3" 
                                                style="font-size: 0.75rem; border-radius: 10px;" 
                                                onclick="toggleCompanyStatus(<?= $c['id'] ?>, 'pendiente')">
                                            <i class="fas fa-check"></i> Aprobar
                                        </button>
                                    <?php else: ?>
                                        <button id="toggle-btn-<?= $c['id'] ?>" 
                                                class="<?= $isBlocked ? 'btn-futuristic' : 'btn-danger-ghost' ?> py-2 px-3" 
                                                style="font-size: 0.75rem; border-radius: 10px;" 
                                                onclick="toggleCompanyStatus(<?= $c['id'] ?>, '<?= $c['estado'] ?>')">
                                            <i class="fas <?= $isBlocked ? 'fa-unlock' : 'fa-lock' ?>"></i>
                                            <?= $isBlocked ? 'Activar' : 'Bloquear' ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal: Enviar Correo Personalizado -->
    <div class="modal-overlay" id="emailModal" style="z-index: 1100;">
        <div class="modal-glass" style="max-width: 500px; text-align: left; padding: 2.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-900 mb-0"><i class="fas fa-paper-plane text-primary me-2"></i> Enviar Mensaje</h4>
                <button onclick="closeEmailModal()" class="btn-ghost p-2"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="form-group mb-4">
                <label class="xsmall fw-800 text-muted mb-2 ls-1">DESTINATARIO</label>
                <input type="text" id="mailToEmail" class="form-input" readonly style="background: rgba(255,255,255,0.05);">
                <input type="hidden" id="mailToName">
            </div>

            <div class="form-group mb-4">
                <label class="xsmall fw-800 text-muted mb-2 ls-1">ASUNTO</label>
                <input type="text" id="mailSubject" class="form-input" placeholder="Ej: Información sobre su cuenta">
            </div>

            <div class="form-group mb-4">
                <label class="xsmall fw-800 text-muted mb-2 ls-1">MENSAJE</label>
                <textarea id="mailMessage" class="form-input" rows="5" style="resize: none;" placeholder="Escriba su mensaje aquí..."></textarea>
            </div>

            <div class="d-flex gap-3 pt-3">
                <button onclick="closeEmailModal()" class="btn-ghost flex-1 py-3">Cancelar</button>
                <button onclick="sendMailAction()" id="btnSendMail" class="btn-futuristic flex-1 py-3">
                    <i class="fas fa-share me-2"></i> Enviar Ahora
                </button>
            </div>
        </div>
    </div>

    <script>
    async function toggleCompanyStatus(id, currentStatus) {
        let newStatus, confirmMsg;
        
        if (currentStatus === 'pendiente') {
            newStatus = 'activo';
            confirmMsg = '¿Deseas APROBAR a esta empresa para que pueda empezar a publicar vacantes?';
        } else {
            newStatus = (currentStatus === 'activo') ? 'bloqueado' : 'activo';
            confirmMsg = (newStatus === 'bloqueado') 
                ? '¿Estás seguro de BLOQUEAR esta empresa? No podrá acceder a su cuenta.'
                : '¿Reactivar acceso para esta empresa?';
        }

        if (!confirm(confirmMsg)) return;

        const btn = document.getElementById(`toggle-btn-${id}`);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

        try {
            const res = await fetch('/admin/empresas/toggle-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ empresa_id: id, status: newStatus })
            });
            const data = await res.json();

            if (data.success) {
                location.reload(); // Sencillo para que se actualice todo el UI de una
            } else {
                alert('Error: ' + data.error);
                btn.disabled = false;
            }
        } catch (e) {
            alert('Error de conexión');
            btn.disabled = false;
        }
    }

    function openEmailModal(email, name) {
        document.getElementById('mailToEmail').value = email;
        document.getElementById('mailToName').value = name;
        document.getElementById('mailSubject').value = '';
        document.getElementById('mailMessage').value = '';
        document.getElementById('emailModal').style.display = 'flex';
    }

    function closeEmailModal() {
        document.getElementById('emailModal').style.display = 'none';
    }

    async function sendMailAction() {
        const email   = document.getElementById('mailToEmail').value;
        const name    = document.getElementById('mailToName').value;
        const subject = document.getElementById('mailSubject').value;
        const message = document.getElementById('mailMessage').value;
        const btn     = document.getElementById('btnSendMail');

        if (!subject || !message) return alert('Por favor complete todos los campos');

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        try {
            const res = await fetch('/admin/empresas/send-email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, name, subject, message })
            });
            const data = await res.json();
            if (data.success) {
                alert('¡Correo enviado con éxito!');
                closeEmailModal();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (e) {
            alert('Error de conexión');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-share me-2"></i> Enviar Ahora';
        }
    }
    </script>
</body>
</html>
