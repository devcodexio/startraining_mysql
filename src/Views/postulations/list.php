<?php
use App\Config\Database;
$db = Database::getConnection();
$companyId = $_SESSION['user_id'];

$filterStatus = $_GET['estado'] ?? '';
$filterSearch = $_GET['search'] ?? '';
$filterVacante = $_GET['vacante_id'] ?? '';

// Join con vacante para obtener requisitos y titulo
$sql = "SELECT p.*, v.titulo_puesto, v.modalidad, v.requisitos_raw
        FROM postulaciones p
        JOIN vacantes v ON p.vacante_id = v.id
        WHERE v.empresa_id = :cid";

$params = [':cid' => $companyId];

if ($filterVacante) {
    $sql .= " AND p.vacante_id = :vacante";
    $params[':vacante'] = $filterVacante;
}
if ($filterStatus) {
    $sql .= " AND p.estado_postulacion = :estado";
    $params[':estado'] = $filterStatus;
}
if ($filterSearch) {
    $sql .= " AND (p.nombre_completo LIKE :s OR v.titulo_puesto LIKE :s)";
    $params[':s'] = '%' . $filterSearch . '%';
}

$sql .= " ORDER BY p.match_porcentaje DESC, p.fecha_postulacion DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$postulations = $stmt->fetchAll();

// Fetch company vacancies for filter
$stmtVacantes = $db->prepare("SELECT id, titulo_puesto FROM vacantes WHERE empresa_id = :cid ORDER BY titulo_puesto ASC");
$stmtVacantes->execute([':cid' => $companyId]);
$vacantesEmpresa = $stmtVacantes->fetchAll();

// Contar pendientes
$totalPendientes = count(array_filter($postulations, fn($p) => $p['estado_postulacion'] === 'en_espera'));

function matchColor($pct)
{
    if ($pct >= 85)
        return '#10b981';
    if ($pct >= 70)
        return '#f59e0b';
    if ($pct > 0)
        return '#ef4444';
    return '#64748b';
}

$estadoMap = [
    'en_espera' => ['label' => 'En Espera', 'class' => 'badge-warning'],
    'IA Realizado' => ['label' => 'IA Realizado', 'class' => 'badge-primary'],
    'Apto' => ['label' => 'Apto', 'class' => 'badge-success'],
    'No Apto' => ['label' => 'No Apto', 'class' => 'badge-danger'],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Candidatos | StarTraining</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* Spinner */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            animation: spin 0.8s linear infinite;
            display: inline-block;
        }

        /* AI Progress Bar */
        .ai-progress-wrap {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: rgba(7, 10, 15, 0.96);
            border: 1px solid var(--primary);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            min-width: 320px;
            z-index: 999;
            box-shadow: var(--shadow-neon);
            display: none;
        }

        .ai-progress-bar-bg {
            height: 8px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.75rem;
        }

        .ai-progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        /* Row glow on analysis */
        .row-analyzing {
            animation: rowPulse 1.2s infinite;
        }

        @keyframes rowPulse {

            0%,
            100% {
                background: transparent;
            }

            50% {
                background: rgba(var(--primary-rgb), 0.04);
            }
        }

        /* Pending Pulse */
        .match-pending {
            cursor: pointer;
            transition: transform 0.2s;
        }

        .match-pending:hover {
            transform: scale(1.05);
            color: var(--primary) !important;
        }

        .pulse-ia {
            animation: pulseIA 2s infinite;
            opacity: 0.7;
        }

        @keyframes pulseIA {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
                text-shadow: 0 0 10px var(--primary);
            }

            100% {
                opacity: 0.5;
            }
        }

        /* Fixed Centered Email Modal */
        #emailModal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            z-index: 10000;
            display: none;
            /* Flex on open */
            align-items: center;
            /* Centrado vertical */
            justify-content: center;
            /* Centrado horizontal */
            padding: 1.5rem;
        }

        #emailModal .glass-card {
            width: 100%;
            max-width: 550px;
            animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>

