<?php $title = 'Settings - ' . h($organization['name']) . ' - Balance'; ?>
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
        <a href="/o/<?= h($organization['slug']) ?>" class="back_link">&larr; Back to <?= h($organization['name']) ?></a>
        <h1>Organization Settings</h1>
    </div>

    <?php if ($flash): ?>
    <div class="flash_message"><?= h($flash) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="error_message"><?= h($error) ?></div>
    <?php endif; ?>

    <section class="settings_section">
        <h2>Invite Member</h2>
        <form method="POST" action="/o/<?= h($organization['slug']) ?>/invite" class="invite_form">
            <input type="email" name="email" placeholder="Email address" required>
            <button type="submit" class="btn btn_primary">Generate Invite Link</button>
        </form>
    </section>

    <section class="settings_section">
        <h2>Members (<?= count($members) ?>)</h2>
        <div class="members_list">
            <?php foreach ($members as $member): ?>
            <div class="member_item">
                <div class="member_info">
                    <span class="member_name"><?= h($member['name']) ?></span>
                    <span class="member_email"><?= h($member['email']) ?></span>
                    <span class="member_role"><?= h($member['role']) ?></span>
                </div>
                <?php if ($member['id'] !== $user['id']): ?>
                <form method="POST" action="/o/<?= h($organization['slug']) ?>/members/<?= $member['id'] ?>/remove" class="inline_form">
                    <button type="submit" class="btn btn_danger btn_small" onclick="return confirm('Remove this member?')">Remove</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../base.php'; ?>
