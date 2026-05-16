<?php
require_once __DIR__ . '/../src/Config/EnvLoader.php';
require_once __DIR__ . '/../src/Config/Database.php';

\App\Config\EnvLoader::load(__DIR__ . '/../.env');

try {
    $db = \App\Config\Database::getConnection();
    $stmt = $db->query("DESCRIBE empresas");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in 'empresas': " . implode(", ", $columns) . "\n";
    
    if (!in_array('foto_selfie', $columns)) {
        echo "MISSING 'foto_selfie' column!\n";
    } else {
        echo "'foto_selfie' column exists.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
