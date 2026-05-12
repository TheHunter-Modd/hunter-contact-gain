<?php
requireAdmin();
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) { setFlash('error', 'Invalid request.'); redirect('admin.php'); }
 $action = $_POST['action'] ?? '';

if ($action === 'approve_payment') {
    $payment_id = (int)($_POST['payment_id'] ?? 0);
    if ($payment_id <= 0) { setFlash('error', 'Invalid payment ID.'); redirect('admin.php'); }
    // FIX: Lowercase 'payment_model'
    require_once __DIR__ . '/../model/payment_model.inc.php';
    $paymentModel = new Payment();
    $result = $paymentModel->verifyPayment($payment_id);
    setFlash($result['success'] ? 'success' : 'error', $result['message']); redirect('admin.php');
}

if ($action === 'create_batch') {
    $batch_name = sanitize($_POST['batch_name'] ?? '');
    if (empty($batch_name)) { setFlash('error', 'Batch name is required.'); redirect('admin.php'); }
    // FIX: Lowercase 'batch_model'
    require_once __DIR__ . '/../model/batch_model.inc.php';
    $batchModel = new Batch();
    $result = $batchModel->createBatch($batch_name);
    setFlash($result['success'] ? 'success' : 'error', $result['message']); redirect('admin.php');
}

if ($action === 'drop_batch') {
    $batch_id = (int)($_POST['batch_id'] ?? 0);
    if ($batch_id <= 0) { setFlash('error', 'Invalid batch ID.'); redirect('admin.php'); }
    // FIX: Lowercase 'batch_model'
    require_once __DIR__ . '/../model/batch_model.inc.php';
    $batchModel = new Batch();
    $result = $batchModel->dropBatch($batch_id);
    setFlash($result['success'] ? 'success' : 'error', $result['message']); redirect('admin.php');
}
redirect('admin.php');