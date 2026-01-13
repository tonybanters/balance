<?php $title = 'Organizations - Balance'; ?>
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
        <div class="page_title_row">
            <h1>Organizations</h1>
            <a href="/organizations/create" class="btn btn_primary">New Organization</a>
        </div>
        <a href="/projects" class="back_link">View personal projects</a>
    </div>

    <?php if (empty($organizations)): ?>
    <div class="empty_state">
        <p>No organizations yet. Create one to collaborate with your team.</p>
    </div>
    <?php else: ?>
    <div class="projects_grid">
        <?php foreach ($organizations as $org): ?>
        <a href="/o/<?= h($org['slug']) ?>" class="project_card">
            <h3><?= h($org['name']) ?></h3>
            <div class="project_meta">
                <span>Role: <?= h($org['role']) ?></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
