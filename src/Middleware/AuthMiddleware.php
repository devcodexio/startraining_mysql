<?php

namespace App\Middleware;

class AuthMiddleware {
    
    public static function check($type = null) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check Maintenance Mode (except for already logged in admins)
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
            // Admin is allowed always
        } else {
            self::checkMaintenance();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        if ($type !== null && isset($_SESSION['user_type']) && $_SESSION['user_type'] !== $type) {
            header('Location: /dashboard?error=access_denied');
            exit();
        }
    }

    public static function checkMaintenance() {
        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT valor FROM configuracion WHERE clave = 'modo_mantenimiento'");
        $stmt->execute();
        $mode = $stmt->fetchColumn() ?: 'off';

        if ($mode === 'on') {
            // Only admins bypass it. If we are on login or maintenance page, don't loop
            $path = explode('?', $_SERVER['REQUEST_URI'])[0];
            if ($path !== '/login' && $path !== '/maintenance' && $path !== '/login-process') {
                header('Location: /maintenance');
                exit();
            }
        }
    }

    public static function isAdmin() {
        self::check();
        if ($_SESSION['user_type'] !== 'admin') {
            header('Location: /403');
            exit();
        }
    }

    public static function isEmpresa() {
        self::check();
        if ($_SESSION['user_type'] !== 'empresa') {
            header('Location: /403');
            exit();
        }
    }

    public static function guestOnly() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit();
        }
    }
}
