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
}
