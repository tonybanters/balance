<?php $title = h($task['title']) . ' - Balance'; ?>
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
        <a href="/projects/<?= $project['id'] ?>" class="back_link">&larr; Back to <?= h($project['name']) ?></a>
        <div class="page_title_row">
            <h1 class="<?= $task['status'] === 'done' ? 'task_title_done' : '' ?>"><?= h($task['title']) ?></h1>
            <div class="page_actions">
                <form method="POST" action="/tasks/<?= $task['id'] ?>/toggle" class="inline_form">
                    <button type="submit" class="btn"><?= $task['status'] === 'done' ? 'Mark Pending' : 'Mark Done' ?></button>
                </form>
                <a href="/tasks/<?= $task['id'] ?>/edit" class="btn">Edit</a>
            </div>
        </div>
    </div>

    <?php if ($flash): ?>
    <div class="flash_message"><?= h($flash) ?></div>
    <?php endif; ?>

    <div class="task_detail">
        <div class="task_meta_row">
            <span class="task_priority priority_<?= $task['priority'] ?? 'medium' ?>"><?= $task['priority'] ?? 'medium' ?></span>
            <?php if ($task['due_date']): ?>
            <?php $is_overdue = $task['status'] !== 'done' && $task['due_date'] < date('Y-m-d'); ?>
            <span class="task_due <?= $is_overdue ? 'task_overdue' : '' ?>">Due: <?= $task['due_date'] ?></span>
            <?php endif; ?>
            <span class="task_status">Status: <?= $task['status'] ?></span>
        </div>

        <?php if ($task['description']): ?>
        <div class="task_detail_desc">
            <?= nl2br(h($task['description'])) ?>
        </div>
        <?php endif; ?>
    </div>

    <section class="comments_section">
        <h2>Comments (<?= count($comments) ?>)</h2>

        <form method="POST" action="/tasks/<?= $task['id'] ?>/comments" class="comment_form">
            <textarea name="body" placeholder="Add a comment..." rows="3" required></textarea>
            <button type="submit" class="btn btn_primary">Add Comment</button>
        </form>

        <?php if (empty($comments)): ?>
        <div class="empty_state">
            <p>No comments yet.</p>
        </div>
        <?php else: ?>
        <div class="comments_list">
            <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment_header">
                    <span class="comment_author"><?= h($comment['user_name']) ?></span>
                    <span class="comment_date"><?= $comment['created_at'] ?></span>
                    <form method="POST" action="/comments/<?= $comment['id'] ?>/delete" class="inline_form">
                        <button type="submit" class="comment_delete">&times;</button>
                    </form>
                </div>
                <div class="comment_body"><?= nl2br(h($comment['body'])) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
