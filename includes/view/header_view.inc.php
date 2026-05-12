<?php
/**
 * Header View
 * Contains the DOCTYPE, head, navbar, and flash messages.
 */
 $current_page = basename($_SERVER['PHP_SELF'], '.php');
 $user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="The Hunter Contact Gain - Grow your WhatsApp network">
    <meta name="theme-color" content="#0d1117">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | ' : ''; ?>Hunter Contact Gain</title>
    <!-- ALWAYS use forward slash for web paths -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- ============ HEADER / NAVBAR ============ -->
    <header class="header">
        <div class="container header__inner">
            <a href="index.php" class="header__logo">
                <span class="logo-icon">🎯</span>
                <span class="logo-text">Hunter <span class="logo-accent">CG</span></span>
            </a>

            <?php if (isLoggedIn()): ?>
            <!-- Hamburger Button -->
            <button class="hamburger" id="hamburgerBtn" aria-label="Toggle navigation" aria-expanded="false">
                <span class="hamburger__line"></span>
                <span class="hamburger__line"></span>
                <span class="hamburger__line"></span>
            </button>

            <!-- Navigation -->
            <nav class="nav" id="mainNav">
                <div class="nav__user">
                    <div class="nav__avatar"><?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?></div>
                    <span class="nav__username"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></span>
                </div>
                <a href="dashboard.php" class="nav__link <?php echo $current_page === 'dashboard' ? 'nav__link--active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="logout.php" class="nav__link nav__link--logout">
                    🚪 Logout
                </a>
            </nav>
            <?php else: ?>
            <nav class="nav nav--guest" id="mainNav">
                <a href="login.php" class="nav__link <?php echo $current_page === 'login' ? 'nav__link--active' : ''; ?>">Login</a>
                <a href="register.php" class="nav__link nav__link--cta <?php echo $current_page === 'register' ? 'nav__link--active' : ''; ?>">Register</a>
            </nav>
            <?php endif; ?>
        </div>
    </header>

    <!-- ============ FLASH MESSAGES ============ -->
    <?php if (hasFlash()): ?>
        <?php $flash = getFlash(); ?>
        <div class="flash flash--<?php echo htmlspecialchars($flash['type']); ?>" id="flashMessage">
            <span class="flash__icon">
                <?php
                $icons = ['success' => '✓', 'error' => '✕', 'warning' => '⚠', 'info' => 'ℹ'];
                echo $icons[$flash['type']] ?? 'ℹ';
                ?>
            </span>
            <span class="flash__text"><?php echo htmlspecialchars($flash['message']); ?></span>
            <button class="flash__close" onclick="closeFlash()" aria-label="Close">&times;</button>
        </div>
    <?php endif; ?>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="main">