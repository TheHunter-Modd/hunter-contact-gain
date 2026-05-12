<?php
/**
 * LOGOUT - Logout Handler
 * Access: Logged-in users
 * 
 * Flow:
 * 1. Load helpers
 * 2. Include auth/logout.php which:
 *    - Clears remember_token from database
 *    - Deletes remember_token cookie
 *    - Destroys session
 *    - Redirects to login.php with flash message
 * 
 * This file has NO view/output — it only processes and redirects
 */
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

// Process logout (clears session, cookie, DB token)
require_once __DIR__ . '/includes/auth/logout_auth.inc.php';