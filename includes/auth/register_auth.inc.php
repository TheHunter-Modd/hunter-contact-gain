<?php
/**
 * Registration Processing
 * Included by root register.php on POST requests
 * Validates input and creates user account
 */

require_once __DIR__ . '/../model/User_model.inc.php';

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request. Please try again.');
    redirect('register.php');
}

// Collect and sanitize input
 $full_name        = sanitize($_POST['full_name'] ?? '');
 $phone            = sanitize($_POST['phone'] ?? '');
 $password         = $_POST['password'] ?? '';
 $confirm_password = $_POST['confirm_password'] ?? '';

// Validation
 $errors = [];

if (empty($full_name)) {
    $errors['full_name'] = 'Full name is required.';
} elseif (strlen($full_name) < 2) {
    $errors['full_name'] = 'Full name must be at least 2 characters.';
} elseif (strlen($full_name) > 100) {
    $errors['full_name'] = 'Full name must be less than 100 characters.';
}

if (empty($phone)) {
    $errors['phone'] = 'Phone number is required.';
} elseif (!preg_match('/^[\+]?[0-9]{7,15}$/', preg_replace('/\s+/', '', $phone))) {
    $errors['phone'] = 'Please enter a valid phone number.';
}

if (empty($password)) {
    $errors['password'] = 'Password is required.';
} elseif (strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters.';
}

if ($password !== $confirm_password) {
    $errors['confirm_password'] = 'Passwords do not match.';
}

// If no errors, attempt registration
if (empty($errors)) {
    $userModel = new User();
    $result = $userModel->register($full_name, $phone, $password);

    if ($result['success']) {
        setFlash('success', 'Registration successful! Please log in with your credentials.');
        redirect('login.php');
    } else {
        $errors['general'] = $result['message'];
    }
}

// Store old input for form repopulation (excluding passwords)
 $old = [
    'full_name' => $full_name,
    'phone'     => $phone
];