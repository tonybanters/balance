<?php $title = 'Login - Balance'; ?>
<?php ob_start(); ?>

<main class="auth_container">
    <div class="auth_box">
        <h1>Balance</h1>
        <p class="auth_subtitle">Sign in to your account</p>

        <?php if ($error === 'invalid'): ?>
        <div class="error_message">Invalid email or password</div>
        <?php endif; ?>

        <form method="POST" action="/login" class="auth_form">
            <label>
                Email
                <input type="email" name="email" required autofocus>
            </label>

            <label>
                Password
                <input type="password" name="password" required>
            </label>

            <button type="submit">Sign In</button>
        </form>

        <p class="auth_switch">
            Don't have an account? <a href="/register">Register</a>
        </p>
    </div>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/base.php'; ?>
