<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }
}
