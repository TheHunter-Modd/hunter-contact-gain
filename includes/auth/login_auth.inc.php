<?php
/**
 * Login Processing
 */

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) { 
    setFlash('error', 'Invalid request.'); 
    redirect('login.php'); 
}

 $phone      = sanitize($_POST['phone'] ?? '');
 $password   = $_POST['password'] ?? '';
 $remember   = isset($_POST['remember']);

// Validation
 $errors = [];
if (empty($phone)) $errors['phone'] = 'Phone number is required.';
if (empty($password)) $errors['password'] = 'Password is required.';

// Attempt Login
if (empty($errors)) {
    require_once __DIR__ . '/../model/user_model.inc.php';
    $userModel = new User();
    $result = $userModel->login($phone, $password);

    if ($result['success']) {
        $user = $result['user'];
        
        // Set session variables
        setUserSession($user);

        // Handle "Remember Me"
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $userModel->setRememberToken($user['id'], $token);
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
        }

        // NOTE: Removed session_regenerate_id(true) to prevent cloud session race conditions
        
        setFlash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
        redirect('dashboard.php');
    } else {
        $errors['general'] = $result['message'];
    }
}

// Store old input for form repopulation
 $old = ['phone' => $phone];