<?php

namespace App\Models;

class ConfigModel extends Model {
    
    public function getSettings() {
        $stmt = $this->db->query("SELECT clave, valor FROM configuracion");
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['clave']] = $row['valor'];
        }
        return $settings;
    }

    public function getLogo() {
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = 'logo_sitio'");
        $stmt->execute();
        return $stmt->fetchColumn() ?: '/assets/img/logo.png';
    }

    public function getSiteName() {
        $stmt = $this->db->prepare("SELECT valor FROM configuracion WHERE clave = 'nombre_sitio'");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 'StarTraining';
    }

    public function set($clave, $valor) {
        $stmt = $this->db->prepare("INSERT INTO configuracion (clave, valor) VALUES (:clave, :valor) 
                                    ON DUPLICATE KEY UPDATE valor = VALUES(valor)");
        return $stmt->execute([':clave' => $clave, ':valor' => $valor]);
    }
}
