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
        
        $success = $stmt->execute([
            ':eid' => $companyId,
            ':cid' => $_POST['carrera_ids'][0] ?? null, // Retrocompatibilidad con la columna vieja por ahora
            ':titulo' => $_POST['titulo_puesto'],
            ':desc' => $_POST['descripcion_puesto'],
            ':req' => $_POST['requisitos_raw'],
            ':modal' => $_POST['modalidad'],
            ':u' => $_POST['ubicacion'] ?: null,
            ':fecha' => $_POST['fecha_limite'] ?: null
        ]);

        if ($success) {
            $vacanteId = $db->lastInsertId();
            // Guardar carreras en tabla relacional
            if (!empty($_POST['carrera_ids'])) {
                $stmtRel = $db->prepare("INSERT INTO vacante_carreras (vacante_id, carrera_id) VALUES (?, ?)");
                foreach ($_POST['carrera_ids'] as $cid) {
                    $stmtRel->execute([$vacanteId, $cid]);
                }
            }
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
        $success = $stmt->execute([
            ':cid' => $_POST['carrera_ids'][0] ?? null, 
            ':titulo' => $_POST['titulo_puesto'],
            ':desc' => $_POST['descripcion_puesto'],
            ':req' => $_POST['requisitos_raw'],
            ':modal' => $_POST['modalidad'],
            ':u' => $_POST['ubicacion'] ?: null,
            ':fecha' => $_POST['fecha_limite'] ?: null,
            ':id' => $id
        ]);

        if ($success) {
            // Sincronizar carreras
            $db->prepare("DELETE FROM vacante_carreras WHERE vacante_id = ?")->execute([$id]);
            if (!empty($_POST['carrera_ids'])) {
                $stmtRel = $db->prepare("INSERT INTO vacante_carreras (vacante_id, carrera_id) VALUES (?, ?)");
                foreach ($_POST['carrera_ids'] as $cid) {
                    $stmtRel->execute([$id, $cid]);
                }
            }
        }

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

    public function export() {
        $companyId = $_SESSION['user_id'] ?? 0;
        if (!$companyId) return;

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT v.*, c.nombre as carrera_nombre 
                               FROM vacantes v 
                               JOIN carreras c ON v.carrera_id = c.id
                               WHERE v.empresa_id = ? 
                               ORDER BY v.fecha_publicacion DESC");
        $stmt->execute([$companyId]);
        $data = $stmt->fetchAll();

        $fileName = "mis_vacantes_" . date('Y-m-d') . ".xls";
        if (ob_get_level()) ob_end_clean();
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<tr style='background-color: #1e293b; color: white; font-weight: bold;'>";
        echo "<th>TITULO PUESTO</th><th>CARRERA</th><th>MODALIDAD</th><th>UBICACION</th><th>ESTADO</th><th>FECHA LIMITE</th><th>POSTULANTES</th>";
        echo "</tr>";
        foreach ($data as $v) {
            $st = $db->prepare("SELECT COUNT(*) FROM postulaciones WHERE vacante_id = ?");
            $st->execute([$v['id']]);
            $count = $st->fetchColumn();

            echo "<tr>";
            echo "<td>".htmlspecialchars($v['titulo_puesto'])."</td>";
            echo "<td>".htmlspecialchars($v['carrera_nombre'])."</td>";
            echo "<td>".htmlspecialchars($v['modalidad'])."</td>";
            echo "<td>".htmlspecialchars($v['ubicacion'])."</td>";
            echo "<td>".htmlspecialchars($v['estado'])."</td>";
            echo "<td>".$v['fecha_limite']."</td>";
            echo "<td style='text-align:center;'>".$count."</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}
