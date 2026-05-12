<?php
/**
 * Payment Model
 * Handles verification status, access codes, expiration
 */
require_once __DIR__ . '/../config/dbh_config.inc.php';

class Payment {
    private $conn;
    private $table = 'payments';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createPayment($user_id, $amount = 0.00) {
        $existing = $this->getVerificationStatus($user_id);
        if ($existing && !$existing['is_verified'] && $existing['expires_at'] === null) {
            return ['success' => false, 'message' => 'You already have a pending payment awaiting approval.'];
        }

        $query = "INSERT INTO " . $this->table . " (user_id, amount) VALUES (:user_id, :amount)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Payment submitted! Awaiting admin verification.'];
        }
        return ['success' => false, 'message' => 'Failed to submit payment.'];
    }

    public function getVerificationStatus($user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function isVerified($user_id) {
        $record = $this->getVerificationStatus($user_id);
        if (!$record) return false;
        if (!$record['is_verified']) return false;
        if ($record['expires_at'] && strtotime($record['expires_at']) < time()) return false;
        return true;
    }

    public function verifyPayment($payment_id) {
        $access_code = strtoupper(bin2hex(random_bytes(6)));

        $query = "UPDATE " . $this->table . " 
                  SET is_verified = 1, 
                      access_code = :access_code,
                      verified_at = NOW(), 
                      expires_at = DATE_ADD(NOW(), INTERVAL 3 WEEK) 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':access_code', $access_code, PDO::PARAM_STR);
        $stmt->bindParam(':id', $payment_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Payment verified! Access code generated.'];
        }
        return ['success' => false, 'message' => 'Failed to verify payment.'];
    }

    public function expireOldVerifications() {
        $query = "UPDATE " . $this->table . " 
                  SET is_verified = 0 
                  WHERE is_verified = 1 
                  AND expires_at IS NOT NULL 
                  AND expires_at < NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getAllPending() {
        $query = "SELECT p.*, u.full_name, u.phone 
                  FROM " . $this->table . " p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.is_verified = 0 AND p.expires_at IS NULL
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllVerified() {
        $query = "SELECT p.*, u.full_name, u.phone 
                  FROM " . $this->table . " p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.is_verified = 1 AND p.expires_at > NOW()
                  ORDER BY p.expires_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}