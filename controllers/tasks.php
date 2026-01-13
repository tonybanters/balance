<?php

function handle_task_create(PDO $db, int $project_id): void {
    $user = require_auth($db);
    $project = project_get_by_id($db, $project_id);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;
    $due_date = $_POST['due_date'] ?: null;

    if (empty($title)) {
        redirect("/projects/$project_id");
    }

    task_create($db, $project_id, $title, $description, $due_date);
    redirect("/projects/$project_id");
}

function handle_task_toggle(PDO $db, int $id): void {
    $user = require_auth($db);
    $task = task_get_by_id($db, $id);

    if (!$task) {
        redirect('/projects');
    }

    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $new_status = $task['status'] === 'done' ? 'pending' : 'done';
    task_update_status($db, $id, $new_status);

    redirect("/projects/{$task['project_id']}");
}

function handle_task_delete(PDO $db, int $id): void {
    $user = require_auth($db);
    $task = task_get_by_id($db, $id);

    if (!$task) {
        redirect('/projects');
    }

    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $project_id = $task['project_id'];
    task_delete($db, $id);

    redirect("/projects/$project_id");
}
