<?php
/**
 * Helper Functions
 * Session management, CSRF protection, flash messages,
 * input sanitization, authentication helpers, .env loader
 */

// ============================================================
// ENVIRONMENT LOADER
// ============================================================
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        
        putenv("$key=$value");
        $_ENV[$key] = $value; 
    }
}

loadEnv(__DIR__ . '/../../.env');

// ============================================================
// SESSION MANAGEMENT
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    
    // If on Railway/HTTPS, force secure cookies
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    
    session_start();
}

// ============================================================
// CSRF PROTECTION
// ============================================================
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken(), ENT_QUOTES, 'UTF-8') . '">';
}

// ============================================================
// INPUT SANITIZATION
// ============================================================
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// ============================================================
// REDIRECT
// ============================================================
function redirect($url) {
    if (ob_get_level()) ob_end_clean();
    header("Location: $url");
    exit();
}

// ============================================================
// FLASH MESSAGES
// ============================================================
function setFlash($type, $message) {
    $_SESSION['flash_type']    = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type'    => $_SESSION['flash_type'] ?? 'info',
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

function hasFlash() {
    return isset($_SESSION['flash_message']);
}

// ============================================================
// AUTHENTICATION HELPERS
// ============================================================
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'        => $_SESSION['user_id'],
        'full_name' => $_SESSION['full_name'] ?? 'User',
        'phone'     => $_SESSION['phone'] ?? '',
        'role'      => $_SESSION['role'] ?? 'user'
    ];
}

function setUserSession($user) {
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['phone']     = $user['phone'];
    $_SESSION['role']      = $user['role'] ?? 'user';
}

function clearUserSession() {
    unset($_SESSION['user_id'], $_SESSION['full_name'], $_SESSION['phone'], $_SESSION['role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in to access that page.');
        redirect('login.php');
    }
}

function requireGuest() {
    if (isLoggedIn()) {
        redirect('dashboard.php');
    }
}

// ============================================================
// ADMIN ACCESS CONTROL
// ============================================================
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        setFlash('error', 'Access Denied. Admins only.');
        redirect('dashboard.php');
    }
}

// ============================================================
// REMEMBER ME FUNCTIONALITY
// ============================================================
function checkRememberMe() {
    if (isLoggedIn()) return;

    if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
        require_once __DIR__ . '/../model/user_model.inc.php';
        $userModel = new User();
        $user = $userModel->findByRememberToken($_COOKIE['remember_token']);

        if ($user) {
            setUserSession($user);
            $newToken = bin2hex(random_bytes(32));
            $userModel->setRememberToken($user['id'], $newToken);
            setcookie('remember_token', $newToken, time() + (86400 * 30), '/', '', false, true);
        } else {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
    }
}

checkRememberMe();

// ============================================================
// APP URL HELPER
// ============================================================
function appUrl($path = '') {
    $baseUrl = getenv('APP_URL') ?: 'http://localhost/hunter-contact-gain';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}