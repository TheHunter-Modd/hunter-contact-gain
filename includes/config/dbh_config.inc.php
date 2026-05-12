<?php
/**
 * Database Connection Class
 * Uses PDO with prepared statements for all queries
 * Reads credentials from environment variables loaded by helpers.php
 */

/**
 * Database Connection
 * Adapted to match the working project configuration
 */
require_once __DIR__ . '/helpers_config.inc.php';

// Load .env file from project root (goes up 2 directories from includes/config/)
loadEnv(__DIR__ . '/../../.env');

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        // Read from $_ENV just like your working project
        $host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port    = $_ENV['DB_PORT'] ?? '3307';
        $dbname  = $_ENV['DB_NAME'] ?? 'hunter_contact_gain';
        $dbuser  = $_ENV['DB_USER'] ?? 'root';
        $dbpass  = $_ENV['DB_PASS'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $dbuser, $dbpass, $options);
        } catch (PDOException $e) {
    // HIDE the real error from users!
    error_log("Database connection failed: " . $e->getMessage()); // Logs to Render's server logs
    die("A database error occurred. Please try again later."); // Shows user a safe message
    }

        return $this->conn;
    }
}