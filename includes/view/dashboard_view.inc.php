<?php
/**
 * Dashboard View
 */
 $data = $dashboardData ?? [];

 $user          = $data['user'] ?? null;
 $payment       = $data['payment'] ?? null;
 $isVerified    = $data['is_verified'] ?? false;
 $isPending     = $data['is_pending'] ?? false;
 $contact       = $data['contact'] ?? null;
 $currentBatch  = $data['current_batch'] ?? null;
 $allBatches    = $data['all_batches'] ?? [];
 $memberType    = $data['member_type'] ?? 'new';
 $daysLeft      = $data['days_left'] ?? null;

if (!$user) return;
?>
<div class="dashboard">
    <div class="container">
        
        <!-- WELCOME SECTION -->
        <div class="dash-welcome">
            <div class="dash-welcome__avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
            <div class="dash-welcome__info">
                <h1 class="dash-welcome__title">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p class="dash-welcome__subtitle">Manage your contacts and verification status</p>
            </div>
        </div>

        <!-- STATUS CARDS -->
        <div class="dash-grid dash-grid--3">
            <div class="card card--status <?php echo $isVerified ? 'card--verified' : 'card--unverified'; ?>">
                <div class="card__icon"><?php echo $isVerified ? '✅' : ($isPending ? '⏳' : '❌'); ?></div>
                <h3 class="card__title">Verification</h3>
                <p class="card__value"><?php echo $isVerified ? 'Verified' : ($isPending ? 'Pending' : 'Not Verified'); ?></p>
                <?php if ($isVerified && $daysLeft !== null): ?>
                <p class="card__meta"><?php echo $daysLeft; ?> days remaining</p>
                <?php elseif ($isPending): ?>
                <p class="card__meta">Awaiting admin approval</p>
                <?php elseif (!$isVerified): ?>
                <p class="card__meta">Payment required</p>
                <?php endif; ?>
                <div class="card__progress">
                    <div class="card__progress-bar" style="width: <?php echo $isVerified ? max(0, min(100, ($daysLeft / 21) * 100)) : 0; ?>%"></div>
                </div>
            </div>

            <div class="card card--status <?php echo $contact ? 'card--verified' : 'card--unverified'; ?>">
                <div class="card__icon"><?php echo $contact ? '📇' : '📝'; ?></div>
                <h3 class="card__title">Contact</h3>
                <p class="card__value"><?php echo $contact ? 'Submitted' : 'Not Submitted'; ?></p>
                <?php if ($contact): ?>
                <p class="card__meta"><?php echo htmlspecialchars($contact['contact_name']); ?></p>
                <?php else: ?>
                <p class="card__meta">Submit your WhatsApp contact</p>
                <?php endif; ?>
            </div>

            <div class="card card--status <?php echo $currentBatch ? 'card--active' : 'card--unverified'; ?>">
                <div class="card__icon">📦</div>
                <h3 class="card__title">Active Batch</h3>
                <p class="card__value"><?php echo $currentBatch ? 'Batch ' . $currentBatch['batch_number'] : 'None'; ?></p>
                <?php if ($currentBatch): ?>
                <p class="card__meta"><?php echo htmlspecialchars($currentBatch['name']); ?></p>
                <?php else: ?>
                <p class="card__meta">No active batch currently</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="dash-section">
            <h2 class="dash-section__title">Quick Actions</h2>
            <div class="dash-grid dash-grid--2">
                
                <?php if (!$contact): ?>
                <button type="button" class="card card--action" id="submitContactBtn">
                    <div class="card__action-icon">📝</div>
                    <div class="card__action-content"><h3>Submit Contact</h3><p>Add your WhatsApp details</p></div>
                    <span class="card__arrow">→</span>
                </button>
                <?php else: ?>
                <button type="button" class="card card--action" id="updateContactBtn">
                    <div class="card__action-icon">✏️</div>
                    <div class="card__action-content"><h3>Update Contact</h3><p>Current: <?php echo htmlspecialchars($contact['whatsapp_number']); ?></p></div>
                    <span class="card__arrow">→</span>
                </button>
                <?php endif; ?>

                <?php if (!$isVerified && !$isPending): ?>
                <button type="button" class="card card--action card--action-highlight" id="makePaymentBtn">
                    <div class="card__action-icon">💰</div>
                    <div class="card__action-content"><h3>Make Payment</h3><p>Get verified to download contacts</p></div>
                    <span class="card__arrow">→</span>
                </button>
                <?php elseif ($isPending): ?>
                <div class="card card--action card--action-disabled">
                    <div class="card__action-icon">⏳</div>
                    <div class="card__action-content"><h3>Payment Pending</h3><p>Waiting for admin</p></div>
                </div>
                <?php else: ?>
                <button type="button" class="card card--action <?php echo $currentBatch ? '' : 'card--action-disabled'; ?>" id="downloadVcfBtn" <?php echo !$currentBatch ? 'disabled' : ''; ?>>
                    <div class="card__action-icon">📥</div>
                    <div class="card__action-content">
                        <h3>Download VCF</h3>
                        <?php if ($currentBatch): ?>
                        <p>Batch <?php echo $currentBatch['batch_number']; ?> — <?php echo ucfirst($memberType); ?> Member</p>
                        <?php else: ?>
                        <p>No batch available yet</p>
                        <?php endif; ?>
                    </div>
                    <span class="card__arrow">→</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- ACCESS CODE -->
        <?php if ($isVerified && $payment && $payment['access_code']): ?>
        <div class="dash-section">
            <h2 class="dash-section__title">Your Access Code</h2>
            <div class="card card--code">
                <p class="card__code-label">Use this code to download VCF files (Expires in <?php echo $daysLeft; ?> days)</p>
                <div class="card__code-value" id="accessCodeDisplay"><?php echo htmlspecialchars($payment['access_code']); ?></div>
                <button type="button" class="btn btn--outline btn--sm" onclick="copyAccessCode()">📋 Copy Code</button>
            </div>
        </div>
        <?php endif; ?>

                <!-- ============ EXCLUSIVE WHATSAPP GROUP ============ -->
        <?php if ($isVerified && !empty($whatsappLink) && $whatsappLink !== 'https://chat.whatsapp.com/DEFAULT'): ?>
        <div class="dash-section">
            <h2 class="dash-section__title">💬 Exclusive Verified Group</h2>
            <div class="card card--verified" style="text-align: center;">
                <div class="card__icon">🚀</div>
                <h3 class="card__title" style="font-size: 1.1rem; margin-bottom: 0.5rem;">Join the Verified Hunters Group!</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">Network exclusively with other verified members.</p>
                <a href="<?php echo htmlspecialchars($whatsappLink); ?>" target="_blank" class="btn btn--primary">
                    Join WhatsApp Group 💬
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- BATCH HISTORY -->
        <?php if (!empty($allBatches)): ?>
        <div class="dash-section">
            <h2 class="dash-section__title">Batch History</h2>
            <div class="card">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Batch</th><th>Name</th><th>Status</th><th>Created</th></tr></thead>
                        <tbody>
                            <?php foreach ($allBatches as $batch): ?>
                            <tr>
                                <td><strong>Batch <?php echo $batch['batch_number']; ?></strong></td>
                                <td><?php echo htmlspecialchars($batch['name']); ?></td>
                                <td><span class="badge badge--<?php echo $batch['status']; ?>"><?php echo ucfirst($batch['status']); ?></span></td>
                                <td><?php echo date('M j, Y', strtotime($batch['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- CONTACT MODAL -->
<div class="modal-overlay" id="contactModalOverlay">
    <div class="modal">
        <div class="modal__header">
            <h2 class="modal__title"><?php echo $contact ? '✏️ Update Contact' : '📝 Submit Contact'; ?></h2>
            <button type="button" class="modal__close" id="closeContactModal">&times;</button>
        </div>
        <form action="dashboard.php" method="POST" class="modal__body form">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="submit_contact">
            <div class="form__group">
                <label for="contact_name" class="form__label">Preferred Saved Name</label>
                <input type="text" id="contact_name" name="contact_name" class="form__input" placeholder="e.g. John Marketer" value="<?php echo htmlspecialchars($contact['contact_name'] ?? ''); ?>" required>
            </div>
            <div class="form__group">
                <label for="whatsapp_number" class="form__label">WhatsApp Phone Number</label>
                <input type="tel" id="whatsapp_number" name="whatsapp_number" class="form__input" placeholder="e.g. +234 800 000 0000" value="<?php echo htmlspecialchars($contact['whatsapp_number'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn--primary btn--full"><?php echo $contact ? 'Update Contact' : 'Submit Contact'; ?></button>
        </form>
    </div>
</div>

<!-- PAYMENT MODAL -->
<div class="modal-overlay" id="paymentModalOverlay">
    <div class="modal">
        <div class="modal__header">
            <h2 class="modal__title">💰 Make Payment</h2>
            <button type="button" class="modal__close" id="closePaymentModal">&times;</button>
        </div>
        <form action="dashboard.php" method="POST" class="modal__body form">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="submit_payment">
            <div class="form__group" style="text-align: center;">
                <p style="margin-bottom: 1rem; color: var(--text-secondary);">Transfer 1,000 NGN to the following details:</p>
                <div style="background: var(--bg-input); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--border);">
                    <p style="font-weight: 700; color: var(--accent);">Bank Name: Palmpay</p>
                    <p style="font-weight: 700; font-size: 1.1rem; margin-top: 0.5rem;">9043071224</p>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Oyeleke Victor .O</p>
                </div>
            </div>
            <button type="submit" class="btn btn--primary btn--full">I've Made Payment ✅</button>
        </form>
    </div>
</div>

<!-- VCF DOWNLOAD MODAL -->
<div class="modal-overlay" id="vcfModalOverlay">
    <div class="modal">
        <div class="modal__header">
            <h2 class="modal__title">📥 Download VCF</h2>
            <button type="button" class="modal__close" id="closeVcfModal">&times;</button>
        </div>
        <form action="download.php" method="POST" class="modal__body form">
            <?php echo csrfField(); ?>
            <input type="hidden" name="action" value="download_vcf">
            <div class="form__group" style="text-align: center;">
                <p style="margin-bottom: 1rem; color: var(--text-secondary);">Enter your unique access code to download the VCF file for Batch <?php echo $currentBatch ? $currentBatch['batch_number'] : ''; ?>.</p>
            </div>
            <div class="form__group">
                <label for="vcf_access_code" class="form__label">Access Code</label>
                <input type="text" id="vcf_access_code" name="access_code" class="form__input" placeholder="Enter your 12-character code" value="<?php echo htmlspecialchars($payment['access_code'] ?? ''); ?>" required style="text-align: center; font-weight: 700; letter-spacing: 2px;">
            </div>
            <button type="submit" class="btn btn--primary btn--full">Download VCF File</button>
        </form>
    </div>
</div>

<script>
function copyAccessCode() {
    const codeEl = document.getElementById('accessCodeDisplay');
    if (!codeEl) return;
    const text = codeEl.textContent.trim();
    navigator.clipboard.writeText(text).then(function() {
        const original = codeEl.textContent;
        codeEl.textContent = 'Copied!';
        setTimeout(function() { codeEl.textContent = original; }, 1500);
    });
}
</script>