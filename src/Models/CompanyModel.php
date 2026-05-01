<?php

namespace App\Models;

class CompanyModel extends Model {
    
    public function create($data) {
        $sql = "INSERT INTO empresas (
                    nombre_comercial, ruc, sector, correo_contacto, 
                    telefono, direccion, password_hash, foto_perfil
                ) VALUES (
                    :nombre_comercial, :ruc, :sector, :correo_contacto, 
                    :telefono, :direccion, :password_hash, :foto_perfil
                )";
        
        $stmt = $this->db->prepare($sql);
        
        // Hash password
        $passHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            ':nombre_comercial' => $data['nombre_comercial'],
            ':ruc' => $data['ruc'],
            ':sector' => $data['sector'] ?? 'TECNOLOGIA',
            ':correo_contacto' => $data['correo_contacto'],
            ':telefono' => $data['telefono'] ?? '',
            ':direccion' => $data['direccion'] ?? '',
            ':password_hash' => $passHash,
            ':foto_perfil' => $data['foto_perfil'] ?? null
        ]);
    }

    public function getByRuc($ruc) {
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE ruc = :ruc");
        $stmt->execute([':ruc' => $ruc]);
        return $stmt->fetch();
    }

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE correo_contacto = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
}
