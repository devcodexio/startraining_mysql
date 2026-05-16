<?php
use App\Models\ConfigModel;
$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';
$logoSitio = $config['logo_sitio'] ?? '';

$userType   = $_SESSION['user_type'] ?? 'empresa';
$userName   = $_SESSION['user_nombre'] ?? 'Usuario';
$profileImg = $_SESSION['user_foto'] ?? '';
if ($profileImg && strpos($profileImg, 'http') === false && strpos($profileImg, '/') !== 0) {
    $profileImg = '/' . $profileImg;
}
$avatarFallback = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=3b82f6&color=fff&size=96';
$imgSrc = $profileImg ?: $avatarFallback;
$currentUrl = $_SERVER['REQUEST_URI'];
?>
<aside class="sidebar" id="mainSidebar">
<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Override default collapse behavior to show icons only */
@media (min-width: 769px) {
    .sidebar { transition: width 0.3s ease; overflow-x: hidden; }
    .sidebar.collapsed { width: 85px !important; transform: none !important; }
    .sidebar.collapsed .sidebar-texts,
    .sidebar.collapsed .sidebar-profile-texts,
    .sidebar.collapsed .sidebar-profile-role,
    .sidebar.collapsed .nav-section-label,
    .sidebar.collapsed .nav-link-text { display: none !important; }
    
    .sidebar.collapsed .sidebar-profile i { display: none !important; }
    .sidebar.collapsed .nav-link { justify-content: center; padding: 1rem 0; }
    .sidebar.collapsed .nav-link i { margin-right: 0 !important; font-size: 1.25rem; }
    .sidebar.collapsed .sidebar-logo { padding-left: 0; padding-right: 0; justify-content: center; }
    
    .main-content.expanded { margin-left: 85px !important; }
    .top-header.expanded { left: 85px !important; width: calc(100% - 85px) !important; }
}
</style>


    <div class="sidebar-logo flex items-center gap-3" style="padding: 1.5rem 1rem;">
        <?php if ($logoSitio): ?>
            <img src="<?= htmlspecialchars($logoSitio) ?>" alt="Logo" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" style="width:40px; height:40px;">
        <?php else: ?>
            <div class="sidebar-grid-icon flex-shrink-0" style="margin-bottom:0;">
                <?php for($i=0;$i<9;$i++): ?><span></span><?php endfor; ?>
            </div>
        <?php endif; ?>
        <div class="sidebar-texts flex flex-col justify-center overflow-hidden whitespace-nowrap transition-all duration-300">
            <span class="logo-text text-lg font-bold leading-tight" style="margin:0;"><?= htmlspecialchars($siteName) ?></span>
            <span class="logo-tag text-xs text-gray-400">Recruitment Platform</span>
        </div>
    </div>

    <!-- User Profile Widget -->
    <a href="<?= $userType === 'admin' ? '/admin/profile' : '/company/profile' ?>"
       class="sidebar-profile" style="display:flex; color: inherit;">
        <img src="<?= $imgSrc ?>"
             onerror="this.src='<?= $avatarFallback ?>'"
             alt="<?= htmlspecialchars($userName) ?>"
             class="sidebar-profile-img">
        <div class="sidebar-profile-texts" style="overflow:hidden; flex:1;">
            <div class="sidebar-profile-name"><?= htmlspecialchars($userName) ?></div>
            <div class="sidebar-profile-role"><?= ucfirst($userType) ?></div>
        </div>
        <i class="fas fa-chevron-down" style="font-size:0.6rem; color:var(--text-muted); align-self:center; flex-shrink:0;"></i> <span class="nav-link-text"> </span> </a>

    <nav class="nav-group">
        <a href="/dashboard" class="nav-link <?= $currentUrl === '/dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> <span class="nav-link-text">Dashboard</span> </a>

        <?php if ($userType === 'empresa'): ?>

            <span class="nav-section-label">Gestión</span>

            <a href="/vacancies" class="nav-link <?= $currentUrl === '/vacancies' ? 'active' : '' ?>">
                <i class="fas fa-briefcase"></i> <span class="nav-link-text">Convocatorias</span> </a>
            <a href="/vacancies/expired" class="nav-link <?= $currentUrl === '/vacancies/expired' ? 'active' : '' ?>">
                <i class="fas fa-archive"></i> <span class="nav-link-text">Finalizadas</span> </a>
            <a href="/postulations" class="nav-link <?= str_starts_with($currentUrl, '/postulations') ? 'active' : '' ?>">
                <i class="fas fa-users"></i> <span class="nav-link-text">Candidatos</span> </a>
            <a href="/postulations/export" class="nav-link">
                <i class="fas fa-download"></i> <span class="nav-link-text">Backups</span> </a>

            <span class="nav-section-label">Cuenta</span>

            <a href="/company/profile" class="nav-link <?= $currentUrl === '/company/profile' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i> <span class="nav-link-text">Mi Perfil</span> </a>

        <?php else: ?>

            <span class="nav-section-label">Administración</span>

            <a href="/admin/empresas" class="nav-link <?= str_starts_with($currentUrl, '/admin/empresas') ? 'active' : '' ?>">
                <i class="fas fa-building"></i> <span class="nav-link-text">Empresas</span> </a>
            <a href="/admin/carreras" class="nav-link <?= str_starts_with($currentUrl, '/admin/carreras') ? 'active' : '' ?>">
                <i class="fas fa-graduation-cap"></i> <span class="nav-link-text">Carreras</span> </a>

            <span class="nav-section-label">Convocatorias</span>
            <a href="/admin/vacancies" class="nav-link <?= $currentUrl === '/admin/vacancies' ? 'active' : '' ?>">
                <i class="fas fa-briefcase"></i> <span class="nav-link-text">Activas</span> </a>
            <a href="/admin/vacancies/expired" class="nav-link <?= $currentUrl === '/admin/vacancies/expired' ? 'active' : '' ?>">
                <i class="fas fa-archive"></i> <span class="nav-link-text">Cerradas</span> </a>

            <span class="nav-section-label">Sistema</span>

            <a href="/admin/config" class="nav-link <?= $currentUrl === '/admin/config' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i> <span class="nav-link-text">Configuración</span> </a>
            <a href="/postulations/export" class="nav-link">
                <i class="fas fa-download"></i> <span class="nav-link-text">Backups</span> </a>
            <a href="/admin/profile" class="nav-link <?= $currentUrl === '/admin/profile' ? 'active' : '' ?>">
                <i class="fas fa-user-shield"></i> <span class="nav-link-text">Mi Perfil</span> </a>

        <?php endif; ?>
    </nav>

    <div class="sidebar-bottom">
        <a href="/logout" class="nav-link danger">
            <i class="fas fa-arrow-right-from-bracket"></i> <span class="nav-link-text">Cerrar Sesión
        </span> </a>
    </div>
