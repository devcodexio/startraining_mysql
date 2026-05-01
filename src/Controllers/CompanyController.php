<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Services\ExternalApiService;
use App\Config\Database;

class CompanyController {

    public function consultRuc($ruc) {
        $data = ExternalApiService::consultRuc($ruc);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $model = new CompanyModel();

        if ($model->getByRuc($_POST['ruc'])) {
            header('Location: /register-company?error=ruc_exists');
            return;
        }

        if ($model->getByEmail($_POST['correo_contacto'])) {
            header('Location: /register-company?error=email_exists');
            return;
        }

        // Validate phone: exactly 9 digits
        $telefono = $_POST['telefono'] ?? '';
        if (strlen($telefono) !== 9 || !ctype_digit($telefono)) {
            header('Location: /register-company?error=phone_invalid');
            return;
        }

        if ($_POST['password'] !== $_POST['password_confirm']) {
            header('Location: /register-company?error=pass_mismatch');
            return;
        }

        $foto_perfil = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            try {
                $foto_perfil = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['foto_perfil']['tmp_name'],
                    'logo_' . $_POST['ruc'] . '.png',
                    'startraining/logos',
                    'logo_' . $_POST['ruc'],
                    'logos'
                );
            } catch (\Exception $e) { }
        }

        $data = $_POST;
        $data['foto_perfil'] = $foto_perfil;

        if ($model->create($data)) {
            header('Location: /login?reg=success');
        } else {
            header('Location: /register-company?error=db_fail');
        }
    }

    /**
     * Actualizar perfil de empresa (foto, sector, teléfono, dirección, correo)
     * POST /company/profile-update
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $companyId = $_SESSION['user_id'];
        $db = Database::getConnection();

        // Fetch current data to keep RUC for filename and keep other unchangeable data
        $stmt = $db->prepare("SELECT * FROM empresas WHERE id = ?");
        $stmt->execute([$companyId]);
        $current = $stmt->fetch();

        $foto_perfil = $current['foto_perfil'];

        // Handle new logo upload
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $timestamp = time();
            $fileName = 'logo_' . $current['ruc'] . '_' . $timestamp . '.' . $ext;
            
            try {
                $foto_perfil = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['foto_perfil']['tmp_name'],
                    $fileName,
                    'startraining/logos',
                    'logo_' . $current['ruc'] . '_' . $timestamp,
                    'logos'
                );
                // Update session so header/sidebar update immediately
                $_SESSION['user_foto'] = $foto_perfil;
            } catch (\Exception $e) { }
        }

        $correo   = $_POST['correo_contacto'] ?? $current['correo_contacto'];
        $telefono = $_POST['telefono'] ?? $current['telefono'];

        // Validate phone: exactly 9 digits
        if (strlen($telefono) !== 9 || !ctype_digit($telefono)) {
            header('Location: /company/profile?error=phone_invalid');
            exit;
        }

        $sql = "UPDATE empresas SET correo_contacto = ?, telefono = ?, foto_perfil = ? WHERE id = ?";
        $params = [$correo, $telefono, $foto_perfil, $companyId];

        $newPassword = trim($_POST['new_password'] ?? '');
        if (!empty($newPassword)) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE empresas SET correo_contacto = ?, telefono = ?, foto_perfil = ?, password_hash = ? WHERE id = ?";
            $params = [$correo, $telefono, $foto_perfil, $hash, $companyId];
        }

        $db->prepare($sql)->execute($params);

        header('Location: /company/profile?success=1');
        exit;
    }

    /**
     * Admin: Toggle empresa activo/bloqueado
     * POST /admin/empresas/toggle-status
     */
    public function toggleStatus() {
        if ($_SESSION['user_type'] !== 'admin') {
            http_response_code(403);
            return;
        }

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $empresaId = intval($input['empresa_id'] ?? 0);
        $newStatus = $input['status'] ?? 'activo'; // 'activo' | 'bloqueado'

        if (!$empresaId) {
            echo json_encode(['success' => false, 'error' => 'ID inválido']);
            return;
        }

        $db = Database::getConnection();
        $upd = $db->prepare("UPDATE empresas SET estado = ? WHERE id = ?");
        $upd->execute([$newStatus, $empresaId]);

        echo json_encode(['success' => true, 'nuevo_estado' => $newStatus]);
    }

    /**
     * Admin: Update admin profile (name + password)
     * POST /admin/save-profile
     */
    public function updateAdminProfile() {
        if ($_SESSION['user_type'] !== 'admin') {
            header('Location: /login');
            exit;
        }

        $adminId = $_SESSION['user_id'];
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM administradores WHERE id = ?");
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch();

        $nombre = $_POST['nombre'] ?? $admin['nombre'];
        $foto   = $admin['foto_perfil'] ?? '';

        // Handle photo upload
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            $ext      = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $timestamp = time();
            $fileName = 'admin_' . $adminId . '_' . $timestamp . '.' . $ext;
            
            try {
                $foto = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['foto_perfil']['tmp_name'],
                    $fileName,
                    'startraining/admins',
                    'admin_' . $adminId . '_' . $timestamp,
                    'admins'
                );
                $_SESSION['user_foto'] = $foto;
            } catch (\Exception $e) { }
        }

        // Handle password update
        $sql = "UPDATE administradores SET nombre = ?, foto_perfil = ? WHERE id = ?";
        $params = [$nombre, $foto, $adminId];

        $newPassword = trim($_POST['new_password'] ?? '');
        if (!empty($newPassword)) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE administradores SET nombre = ?, foto_perfil = ?, password_hash = ? WHERE id = ?";
            $params = [$nombre, $foto, $hash, $adminId];
        }

        $db->prepare($sql)->execute($params);
        $_SESSION['user_nombre'] = $nombre;

        header('Location: /admin/profile?success=1');
        exit;
    }
}
