<?php
if (!isLoggedIn()) redirect('login.php');
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) { setFlash('error', 'Invalid request.'); redirect('dashboard.php'); }
if (($_POST['action'] ?? '') !== 'submit_contact') redirect('dashboard.php');

 $contact_name    = sanitize($_POST['contact_name'] ?? '');
 $whatsapp_number = sanitize($_POST['whatsapp_number'] ?? '');
 $errors = [];
if (empty($contact_name)) $errors[] = 'Name is required.';
if (empty($whatsapp_number)) $errors[] = 'WhatsApp number is required.';

if (!empty($errors)) { setFlash('error', implode('<br>', $errors)); redirect('dashboard.php?show_contact_modal=1'); }

// FIX: Lowercase 'contact_model'
require_once __DIR__ . '/../model/contact_model.inc.php';
 $contactModel = new Contact();
 $result = $contactModel->submitContact($_SESSION['user_id'], $contact_name, $whatsapp_number);
setFlash($result['success'] ? 'success' : 'error', $result['message']);
redirect('dashboard.php');