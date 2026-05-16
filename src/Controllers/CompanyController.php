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

        // 1. Honeypot check
        if (!empty($_POST['website_url'])) {
            header('Location: /register-company?error=bot_detected');
            return;
        }

        $model = new CompanyModel();
        $ruc = $_POST['ruc'] ?? '';

        if ($model->getByRuc($ruc)) {
            header('Location: /register-company?error=ruc_exists');
            return;
        }

        if ($model->getByEmail($_POST['correo_contacto'])) {
            header('Location: /register-company?error=email_exists');
            return;
        }

        // 2. Backend Verification of RUC status
        $verify = ExternalApiService::consultRuc($ruc);
        if (!$verify['success']) {
            header('Location: /register-company?error=ruc_invalid_verification');
            return;
        }

        $rucData = $verify['data'];
        if (strtoupper($rucData['estado'] ?? '') !== 'ACTIVO' || strtoupper($rucData['condicion'] ?? '') !== 'HABIDO') {
            header('Location: /register-company?error=ruc_inactive_or_nohabido');
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

        // Check terms
        if (!isset($_POST['accept_terms'])) {
            header('Location: /register-company?error=terms_not_accepted');
            return;
        }

        $foto_perfil = null;
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === 0) {
            try {
                $foto_perfil = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['foto_perfil']['tmp_name'],
                    'logo_' . $ruc . '.png',
                    'startraining/logos',
                    'logo_' . $ruc,
                    'logos'
                );
            } catch (\Exception $e) {
                header('Location: /register-company?error=upload_logo_failed');
                return;
            }
        } else {
            header('Location: /register-company?error=logo_required');
            return;
        }

        // 3. Mandatory Ficha RUC (PDF)
        $ficha_ruc = null;
        if (isset($_FILES['ficha_ruc']) && $_FILES['ficha_ruc']['error'] === 0) {
            try {
                $ficha_ruc = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['ficha_ruc']['tmp_name'],
                    'ficha_' . $ruc . '.pdf',
                    'startraining/documentos',
                    'ficha_' . $ruc,
                    'documentos'
                );
            } catch (\Exception $e) {
                header('Location: /register-company?error=upload_ficha_failed');
                return;
            }
        } else {
            header('Location: /register-company?error=ficha_required');
            return;
        }

        // 4. Mandatory DNI (Frente y Reverso)
        $dni_frente = null;
        $dni_reverso = null;
        if (isset($_FILES['dni_frente']) && $_FILES['dni_frente']['error'] === 0 && 
            isset($_FILES['dni_reverso']) && $_FILES['dni_reverso']['error'] === 0) {
            try {
                $dni_frente = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['dni_frente']['tmp_name'],
                    'dni_f_' . $ruc . '.png',
                    'startraining/documentos',
                    'dni_f_' . $ruc,
                    'documentos'
                );
                $dni_reverso = \App\Services\CloudinaryService::uploadFile(
                    $_FILES['dni_reverso']['tmp_name'],
                    'dni_r_' . $ruc . '.png',
                    'startraining/documentos',
                    'dni_r_' . $ruc,
                    'documentos'
                );
            } catch (\Exception $e) {
                header('Location: /register-company?error=upload_dni_failed');
                return;
            }
        } else {
            header('Location: /register-company?error=dni_required');
            return;
        }

        // 5. Mandatory Selfie (Base64 from Camera)
        $foto_selfie = null;
        if (!empty($_POST['foto_selfie_base64'])) {
            try {
                $base64Data = $_POST['foto_selfie_base64'];
                $base64Data = str_replace('data:image/png;base64,', '', $base64Data);
                $base64Data = str_replace(' ', '+', $base64Data);
                $imageBinary = base64_decode($base64Data);
                
                $tempName = tempnam(sys_get_temp_dir(), 'selfie_') . '.png';
                file_put_contents($tempName, $imageBinary);
                
                $foto_selfie = \App\Services\CloudinaryService::uploadFile(
                    $tempName,
                    'selfie_' . $ruc . '.png',
                    'startraining/biometria',
                    'selfie_' . $ruc,
                    'biometria'
                );
                
                unlink($tempName);
            } catch (\Exception $e) {
                header('Location: /register-company?error=upload_selfie_failed');
                return;
            }
        } else {
            header('Location: /register-company?error=selfie_required');
            return;
        }

        $data = $_POST;
        $data['foto_perfil'] = $foto_perfil;
        $data['ficha_ruc']   = $ficha_ruc;
        $data['dni_frente']  = $dni_frente;
        $data['dni_reverso'] = $dni_reverso;
        $data['foto_selfie'] = $foto_selfie;

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
        
        // Fetch company info before update to get email and name
        $stmtC = $db->prepare("SELECT nombre_comercial, correo_contacto FROM empresas WHERE id = ?");
        $stmtC->execute([$empresaId]);
        $empresa = $stmtC->fetch();

        $upd = $db->prepare("UPDATE empresas SET estado = ? WHERE id = ?");
        $upd->execute([$newStatus, $empresaId]);

        // Send email if approved
        $mailSent = false;
        if ($newStatus === 'activo' && $empresa) {
            $subject = "¡Cuenta Activada! - StarTraining";
            $message = "Hola " . $empresa['nombre_comercial'] . ",\n\n" .
                       "Nos complace informarte que tu cuenta en StarTraining ha sido revisada y APROBADA por nuestro equipo administrativo.\n\n" .
                       "Ya puedes iniciar sesión en nuestro portal para empresas y comenzar a publicar tus convocatorias de prácticas.\n\n" .
                       "Accede aquí: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/login\n\n" .
                       "¡Bienvenido a bordo!\n" .
                       "Equipo StarTraining.";
            
            $res = \App\Services\MailService::sendGmail($empresa['correo_contacto'], $subject, $message, $empresa['nombre_comercial']);
            $mailSent = $res['success'];
        }

        echo json_encode(['success' => true, 'nuevo_estado' => $newStatus, 'mail_sent' => $mailSent]);
    }

    /**
     * Admin: Send manual email to company
     * POST /admin/empresas/send-email
     */
    public function sendCustomEmail() {
        if ($_SESSION['user_type'] !== 'admin') {
            http_response_code(403);
            return;
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        $email   = $input['email'] ?? '';
        $subject = $input['subject'] ?? '';
        $message = $input['message'] ?? '';
        $name    = $input['name'] ?? '';

        if (empty($email) || empty($subject) || empty($message)) {
            echo json_encode(['success' => false, 'error' => 'Campos incompletos']);
            return;
        }

        $res = \App\Services\MailService::sendGmail($email, $subject, $message, $name);
        echo json_encode($res);
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
