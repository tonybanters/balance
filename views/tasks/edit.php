<?php $title = 'Edit Task - Balance'; ?>
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
        <h1>Edit Task</h1>
    </div>

    <form method="POST" action="/tasks/<?= $task['id'] ?>/edit" class="form">
        <label>
            Title
            <input type="text" name="title" value="<?= h($task['title']) ?>" required autofocus>
        </label>

        <label>
            Description (optional)
            <textarea name="description" rows="3"><?= h($task['description'] ?? '') ?></textarea>
        </label>

        <label>
            Due Date (optional)
            <input type="date" name="due_date" value="<?= h($task['due_date'] ?? '') ?>">
        </label>

        <label>
            Priority
            <select name="priority">
                <option value="low" <?= ($task['priority'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Low</option>
                <option value="medium" <?= ($task['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                <option value="high" <?= ($task['priority'] ?? 'medium') === 'high' ? 'selected' : '' ?>>High</option>
            </select>
        </label>

        <div class="form_actions">
            <a href="/projects/<?= $project['id'] ?>" class="btn">Cancel</a>
            <button type="submit" class="btn btn_primary">Save Changes</button>
        </div>
    </form>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
