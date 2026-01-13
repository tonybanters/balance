<?php $title = h($organization['name']) . ' - Balance'; ?>
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
        <a href="/organizations" class="back_link">&larr; Back to organizations</a>
        <div class="page_title_row">
            <h1><?= h($organization['name']) ?></h1>
            <div class="page_actions">
                <a href="/o/<?= h($organization['slug']) ?>/projects/create" class="btn btn_primary">New Project</a>
                <?php if ($is_owner): ?>
                <a href="/o/<?= h($organization['slug']) ?>/settings" class="btn">Settings</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($flash): ?>
    <div class="flash_message"><?= h($flash) ?></div>
    <?php endif; ?>

    <section>
        <h2>Projects</h2>
        <?php if (empty($projects)): ?>
        <div class="empty_state">
            <p>No projects yet. Create one to get started.</p>
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
    </section>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
