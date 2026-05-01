<?php

namespace App\Models;

use App\Config\Database;

class CarreraModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM carreras ORDER BY nombre ASC");
        return $stmt->fetchAll();
    }
}
