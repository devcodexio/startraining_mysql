<?php

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// Load Environment Variables
\App\Config\EnvLoader::load(__DIR__ . '/../.env');

session_start();

$request = $_SERVER['REQUEST_URI'];
$path = explode('?', $request)[0];
$base_path = '/'; 

// Router logic
switch (true) {
    case ($path === $base_path || $path === $base_path . 'index'):
        \App\Middleware\AuthMiddleware::checkMaintenance();
        require __DIR__ . '/../src/Views/home.php';
        break;
        
    case ($path === $base_path . 'login'):
        require __DIR__ . '/../src/Views/login.php';
        break;
        
    case ($path === $base_path . 'login-process'):
        $ctrl = new \App\Controllers\AuthController();
        $ctrl->login();
        break;

    case ($path === $base_path . 'register-company'):
        require __DIR__ . '/../src/Views/register-company.php';
        break;

    case ($path === $base_path . 'register-company-process'):
        $ctrl = new \App\Controllers\CompanyController();
        $ctrl->store();
        break;

    case ($path === $base_path . 'api/consultar-ruc'):
        $ruc = $_GET['ruc'] ?? '';
        $ctrl = new \App\Controllers\CompanyController();
        $ctrl->consultRuc($ruc);
        break;

    case ($path === $base_path . 'api/consultar-dni'):
    case ($path === $base_path . 'api/dni'):
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->consultDni();
        break;

    case ($path === $base_path . 'dashboard'):
        \App\Middleware\AuthMiddleware::check();
        require __DIR__ . '/../src/Views/dashboard.php';
        break;

    case ($path === $base_path . 'vacancies'):
        \App\Middleware\AuthMiddleware::check('empresa');
        require __DIR__ . '/../src/Views/vacancies/list.php';
        break;

    case ($path === $base_path . 'vacancies/create'):
        \App\Middleware\AuthMiddleware::check('empresa');
        require __DIR__ . '/../src/Views/vacancies/create.php';
        break;

    case (preg_match('/^\/vacancies\/edit\/(\d+)$/', $path, $matches)):
        \App\Middleware\AuthMiddleware::check('empresa');
        require __DIR__ . '/../src/Views/vacancies/edit.php';
        break;

    case ($path === $base_path . 'vacancies/update'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->update();
        break;

    case ($path === $base_path . 'vacancies/delete'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->delete();
        break;

    case ($path === $base_path . 'vacancies/toggle-status'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->toggleStatus();
        break;

    case ($path === $base_path . 'vacancies/expired'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->expired();
        break;

    case ($path === $base_path . 'vacancies/store'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->store();
        break;

    case ($path === $base_path . 'postulations'):
        \App\Middleware\AuthMiddleware::check('empresa');
        require __DIR__ . '/../src/Views/postulations/list.php';
        break;

    case ($path === $base_path . 'company/profile'):
        \App\Middleware\AuthMiddleware::check('empresa');
        require __DIR__ . '/../src/Views/company/profile.php';
        break;

    case ($path === $base_path . 'admin/empresas'):
        \App\Middleware\AuthMiddleware::check('admin');
        require __DIR__ . '/../src/Views/admin/companies.php';
        break;

    case ($path === $base_path . 'admin/config'):
        \App\Middleware\AuthMiddleware::check('admin');
        require __DIR__ . '/../src/Views/admin/config.php';
        break;

    case ($path === $base_path . 'admin/save-config'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\ConfigController();
        $ctrl->update();
        break;

    case ($path === $base_path . 'admin/profile'):
        \App\Middleware\AuthMiddleware::check('admin');
        require __DIR__ . '/../src/Views/admin/profile.php';
        break;


    case (preg_match('/^\/vacante\/(\d+)$/', $path, $matches)):
        $id = $matches[1];
        require __DIR__ . '/../src/Views/vacancies/detail.php';
        break;

    case (preg_match('/^\/vacante\/(\d+)\/postular$/', $path, $matches)):
        $id = $matches[1];
        require __DIR__ . '/../src/Views/vacancies/apply.php';
        break;

    case ($path === $base_path . 'vacancies/postular-process'):
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->store();
        break;

    case ($path === $base_path . 'api/analizar-cv'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->analizarCv();
        break;

    case ($path === $base_path . 'api/postulacion/resultado'):
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->getResultadoDni();
        break;

    case ($path === $base_path . 'api/analizar-todos'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->analizarTodos();
        break;

    case ($path === $base_path . 'api/postulacion/update-status'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->updateStatus();
        break;

    case ($path === $base_path . 'api/postulacion/send-email'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->sendEmail();
        break;

    case ($path === $base_path . 'api/notificaciones/leer'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->marcarNotificacionLeida();
        break;

    case ($path === $base_path . 'api/notificaciones/leer_todas'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\PostulationController();
        $ctrl->marcarTodasNotificacionesLeidas();
        break;

    case ($path === $base_path . 'company/profile-update'):
        \App\Middleware\AuthMiddleware::check('empresa');
        $ctrl = new \App\Controllers\CompanyController();
        $ctrl->updateProfile();
        break;

    case ($path === $base_path . 'admin/save-profile'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\CompanyController();
        $ctrl->updateAdminProfile();
        break;

    case ($path === $base_path . 'admin/empresas/toggle-status'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\CompanyController();
        $ctrl->toggleStatus();
        break;

    case ($path === $base_path . 'admin/carreras'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\AdminController();
        $ctrl->indexCarreras();
        break;

    case ($path === $base_path . 'admin/carreras/store'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\AdminController();
        $ctrl->storeCarrera();
        break;

    case ($path === $base_path . 'admin/carreras/delete'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\AdminController();
        $ctrl->deleteCarrera();
        break;

    case ($path === $base_path . 'admin/vacancies'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->indexAdmin();
        break;

    case ($path === $base_path . 'admin/vacancies/expired'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->adminExpired();
        break;

    case ($path === $base_path . 'admin/vacancies/toggle-status'):
        \App\Middleware\AuthMiddleware::check('admin');
        $ctrl = new \App\Controllers\VacancyController();
        $ctrl->toggleStatus();
        break;

    case (preg_match('/^\/admin\/empresas\/detalle\/(\d+)$/', $path, $matches)):
        \App\Middleware\AuthMiddleware::check('admin');
        $id = $matches[1];
        require __DIR__ . '/../src/Views/admin/company_detail.php';
        break;

    case ($path === $base_path . 'logout'):
        session_destroy();
        header('Location: /login');
        break;
        
    case ($path === $base_path . 'maintenance'):
        require __DIR__ . '/../src/Views/maintenance.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../src/Views/404.php';
        break;
}
