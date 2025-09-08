<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(array $config): PDO
    {
        if (self::$pdo === null) {
            $host = $config['db']['host'];
            $db = $config['db']['dbname'];
            $user = $config['db']['user'];
            $pass = $config['db']['pass'];
            $charset = $config['db']['charset'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $opts);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database connection failed']);
                exit;
            }
        }

        return self::$pdo;
    }
}