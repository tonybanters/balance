<?php $title = h($project['name']) . ' - Balance'; ?>
<?php $flash = get_flash('success'); ?>
<?php ob_start(); ?>

<header class="top_nav">
    <div class="nav_brand">Balance</div>
    <form action="/search" method="GET" class="nav_search">
        <input type="text" name="q" placeholder="Search... (/)" class="search_input">
    </form>
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

    <?php if ($flash): ?>
    <div class="flash_message"><?= h($flash) ?></div>
    <?php endif; ?>

    <section class="tasks_section">
        <div class="tasks_header">
            <h2>Tasks</h2>
            <div class="task_filters">
                <a href="/projects/<?= $project['id'] ?>" class="filter_btn <?= !$filter ? 'filter_active' : '' ?>">All</a>
                <a href="/projects/<?= $project['id'] ?>?filter=pending" class="filter_btn <?= $filter === 'pending' ? 'filter_active' : '' ?>">Pending</a>
                <a href="/projects/<?= $project['id'] ?>?filter=today" class="filter_btn <?= $filter === 'today' ? 'filter_active' : '' ?>">Due Today</a>
                <a href="/projects/<?= $project['id'] ?>?filter=overdue" class="filter_btn <?= $filter === 'overdue' ? 'filter_active' : '' ?>">Overdue</a>
                <a href="/projects/<?= $project['id'] ?>?filter=high" class="filter_btn <?= $filter === 'high' ? 'filter_active' : '' ?>">High Priority</a>
            </div>
        </div>

        <form method="POST" action="/projects/<?= $project['id'] ?>/tasks" class="add_task_form">
            <input type="text" name="title" placeholder="Add a task..." required>
            <input type="text" name="description" placeholder="Description (optional)">
            <input type="date" name="due_date">
            <select name="priority" class="priority_select">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>
            <button type="submit" class="btn btn_primary">Add</button>
        </form>

        <?php if (empty($tasks)): ?>
        <div class="empty_state">
            <p>No tasks yet. Add your first task above.</p>
        </div>
        <?php else: ?>
        <ul class="task_list" id="task_list" data-project-id="<?= $project['id'] ?>">
            <?php foreach ($tasks as $task): ?>
            <li class="task_item <?= $task['status'] === 'done' ? 'task_done' : '' ?>" data-task-id="<?= $task['id'] ?>" draggable="true">
                <span class="task_drag_handle">⋮⋮</span>
                <form method="POST" action="/tasks/<?= $task['id'] ?>/toggle" class="inline_form">
                    <button type="submit" class="task_toggle">
                        <?= $task['status'] === 'done' ? '✓' : '○' ?>
                    </button>
                </form>
                <div class="task_content">
                    <div class="task_main">
                        <a href="/tasks/<?= $task['id'] ?>" class="task_title_link"><?= h($task['title']) ?></a>
                        <span class="task_priority priority_<?= $task['priority'] ?? 'medium' ?>"><?= $task['priority'] ?? 'medium' ?></span>
                        <?php if ($task['due_date']): ?>
                        <?php $is_overdue = $task['status'] !== 'done' && $task['due_date'] < date('Y-m-d'); ?>
                        <span class="task_due <?= $is_overdue ? 'task_overdue' : '' ?>"><?= $task['due_date'] ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($task['description'])): ?>
                    <div class="task_description"><?= h($task['description']) ?></div>
                    <?php endif; ?>
                </div>
                <a href="/tasks/<?= $task['id'] ?>/edit" class="task_edit">Edit</a>
                <form method="POST" action="/tasks/<?= $task['id'] ?>/delete" class="inline_form">
                    <button type="submit" class="task_delete">&times;</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </section>

    <div class="shortcuts_hint">
        <kbd>n</kbd> new task
        <kbd>j</kbd><kbd>k</kbd> navigate
        <kbd>x</kbd> toggle
        <kbd>e</kbd> edit
        <kbd>d</kbd> delete
    </div>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
