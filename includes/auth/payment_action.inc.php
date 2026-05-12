<?php
/**
 * Payment Submission Processor
 */

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('dashboard.php');
}

if (($_POST['action'] ?? '') !== 'submit_payment') {
    redirect('dashboard.php');
}

require_once __DIR__ . '/../model/payment_model.inc.php';

 $paymentModel = new Payment();
 $result = $paymentModel->createPayment($_SESSION['user_id'], 0.00);

if ($result['success']) {
    setFlash('success', $result['message']);
} else {
    setFlash('error', $result['message']);
}

redirect('dashboard.php');