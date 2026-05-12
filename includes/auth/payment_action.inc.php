<?php
if (!isLoggedIn()) redirect('login.php');
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) { setFlash('error', 'Invalid request.'); redirect('dashboard.php'); }
if (($_POST['action'] ?? '') !== 'submit_payment') redirect('dashboard.php');

// FIX: Lowercase 'payment_model'
require_once __DIR__ . '/../model/payment_model.inc.php';
 $paymentModel = new Payment();
 $result = $paymentModel->createPayment($_SESSION['user_id'], 0.00);
setFlash($result['success'] ? 'success' : 'error', $result['message']);
redirect('dashboard.php');