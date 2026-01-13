<?php

function comment_create(PDO $db, int $task_id, int $user_id, string $body): array {
    $stmt = $db->prepare("
        INSERT INTO comments (task_id, user_id, body)
        VALUES (:task_id, :user_id, :body)
    ");
    $stmt->execute([
        'task_id' => $task_id,
        'user_id' => $user_id,
        'body' => $body,
    ]);

    return [
        'id' => (int) $db->lastInsertId(),
        'task_id' => $task_id,
        'user_id' => $user_id,
        'body' => $body,
        'created_at' => date('Y-m-d H:i:s'),
    ];
}

function comment_get_all_for_task(PDO $db, int $task_id): array {
    $stmt = $db->prepare("
        SELECT c.*, u.name as user_name
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.task_id = :task_id
        ORDER BY c.created_at ASC
    ");
    $stmt->execute(['task_id' => $task_id]);
    return $stmt->fetchAll();
}

function comment_delete(PDO $db, int $id): void {
    $stmt = $db->prepare("DELETE FROM comments WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

function comment_get_by_id(PDO $db, int $id): ?array {
    $stmt = $db->prepare("SELECT * FROM comments WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $comment = $stmt->fetch();
    return $comment ?: null;
}
