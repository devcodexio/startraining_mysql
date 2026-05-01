<?php
$userType   = $_SESSION['user_type'] ?? 'empresa';
$userName   = $_SESSION['user_nombre'] ?? 'Invitado';
$profileImg = $_SESSION['user_foto'] ?? '';
if ($profileImg && strpos($profileImg, 'http') === false && strpos($profileImg, '/') !== 0) {
    $profileImg = '/' . $profileImg;
}
$pageTitle = 'Panel de Control';
// Get current page title from context
$uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$pageTitles = [
    'dashboard'   => 'Dashboard',
    'vacancies'   => 'Convocatorias',
    'postulations'=> 'Candidatos',
    'profile'     => 'Mi Perfil',
    'config'      => 'Configuración',
    'empresas'    => 'Empresas',
    'admin'       => 'Administración',
];
if (!empty($uriParts[0]) && isset($pageTitles[$uriParts[0]])) {
    $pageTitle = $pageTitles[$uriParts[0]];
} elseif (count($uriParts) > 1 && isset($pageTitles[end($uriParts)])) {
    $pageTitle = $pageTitles[end($uriParts)];
}

$avatarFallback = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=3b82f6&color=fff&size=96';
$imgSrc = $profileImg ?: $avatarFallback;

// Fetch Notifications
use App\Config\Database;
$db = Database::getConnection();
// Safe migration for testing on user side
try {
    $db->exec("ALTER TABLE postulaciones ADD COLUMN IF NOT EXISTS notificacion_leida BOOLEAN DEFAULT FALSE;");
} catch (\Exception $e) {}

$notifs = [];
if ($userType === 'empresa') {
    $stmt = $db->prepare("SELECT p.id, p.nombre_completo as titulo, v.id as vacante_id, v.titulo_puesto as sub, p.fecha_postulacion as fecha 
                           FROM postulaciones p 
                           JOIN vacantes v ON p.vacante_id = v.id 
                           WHERE v.empresa_id = ? AND p.notificacion_leida = FALSE
                           ORDER BY p.fecha_postulacion DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $notifs = $stmt->fetchAll();
} else {
    // Admin notifications
    // Note: If you want similar behavior for Admin with missing companies, you could add it.
    // For now, let's keep it generic for admin or you can clear it.
    $stmt = $db->prepare("SELECT id, nombre_comercial as titulo, ruc as sub, creado_en as fecha 
                           FROM empresas 
                           WHERE estado = 'pendiente'
                           ORDER BY creado_en DESC LIMIT 5");
    $stmt->execute();
    $notifs = $stmt->fetchAll();
}
?>
<header class="top-header" id="topHeader">
    <!-- Left: Hamburger + Page title + greeting -->
    <div class="d-flex align-items-center gap-3">
        <div class="menu-trigger" id="sidebarToggle" title="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </div>
        <div style="line-height: 1.15;">
            <div style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.3px;">
                Hola, <?= htmlspecialchars(explode(' ', $userName)[0]) ?>! 👋
            </div>
            <div style="font-size: 0.72rem; font-weight: 500; color: var(--text-secondary);">
                Bienvenido a tu espacio de trabajo
            </div>
        </div>
    </div>

    <!-- Right: Actions -->
    <div class="d-flex align-items-center gap-2">
        <!-- Theme Toggle -->
        <button id="theme-toggle" class="theme-toggle-btn" title="Cambiar tema">
            <i class="fas fa-sun"></i>
        </button>

        <!-- Notification Bell -->
        <div class="notif-btn" title="Notificaciones" id="notifBell">
            <i class="fas fa-bell"></i>
            <?php if (!empty($notifs)): ?><span class="dot"></span><?php endif; ?>
            
            <!-- Notif Dropdown -->
            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header" style="display:flex; align-items:center; gap:0.5rem;">
                    <span>Notificaciones</span>
                    <span class="badge badge-primary"><?= count($notifs) ?></span>
                    <?php if (!empty($notifs)): ?>
                        <button onclick="markAllRead(event);" class="btn-ghost py-1 px-2" style="font-size:0.6rem; margin-left:auto; z-index:99;">
                            <i class="fas fa-check-double mt-0"></i> Visto
                        </button>
                    <?php endif; ?>
                </div>
                <div class="notif-body">
                    <?php if (empty($notifs)): ?>
                        <div class="p-4 text-center text-muted xsmall">No hay notificaciones nuevas</div>
                    <?php else: ?>
                        <?php foreach ($notifs as $n): ?>
                            <?php 
                                if ($userType === 'empresa') {
                                    $link = "/postulations?vacante_id=" . $n['vacante_id'] . "&search=" . urlencode($n['titulo']);
                                    $onClick = "markReadAndGo({$n['id']}, '{$link}'); return false;";
                                } else {
                                    $onClick = "window.location.href='/admin/empresas'; return false;";
                                }
                            ?>
                            <a href="#" onclick="<?= $onClick ?>" class="notif-item">
                                <div class="notif-icon"><i class="fas <?= $userType==='admin' ? 'fa-building' : 'fa-user-tie' ?>"></i></div>
                                <div class="notif-content">
                                    <div class="notif-title"><?= htmlspecialchars($n['titulo']) ?> <?= $userType==='empresa' ? 'postuló a:' : 'ha solicitado registro' ?></div>
                                    <div class="notif-sub"><?= htmlspecialchars($n['sub']) ?></div>
                                    <div class="notif-time"><?= date('d/m H:i', strtotime($n['fecha'])) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if ($userType === 'empresa'): ?>
                    <a href="/postulations" class="notif-footer">Ver todas las postulaciones</a>
                <?php else: ?>
                    <a href="/admin/empresas" class="notif-footer">Ver todas las empresas</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile Chip -->
        <a href="<?= $userType === 'admin' ? '/admin/profile' : '/company/profile' ?>"
           class="profile-chip" style="color: inherit;">
            <img src="<?= $imgSrc ?>"
                 onerror="this.src='<?= $avatarFallback ?>'"
                 alt="<?= htmlspecialchars($userName) ?>"
                 class="profile-chip-img">
            <div>
                <div class="profile-chip-name"><?= htmlspecialchars($userName) ?></div>
                <div class="profile-chip-role"><?= ucfirst($userType) ?> <i class="fas fa-chevron-down" style="font-size:0.5rem; opacity:0.6;"></i></div>
            </div>
        </a>
    </div>
</header>

<script>
// Toggle Notifications
document.getElementById('notifBell').addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('notifDropdown').classList.toggle('show');
});
document.addEventListener('click', () => {
    document.getElementById('notifDropdown').classList.remove('show');
});
document.getElementById('notifDropdown').addEventListener('click', e => e.stopPropagation());

async function markReadAndGo(postId, url) {
    try {
        await fetch('/api/notificaciones/leer', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: postId})
        });
    } catch(e) {}
    window.location.href = url;
}

async function markAllRead(e) {
    e.preventDefault();
    e.stopPropagation();
    try {
        await fetch('/api/notificaciones/leer_todas', { method: 'POST' });
        document.querySelector('.notif-body').innerHTML = '<div class="p-4 text-center text-muted xsmall">No hay notificaciones nuevas</div>';
        const badge = document.querySelector('.notif-header .badge');
        if(badge) badge.innerText = '0';
        const dot = document.querySelector('.notif-btn .dot');
        if(dot) dot.remove();
        
        // Remove button Marking all read
        e.currentTarget.remove();
    } catch(e) {}
}
</script>
