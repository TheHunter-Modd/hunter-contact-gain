<?php
/**
 * Login Processing
 * Included by root login.php on POST requests
 * Validates credentials and creates session
 */

require_once __DIR__ . '/../model/User_model.inc.php';

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('login.php');
}

// Collect input
 $phone      = sanitize($_POST['phone'] ?? '');
 $password   = $_POST['password'] ?? '';
 $remember   = isset($_POST['remember']);

// Validation
 $errors = [];

if (empty($phone)) {
    $errors['phone'] = 'Phone number is required.';
}

if (empty($password)) {
    $errors['password'] = 'Password is required.';
}

// Attempt login
if (empty($errors)) {
    $userModel = new User();
    $result = $userModel->login($phone, $password);

    if ($result['success']) {
        $user = $result['user'];

        // Set session
        setUserSession($user);

        // Handle "Remember Me"
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $userModel->setRememberToken($user['id'], $token);
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        setFlash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
        redirect('dashboard.php');
    } else {
        $errors['general'] = $result['message'];
    }
}

// Store old input
 $old = [
    'phone' => $phone
];