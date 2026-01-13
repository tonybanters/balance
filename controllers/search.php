<?php

function handle_search(PDO $db): void {
    $user = require_auth($db);

    $query = trim($_GET['q'] ?? '');

    if (empty($query)) {
        $projects = [];
        $tasks = [];
    } else {
        $projects = project_search($db, $user['id'], $query);
        $tasks = task_search($db, $user['id'], $query);
    }

    require __DIR__ . '/../views/search.php';
}
