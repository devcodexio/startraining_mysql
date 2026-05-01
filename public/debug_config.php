<?php
require_once __DIR__ . '/../src/Config/Database.php';
\App\Config\EnvLoader::load(__DIR__ . '/../.env');
$db = \App\Config\Database::getConnection();
$stmt = $db->query("SELECT * FROM configuracion");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
