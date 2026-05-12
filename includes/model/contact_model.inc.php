<?php
/**
 * Contact Model
 * Handles user-submitted WhatsApp contacts
 */
require_once __DIR__ . '/../config/dbh_config.inc.php';

class Contact {
    private $conn;
    private $table = 'contacts';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Create or update a user's contact submission
     */
    public function submitContact($user_id, $contact_name, $whatsapp_number) {
        $existing = $this->getByUserId($user_id);

        if ($existing) {
            $query = "UPDATE " . $this->table . " 
                      SET contact_name = :contact_name, whatsapp_number = :whatsapp_number 
                      WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':contact_name', $contact_name, PDO::PARAM_STR);
            $stmt->bindParam(':whatsapp_number', $whatsapp_number, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Contact updated successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to update contact.'];
        }

        $query = "INSERT INTO " . $this->table . " (user_id, contact_name, whatsapp_number) 
                  VALUES (:user_id, :contact_name, :whatsapp_number)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':contact_name', $contact_name, PDO::PARAM_STR);
        $stmt->bindParam(':whatsapp_number', $whatsapp_number, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Contact submitted successfully.'];
        }
        return ['success' => false, 'message' => 'Failed to submit contact.'];
    }

    /**
     * Get contact by user ID
     */
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Get all verified contacts (for CSV/VCF generation later)
     */
    public function getAllVerifiedContacts() {
        $query = "SELECT c.*, u.full_name 
                  FROM " . $this->table . " c
                  JOIN users u ON c.user_id = u.id
                  JOIN payments p ON c.user_id = p.user_id
                  WHERE p.is_verified = 1 AND p.expires_at > NOW()
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count total contacts
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)($result['total'] ?? 0);
    }
}