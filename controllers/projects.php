<?php

function handle_projects_list(PDO $db): void {
    $user = require_auth($db);
    $projects = project_get_all_for_user($db, $user['id']);
    require __DIR__ . '/../views/projects/index.php';
}

function handle_project_create_page(PDO $db): void {
    $user = require_auth($db);
    $error = get_flash('error');
    require __DIR__ . '/../views/projects/create.php';
}

function handle_project_create(PDO $db): void {
    $user = require_auth($db);

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;

    if (empty($name)) {
        set_flash('error', 'Name is required');
        redirect('/projects/create');
    }

    $project = project_create($db, $user['id'], $name, $description);
    redirect('/projects/' . $project['id']);
}

function handle_project_show(PDO $db, int $id): void {
    $user = require_auth($db);
    $project = project_get_by_id($db, $id);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $tasks = task_get_all_for_project($db, $id);
    require __DIR__ . '/../views/projects/show.php';
}

function handle_project_edit_page(PDO $db, int $id): void {
    $user = require_auth($db);
    $project = project_get_by_id($db, $id);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $error = get_flash('error');
    require __DIR__ . '/../views/projects/edit.php';
}

function handle_project_update(PDO $db, int $id): void {
    $user = require_auth($db);
    $project = project_get_by_id($db, $id);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;

    if (empty($name)) {
        set_flash('error', 'Name is required');
        redirect("/projects/$id/edit");
    }

    project_update($db, $id, $name, $description);
    redirect("/projects/$id");
}

function handle_project_delete(PDO $db, int $id): void {
    $user = require_auth($db);
    $project = project_get_by_id($db, $id);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    project_delete($db, $id);
    redirect('/projects');
}
