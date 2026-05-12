<?php
/**
 * Batch Model
 * Handles batch creation, contact assignment, and VCF/CSV generation
 */
require_once __DIR__ . '/../config/dbh_config.inc.php';

class Batch {
    private $conn;
    private $table = 'batches';
    private $contacts_table = 'batch_contacts';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createBatch($name) {
        $lastBatch = $this->getLatestBatch();
        $nextNumber = $lastBatch ? (int)$lastBatch['batch_number'] + 1 : 1;

        $query = "INSERT INTO " . $this->table . " (batch_number, name) VALUES (:batch_number, :name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_number', $nextNumber, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Batch ' . $nextNumber . ' created!'];
        }
        return ['success' => false, 'message' => 'Failed to create batch.'];
    }

    public function getActiveBatch() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY batch_number DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getLatestBatch() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY batch_number DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function getAllBatches() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY batch_number DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    /**
     * Process/Drop a Batch
     * Assigns all currently verified contacts to this batch
     */
    public function dropBatch($batch_id) {
        $batch = $this->findById($batch_id);
        if (!$batch) return ['success' => false, 'message' => 'Batch not found.'];

        // Get all currently verified users with submitted contacts
        $query = "SELECT c.user_id, c.contact_name, c.whatsapp_number, p.verified_at
                  FROM contacts c
                  JOIN payments p ON c.user_id = p.user_id
                  WHERE p.is_verified = 1 AND p.expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $verifiedContacts = $stmt->fetchAll();

        // Clear existing records for this batch (in case admin re-drops)
        $delQuery = "DELETE FROM " . $this->contacts_table . " WHERE batch_id = :batch_id";
        $delStmt = $this->conn->prepare($delQuery);
        $delStmt->bindParam(':batch_id', $batch_id, PDO::PARAM_INT);
        $delStmt->execute();

        $batchCreatedAt = strtotime($batch['created_at']);
        $insertCount = 0;

        foreach ($verifiedContacts as $vc) {
            // Determine member type: 'old' if verified BEFORE batch was created, 'new' if after
            $member_type = (strtotime($vc['verified_at']) < $batchCreatedAt) ? 'old' : 'new';

            $insQuery = "INSERT INTO " . $this->contacts_table . " 
                         (batch_id, user_id, contact_name, whatsapp_number, member_type) 
                         VALUES (:batch_id, :user_id, :contact_name, :whatsapp_number, :member_type)";
            $insStmt = $this->conn->prepare($insQuery);
            $insStmt->bindParam(':batch_id', $batch_id, PDO::PARAM_INT);
            $insStmt->bindParam(':user_id', $vc['user_id'], PDO::PARAM_INT);
            $insStmt->bindParam(':contact_name', $vc['contact_name'], PDO::PARAM_STR);
            $insStmt->bindParam(':whatsapp_number', $vc['whatsapp_number'], PDO::PARAM_STR);
            $insStmt->bindParam(':member_type', $member_type, PDO::PARAM_STR);
            
            if ($insStmt->execute()) $insertCount++;
        }

        return ['success' => true, 'message' => "Batch dropped! $insertCount contacts processed."];
    }

    /**
     * Determine member type for a user in a given batch
     */
    public function determineMemberType($user_id, $batch_id) {
        $batch = $this->findById($batch_id);
        if (!$batch) return 'new';

        require_once __DIR__ . '/payment_model.inc.php';
        $paymentModel = new Payment();
        $verification = $paymentModel->getVerificationStatus($user_id);

        if ($verification && $verification['verified_at'] && strtotime($verification['verified_at']) < strtotime($batch['created_at'])) {
            return 'old';
        }
        return 'new';
    }

    /**
     * Get contacts for VCF generation based on member type
     * FIXED: Batch 1 gives ALL contacts to everyone. Batch 2+ applies Old/New logic.
     */
    public function getContactsForVCF($batch_id, $member_type) {
        $batch = $this->findById($batch_id);
        
        // If it's Batch 1, everyone downloads everything (it's the baseline)
        if ($batch && $batch['batch_number'] == 1) {
            $query = "SELECT * FROM " . $this->contacts_table . " WHERE batch_id = :batch_id ORDER BY created_at ASC";
        } else {
            // For Batch 2 and beyond
            if ($member_type === 'new') {
                // New members download ALL contacts in the batch
                $query = "SELECT * FROM " . $this->contacts_table . " WHERE batch_id = :batch_id ORDER BY created_at ASC";
            } else {
                // Old members download ONLY new contacts (to avoid duplicates from previous batches)
                $query = "SELECT * FROM " . $this->contacts_table . " WHERE batch_id = :batch_id AND member_type = 'new' ORDER BY created_at ASC";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':batch_id', $batch_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}