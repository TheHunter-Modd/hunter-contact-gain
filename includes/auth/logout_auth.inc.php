<?php
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) { setFlash('error', 'Invalid request.'); redirect('login.php'); }

 $phone      = sanitize($_POST['phone'] ?? '');
 $password   = $_POST['password'] ?? '';
 $remember   = isset($_POST['remember']);
 $errors = [];
if (empty($phone)) $errors['phone'] = 'Phone number is required.';
if (empty($password)) $errors['password'] = 'Password is required.';

if (empty($errors)) {
    // FIX: Lowercase 'user_model'
    require_once __DIR__ . '/../model/user_model.inc.php';
    $userModel = new User();
    $result = $userModel->login($phone, $password);
    if ($result['success']) {
        $user = $result['user'];
        setUserSession($user);
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $userModel->setRememberToken($user['id'], $token);
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
        }
        session_regenerate_id(true);
        setFlash('success', 'Welcome back!'); redirect('dashboard.php');
    } else $errors['general'] = $result['message'];
}
 $old = ['phone' => $phone];