<?php
/**
 * Admin Controller
 */
require_once __DIR__ . '/../model/payment_model.inc.php';
require_once __DIR__ . '/../model/batch_model.inc.php';

class AdminContr {
    private $paymentModel;
    private $batchModel;

    public function __construct() {
        $this->paymentModel = new Payment();
        $this->batchModel   = new Batch();
    }

    public function getAdminData() {
        $this->paymentModel->expireOldVerifications();

        return [
            'pending_payments'  => $this->paymentModel->getAllPending(),
            'verified_payments' => $this->paymentModel->getAllVerified(),
            'all_batches'       => $this->batchModel->getAllBatches()
        ];
    }
}