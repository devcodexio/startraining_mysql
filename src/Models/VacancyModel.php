<?php

namespace App\Models;

class VacancyModel extends Model {
    
    public function getAll($filters = []) {
        $sql = "SELECT v.*, e.nombre_comercial, e.foto_perfil, c.nombre as carrera, v.requisitos_raw as requisitos
                FROM vacantes v
                JOIN empresas e ON v.empresa_id = e.id
                LEFT JOIN carreras c ON v.carrera_id = c.id
                WHERE v.estado = 'abierta' 
                  AND (v.fecha_limite IS NULL OR v.fecha_limite >= CURRENT_DATE)";
        
        if (!empty($filters['search'])) {
            $sql .= " AND (v.titulo_puesto LIKE :search OR v.descripcion_puesto LIKE :search)";
        }
        if (!empty($filters['carrera'])) {
            $sql .= " AND v.carrera_id = :carrera";
        }
        if (!empty($filters['modalidad'])) {
            $sql .= " AND v.modalidad = :modalidad";
        }
        if (!empty($filters['empresa_id'])) {
            $sql .= " AND v.empresa_id = :empresa_id";
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
                                           c.nombre as carrera, v.requisitos_raw as requisitos
                                    FROM vacantes v 
                                    JOIN empresas e ON v.empresa_id = e.id 
                                    LEFT JOIN carreras c ON v.carrera_id = c.id
                                    WHERE v.id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getExpired($companyId = null) {
        $sql = "SELECT v.*, e.nombre_comercial, e.foto_perfil, c.nombre as carrera
                FROM vacantes v
                JOIN empresas e ON v.empresa_id = e.id
                LEFT JOIN carreras c ON v.carrera_id = c.id
                WHERE (v.fecha_limite < CURRENT_DATE OR v.estado = 'cerrada')";
        
        if ($companyId) {
            $sql .= " AND v.empresa_id = :cid";
        }
        
        $sql .= " ORDER BY v.fecha_limite DESC";
        $stmt = $this->db->prepare($sql);
        if ($companyId) {
            $stmt->execute([':cid' => $companyId]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
}
