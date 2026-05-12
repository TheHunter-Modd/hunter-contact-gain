<?php
/**
 * Auth Controller
 * Helper class for authentication-related operations
 * Used by root entry point files
 */
class AuthController {
    
    /**
     * Handle the registration page request
     * @return array Data for the view
     */
    public static function handleRegister() {
        $data = [
            'errors' => [],
            'old'    => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process form (includes/auth/register_auth.inc.php sets $errors and $old)
            require_once __DIR__ . '/../auth/register_auth.inc.php';
            $data['errors'] = $errors ?? [];
            $data['old']    = $old ?? [];
        }

        return $data;
    }

    /**
     * Handle the login page request
     * @return array Data for the view
     */
    public static function handleLogin() {
        $data = [
            'errors' => [],
            'old'    => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../auth/login_auth.inc.php';
            $data['errors'] = $errors ?? [];
            $data['old']    = $old ?? [];
        }

        return $data;
    }
}