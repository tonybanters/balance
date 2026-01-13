<?php $title = 'Register - Balance'; ?>
<?php ob_start(); ?>

<main class="auth_container">
    <div class="auth_box">
        <h1>Balance</h1>
        <p class="auth_subtitle">Create your account</p>

        <?php if ($error === 'exists'): ?>
        <div class="error_message">An account with that email already exists</div>
        <?php endif; ?>

        <form method="POST" action="/register" class="auth_form">
            <label>
                Name
                <input type="text" name="name" required autofocus>
            </label>

            <label>
                Email
                <input type="email" name="email" required>
            </label>

            <label>
                Password
                <input type="password" name="password" required minlength="8">
            </label>

            <button type="submit">Create Account</button>
        </form>

        <p class="auth_switch">
            Already have an account? <a href="/login">Sign in</a>
        </p>
    </div>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/base.php'; ?>
