<?php
/**
 * Database Connection
 */
require_once __DIR__ . '/helpers_config.inc.php';

// Load .env file from project root (for local XAMPP only)
loadEnv(__DIR__ . '/../../.env');

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        // FIX: Use getenv() instead of $_ENV for Railway compatibility!
        $host    = getenv('DB_HOST') ?: '127.0.0.1';
        $port    = getenv('DB_PORT') ?: '3307';
        $dbname  = getenv('DB_NAME') ?: '';
        $dbuser  = getenv('DB_USER') ?: 'root';
        $dbpass  = getenv('DB_PASS') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $dbuser, $dbpass, $options);
                } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("A database error occurred. Please try again later.");
        }

        return $this->conn;
    }
}