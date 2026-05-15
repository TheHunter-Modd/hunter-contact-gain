<?php
require_once __DIR__ . '/../model/payment_model.inc.php';
require_once __DIR__ . '/../model/batch_model.inc.php';
require_once __DIR__ . '/../model/setting_model.inc.php'; // NEW

class AdminContr {
    private $paymentModel; private $batchModel; private $settingModel;
    public function __construct() { 
        $this->paymentModel = new Payment(); $this->batchModel = new Batch(); 
        $this->settingModel = new Setting(); // NEW
    }
    public function getAdminData() {
        $this->paymentModel->expireOldVerifications();
        return [
            'pending_payments' => $this->paymentModel->getAllPending(), 
            'verified_payments' => $this->paymentModel->getAllVerified(), 
            'all_batches' => $this->batchModel->getAllBatches(),
            'whatsapp_link' => $this->settingModel->getSetting('whatsapp_group_link') // NEW
        ];
    }
}