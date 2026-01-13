<?php

function project_create(PDO $db, int $user_id, string $name, ?string $description = null): array {
    $stmt = $db->prepare("
        INSERT INTO projects (user_id, name, description)
        VALUES (:user_id, :name, :description)
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'name' => $name,
        'description' => $description,
    ]);

    return [
        'id' => (int) $db->lastInsertId(),
        'user_id' => $user_id,
        'name' => $name,
        'description' => $description,
    ];
}

function project_get_all_for_user(PDO $db, int $user_id): array {
    $stmt = $db->prepare("
        SELECT p.*, COUNT(t.id) as task_count
        FROM projects p
        LEFT JOIN tasks t ON t.project_id = p.id
        WHERE p.user_id = :user_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll();
}

function project_get_by_id(PDO $db, int $id): ?array {
    $stmt = $db->prepare("SELECT * FROM projects WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $project = $stmt->fetch();
    return $project ?: null;
}

function project_update(PDO $db, int $id, string $name, ?string $description): void {
    $stmt = $db->prepare("
        UPDATE projects SET name = :name, description = :description WHERE id = :id
    ");
    $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description]);
}

function project_delete(PDO $db, int $id): void {
    $db->prepare("DELETE FROM tasks WHERE project_id = :id")->execute(['id' => $id]);
    $db->prepare("DELETE FROM projects WHERE id = :id")->execute(['id' => $id]);
}

function project_search(PDO $db, int $user_id, string $query): array {
    $stmt = $db->prepare("
        SELECT * FROM projects
        WHERE user_id = :user_id
          AND (name LIKE :query OR description LIKE :query)
        ORDER BY created_at DESC
        LIMIT 20
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'query' => "%$query%",
    ]);
    return $stmt->fetchAll();
}