</aside>

<!-- StarAlert Modal -->
<div id="modalOverlay" class="modal-overlay">
    <div class="modal-glass">
        <span id="modalIcon" class="modal-icon"></span>
        <h3 id="modalTitle" class="mb-2 fw-700" style="font-size:1.2rem;"></h3>
        <p id="modalMsg" class="text-muted mb-4" style="font-size:0.9rem; line-height:1.6;"></p>
        <button onclick="StarAlert.hide()" class="btn-futuristic px-5" style="padding:0.7rem 2rem;">Aceptar</button>
    </div>
</div>

<!-- Detail Modal (candidate/company popups) -->
<div id="detailModalOverlay" class="modal-overlay" onclick="if(event.target===this) closeDetailModal()">
    <div class="modal-glass detail-modal" id="detailModalContent" onclick="event.stopPropagation()">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 id="detailModalTitle" class="fw-700" style="font-size:1.1rem;"></h3>
            <button onclick="closeDetailModal()" class="btn-ghost" style="padding:0.4rem 0.75rem; border-radius:8px; font-size:0.85rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="detailModalBody"></div>
    </div>
</div>

<script>
/* ============================================
   StarAlert System
   ============================================ */
const StarAlert = {
    show(title, msg, type = 'info') {
        const icons = {
            success: 'fa-check-circle',
            error:   'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info:    'fa-info-circle'
        };
        document.getElementById('modalIcon').innerHTML = `<i class="fas ${icons[type] || icons.info}"></i>`;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalMsg').innerText = msg;
        document.getElementById('modalOverlay').style.display = 'flex';
    },
    hide() {
        document.getElementById('modalOverlay').style.display = 'none';
        window.history.replaceState({}, document.title, window.location.pathname);
    }
};

function openDetailModal(title, bodyHtml) {
    document.getElementById('detailModalTitle').innerText = title;
    document.getElementById('detailModalBody').innerHTML = bodyHtml;
    document.getElementById('detailModalOverlay').style.display = 'flex';
}
function closeDetailModal() {
    document.getElementById('detailModalOverlay').style.display = 'none';
}

/* ============================================
   Sidebar & Theme Toggle
   ============================================ */
document.addEventListener('click', e => {
    // Theme toggle
    if (e.target.closest('#theme-toggle')) {
        const doc = document.documentElement;
        const newTheme = doc.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        doc.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        const btn = document.getElementById('theme-toggle');
        if (btn) btn.innerHTML = newTheme === 'dark'
            ? '<i class="fas fa-moon"></i>'
            : '<i class="fas fa-sun"></i>';
    }

    // Sidebar toggle
    if (e.target.closest('#sidebarToggle')) {
        const sidebar  = document.getElementById('mainSidebar');
        const main     = document.querySelector('.main-content');
        const header   = document.querySelector('.top-header');
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
            sidebar.classList.toggle('mobile-open');
        } else {
            sidebar.classList.toggle('collapsed');
            main   && main.classList.toggle('expanded');
            header && header.classList.toggle('expanded');
        }
    }
});

// Close mobile sidebar on outside click
document.addEventListener('click', e => {
    if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('mainSidebar');
        if (sidebar && sidebar.classList.contains('mobile-open')
            && !e.target.closest('#mainSidebar')
            && !e.target.closest('#sidebarToggle')) {
            sidebar.classList.remove('mobile-open');
        }
    }
});

/* ============================================
   Init theme & URL-based alerts
   ============================================ */
(function() {
    const saved = localStorage.getItem('theme') || 'light'; // default light (Devoryn)
    document.documentElement.setAttribute('data-theme', saved);
    const btn = document.getElementById('theme-toggle');
    if (btn) btn.innerHTML = saved === 'dark'
        ? '<i class="fas fa-moon"></i>'
        : '<i class="fas fa-sun"></i>';
})();

window.addEventListener('load', () => {
    const p = new URLSearchParams(window.location.search);
    if (p.get('success') === '1')
        StarAlert.show('¡Guardado!', 'Los cambios fueron aplicados correctamente.', 'success');
    if (p.get('postulado') === 'success')
        StarAlert.show('¡Postulación Enviada!', 'Tu perfil fue recibido. La empresa te notificará pronto con los resultados del análisis de IA.', 'success');
    if (p.get('postulado') === 'error')
        StarAlert.show('Error al Postular', p.get('msg') || 'Ocurrió un error inesperado.', 'error');
    if (p.get('error') === '1')
        StarAlert.show('Error', p.get('msg') || 'Ocurrió un error.', 'error');
});
</script>
