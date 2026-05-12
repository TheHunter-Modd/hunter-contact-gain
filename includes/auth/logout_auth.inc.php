
 <?php
if (isLoggedIn()) {
    // FIX: Lowercase 'user_model'
    require_once __DIR__ . '/../model/user_model.inc.php';
    $userModel = new User();
    $userModel->clearRememberToken($_SESSION['user_id']);
    if (isset($_COOKIE['remember_token'])) { setcookie('remember_token', '', time() - 3600, '/', '', false, true); unset($_COOKIE['remember_token']); }
    clearUserSession(); session_regenerate_id(true); session_destroy();
}
session_start(); setFlash('success', 'You have been logged out.'); redirect('login.php');