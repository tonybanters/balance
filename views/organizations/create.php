<?php $title = 'New Organization - Balance'; ?>
<?php ob_start(); ?>

<header class="top_nav">
    <div class="nav_brand">Balance</div>
    <nav class="nav_links">
        <span class="nav_user"><?= h($user['name']) ?></span>
        <a href="/logout" class="nav_logout">Logout</a>
    </nav>
</header>

<main class="content">
    <div class="page_header">
        <a href="/organizations" class="back_link">&larr; Back to organizations</a>
        <h1>New Organization</h1>
    </div>

    <?php if ($error): ?>
    <div class="error_message"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/organizations/create" class="form">
        <label>
            Name
            <input type="text" name="name" required autofocus>
        </label>

        <div class="form_actions">
            <a href="/organizations" class="btn">Cancel</a>
            <button type="submit" class="btn btn_primary">Create Organization</button>
        </div>
    </form>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
