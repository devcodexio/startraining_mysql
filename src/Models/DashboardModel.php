<?php

namespace App\Models;

class DashboardModel extends Model {
    
    public function getCompanyStats($companyId) {
        $stats = [];
        
        // Total Vacantes
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM vacantes WHERE empresa_id = :id");
        $stmt->execute([':id' => $companyId]);
        $stats['total_vacantes'] = $stmt->fetchColumn();

        // Total Postulaciones
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postulaciones p 
                                    JOIN vacantes v ON p.vacante_id = v.id 
                                    WHERE v.empresa_id = :id");
        $stmt->execute([':id' => $companyId]);
        $stats['total_postulaciones'] = $stmt->fetchColumn();

        // Postulaciones Pendientes
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM postulaciones p 
                                    JOIN vacantes v ON p.vacante_id = v.id 
                                    WHERE v.empresa_id = :id AND p.estado_postulacion = 'en_espera'");
        $stmt->execute([':id' => $companyId]);
        $stats['pendientes'] = $stmt->fetchColumn();

        return $stats;
    }

    public function getAdminStats() {
        $stats = [];
        
        $stats['total_empresas'] = $this->db->query("SELECT COUNT(*) FROM empresas")->fetchColumn();
        $stats['total_vacantes'] = $this->db->query("SELECT COUNT(*) FROM vacantes")->fetchColumn();
        $stats['total_postulaciones'] = $this->db->query("SELECT COUNT(*) FROM postulaciones")->fetchColumn();
        
        return $stats;
    }
}
