<?php

function organization_create(PDO $db, string $name, int $owner_id): array {
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    $slug = trim($slug, '-');

    $base_slug = $slug;
    $counter = 1;
    while (organization_get_by_slug($db, $slug)) {
        $slug = $base_slug . '-' . $counter;
        $counter++;
    }

    $stmt = $db->prepare("
        INSERT INTO organizations (name, slug)
        VALUES (:name, :slug)
    ");
    $stmt->execute(['name' => $name, 'slug' => $slug]);

    $org_id = (int) $db->lastInsertId();

    $db->prepare("
        INSERT INTO organization_members (organization_id, user_id, role)
        VALUES (:org_id, :user_id, 'owner')
    ")->execute(['org_id' => $org_id, 'user_id' => $owner_id]);

    return [
        'id' => $org_id,
        'name' => $name,
        'slug' => $slug,
    ];
}

function organization_get_by_id(PDO $db, int $id): ?array {
    $stmt = $db->prepare("SELECT * FROM organizations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $org = $stmt->fetch();
    return $org ?: null;
}

function organization_get_by_slug(PDO $db, string $slug): ?array {
    $stmt = $db->prepare("SELECT * FROM organizations WHERE slug = :slug");
    $stmt->execute(['slug' => $slug]);
    $org = $stmt->fetch();
    return $org ?: null;
}

function organization_get_all_for_user(PDO $db, int $user_id): array {
    $stmt = $db->prepare("
        SELECT o.*, om.role
        FROM organizations o
        JOIN organization_members om ON o.id = om.organization_id
        WHERE om.user_id = :user_id
        ORDER BY o.name ASC
    ");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll();
}

function organization_get_members(PDO $db, int $org_id): array {
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.email, om.role, om.created_at as joined_at
        FROM users u
        JOIN organization_members om ON u.id = om.user_id
        WHERE om.organization_id = :org_id
        ORDER BY om.created_at ASC
    ");
    $stmt->execute(['org_id' => $org_id]);
    return $stmt->fetchAll();
}

function organization_is_member(PDO $db, int $org_id, int $user_id): bool {
    $stmt = $db->prepare("
        SELECT 1 FROM organization_members
        WHERE organization_id = :org_id AND user_id = :user_id
    ");
    $stmt->execute(['org_id' => $org_id, 'user_id' => $user_id]);
    return (bool) $stmt->fetch();
}

function organization_is_owner(PDO $db, int $org_id, int $user_id): bool {
    $stmt = $db->prepare("
        SELECT 1 FROM organization_members
        WHERE organization_id = :org_id AND user_id = :user_id AND role = 'owner'
    ");
    $stmt->execute(['org_id' => $org_id, 'user_id' => $user_id]);
    return (bool) $stmt->fetch();
}

function organization_add_member(PDO $db, int $org_id, int $user_id, string $role = 'member'): void {
    $stmt = $db->prepare("
        INSERT OR IGNORE INTO organization_members (organization_id, user_id, role)
        VALUES (:org_id, :user_id, :role)
    ");
    $stmt->execute(['org_id' => $org_id, 'user_id' => $user_id, 'role' => $role]);
}

function organization_remove_member(PDO $db, int $org_id, int $user_id): void {
    $stmt = $db->prepare("
        DELETE FROM organization_members
        WHERE organization_id = :org_id AND user_id = :user_id
    ");
    $stmt->execute(['org_id' => $org_id, 'user_id' => $user_id]);
}

function invite_create(PDO $db, int $org_id, string $email): string {
    $token = bin2hex(random_bytes(16));

    $stmt = $db->prepare("
        INSERT INTO invites (organization_id, email, token)
        VALUES (:org_id, :email, :token)
    ");
    $stmt->execute(['org_id' => $org_id, 'email' => $email, 'token' => $token]);

    return $token;
}

function invite_get_by_token(PDO $db, string $token): ?array {
    $stmt = $db->prepare("
        SELECT i.*, o.name as organization_name
        FROM invites i
        JOIN organizations o ON i.organization_id = o.id
        WHERE i.token = :token
    ");
    $stmt->execute(['token' => $token]);
    $invite = $stmt->fetch();
    return $invite ?: null;
}

function invite_delete(PDO $db, int $id): void {
    $stmt = $db->prepare("DELETE FROM invites WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

function project_get_all_for_organization(PDO $db, int $org_id): array {
    $stmt = $db->prepare("
        SELECT p.*, COUNT(t.id) as task_count
        FROM projects p
        LEFT JOIN tasks t ON t.project_id = p.id
        WHERE p.organization_id = :org_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute(['org_id' => $org_id]);
    return $stmt->fetchAll();
}
