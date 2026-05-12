<?php
/**
 * Logout Processing
 * Destroys session and clears remember me cookie
 */

if (isLoggedIn()) {
    // Clear remember token from database
    require_once __DIR__ . '/../model/User_model.inc.php';
    $userModel = new User();
    $userModel->clearRememberToken($_SESSION['user_id']);

    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        unset($_COOKIE['remember_token']);
    }

    // Clear session
    clearUserSession();
    session_regenerate_id(true);
    session_destroy();
}

// Start fresh session for flash message
session_start();
setFlash('success', 'You have been logged out successfully.');
redirect('login.php');