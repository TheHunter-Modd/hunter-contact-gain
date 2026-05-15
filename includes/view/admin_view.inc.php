<?php
/**
 * Admin Panel View
 */
 $data = $adminData ?? [];
 $pendingPayments  = $data['pending_payments'] ?? [];
 $verifiedPayments = $data['verified_payments'] ?? [];
 $allBatches       = $data['all_batches'] ?? [];
?>

<div class="dashboard">
    <div class="container">
        <div class="dash-welcome">
            <div class="dash-welcome__avatar">🛡️</div>
            <div class="dash-welcome__info">
                <h1 class="dash-welcome__title">Admin Panel</h1>
                <p class="dash-welcome__subtitle">Manage payments, verifications, and batches</p>
            </div>
        </div>

                <!-- WHATSAPP GROUP LINK MANAGEMENT -->
        <div class="dash-section">
            <h2 class="dash-section__title">💬 WhatsApp Group Link</h2>
            <div class="card">
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                    This link is hidden from unverified users and revealed on the dashboard once they become verified.
                </p>
                <form action="admin.php" method="POST" class="form">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="action" value="update_whatsapp_link">
                    <div class="form__group">
                        <label class="form__label">Verified Members Group Invite Link</label>
                        <input type="url" name="whatsapp_link" class="form__input" value="<?php echo htmlspecialchars($whatsappLink ?? ''); ?>" placeholder="https://chat.whatsapp.com/..." required>
                    </div>
                    <button type="submit" class="btn btn--primary btn--sm">Update Link</button>
                </form>
            </div>
        </div>

        <!-- BATCH MANAGEMENT -->
        <div class="dash-section">
            <h2 class="dash-section__title">📦 Batch Management</h2>
            <div class="dash-grid dash-grid--2">
                <div class="card">
                    <h3 style="margin-bottom: 1rem;">Create New Batch</h3>
                    <form action="admin.php" method="POST" class="form">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="create_batch">
                        <div class="form__group">
                            <input type="text" name="batch_name" class="form__input" placeholder="e.g. January 2024 Contacts" required>
                        </div>
                        <button type="submit" class="btn btn--primary btn--full">Create Batch</button>
                    </form>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 1rem;">Process/Drop Batch</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                        This will collect all currently verified contacts and lock them into the batch for download.
                    </p>
                    <?php if (!empty($allBatches) && $allBatches[0]['status'] === 'active'): ?>
                    <form action="admin.php" method="POST">
                        <?php echo csrfField(); ?>
                        <input type="hidden" name="action" value="drop_batch">
                        <input type="hidden" name="batch_id" value="<?php echo $allBatches[0]['id']; ?>">
                        <button type="submit" class="btn btn--outline btn--full">⚡ Drop Batch <?php echo $allBatches[0]['batch_number']; ?></button>
                    </form>
                    <?php else: ?>
                    <button class="btn btn--outline btn--full" disabled>No Active Batch to Drop</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PENDING PAYMENTS -->
        <div class="dash-section">
            <h2 class="dash-section__title">Pending Approvals (<?php echo count($pendingPayments); ?>)</h2>
            <?php if (empty($pendingPayments)): ?>
                <div class="card"><p style="text-align: center; color: var(--text-muted); padding: 1rem;">No pending payments right now.</p></div>
            <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>User</th><th>Phone</th><th>Date</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach ($pendingPayments as $p): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($p['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($p['phone']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($p['created_at'])); ?></td>
                                    <td>
                                        <form action="admin.php" method="POST" style="display:inline;">
                                            <?php echo csrfField(); ?>
                                            <input type="hidden" name="action" value="approve_payment">
                                            <input type="hidden" name="payment_id" value="<?php echo $p['id']; ?>">
                                            <button type="submit" class="btn btn--primary btn--sm">Approve ✅</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- VERIFIED PAYMENTS -->
        <div class="dash-section">
            <h2 class="dash-section__title">Active Verified Users (<?php echo count($verifiedPayments); ?>)</h2>
            <?php if (empty($verifiedPayments)): ?>
                <div class="card"><p style="text-align: center; color: var(--text-muted); padding: 1rem;">No active verified users.</p></div>
            <?php else: ?>
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>User</th><th>Access Code</th><th>Expires</th></tr></thead>
                            <tbody>
                                <?php foreach ($verifiedPayments as $p): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($p['full_name']); ?></strong></td>
                                    <td><code style="background: var(--accent-dim); color: var(--accent); padding: 2px 6px; border-radius: 4px; font-weight:700;"><?php echo htmlspecialchars($p['access_code']); ?></code></td>
                                    <td><?php echo date('M j, Y', strtotime($p['expires_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>