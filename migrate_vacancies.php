<?php
require_once __DIR__ . '/src/Config/Database.php';

// Cargar .env manualmente
if (file_exists('.env')) {
    $env = file_get_contents('.env');
    foreach (explode("\n", $env) as $line) {
        if (trim($line) && strpos($line, '=') !== false) {
            putenv(trim($line));
        }
    }
}

use App\Config\Database;

try {
    $pdo = Database::getConnection();
    
    // 1. Crear tabla relacional
    $pdo->exec("CREATE TABLE IF NOT EXISTS vacante_carreras (
        vacante_id int(11) NOT NULL,
        carrera_id int(11) NOT NULL,
        PRIMARY KEY (vacante_id, carrera_id),
        CONSTRAINT fk_vc_vacante FOREIGN KEY (vacante_id) REFERENCES vacantes (id) ON DELETE CASCADE,
        CONSTRAINT fk_vc_carrera FOREIGN KEY (carrera_id) REFERENCES carreras (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 2. Migrar datos existentes
    $pdo->exec("INSERT IGNORE INTO vacante_carreras (vacante_id, carrera_id) 
                SELECT id, carrera_id FROM vacantes WHERE carrera_id IS NOT NULL");

    echo "Migración de vacantes completada con éxito.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
