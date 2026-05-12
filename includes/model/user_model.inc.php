<?php
/**
 * User Model
 * Handles all database operations for the users table
 */
require_once __DIR__ . '/../config/dbh_config.inc.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Register a new user
     * @param string $full_name
     * @param string $phone
     * @param string $password (plain text — will be hashed)
     * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public function register($full_name, $phone, $password) {
        // Check if phone already exists
        if ($this->findByPhone($phone)) {
            return ['success' => false, 'message' => 'This phone number is already registered.', 'user_id' => null];
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $query = "INSERT INTO " . $this->table . " (full_name, phone, password) VALUES (:full_name, :phone, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful!', 'user_id' => $this->conn->lastInsertId()];
        }

        return ['success' => false, 'message' => 'Registration failed. Please try again.', 'user_id' => null];
    }

    /**
     * Authenticate user login
     * @param string $phone
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login($phone, $password) {
        $user = $this->findByPhone($phone);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid phone number or password.', 'user' => null];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid phone number or password.', 'user' => null];
        }

        return ['success' => true, 'message' => 'Login successful!', 'user' => $user];
    }

    /**
     * Find user by phone number
     * @param string $phone
     * @return array|false
     */
    public function findByPhone($phone) {
        $query = "SELECT * FROM " . $this->table . " WHERE phone = :phone LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Find user by ID
     * @param int $id
     * @return array|false
     */
    public function findById($id) {
        $query = "SELECT id, full_name, phone, created_at FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Set remember token for "Remember Me" functionality
     * @param int $user_id
     * @param string $token
     * @return bool
     */
    public function setRememberToken($user_id, $token) {
        $query = "UPDATE " . $this->table . " SET remember_token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Find user by remember token
     * @param string $token
     * @return array|false
     */
    public function findByRememberToken($token) {
        $query = "SELECT * FROM " . $this->table . " WHERE remember_token = :token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Clear remember token (on logout)
     * @param int $user_id
     * @return bool
     */
    public function clearRememberToken($user_id) {
        $query = "UPDATE " . $this->table . " SET remember_token = NULL WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Count total registered users
     * @return int
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
}