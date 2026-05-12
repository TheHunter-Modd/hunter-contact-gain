<?php
/**
 * Login Form View
 * Expects: $errors (array), $old (array)
 */
 $errors = $errors ?? [];
 $old    = $old ?? [];
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-card__header">
            <div class="auth-card__icon">🎯</div>
            <h1 class="auth-card__title">Welcome Back</h1>
            <p class="auth-card__subtitle">Log in to your Hunter Contact Gain account</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error">
            <span class="alert__icon">✕</span>
            <?php echo htmlspecialchars($errors['general']); ?>
        </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="form" id="loginForm" novalidate>
            <?php echo csrfField(); ?>

            <div class="form__group">
                <label for="phone" class="form__label">Phone Number</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form__input <?php echo isset($errors['phone']) ? 'form__input--error' : ''; ?>"
                    placeholder="Enter your phone number"
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
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >
                    <button type="button" class="form__toggle-password" data-target="password" aria-label="Toggle password visibility">
                        👁
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                <span class="form__error"><?php echo htmlspecialchars($errors['password']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form__group form__group--row">
                <label class="form__checkbox-label">
                    <input type="checkbox" name="remember" id="remember" class="form__checkbox">
                    <span class="form__checkbox-custom"></span>
                    <span class="form__checkbox-text">Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn--primary btn--full" id="loginBtn">
                <span class="btn__text">Log In</span>
                <span class="btn__loader" style="display:none;"></span>
            </button>
        </form>

        <div class="auth-card__footer">
            <p>Don't have an account? <a href="register.php" class="auth-card__link">Register</a></p>
        </div>
    </div>
</div>