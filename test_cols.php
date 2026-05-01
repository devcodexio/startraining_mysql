<?php
require_once __DIR__ . '/src/Config/Database.php';

$db = \App\Config\Database::getConnection();
$stmt = $db->query("SELECT * FROM empresas LIMIT 1");
$row = $stmt->fetch();
echo "Columnas en empresas:\n";
print_r(array_keys($row));
