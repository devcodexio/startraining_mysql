<?php

namespace App\Controllers;

use App\Config\Database;

class AuthController {
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $userOrEmail = $_POST['login_user'] ?? '';
        $password    = $_POST['login_pass'] ?? '';
        
        $db = Database::getConnection();

        // 1. INTENTO COMO ADMINISTRADOR (Buscando por usuario)
        $stmt = $db->prepare("SELECT * FROM administradores WHERE usuario = :u");
        $stmt->execute([':u' => $userOrEmail]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['user_id']     = $admin['id'];
            $_SESSION['user_type']   = 'admin';
            $_SESSION['user_nombre'] = $admin['nombre'];
            $_SESSION['user_foto']   = $admin['foto_perfil'];
            header('Location: /dashboard');
            return;
        }

        // 2. INTENTO COMO EMPRESA (Buscando por correo_contacto)
        $stmt = $db->prepare("SELECT * FROM empresas WHERE correo_contacto = :e");
        $stmt->execute([':e' => $userOrEmail]);
        $empresa = $stmt->fetch();

        if ($empresa) {
            // El administrador debe aprobar la empresa para que pueda loguearse
            if ($empresa['estado'] === 'bloqueado') {
                header('Location: /login?error=account_blocked');
                exit;
            }

            if ($empresa['estado'] === 'pendiente') {
                header('Location: /login?error=account_pending');
                exit;
            }

            if (password_verify($password, $empresa['password_hash'])) {
                $_SESSION['user_id']     = $empresa['id'];
                $_SESSION['user_type']   = 'empresa';
                $_SESSION['user_nombre'] = $empresa['nombre_comercial'];
                $_SESSION['user_foto']   = $empresa['foto_perfil'];
                header('Location: /dashboard');
                return;
            }
        }

        // 3. SI NADA COINCIDE
        header('Location: /login?error=invalid_credentials');
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
    }
}
