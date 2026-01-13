<?php

function handle_organizations_list(PDO $db): void {
    $user = require_auth($db);
    $organizations = organization_get_all_for_user($db, $user['id']);
    require __DIR__ . '/../views/organizations/index.php';
}

function handle_organization_create_page(PDO $db): void {
    $user = require_auth($db);
    $error = get_flash('error');
    require __DIR__ . '/../views/organizations/create.php';
}

function handle_organization_create(PDO $db): void {
    $user = require_auth($db);

    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        set_flash('error', 'Name is required');
        redirect('/organizations/create');
    }

    $org = organization_create($db, $name, $user['id']);
    set_flash('success', 'Organization created');
    redirect('/o/' . $org['slug']);
}

function handle_organization_show(PDO $db, string $slug): void {
    $user = require_auth($db);
    $organization = organization_get_by_slug($db, $slug);

    if (!$organization || !organization_is_member($db, $organization['id'], $user['id'])) {
        redirect('/organizations');
    }

    $projects = project_get_all_for_organization($db, $organization['id']);
    $is_owner = organization_is_owner($db, $organization['id'], $user['id']);
    $flash = get_flash('success');
    require __DIR__ . '/../views/organizations/show.php';
}

function handle_organization_settings(PDO $db, string $slug): void {
    $user = require_auth($db);
    $organization = organization_get_by_slug($db, $slug);

    if (!$organization || !organization_is_owner($db, $organization['id'], $user['id'])) {
        redirect('/organizations');
    }

    $members = organization_get_members($db, $organization['id']);
    $flash = get_flash('success');
    $error = get_flash('error');
    require __DIR__ . '/../views/organizations/settings.php';
}

function handle_organization_invite(PDO $db, string $slug): void {
    $user = require_auth($db);
    $organization = organization_get_by_slug($db, $slug);

    if (!$organization || !organization_is_owner($db, $organization['id'], $user['id'])) {
        redirect('/organizations');
    }

    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('error', 'Valid email is required');
        redirect("/o/$slug/settings");
    }

    $token = invite_create($db, $organization['id'], $email);

    set_flash('success', "Invite link: " . $_SERVER['HTTP_HOST'] . "/invite/$token");
    redirect("/o/$slug/settings");
}

function handle_invite_accept_page(PDO $db, string $token): void {
    $invite = invite_get_by_token($db, $token);

    if (!$invite) {
        redirect('/login');
    }

    require __DIR__ . '/../views/organizations/invite.php';
}

function handle_invite_accept(PDO $db, string $token): void {
    $user = require_auth($db);
    $invite = invite_get_by_token($db, $token);

    if (!$invite) {
        redirect('/organizations');
    }

    organization_add_member($db, $invite['organization_id'], $user['id']);
    invite_delete($db, $invite['id']);

    $org = organization_get_by_id($db, $invite['organization_id']);
    set_flash('success', 'Welcome to ' . $org['name']);
    redirect('/o/' . $org['slug']);
}

function handle_organization_remove_member(PDO $db, string $slug, int $member_id): void {
    $user = require_auth($db);
    $organization = organization_get_by_slug($db, $slug);

    if (!$organization || !organization_is_owner($db, $organization['id'], $user['id'])) {
        redirect('/organizations');
    }

    if ($member_id === $user['id']) {
        set_flash('error', 'Cannot remove yourself');
        redirect("/o/$slug/settings");
    }

    organization_remove_member($db, $organization['id'], $member_id);
    set_flash('success', 'Member removed');
    redirect("/o/$slug/settings");
}

function handle_organization_project_create_page(PDO $db, string $slug): void {
    $user = require_auth($db);
    $organization = organization_get_by_slug($db, $slug);

    if (!$organization || !organization_is_member($db, $organization['id'], $user['id'])) {
        redirect('/organizations');
    }

    $error = get_flash('error');
    require __DIR__ . '/../views/organizations/project_create.php';
}

function handle_organization_project_create(PDO $db, string $slug): void {
    $user = require_auth($db);
    $organization = organization_get_by_slug($db, $slug);

    if (!$organization || !organization_is_member($db, $organization['id'], $user['id'])) {
        redirect('/organizations');
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;

    if (empty($name)) {
        set_flash('error', 'Name is required');
        redirect("/o/$slug/projects/create");
    }

    $stmt = $db->prepare("
        INSERT INTO projects (user_id, organization_id, name, description)
        VALUES (:user_id, :org_id, :name, :description)
    ");
    $stmt->execute([
        'user_id' => $user['id'],
        'org_id' => $organization['id'],
        'name' => $name,
        'description' => $description,
    ]);

    $project_id = $db->lastInsertId();
    redirect("/projects/$project_id");
}
