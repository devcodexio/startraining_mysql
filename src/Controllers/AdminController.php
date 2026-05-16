<?php

namespace App\Controllers;

use App\Config\Database;

class AdminController {
    
    public function indexCarreras() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM carreras ORDER BY nombre ASC");
        $carreras = $stmt->fetchAll();
        require __DIR__ . '/../Views/admin/carreras.php';
    }

    public function storeCarrera() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $nombre = trim($_POST['nombre'] ?? '');
        if (empty($nombre)) {
            header('Location: /admin/carreras?err=empty');
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO carreras (nombre) VALUES (?)");
        
        try {
            $stmt->execute([$nombre]);
            header('Location: /admin/carreras?reg=success');
        } catch (\Exception $e) {
            header('Location: /admin/carreras?err=exists');
        }
    }

    public function deleteCarrera() {
        $id = intval($_GET['id'] ?? 0);
        if (!$id) return;

        $db = Database::getConnection();
        
        // Verificar si hay vacantes usando esta carrera
        $check = $db->prepare("SELECT COUNT(*) FROM vacantes WHERE carrera_id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            header('Location: /admin/carreras?err=in_use');
            return;
        }

        $db->prepare("DELETE FROM carreras WHERE id = ?")->execute([$id]);
        header('Location: /admin/carreras?del=success');
    }

    public function updateCarrera() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        
        if (!$id || empty($nombre)) {
            header('Location: /admin/carreras?err=empty');
            return;
        }

        $db = Database::getConnection();
        
        try {
            $stmt = $db->prepare("UPDATE carreras SET nombre = ? WHERE id = ?");
            $stmt->execute([$nombre, $id]);
            header('Location: /admin/carreras?upd=success');
        } catch (\Exception $e) {
            header('Location: /admin/carreras?err=exists');
        }
    }

    public function getApplicantsByVacancy() {
        $vacante_id = intval($_GET['vacante_id'] ?? 0);
        if (!$vacante_id) {
            echo json_encode(['success' => false, 'message' => 'ID de vacante no válido']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM postulaciones WHERE vacante_id = ? ORDER BY match_porcentaje DESC");
        $stmt->execute([$vacante_id]);
        $postulantes = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $postulantes]);
    }

    public function exportCompanies() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM empresas ORDER BY nombre_comercial ASC");
        $data = $stmt->fetchAll();

        $fileName = "reporte_empresas_" . date('Y-m-d') . ".xls";
        if (ob_get_level()) ob_end_clean();
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo "<table border='1'>";
        echo "<tr style='background-color: #0f172a; color: white; font-weight: bold;'>";
        echo "<th>RUC</th><th>EMPRESA</th><th>CORREO CONTACTO</th><th>TELEFONO</th><th>ESTADO</th><th>VACANTES</th>";
        echo "</tr>";
        foreach ($data as $e) {
            $st = $db->prepare("SELECT COUNT(*) FROM vacantes WHERE empresa_id = ?");
            $st->execute([$e['id']]);
            $count = $st->fetchColumn();
            echo "<tr>";
            echo "<td>".$e['ruc']."</td>";
            echo "<td>".htmlspecialchars($e['nombre_comercial'])."</td>";
            echo "<td>".$e['correo_contacto']."</td>";
            echo "<td>".$e['telefono']."</td>";
            echo "<td>".$e['estado']."</td>";
            echo "<td style='text-align:center;'>".$count."</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }

    public function exportCarreras() {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT c.*, (SELECT COUNT(*) FROM vacantes WHERE carrera_id = c.id) as total_vacantes FROM carreras c ORDER BY nombre ASC");
        $data = $stmt->fetchAll();

        $fileName = "reporte_carreras_" . date('Y-m-d') . ".xls";
        if (ob_get_level()) ob_end_clean();
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$fileName");

        echo "<table border='1'>";
        echo "<tr style='background-color: #065f46; color: white; font-weight: bold;'>";
        echo "<th>ID</th><th>NOMBRE CARRERA</th><th>DEMANDA (VACANTES)</th>";
        echo "</tr>";
        foreach ($data as $c) {
            echo "<tr>";
            echo "<td>".$c['id']."</td>";
            echo "<td>".htmlspecialchars($c['nombre'])."</td>";
            echo "<td style='text-align:center;'>".$c['total_vacantes']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}
