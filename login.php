<?php
/**
 * LOGIN - Login Page
 * Access: Guests only
 * 
 * Flow:
 * 1. Load helpers
 * 2. Redirect logged-in users away
 * 3. If POST → include auth/login.php (validates credentials)
 * 4. On success → creates session + remember token + redirects to dashboard
 * 5. On failure → shows errors + old input
 * 6. Render header → view → footer
 */
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

// Only guests can access this page
requireGuest();

 $page_title = 'Login';

// Initialize variables for the view
 $errors = [];
 $old    = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The auth file processes the form
    // It sets $errors and $old on failure
    // On success it redirects to dashboard.php
    require_once __DIR__ . '/includes/auth/login_auth.inc.php';
}

// Render the page
require_once __DIR__ . '/includes/view/header_view.inc.php';
require_once __DIR__ . '/includes/view/login_view.inc.php';
require_once __DIR__ . '/includes/view/footer_view.inc.php';