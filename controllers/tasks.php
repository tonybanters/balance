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
    $priority = $_POST['priority'] ?? 'medium';

    if (!in_array($priority, ['low', 'medium', 'high'])) {
        $priority = 'medium';
    }

    if (empty($title)) {
        redirect("/projects/$project_id");
    }

    task_create($db, $project_id, $title, $description, $due_date, $priority);
    set_flash('success', 'Task added');
    redirect("/projects/$project_id");
}

function handle_task_reorder(PDO $db, int $project_id): void {
    $user = require_auth($db);
    $project = project_get_by_id($db, $project_id);

    if (!$project || $project['user_id'] !== $user['id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $task_ids = $input['task_ids'] ?? [];

    if (empty($task_ids)) {
        http_response_code(400);
        echo json_encode(['error' => 'No task IDs provided']);
        return;
    }

    task_reorder($db, $project_id, array_map('intval', $task_ids));

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
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

    set_flash('success', 'Task deleted');
    redirect("/projects/$project_id");
}

function handle_task_edit_page(PDO $db, int $id): void {
    $user = require_auth($db);
    $task = task_get_by_id($db, $id);

    if (!$task) {
        redirect('/projects');
    }

    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    require __DIR__ . '/../views/tasks/edit.php';
}

function handle_task_edit(PDO $db, int $id): void {
    $user = require_auth($db);
    $task = task_get_by_id($db, $id);

    if (!$task) {
        redirect('/projects');
    }

    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '') ?: null;
    $due_date = $_POST['due_date'] ?: null;
    $priority = $_POST['priority'] ?? 'medium';

    if (!in_array($priority, ['low', 'medium', 'high'])) {
        $priority = 'medium';
    }

    if (empty($title)) {
        redirect("/tasks/$id/edit");
    }

    task_update($db, $id, $title, $description, $due_date, $priority);

    set_flash('success', 'Task updated');
    redirect("/tasks/$id");
}

function handle_task_show(PDO $db, int $id): void {
    $user = require_auth($db);
    $task = task_get_by_id($db, $id);

    if (!$task) {
        redirect('/projects');
    }

    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $comments = comment_get_all_for_task($db, $id);
    $flash = get_flash('success');
    require __DIR__ . '/../views/tasks/show.php';
}

function handle_comment_create(PDO $db, int $task_id): void {
    $user = require_auth($db);
    $task = task_get_by_id($db, $task_id);

    if (!$task) {
        redirect('/projects');
    }

    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    $body = trim($_POST['body'] ?? '');

    if (!empty($body)) {
        comment_create($db, $task_id, $user['id'], $body);
        set_flash('success', 'Comment added');
    }

    redirect("/tasks/$task_id");
}

function handle_comment_delete(PDO $db, int $id): void {
    $user = require_auth($db);
    $comment = comment_get_by_id($db, $id);

    if (!$comment) {
        redirect('/projects');
    }

    $task = task_get_by_id($db, $comment['task_id']);
    $project = project_get_by_id($db, $task['project_id']);

    if (!$project || $project['user_id'] !== $user['id']) {
        redirect('/projects');
    }

    comment_delete($db, $id);
    set_flash('success', 'Comment deleted');
    redirect("/tasks/{$comment['task_id']}");
}
