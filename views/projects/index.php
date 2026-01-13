<?php $title = 'Projects - Balance'; ?>
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
        <h1>Projects</h1>
        <a href="/projects/create" class="btn btn_primary">New Project</a>
    </div>

    <?php if (empty($projects)): ?>
    <div class="empty_state">
        <p>No projects yet. Create your first project to get started.</p>
    </div>
    <?php else: ?>
    <div class="projects_grid">
        <?php foreach ($projects as $project): ?>
        <a href="/projects/<?= $project['id'] ?>" class="project_card">
            <h3><?= h($project['name']) ?></h3>
            <?php if ($project['description']): ?>
            <p class="project_desc"><?= h($project['description']) ?></p>
            <?php endif; ?>
            <div class="project_meta">
                <span><?= $project['task_count'] ?> task<?= $project['task_count'] !== 1 ? 's' : '' ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
