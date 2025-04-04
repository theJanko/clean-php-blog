<?php

namespace App\Models;

use App\Core\Config;

final class Database
{
    private static ?self $instance = null;
    private \PDO $conn;

    private function __construct()
    {
        $dsn = Config::get('DB_DSN');
        $user = Config::get('DB_USER');
        $pass = Config::get('DB_PASSWORD');

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->conn;
    }
}
