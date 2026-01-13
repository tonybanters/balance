<?php $title = 'Search - Balance'; ?>
<?php ob_start(); ?>

<header class="top_nav">
    <div class="nav_brand">Balance</div>
    <form action="/search" method="GET" class="nav_search">
        <input type="text" name="q" value="<?= h($query) ?>" placeholder="Search..." class="search_input" autofocus>
    </form>
    <nav class="nav_links">
        <span class="nav_user"><?= h($user['name']) ?></span>
        <a href="/logout" class="nav_logout">Logout</a>
    </nav>
</header>

<main class="content">
    <div class="page_header">
        <h1>Search Results</h1>
        <?php if ($query): ?>
        <p class="search_query">Results for "<?= h($query) ?>"</p>
        <?php endif; ?>
    </div>

    <?php if (empty($query)): ?>
    <div class="empty_state">
        <p>Enter a search term to find projects and tasks.</p>
    </div>
    <?php elseif (empty($projects) && empty($tasks)): ?>
    <div class="empty_state">
        <p>No results found for "<?= h($query) ?>"</p>
    </div>
    <?php else: ?>

    <?php if (!empty($projects)): ?>
    <section class="search_section">
        <h2>Projects (<?= count($projects) ?>)</h2>
        <div class="projects_grid">
            <?php foreach ($projects as $project): ?>
            <a href="/projects/<?= $project['id'] ?>" class="project_card">
                <h3><?= h($project['name']) ?></h3>
                <?php if ($project['description']): ?>
                <p class="project_desc"><?= h($project['description']) ?></p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($tasks)): ?>
    <section class="search_section">
        <h2>Tasks (<?= count($tasks) ?>)</h2>
        <ul class="task_list">
            <?php foreach ($tasks as $task): ?>
            <li class="task_item search_task_item <?= $task['status'] === 'done' ? 'task_done' : '' ?>">
                <span class="task_toggle"><?= $task['status'] === 'done' ? '✓' : '○' ?></span>
                <div class="task_content">
                    <div class="task_main">
                        <a href="/projects/<?= $task['project_id'] ?>" class="task_title"><?= h($task['title']) ?></a>
                        <span class="task_priority priority_<?= $task['priority'] ?? 'medium' ?>"><?= $task['priority'] ?? 'medium' ?></span>
                        <?php if ($task['due_date']): ?>
                        <span class="task_due"><?= $task['due_date'] ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="task_project_name">in <?= h($task['project_name']) ?></div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

    <?php endif; ?>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/base.php'; ?>
