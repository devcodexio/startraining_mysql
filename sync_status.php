<?php
require_once __DIR__ . '/src/Config/Database.php';

// Cargar .env manualmente
$env = file_get_contents('.env');
foreach (explode("\n", $env) as $line) {
    if (trim($line) && strpos($line, '=') !== false) {
        putenv(trim($line));
    }
}

use App\Config\Database;

try {
    $pdo = Database::getConnection();
    // Actualizar candidatos que ya tenían porcentaje pero estaban en limbo
    $pdo->exec("UPDATE postulaciones SET estado_postulacion = 'Apto' WHERE match_porcentaje >= 60");
    $pdo->exec("UPDATE postulaciones SET estado_postulacion = 'No Apto' WHERE match_porcentaje < 60 AND match_porcentaje > 0");
    echo "Sincronización completada.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
