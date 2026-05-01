<?php
use App\Models\ConfigModel;
$configModel = new ConfigModel();
$config = $configModel->getSettings();
$siteName = $config['nombre_sitio'] ?? 'StarTraining';

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

    <div class="sidebar-logo">
        <!-- Devoryn-style grid icon -->
        <div class="sidebar-grid-icon" style="margin-bottom:0.75rem;">
            <?php for($i=0;$i<9;$i++): ?><span></span><?php endfor; ?>
        </div>
        <span class="logo-text"><?= htmlspecialchars($siteName) ?></span>
        <span class="logo-tag">Recruitment Platform</span>
    </div>

    <!-- User Profile Widget -->
    <a href="<?= $userType === 'admin' ? '/admin/profile' : '/company/profile' ?>"
       class="sidebar-profile" style="display:flex; color: inherit;">
        <img src="<?= $imgSrc ?>"
             onerror="this.src='<?= $avatarFallback ?>'"
             alt="<?= htmlspecialchars($userName) ?>"
             class="sidebar-profile-img">
        <div style="overflow:hidden; flex:1;">
            <div class="sidebar-profile-name"><?= htmlspecialchars($userName) ?></div>
            <div class="sidebar-profile-role"><?= ucfirst($userType) ?></div>
        </div>
        <i class="fas fa-chevron-down" style="font-size:0.6rem; color:var(--text-muted); align-self:center; flex-shrink:0;"></i>
    </a>

    <nav class="nav-group">
        <a href="/dashboard" class="nav-link <?= $currentUrl === '/dashboard' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <?php if ($userType === 'empresa'): ?>

            <span class="nav-section-label">Gestión</span>

            <a href="/vacancies" class="nav-link <?= $currentUrl === '/vacancies' ? 'active' : '' ?>">
                <i class="fas fa-briefcase"></i> Convocatorias
            </a>
            <a href="/vacancies/expired" class="nav-link <?= $currentUrl === '/vacancies/expired' ? 'active' : '' ?>">
                <i class="fas fa-archive"></i> Finalizadas
            </a>
            <a href="/postulations" class="nav-link <?= str_starts_with($currentUrl, '/postulations') ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Candidatos
            </a>

            <span class="nav-section-label">Cuenta</span>

            <a href="/company/profile" class="nav-link <?= $currentUrl === '/company/profile' ? 'active' : '' ?>">
                <i class="fas fa-user-circle"></i> Mi Perfil
            </a>

        <?php else: ?>

            <span class="nav-section-label">Administración</span>

            <a href="/admin/empresas" class="nav-link <?= str_starts_with($currentUrl, '/admin/empresas') ? 'active' : '' ?>">
                <i class="fas fa-building"></i> Empresas
            </a>
            <a href="/admin/carreras" class="nav-link <?= str_starts_with($currentUrl, '/admin/carreras') ? 'active' : '' ?>">
                <i class="fas fa-graduation-cap"></i> Carreras
            </a>

            <span class="nav-section-label">Convocatorias</span>
            <a href="/admin/vacancies" class="nav-link <?= $currentUrl === '/admin/vacancies' ? 'active' : '' ?>">
                <i class="fas fa-briefcase"></i> Activas
            </a>
            <a href="/admin/vacancies/expired" class="nav-link <?= $currentUrl === '/admin/vacancies/expired' ? 'active' : '' ?>">
                <i class="fas fa-archive"></i> Cerradas
            </a>

            <span class="nav-section-label">Sistema</span>

            <a href="/admin/config" class="nav-link <?= $currentUrl === '/admin/config' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i> Configuración
            </a>
            <a href="/admin/profile" class="nav-link <?= $currentUrl === '/admin/profile' ? 'active' : '' ?>">
                <i class="fas fa-user-shield"></i> Mi Perfil
            </a>

        <?php endif; ?>
    </nav>

    <div class="sidebar-bottom">
        <a href="/logout" class="nav-link danger">
            <i class="fas fa-arrow-right-from-bracket"></i> Cerrar Sesión
        </a>
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
