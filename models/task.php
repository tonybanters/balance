<?php

function task_create(
    PDO $db,
    int $project_id,
    string $title,
    ?string $description = null,
    ?string $due_date = null,
    string $priority = 'medium'
): array {
    $max_pos = $db->query("SELECT COALESCE(MAX(position), 0) FROM tasks WHERE project_id = $project_id")->fetchColumn();
    $position = $max_pos + 1;

    $stmt = $db->prepare("
        INSERT INTO tasks (project_id, title, description, due_date, priority, position)
        VALUES (:project_id, :title, :description, :due_date, :priority, :position)
    ");
    $stmt->execute([
        'project_id' => $project_id,
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date,
        'priority' => $priority,
        'position' => $position,
    ]);

    return [
        'id' => (int) $db->lastInsertId(),
        'project_id' => $project_id,
        'title' => $title,
        'description' => $description,
        'status' => 'pending',
        'due_date' => $due_date,
        'priority' => $priority,
        'position' => $position,
    ];
}

function task_get_all_for_project(PDO $db, int $project_id, ?string $filter = null): array {
    $where = "project_id = :project_id";
    $params = ['project_id' => $project_id];

    if ($filter === 'today') {
        $where .= " AND due_date = :today AND status = 'pending'";
        $params['today'] = date('Y-m-d');
    } elseif ($filter === 'overdue') {
        $where .= " AND due_date < :today AND status = 'pending'";
        $params['today'] = date('Y-m-d');
    } elseif ($filter === 'high') {
        $where .= " AND priority = 'high' AND status = 'pending'";
    } elseif ($filter === 'pending') {
        $where .= " AND status = 'pending'";
    }

    $stmt = $db->prepare("
        SELECT * FROM tasks
        WHERE $where
        ORDER BY
            CASE status WHEN 'pending' THEN 0 WHEN 'done' THEN 1 END,
            position ASC,
            created_at DESC
    ");
    $stmt->execute($params);
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
    ?string $due_date,
    string $priority = 'medium'
): void {
    $stmt = $db->prepare("
        UPDATE tasks
        SET title = :title, description = :description, due_date = :due_date, priority = :priority
        WHERE id = :id
    ");
    $stmt->execute([
        'id' => $id,
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date,
        'priority' => $priority,
    ]);
}

function task_update_position(PDO $db, int $id, int $position): void {
    $stmt = $db->prepare("UPDATE tasks SET position = :position WHERE id = :id");
    $stmt->execute(['id' => $id, 'position' => $position]);
}

function task_reorder(PDO $db, int $project_id, array $task_ids): void {
    $position = 1;
    foreach ($task_ids as $task_id) {
        $stmt = $db->prepare("UPDATE tasks SET position = :position WHERE id = :id AND project_id = :project_id");
        $stmt->execute(['position' => $position, 'id' => $task_id, 'project_id' => $project_id]);
        $position++;
    }
}

function task_delete(PDO $db, int $id): void {
    $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

function task_search(PDO $db, int $user_id, string $query): array {
    $stmt = $db->prepare("
        SELECT t.*, p.name as project_name
        FROM tasks t
        JOIN projects p ON t.project_id = p.id
        WHERE p.user_id = :user_id
          AND (t.title LIKE :query OR t.description LIKE :query)
        ORDER BY t.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'query' => "%$query%",
    ]);
    return $stmt->fetchAll();
}
