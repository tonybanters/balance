<?php

/**
 * @param PDO $db 
 * @param string $email 
 * @param string $password 
 * @param string $name 
 *
 * @return array|null 
 */
function user_create(
        PDO $db,
        string $email,
        string $password,
        string $name
    ): ?array {
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $db->prepare("
            INSERT INTO users (email, password_hash, name)
            VALUES (:email, :password_hash, :name)
        ");
        $stmt->execute([
            'email' => $email,
            'password_hash' => $hash,
            'name' => $name,
        ]);

        return [
            'id' => (int) $db->lastInsertId(),
            'email' => $email,
            'name' => $name,
        ];
    } catch (PDOException $e) {
        return null; // user already exists
    }
}

function user_authenticate(
        PDO $db,
        string $email,
        string $password
    ): ?array {
    $stmt = $db->prepare("
        SELECT id, email, password_hash, name, created_at
        FROM users WHERE email = :email
    ");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        return null;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    unset($user['password_hash']);
    return $user;
}

function user_get_by_id(PDO $db, int $id): ?array {
    $stmt = $db->prepare("
        SELECT id, email, name, created_at
        FROM users WHERE id = :id
    ");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();

    return $user ?: null;
}
