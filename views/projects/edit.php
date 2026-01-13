<?php $title = 'Edit ' . h($project['name']) . ' - Balance'; ?>
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
        <a href="/projects/<?= $project['id'] ?>" class="back_link">&larr; Back to project</a>
        <h1>Edit Project</h1>
    </div>

    <?php if ($error): ?>
    <div class="error_message"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/projects/<?= $project['id'] ?>/edit" class="form">
        <label>
            Name
            <input type="text" name="name" value="<?= h($project['name']) ?>" required autofocus>
        </label>

        <label>
            Description (optional)
            <textarea name="description" rows="3"><?= h($project['description'] ?? '') ?></textarea>
        </label>

        <div class="form_actions">
            <a href="/projects/<?= $project['id'] ?>" class="btn">Cancel</a>
            <button type="submit" class="btn btn_primary">Save Changes</button>
        </div>
    </form>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
