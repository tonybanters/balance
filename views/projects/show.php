<?php $title = h($project['name']) . ' - Balance'; ?>
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
        <div class="page_title_row">
            <h1><?= h($project['name']) ?></h1>
            <div class="page_actions">
                <a href="/projects/<?= $project['id'] ?>/edit" class="btn">Edit</a>
                <form method="POST" action="/projects/<?= $project['id'] ?>/delete" class="inline_form"
                      onsubmit="return confirm('Delete this project and all its tasks?')">
                    <button type="submit" class="btn btn_danger">Delete</button>
                </form>
            </div>
        </div>
        <?php if ($project['description']): ?>
        <p class="project_description"><?= h($project['description']) ?></p>
        <?php endif; ?>
    </div>

    <section class="tasks_section">
        <h2>Tasks</h2>

        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks" class="add_task_form">
            <input type="text" name="title" placeholder="Add a task..." required>
            <input type="date" name="due_date">
            <button type="submit" class="btn btn_primary">Add</button>
        </form>

        <?php if (empty($tasks)): ?>
        <div class="empty_state">
            <p>No tasks yet. Add your first task above.</p>
        </div>
        <?php else: ?>
        <ul class="task_list">
            <?php foreach ($tasks as $task): ?>
            <li class="task_item <?= $task['status'] === 'done' ? 'task_done' : '' ?>">
                <form method="POST" action="/tasks/<?= $task['id'] ?>/toggle" class="inline_form">
                    <button type="submit" class="task_toggle">
                        <?= $task['status'] === 'done' ? '✓' : '○' ?>
                    </button>
                </form>
                <div class="task_content">
                    <span class="task_title"><?= h($task['title']) ?></span>
                    <?php if ($task['due_date']): ?>
                    <span class="task_due"><?= $task['due_date'] ?></span>
                    <?php endif; ?>
                </div>
                <form method="POST" action="/tasks/<?= $task['id'] ?>/delete" class="inline_form">
                    <button type="submit" class="task_delete">&times;</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </section>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
