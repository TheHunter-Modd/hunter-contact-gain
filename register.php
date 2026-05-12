<?php
/**
 * REGISTER - Registration Page
 * Access: Guests only
 * 
 * Flow:
 * 1. Load helpers (session, CSRF, env, auth check)
 * 2. Redirect logged-in users away
 * 3. If POST → include auth/register.php (validates + creates user)
 * 4. If GET  → show empty form
 * 5. Pass errors + old input to the view
 * 6. Render header → view → footer
 */
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

// Only guests can access this page
requireGuest();

 $page_title = 'Register';

// Initialize variables for the view
 $errors = [];
 $old    = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The auth file processes the form
    // It sets $errors and $old on failure
    // On success it redirects to login.php
    require_once __DIR__ . '/includes/auth/register_auth.inc.php';
}

// Render the page
require_once __DIR__ . '/includes/view/header_view.inc.php';
require_once __DIR__ . '/includes/view/register_view.inc.php';
require_once __DIR__ . '/includes/view/footer_view.inc.php';