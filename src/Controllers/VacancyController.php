<?php

namespace App\Controllers;

use App\Config\Database;

class VacancyController {
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $db = Database::getConnection();
        $companyId = $_SESSION['user_id'];

        $sql = "INSERT INTO vacantes (
                    empresa_id, carrera_id, titulo_puesto, 
                    descripcion_puesto, requisitos_raw, modalidad, 
                    ubicacion, fecha_limite
                ) VALUES (
                    :eid, :cid, :titulo, :desc, :req, :modal, :u, :fecha
                )";
        
        $stmt = $db->prepare($sql);
        
        if ($stmt->execute([
            ':eid' => $companyId,
            ':cid' => $_POST['carrera_id'],
            ':titulo' => $_POST['titulo_puesto'],
            ':desc' => $_POST['descripcion_puesto'],
            ':req' => $_POST['requisitos_raw'],
            ':modal' => $_POST['modalidad'],
            ':u' => $_POST['ubicacion'] ?: null,
            ':fecha' => $_POST['fecha_limite'] ?: null
        ])) {
            header('Location: /vacancies?reg=success');
        } else {
            header('Location: /vacancies/create?error=db_fail');
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $db = Database::getConnection();
        $id = $_POST['id'];
        $companyId = $_SESSION['user_id'];

        // Verify ownership
        $st = $db->prepare("SELECT id FROM vacantes WHERE id = ? AND empresa_id = ?");
        $st->execute([$id, $companyId]);
        if (!$st->fetch()) die("No tienes permiso.");

        $sql = "UPDATE vacantes SET 
                    carrera_id = :cid, 
                    titulo_puesto = :titulo, 
                    descripcion_puesto = :desc, 
                    requisitos_raw = :req, 
                    modalidad = :modal, 
                    ubicacion = :u,
                    fecha_limite = :fecha
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':cid' => $_POST['carrera_id'],
            ':titulo' => $_POST['titulo_puesto'],
            ':desc' => $_POST['descripcion_puesto'],
            ':req' => $_POST['requisitos_raw'],
            ':modal' => $_POST['modalidad'],
            ':u' => $_POST['ubicacion'] ?: null,
            ':fecha' => $_POST['fecha_limite'] ?: null,
            ':id' => $id
        ]);

        header('Location: /vacancies?upd=success');
    }

    public function delete() {
        $id = $_GET['id'] ?? 0;
        $companyId = $_SESSION['user_id'];
        $db = Database::getConnection();

        // Verify ownership
        $st = $db->prepare("SELECT id FROM vacantes WHERE id = ? AND empresa_id = ?");
        $st->execute([$id, $companyId]);
        if (!$st->fetch()) die("No tienes permiso.");

        $db->prepare("DELETE FROM vacantes WHERE id = ?")->execute([$id]);
        header('Location: /vacancies?del=success');
    }

    public function expired() {
        $model = new \App\Models\VacancyModel();
        $vacancies = $model->getExpired($_SESSION['user_id'] ?? null);
        require __DIR__ . '/../Views/vacancies/expired.php';
    }

    public function adminExpired() {
        $model = new \App\Models\VacancyModel();
        $vacancies = $model->getExpired();
        require __DIR__ . '/../Views/vacancies/expired.php';
    }

    public function indexAdmin() {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'carrera' => $_GET['carrera'] ?? '',
            'modalidad' => $_GET['modalidad'] ?? ''
        ];
        $model = new \App\Models\VacancyModel();
        // Por ahora getAll() filtra solo abiertas, lo usaré para el listado principal de admin
        $vacancies = $model->getAll($filters);
        require __DIR__ . '/../Views/admin/vacancies.php';
    }

    public function toggleStatus() {
        $id = $_GET['id'] ?? 0;
        $userType = $_SESSION['user_type'] ?? 'empresa';
        $userId = $_SESSION['user_id'];
        $db = Database::getConnection();

        // Verify ownership (ignore if admin)
        $sqlCheck = "SELECT id, estado, fecha_limite FROM vacantes WHERE id = ?";
        $paramsCheck = [$id];
        if ($userType !== 'admin') {
            $sqlCheck .= " AND empresa_id = ?";
            $paramsCheck[] = $userId;
        }

        $st = $db->prepare($sqlCheck);
        $st->execute($paramsCheck);
        $v = $st->fetch();
        if (!$v) die("No tienes permiso o la vacante no existe.");

        $newStatus = ($v['estado'] === 'abierta') ? 'cerrada' : 'abierta';
        $sqlUpd = "UPDATE vacantes SET estado = :new";
        $paramsUpd = [':new' => $newStatus, ':id' => $id];

        if ($newStatus === 'abierta') {
            $limit = $v['fecha_limite'] ? strtotime($v['fecha_limite']) : 0;
            if ($limit < time()) {
                $sqlUpd .= ", fecha_limite = :fecha";
                $paramsUpd[':fecha'] = date('Y-m-d', strtotime('+15 days'));
            }
        }

        $sqlUpd .= " WHERE id = :id";
        $db->prepare($sqlUpd)->execute($paramsUpd);

        $referer = $_SERVER['HTTP_REFERER'] ?: '/vacancies';
        header('Location: ' . $referer);
    }
}