<body class="animate">
    <?php require_once __DIR__ . '/../../Layouts/Sidebar.php'; ?>
    <?php require_once __DIR__ . '/../../Layouts/Header.php'; ?>

    <main class="main-content">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="text-gradient mb-1">Postulantes</h1>
                <p class="text-muted small">Gestiona los postulantes y activa el análisis de IA cuando estés listo.</p>
            </div>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <!-- Bulk AI Button -->
                <?php if ($totalPendientes > 0): ?>
                    <button id="btnAnalizarTodos" class="btn-futuristic"
                        style="background: linear-gradient(135deg, var(--accent), #a855f7); box-shadow: 0 4px 20px rgba(240,147,251,0.4);"
                        onclick="analizarTodos()">
                        <i class="fas fa-robot me-2"></i>
                        ANALIZAR TODOS (<?= $totalPendientes ?> pendientes)
                    </button>
                <?php else: ?>
                    <button class="btn-ghost" disabled style="opacity:0.4; cursor: not-allowed;">
                        <i class="fas fa-robot me-2"></i> Sin pendientes
                    </button>
                <?php endif; ?>
                <span class="badge badge-primary"
                    style="font-size: 0.82rem; padding: 0.5rem 1rem;"><?= count($postulations) ?> candidatos</span>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="glass-card mb-4" style="padding: 1.2rem 1.5rem;">
            <div class="d-flex gap-3 flex-wrap align-items-center">
                <div class="d-flex align-items-center gap-2"
                    style="flex:1; min-width:200px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass); border-radius: 12px; padding: 0.6rem 1rem;">
                    <i class="fas fa-search text-muted xsmall"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($filterSearch) ?>"
                        placeholder="Buscar nombre o vacante..."
                        style="background: none; border: none; outline: none; color: var(--text-primary); font-family: var(--font); font-size: 0.9rem; width: 100%;">
                </div>
                <select name="vacante_id" class="form-input" style="max-width:300px; padding: 0.6rem 1rem; width: 100%; border-radius: 12px;">
                    <option value="">Todas las convocatorias</option>
                    <?php foreach ($vacantesEmpresa as $ve): ?>
                        <option value="<?= $ve['id'] ?>" <?= $filterVacante == $ve['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ve['titulo_puesto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="estado" class="form-input" style="max-width:200px; padding: 0.6rem 1rem; width: 100%; border-radius: 12px;">
                    <option value="">Todos los estados</option>
                    <option value="en_espera" <?= $filterStatus === 'en_espera' ? 'selected' : '' ?>>En Espera</option>
                    <option value="IA Realizado" <?= $filterStatus === 'IA Realizado' ? 'selected' : '' ?>>IA Realizado
                    </option>
                    <option value="Apto" <?= $filterStatus === 'Apto' ? 'selected' : '' ?>>Apto</option>
                    <option value="No Apto" <?= $filterStatus === 'No Apto' ? 'selected' : '' ?>>No Apto</option>
                </select>
                <button type="submit" class="btn-futuristic" style="padding: 0.65rem 1.5rem; font-size:0.78rem;">
                    <i class="fas fa-filter"></i> FILTRAR
                </button>
                <a href="/postulations" class="btn-ghost"
                    style="padding: 0.65rem 1.5rem; font-size:0.78rem;">Limpiar</a>
            </div>
        </form>

        <!-- Candidates Table -->
        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <table class="table-cyber">
                <thead>
                    <tr>
                        <th class="ps-5">CANDIDATO</th>
                        <th>VACANTE</th>
                        <th>FECHA</th>
                        <th>AI MATCH</th>
                        <th>ESTADO</th>
                        <th class="pe-5" style="text-align:right; min-width: 220px;">ACCIONES</th>
                    </tr>
                </thead>
                <tbody id="candidatesTable">
                    <?php foreach ($postulations as $p):
                        $pct = round($p['match_porcentaje'] ?? 0);
                        $color = matchColor($pct);
                        $estado = $estadoMap[$p['estado_postulacion']] ?? ['label' => $p['estado_postulacion'], 'class' => 'badge-warning'];
                        $isPending = $p['estado_postulacion'] === 'en_espera';
                        $urlRaw = trim($p['url_cv_pdf'] ?? '');
                        $cvUrl = $urlRaw ? (str_starts_with($urlRaw, 'http') ? $urlRaw : '/' . $urlRaw) : '';

                        // Build safe JS strings for the modal
                        $jsDni = addslashes($p['dni']);
                        $jsNombre = addslashes(htmlspecialchars($p['nombre_completo']));
                        $jsCorreo = addslashes(htmlspecialchars($p['correo_estudiante']));
                        $jsCelular = addslashes($p['celular']);
                        $jsPuesto = addslashes(htmlspecialchars($p['titulo_puesto']));
                        $jsDescripcion = addslashes(htmlspecialchars($p['ia_analisis_descripcion'] ?? ''));
                        ?>
                        <tr id="row-<?= $p['id'] ?>">
                            <td class="ps-5">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        style="width:44px; height:44px; border-radius:12px; background:rgba(var(--primary-rgb),0.1); display:flex; align-items:center; justify-content:center; color:var(--primary); flex-shrink:0; font-size:1.1rem;">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-700" style="font-size:0.92rem;" id="name-<?= $p['id'] ?>">
                                            <?= htmlspecialchars($p['nombre_completo']) ?></p>
                                        <span
                                            class="text-muted xsmall"><?= htmlspecialchars($p['correo_estudiante']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 fw-600" style="font-size:0.88rem;">
                                    <?= htmlspecialchars($p['titulo_puesto']) ?></p>
                                <span class="badge badge-primary" style="font-size:0.55rem;"><?= $p['modalidad'] ?></span>
                            </td>
                            <td class="text-muted small"><?= date('d M, Y', strtotime($p['fecha_postulacion'])) ?></td>
                            <td id="match-<?= $p['id'] ?>">
                                <?php if ($p['estado_postulacion'] !== 'en_espera'): ?>
                                    <span class="fw-900" style="font-size:1.3rem; color:<?= $color ?>;"><?= $pct ?>%</span>
                                    <div class="match-bar-bg" style="width:80px; margin-top:0.3rem;">
                                        <div class="match-bar-fill"
                                            style="width:<?= max(2, $pct) ?>%; background:<?= $color ?>;"></div>
                                    </div>
                                <?php else: ?>
                                    <button class="btn-futuristic py-2 px-3 small pulse-ia"
                                        style="background: linear-gradient(135deg, var(--accent), #a855f7); border-radius: 12px; font-size: 0.68rem;"
                                        onclick="analizarUno(<?= $p['id'] ?>, '<?= $jsPuesto ?>')">
                                        <i class="fas fa-robot me-1"></i> ANALIZAR
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td id="status-<?= $p['id'] ?>">
                                <span class="badge <?= $estado['class'] ?>"><?= $estado['label'] ?></span>
                            </td>
                            <td class="pe-5" style="text-align:right;">
                                <div class="d-flex gap-2 justify-content-end align-items-center">

                                    <!-- Analyze individually -->
                                    <?php if ($isPending): ?>
                                        <button id="btn-ia-<?= $p['id'] ?>" class="btn-futuristic py-2 px-3"
                                            style="font-size:0.72rem; background: linear-gradient(135deg, var(--accent), #a855f7); box-shadow: 0 2px 12px rgba(240,147,251,0.3); border-radius:10px;"
                                            onclick="analizarUno(<?= $p['id'] ?>, '<?= $jsPuesto ?>')"
                                            title="Analizar este CV con IA">
                                            <i class="fas fa-robot"></i> IA
                                        </button>
                                    <?php else: ?>
                                        <span title="Ya analizado"
                                            style="width:32px; display:inline-flex; align-items:center; justify-content:center; color: var(--primary); font-size:1rem;">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    <?php endif; ?>

                                    <!-- View candidate details -->
                                    <button class="btn-ghost py-2 px-3" style="font-size:0.72rem; border-radius:10px;"
                                        onclick="openDetailModal('Perfil del Candidato', buildCandidateHtml(
                                        '<?= $jsDni ?>', '<?= $jsNombre ?>', '<?= $jsCorreo ?>', '<?= $jsCelular ?>',
                                        '<?= $jsPuesto ?>', <?= $pct ?>, '<?= $estado['class'] ?>', '<?= $estado['label'] ?>',
                                        '<?= $jsDescripcion ?>', '<?= addslashes($cvUrl) ?>', <?= $p['id'] ?>, <?= var_export($isPending, true) ?>
                                    ))">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- CV Download -->
                                    <?php if ($cvUrl): ?>
                                        <a href="<?= $cvUrl ?>" target="_blank" class="btn-ghost py-2 px-3"
                                            style="font-size:0.72rem; border-radius:10px;" title="Ver CV">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($postulations)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-satellite-dish text-muted"
                                    style="font-size:2.5rem; opacity:0.3; display:block; margin-bottom:1rem;"></i>
                                <p class="text-muted">No hay candidatos que coincidan con los filtros aplicados.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- AI Bulk Progress Notification -->
    <div id="aiProgressWrap" class="ai-progress-wrap">
        <div class="d-flex align-items-center gap-3 mb-2">
            <i class="fas fa-robot text-primary" style="font-size:1.4rem;"></i>
            <div>
                <p class="fw-800 mb-0" style="font-size:0.9rem;">Análisis IA en Progreso</p>
                <p id="aiProgressText" class="text-muted xsmall mb-0">Iniciando...</p>
            </div>
        </div>
        <div class="ai-progress-bar-bg">
            <div class="ai-progress-bar-fill" id="aiProgressBar" style="width:0%"></div>
        </div>
    </div>

    <!-- Email Modal (Centered) -->
    <div id="emailModal">
        <div class="glass-card" onclick="event.stopPropagation()">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-gradient mb-0"><i class="fas fa-paper-plane me-2"></i>Enviar Notificación</h3>
                <button onclick="closeEmailModal()" class="btn-ghost"
                    style="padding:0.4rem 0.75rem; border-radius:10px;"><i class="fas fa-times"></i></button>
            </div>

            <form id="emailForm" onsubmit="sendCandidateEmail(event)">
                <input type="hidden" id="emailPostId">
                <div class="mb-3">
                    <label class="xsmall text-muted fw-800 mb-1 d-block text-uppercase">Para:</label>
                    <input type="text" id="emailTo" class="form-input" readonly
                        style="background:rgba(255,255,255,0.05);">
                </div>
                <div class="mb-3">
                    <label class="xsmall text-muted fw-800 mb-1 d-block text-uppercase">Asunto:</label>
                    <input type="text" id="emailSubject" class="form-input" required
                        value="Noticias sobre tu postulación | StarTraining">
                </div>
                <div class="mb-4">
                    <label class="xsmall text-muted fw-800 mb-1 d-block text-uppercase">Mensaje Adicional:</label>
                    <textarea id="emailMessage" class="form-input" rows="5" required
                        placeholder="Escribe el mensaje para el candidato..."></textarea>
                </div>
                <div class="d-flex gap-3 justify-content-end">
                    <button type="button" onclick="closeEmailModal()" class="btn-ghost"
                        style="padding:0.7rem 1.5rem;">Cerrar</button>
                    <button type="submit" id="btnSendEmail" class="btn-futuristic"
                        style="padding:0.7rem 2rem; background:linear-gradient(135deg, #007bff, #00d2ff);">
                        <i class="fas fa-paper-plane me-2"></i> ENVIAR AHORA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        /* ============================================
           Helper: Build candidate detail HTML
           ============================================ */
        function buildCandidateHtml(dni, nombre, correo, celular, puesto, pct, estadoClass, estadoLabel, descripcion, cvUrl, postId, isPending) {
            const color = pct >= 85 ? '#10b981' : pct >= 70 ? '#f59e0b' : pct > 0 ? '#ef4444' : '#64748b';
            const matchHtml = pct > 0
                ? `<span style="font-size:1.8rem; font-weight:900; color:${color};">${pct}%</span>`
                : `<span class="text-muted">Pendiente de análisis</span>`;

            const descHtml = descripcion
                ? `<div class="border-top pt-3 mt-3"><p class="xsmall text-muted fw-800 mb-1">OPINIÓN DE LA IA</p><p style="font-size:0.9rem; line-height:1.6;">${descripcion}</p></div>`
                : '';

            const btnIA = isPending
                ? `<button class="btn-futuristic py-2 px-4" style="font-size:0.78rem; background: linear-gradient(135deg, var(--accent), #a855f7);" onclick="closeDetailModal(); analizarUno(${postId}, '${puesto}')"><i class='fas fa-robot me-2'></i> ANALIZAR CON IA</button>`
                : '';

            const btnCV = cvUrl
                ? `<a href="${cvUrl}" target="_blank" class="btn-futuristic py-2 px-4" style="font-size:0.78rem;"><i class='fas fa-file-pdf me-2'></i> VER CV</a>`
                : '';

            // Solo mostrar botón de correo si es APTO
            const btnEmail = (estadoLabel === 'Apto')
                ? `<button class="btn-futuristic py-2 px-4" style="font-size:0.78rem; background: #007bff;" onclick="closeDetailModal(); openEmailModal(${postId}, '${nombre}', '${correo}')"><i class='fas fa-envelope me-2'></i> ENVIAR CORREO</button>`
                : '';

            return `
                <div class="detail-modal-grid" style="gap:1.2rem;">
                    <div class="detail-field"><div class="detail-field-label">DNI</div><div class="detail-field-value fw-800">${dni}</div></div>
                    <div class="detail-field"><div class="detail-field-label">Nombre</div><div class="detail-field-value">${nombre}</div></div>
                    <div class="detail-field"><div class="detail-field-label">Correo</div><div class="detail-field-value small">${correo}</div></div>
                    <div class="detail-field"><div class="detail-field-label">Celular</div><div class="detail-field-value">${celular}</div></div>
                    <div class="detail-field"><div class="detail-field-label">Vacante</div><div class="detail-field-value">${puesto}</div></div>
                    <div class="detail-field"><div class="detail-field-label">Estado Actual</div><span class="badge ${estadoClass}">${estadoLabel}</span></div>
                    
                    <div class="detail-field" style="grid-column:span 2;">
                        <div class="detail-field-label">Cambiar Estado</div>
                        <div class="d-flex gap-2">
                            <button class="btn-futuristic small flex-grow-1 py-2" style="background:#10b981; font-size:0.65rem;" onclick="updateCandidateStatus(${postId}, 'Apto')">MARCAR APTO</button>
                            <button class="btn-futuristic small flex-grow-1 py-2" style="background:#ef4444; font-size:0.65rem;" onclick="updateCandidateStatus(${postId}, 'No Apto')">MARCAR NO APTO</button>
                        </div>
                    </div>

                    <div class="detail-field" style="grid-column:span 2;">
                        <div class="detail-field-label">AI Match Score</div>${matchHtml}
                    </div>
                </div>
                ${descHtml}
                <div class="d-flex gap-2 mt-4 pt-3 border-top justify-content-end">
                    ${btnIA}${btnEmail}${btnCV}
                </div>`;
        }

        /* ============================================
           Status/Email JS Actions
           ============================================ */
        async function updateCandidateStatus(postId, newStatus) {
            if (!confirm(`¿Cambiar estado a "${newStatus}"?`)) return;
            try {
                const res = await fetch('/api/postulacion/update-status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ postulacion_id: postId, estado: newStatus })
                });
                const data = await res.json();
                if (data.success) {
                    StarAlert.show('Actualizado', `Estado cambiado a ${newStatus}`, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            } catch (e) { console.error(e); }
        }

        function openEmailModal(postId, nombre, correo) {
            document.getElementById('emailPostId').value = postId;
            document.getElementById('emailTo').value = `${nombre} <${correo}>`;
            document.getElementById('emailModal').style.display = 'flex';
        }

        function closeEmailModal() {
            document.getElementById('emailModal').style.display = 'none';
        }

        async function sendCandidateEmail(event) {
            event.preventDefault();
            const btn = document.getElementById('btnSendEmail');
            const postId = document.getElementById('emailPostId').value;
            const subject = document.getElementById('emailSubject').value;
            const message = document.getElementById('emailMessage').value;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner spinner"></i> ENVIANDO...';

            try {
                const res = await fetch('/api/postulacion/send-email', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        postulacion_id: postId,
                        subject: subject,
                        message: message
                    })
                });
                const data = await res.json();
                if (data.success) {
                    StarAlert.show('¡Enviado!', 'El correo se ha enviado exitosamente.', 'success');
                    closeEmailModal();
                } else {
                    StarAlert.show('Error', data.error || 'No se pudo enviar el correo.', 'error');
                }
            } catch (e) {
                console.error(e);
                StarAlert.show('Error', 'Fallo de conexión.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-robot me-1"></i> ENVIAR CORREO';
            }
        }

        /* ============================================
           Analyze ONE candidate
           ============================================ */
        async function analizarUno(postId, puesto) {
            const btn = document.getElementById(`btn-ia-${postId}`);
            const row = document.getElementById(`row-${postId}`);
            if (!btn) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner spinner"></i> Analizando...';
            row.classList.add('row-analyzing');

            try {
                const res = await fetch('/api/analizar-cv', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ postulacion_id: postId })
                });

                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Response non-JSON:', text);
                    StarAlert.show('Error de Servidor', 'El servidor devolvió una respuesta inválida. ' + text.substring(0, 100), 'error');
                    return;
                }

                if (data.success) {
                    const pct = Math.round(data.puntaje);
                    const color = pct >= 85 ? '#10b981' : pct >= 70 ? '#f59e0b' : '#ef4444';

                    document.getElementById(`match-${postId}`).innerHTML = `
                        <span class="fw-900" style="font-size:1.3rem; color:${color};">${pct}%</span>
                        <div class="match-bar-bg" style="width:80px; margin-top:0.3rem;">
                            <div class="match-bar-fill" style="width:${pct}%; background:${color};"></div>
                        </div>`;

                    document.getElementById(`status-${postId}`).innerHTML = `<span class="badge badge-primary">IA Realizado</span>`;

                    btn.outerHTML = `<span id="btn-ia-${postId}" title="Análisis completado" style="width:32px; display:inline-flex; align-items:center; justify-content:center; color:var(--primary);">
                                        <i class="fas fa-check-circle"></i></span>`;

                    StarAlert.show('¡Análisis Completado!', `Puntuación para "${puesto}": ${pct}%. ${data.descripcion}`, 'success');
                } else {
                    StarAlert.show('Error de IA', data.error || 'No se pudo conectar con n8n. Verifica el webhook.', 'error');
                }
            } catch (e) {
                console.error('Fetch error:', e);
                StarAlert.show('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-robot"></i> IA';
                row.classList.remove('row-analyzing');
            }
        }

        /* ============================================
           Analyze ALL pending candidates
           ============================================ */
        async function analizarTodos() {
            const btn = document.getElementById('btnAnalizarTodos');
            const progressWrap = document.getElementById('aiProgressWrap');
            const progressBar = document.getElementById('aiProgressBar');
            const progressText = document.getElementById('aiProgressText');

            if (!confirm('¿Iniciar el análisis de IA para todos los candidatos en espera?\n\nEsto puede tomar varios minutos.')) return;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner spinner me-2"></i> ANALIZANDO...';
            progressWrap.style.display = 'block';
            progressText.innerText = 'Conectando con el sistema de IA...';
            progressBar.style.width = '5%';

            try {
                const res = await fetch('/api/analizar-todos', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                });

                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Bulk non-JSON:', text);
                    StarAlert.show('Error de Servidor', 'El servidor devolvió una respuesta inválida en el análisis masivo. ' + text.substring(0, 100), 'error');
                    return;
                }

                progressBar.style.width = '100%';

                if (data.success) {
                    progressText.innerText = `✓ ${data.analizados} analizados, ${data.fallidos} fallidos.`;
                    StarAlert.show(
                        '¡Análisis Masivo Completado!',
                        `Se analizaron ${data.analizados} de ${data.total} candidatos con IA.\n${data.fallidos > 0 ? `${data.fallidos} no pudieron analizarse.` : ''}`,
                        'success'
                    );
                    setTimeout(() => location.reload(), 2500);
                } else {
                    progressText.innerText = '✗ Error al procesar.';
                    StarAlert.show('Error', data.error || 'Error desconocido al analizar.', 'error');
                }
            } catch (e) {
                console.error('Bulk fetch error:', e);
                progressBar.style.width = '0%';
                progressText.innerText = '✗ Error de conexión.';
                StarAlert.show('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-robot me-2"></i> ANALIZAR TODOS';
                setTimeout(() => { progressWrap.style.display = 'none'; }, 4000);
            }
        }
    </script>
</body>

</html>