<?php
/**
 * Registration Form View
 * Expects: $errors (array), $old (array)
 */
 $errors = $errors ?? [];
 $old    = $old ?? [];
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card__header">
            <div class="auth-card__icon">🎯</div>
            <h1 class="auth-card__title">Create Account</h1>
            <p class="auth-card__subtitle">Join the Hunter Contact Gain network</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <span class="alert__icon">✕</span>
            <?php echo htmlspecialchars($errors['general']); ?>
        </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="form" id="registerForm" novalidate>
            <?php echo csrfField(); ?>

            <div class="form__group">
                <label for="full_name" class="form__label">Full Name</label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    class="form__input <?php echo isset($errors['full_name']) ? 'form__input--error' : ''; ?>"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>"
                    required
                    autocomplete="name"
                >
                <?php if (isset($errors['full_name'])): ?>
                <span class="form__error"><?php echo htmlspecialchars($errors['full_name']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form__group">
                <label for="phone" class="form__label">Phone Number</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form__input <?php echo isset($errors['phone']) ? 'form__input--error' : ''; ?>"
                    placeholder="e.g. +234 800 000 0000"
                    value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>"
                    required
                    autocomplete="tel"
                >
                <?php if (isset($errors['phone'])): ?>
                <span class="form__error"><?php echo htmlspecialchars($errors['phone']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form__group">
                <label for="password" class="form__label">Password</label>
                <div class="form__input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form__input <?php echo isset($errors['password']) ? 'form__input--error' : ''; ?>"
                        placeholder="Minimum 6 characters"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="form__toggle-password" data-target="password" aria-label="Toggle password visibility">
                        👁
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                <span class="form__error"><?php echo htmlspecialchars($errors['password']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form__group">
                <label for="confirm_password" class="form__label">Confirm Password</label>
                <div class="form__input-wrapper">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form__input <?php echo isset($errors['confirm_password']) ? 'form__input--error' : ''; ?>"
                        placeholder="Re-enter your password"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="form__toggle-password" data-target="confirm_password" aria-label="Toggle password visibility">
                        👁
                    </button>
                </div>
                <?php if (isset($errors['confirm_password'])): ?>
                <span class="form__error"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn--primary btn--full" id="registerBtn">
                <span class="btn__text">Create Account</span>
                <span class="btn__loader" style="display:none;"></span>
            </button>
        </form>

        <div class="auth-card__footer">
            <p>Already have an account? <a href="login.php" class="auth-card__link">Log in</a></p>
        </div>
    </div>
</div>