<?php $title = 'New Project - Balance'; ?>
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
        <a href="/projects" class="back_link">&larr; Back to projects</a>
        <h1>New Project</h1>
    </div>

    <?php if ($error): ?>
    <div class="error_message"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/projects/create" class="form">
        <label>
            Name
            <input type="text" name="name" required autofocus>
        </label>

        <label>
            Description (optional)
            <textarea name="description" rows="3"></textarea>
        </label>

        <div class="form_actions">
            <a href="/projects" class="btn">Cancel</a>
            <button type="submit" class="btn btn_primary">Create Project</button>
        </div>
    </form>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
