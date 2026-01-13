<?php $title = 'Join ' . h($invite['organization_name']) . ' - Balance'; ?>
<?php ob_start(); ?>

<div class="auth_container">
    <div class="auth_box">
        <h1>You're Invited</h1>
        <p class="auth_subtitle">Join <?= h($invite['organization_name']) ?> on Balance</p>

        <form method="POST" action="/invite/<?= h($invite['token']) ?>" class="auth_form">
            <button type="submit">Accept Invitation</button>
        </form>

        <div class="auth_switch">
            <a href="/login">Back to login</a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
