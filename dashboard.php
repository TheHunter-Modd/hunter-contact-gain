<?php
/**
 * DASHBOARD - User Dashboard
 */
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

requireLogin();

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_contact') {
    require_once __DIR__ . '/includes/auth/contact_action.inc.php';
}

// Handle Payment Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_payment') {
    require_once __DIR__ . '/includes/auth/payment_action.inc.php';
}

 $page_title = 'Dashboard';

require_once __DIR__ . '/includes/controller/dashboard_contr.inc.php';

 $dashboardContr = new DashboardContr();
 $dashboardData = $dashboardContr->getDashboardData($_SESSION['user_id']);

require_once __DIR__ . '/includes/view/header_view.inc.php';
require_once __DIR__ . '/includes/view/dashboard_view.inc.php';
require_once __DIR__ . '/includes/view/footer_view.inc.php';