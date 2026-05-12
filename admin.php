<?php
/**
 * ADMIN PANEL - Payment Approvals & Batch Management
 */
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

// MUST BE ADMIN
requireAdmin();

// Handle ALL Admin POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once __DIR__ . '/includes/auth/admin_action.inc.php';
}

 $page_title = 'Admin Panel';

require_once __DIR__ . '/includes/controller/admin_contr.inc.php';
 $adminContr = new AdminContr();
 $adminData = $adminContr->getAdminData();

require_once __DIR__ . '/includes/view/header_view.inc.php';
require_once __DIR__ . '/includes/view/admin_view.inc.php';
require_once __DIR__ . '/includes/view/footer_view.inc.php';