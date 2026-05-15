<?php
require_once __DIR__ . '/../model/user_model.inc.php';
require_once __DIR__ . '/../model/payment_model.inc.php';
require_once __DIR__ . '/../model/contact_model.inc.php';
require_once __DIR__ . '/../model/batch_model.inc.php';
require_once __DIR__ . '/../model/setting_model.inc.php'; // NEW

class DashboardContr {
    private $userModel; private $paymentModel; private $contactModel; private $batchModel; private $settingModel;
    public function __construct() {
        $this->userModel = new User(); $this->paymentModel = new Payment();
        $this->contactModel = new Contact(); $this->batchModel = new Batch();
        $this->settingModel = new Setting(); // NEW
    }
    public function getDashboardData($user_id) {
        $this->paymentModel->expireOldVerifications();
        $user = $this->userModel->findById($user_id); $payment = $this->paymentModel->getVerificationStatus($user_id);
        $isVerified = $this->paymentModel->isVerified($user_id); $contact = $this->contactModel->getByUserId($user_id);
        $currentBatch = $this->batchModel->getActiveBatch(); $allBatches = $this->batchModel->getAllBatches();
        $memberType = 'new'; if ($currentBatch && $isVerified) $memberType = $this->batchModel->determineMemberType($user_id, $currentBatch['id']);
        $daysLeft = null; if ($payment && $payment['expires_at'] && strtotime($payment['expires_at']) > time()) $daysLeft = ceil((strtotime($payment['expires_at']) - time()) / 86400);
        $isPending = ($payment && !$payment['is_verified'] && $payment['expires_at'] === null);
        
        // NEW: Get WhatsApp link (only if verified)
        $whatsappLink = '';
        if ($isVerified) {
            $whatsappLink = $this->settingModel->getSetting('whatsapp_group_link');
        }

        return [
            'user' => $user, 'payment' => $payment, 'is_verified' => $isVerified, 'is_pending' => $isPending, 
            'contact' => $contact, 'current_batch' => $currentBatch, 'all_batches' => $allBatches, 
            'member_type' => $memberType, 'days_left' => $daysLeft, 'whatsapp_link' => $whatsappLink // NEW
        ];
    }
}