<?php
/**
 * VCF Download Handler
 * Validates access code and forces VCF file download
 */
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect('dashboard.php');
}

 $action = $_POST['action'] ?? '';
if ($action !== 'download_vcf') {
    redirect('dashboard.php');
}

 $access_code = sanitize($_POST['access_code'] ?? '');

if (empty($access_code)) {
    setFlash('error', 'Please enter your access code.');
    redirect('dashboard.php?show_vcf_modal=1');
}

// Validate Access Code
require_once __DIR__ . '/includes/model/payment_model.inc.php';
 $paymentModel = new Payment();
 $payment = $paymentModel->getVerificationStatus($_SESSION['user_id']);

if (!$payment || $payment['access_code'] !== $access_code || !$payment['is_verified'] || strtotime($payment['expires_at']) < time()) {
    setFlash('error', 'Invalid, expired, or incorrect access code.');
    redirect('dashboard.php?show_vcf_modal=1');
}

// Get Active Batch
require_once __DIR__ . '/includes/model/batch_model.inc.php';
 $batchModel = new Batch();
 $activeBatch = $batchModel->getActiveBatch();

if (!$activeBatch) {
    setFlash('error', 'No active batch available for download right now.');
    redirect('dashboard.php');
}

// Determine Member Type & Get Contacts
 $memberType = $batchModel->determineMemberType($_SESSION['user_id'], $activeBatch['id']);
 $contacts = $batchModel->getContactsForVCF($activeBatch['id'], $memberType);

if (empty($contacts)) {
    setFlash('warning', 'No contacts available for you in this batch yet.');
    redirect('dashboard.php');
}

// Generate VCF Content
 $vcfContent = "";
foreach ($contacts as $contact) {
    $vcfContent .= "BEGIN:VCARD\n";
    $vcfContent .= "VERSION:3.0\n";
    $vcfContent .= "FN:" . $contact['contact_name'] . "\n";
    $vcfContent .= "TEL;TYPE=CELL,WHATSAPP:" . $contact['whatsapp_number'] . "\n";
    $vcfContent .= "END:VCARD\n";
}

// Force Download
 $filename = "HunterCG_Batch" . $activeBatch['batch_number'] . "_" . ucfirst($memberType) . "Member.vcf";

header('Content-Type: text/x-vcard; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($vcfContent));
header('Cache-Control: no-cache, must-revalidate');

echo $vcfContent;
exit;