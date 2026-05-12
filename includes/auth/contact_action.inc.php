<?php
/**
 * Contact Submission Processor
 * Handles POST requests from the dashboard contact modal
 */

if (!isLoggedIn()) {
    redirect('login.php');
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('dashboard.php');
}

// Check correct action
if (($_POST['action'] ?? '') !== 'submit_contact') {
    redirect('dashboard.php');
}

// Collect and sanitize input
 $contact_name    = sanitize($_POST['contact_name'] ?? '');
 $whatsapp_number = sanitize($_POST['whatsapp_number'] ?? '');

// Validation
 $errors = [];

if (empty($contact_name)) {
    $errors[] = 'Preferred saved name is required.';
} elseif (strlen($contact_name) > 100) {
    $errors[] = 'Name must be less than 100 characters.';
}

if (empty($whatsapp_number)) {
    $errors[] = 'WhatsApp number is required.';
} elseif (!preg_match('/^[\+]?[0-9\s\-]{7,15}$/', $whatsapp_number)) {
    $errors[] = 'Please enter a valid WhatsApp phone number.';
}

if (!empty($errors)) {
    setFlash('error', implode('<br>', $errors));
    // Redirect back and force the modal open to show the error
    redirect('dashboard.php?show_contact_modal=1');
}

// Process submission using the Model
require_once __DIR__ . '/../model/Contact_model.inc.php';

 $contactModel = new Contact();
 $result = $contactModel->submitContact($_SESSION['user_id'], $contact_name, $whatsapp_number);

if ($result['success']) {
    setFlash('success', $result['message']);
} else {
    setFlash('error', $result['message']);
}

redirect('dashboard.php');