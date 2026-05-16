<?php

namespace App\Models;

class VacancyModel extends Model {
    
    public function getAll($filters = []) {
        $sql = "SELECT v.*, e.nombre_comercial, e.foto_perfil, 
                       GROUP_CONCAT(c.nombre SEPARATOR ', ') as carrera,
                       GROUP_CONCAT(c.id) as carrera_ids,
                       v.requisitos_raw as requisitos
                FROM vacantes v
                JOIN empresas e ON v.empresa_id = e.id
                LEFT JOIN vacante_carreras vc ON v.id = vc.vacante_id
                LEFT JOIN carreras c ON vc.carrera_id = c.id
                WHERE v.estado = 'abierta' 
                  AND (v.fecha_limite IS NULL OR v.fecha_limite >= CURRENT_DATE)";
        
        if (!empty($filters['search'])) {
            $sql .= " AND (v.titulo_puesto LIKE :search OR v.descripcion_puesto LIKE :search)";
        }
        $sql .= " GROUP BY v.id";
        
        $having = [];
        if (!empty($filters['carrera'])) {
            $having[] = " FIND_IN_SET(:carrera, GROUP_CONCAT(c.id)) ";
        }
        if (!empty($filters['modalidad'])) {
            $having[] = " v.modalidad = :modalidad ";
        }
        if (!empty($filters['empresa_id'])) {
            $having[] = " v.empresa_id = :empresa_id ";
        }

        if (!empty($having)) {
            $sql .= " HAVING " . implode(" AND ", $having);
        }
        
        $sql .= " ORDER BY v.creado_en DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }
        if (!empty($filters['carrera'])) {
            $stmt->bindParam(':carrera', $filters['carrera']);
        }
        if (!empty($filters['modalidad'])) {
            $stmt->bindParam(':modalidad', $filters['modalidad']);
        }
        if (!empty($filters['empresa_id'])) {
            $stmt->bindParam(':empresa_id', $filters['empresa_id']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT v.*, e.nombre_comercial, e.sector, e.direccion, e.foto_perfil, 
                                           GROUP_CONCAT(c.nombre SEPARATOR ', ') as carrera,
                                           GROUP_CONCAT(c.id) as carrera_ids,
                                           v.requisitos_raw as requisitos
                                    FROM vacantes v 
                                    JOIN empresas e ON v.empresa_id = e.id 
                                    LEFT JOIN vacante_carreras vc ON v.id = vc.vacante_id
                                    LEFT JOIN carreras c ON vc.carrera_id = c.id
                                    WHERE v.id = :id
                                    GROUP BY v.id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getExpired($companyId = null) {
        $sql = "SELECT v.*, e.nombre_comercial, e.foto_perfil, 
                       GROUP_CONCAT(c.nombre SEPARATOR ', ') as carrera
                FROM vacantes v
                JOIN empresas e ON v.empresa_id = e.id
                LEFT JOIN vacante_carreras vc ON v.id = vc.vacante_id
                LEFT JOIN carreras c ON vc.carrera_id = c.id
                WHERE (v.fecha_limite < CURRENT_DATE OR v.estado = 'cerrada')";
        
        if ($companyId) {
            $sql .= " AND v.empresa_id = :cid";
        }
        
        $sql .= " GROUP BY v.id ORDER BY v.fecha_limite DESC";
        $stmt = $this->db->prepare($sql);
        if ($companyId) {
            $stmt->execute([':cid' => $companyId]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
}
