<?php

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header("Location: $url");
    exit;
}

function get_flash(string $key): ?string {
    $value = $_SESSION["flash_$key"] ?? null;
    unset($_SESSION["flash_$key"]);
    return $value;
}

function set_flash(string $key, string $value): void {
    $_SESSION["flash_$key"] = $value;
}

function require_auth(PDO $db): array {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        redirect('/login');
    }

    $user = user_get_by_id($db, $user_id);

    if (!$user) {
        session_destroy();
        redirect('/login');
    }

    return $user;
}
