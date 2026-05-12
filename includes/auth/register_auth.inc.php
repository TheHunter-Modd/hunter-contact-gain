<?php
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) { setFlash('error', 'Invalid request.'); redirect('register.php'); }

 $full_name        = sanitize($_POST['full_name'] ?? '');
 $phone            = sanitize($_POST['phone'] ?? '');
 $password         = $_POST['password'] ?? '';
 $confirm_password = $_POST['confirm_password'] ?? '';

 $errors = [];
if (empty($full_name)) $errors['full_name'] = 'Full name is required.';
if (empty($phone)) $errors['phone'] = 'Phone number is required.';
if (empty($password)) $errors['password'] = 'Password is required.';
elseif (strlen($password) < 6) $errors['password'] = 'Password must be at least 6 characters.';
if ($password !== $confirm_password) $errors['confirm_password'] = 'Passwords do not match.';

if (empty($errors)) {
    // FIX: Lowercase 'user_model'
    require_once __DIR__ . '/../model/user_model.inc.php';
    $userModel = new User();
    $result = $userModel->register($full_name, $phone, $password);
    if ($result['success']) { setFlash('success', 'Registration successful! Please log in.'); redirect('login.php'); }
    else $errors['general'] = $result['message'];
}
 $old = ['full_name' => $full_name, 'phone' => $phone];