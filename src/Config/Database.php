<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static $conn = null;

    public static function getConnection()
    {
        if (self::$conn === null) {
            try {
                $url = getenv("DATABASE_URL");

                if (!$url) {
                    throw new \Exception("DATABASE_URL no está configurada");
                }

                $db = parse_url($url);

                $port = isset($db['port']) ? ";port={$db['port']}" : "";
                
                self::$conn = new PDO(
                    "mysql:host={$db['host']}{$port};dbname=" . ltrim($db['path'], '/') . ";charset=utf8mb4",
                    $db['user'] ?? '',
                    $db['pass'] ?? ''
                );

                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                throw new \Exception("Database connection error: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
