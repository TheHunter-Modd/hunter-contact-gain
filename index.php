<?php
require_once __DIR__ . '/includes/config/helpers_config.inc.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

 $page_title = 'Home';

require_once __DIR__ . '/includes/view/header_view.inc.php';
?>

<!-- ============ LANDING CONTENT ============ -->
<div class="landing">
    <div class="container">
        <div class="landing__hero">
            <div class="landing__icon">🎯</div>
            <h1 class="landing__title">
                The Hunter <span class="text-accent">Contact Gain</span>
            </h1>
            <p class="landing__subtitle">
                Grow your WhatsApp network with verified contacts. 
                Submit yours, get access to thousands more.
            </p>
            <div class="landing__actions">
                <a href="register.php" class="btn btn--primary btn--lg">Get Started →</a>
                <a href="login.php" class="btn btn--outline btn--lg">Log In</a>
            </div>
        </div>

        <div class="landing__features">
            <div class="landing__feature">
                <div class="landing__feature-icon">📇</div>
                <h3>Submit Contacts</h3>
                <p>Add your WhatsApp details to the growing network</p>
            </div>
            <div class="landing__feature">
                <div class="landing__feature-icon">✅</div>
                <h3>Get Verified</h3>
                <p>Complete payment to unlock contact access for 3 weeks</p>
            </div>
            <div class="landing__feature">
                <div class="landing__feature-icon">📥</div>
                <h3>Download VCF</h3>
                <p>Save verified contacts directly to your phone in one click</p>
            </div>
            <div class="landing__feature">
                <div class="landing__feature-icon">📦</div>
                <h3>Batch System</h3>
                <p>Get fresh contacts every batch — no duplicates ever</p>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/view/footer_view.inc.php';
?>