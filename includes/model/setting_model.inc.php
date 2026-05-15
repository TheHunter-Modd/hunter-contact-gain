<?php
/**
 * Setting Model
 * Handles site-wide settings like the WhatsApp Group Link
 */
require_once __DIR__ . '/../config/dbh_config.inc.php';

class Setting {
    private $conn;
    private $table = 'settings';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getSetting($key) {
        $query = "SELECT setting_value FROM " . $this->table . " WHERE setting_key = :key LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : '';
    }

    public function updateSetting($key, $value) {
        $query = "INSERT INTO " . $this->table . " (setting_key, setting_value) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE setting_value = :value_update";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key, PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        $stmt->bindParam(':value_update', $value, PDO::PARAM_STR);
        return $stmt->execute();
    }
}