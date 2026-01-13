<?php

function task_create(
    PDO $db,
    int $project_id,
    string $title,
    ?string $description = null,
    ?string $due_date = null
): array {
    $stmt = $db->prepare("
        INSERT INTO tasks (project_id, title, description, due_date)
        VALUES (:project_id, :title, :description, :due_date)
    ");
    $stmt->execute([
        'project_id' => $project_id,
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date,
    ]);

    return [
        'id' => (int) $db->lastInsertId(),
        'project_id' => $project_id,
        'title' => $title,
        'description' => $description,
        'status' => 'pending',
        'due_date' => $due_date,
    ];
}

function task_get_all_for_project(PDO $db, int $project_id): array {
    $stmt = $db->prepare("
        SELECT * FROM tasks
        WHERE project_id = :project_id
        ORDER BY
            CASE status WHEN 'pending' THEN 0 WHEN 'done' THEN 1 END,
            due_date ASC NULLS LAST,
            created_at DESC
    ");
    $stmt->execute(['project_id' => $project_id]);
    return $stmt->fetchAll();
}

function task_get_by_id(PDO $db, int $id): ?array {
    $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $task = $stmt->fetch();
    return $task ?: null;
}

function task_update_status(PDO $db, int $id, string $status): void {
    $stmt = $db->prepare("UPDATE tasks SET status = :status WHERE id = :id");
    $stmt->execute(['id' => $id, 'status' => $status]);
}

function task_update(
    PDO $db,
    int $id,
    string $title,
    ?string $description,
    ?string $due_date
): void {
    $stmt = $db->prepare("
        UPDATE tasks
        SET title = :title, description = :description, due_date = :due_date
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => $id,
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date,
    ]);
}

function task_delete(PDO $db, int $id): void {
    $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
}
