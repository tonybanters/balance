<?php

function route(string $method, string $path, PDO $db): void {
    // static routes
    $routes = [
        'GET' => [
            '/' => fn() => redirect('/projects'),
            '/login' => fn() => handle_login_page(),
            '/register' => fn() => handle_register_page(),
            '/dashboard' => fn() => handle_dashboard($db),
            '/logout' => fn() => handle_logout(),
            '/projects' => fn() => handle_projects_list($db),
            '/projects/create' => fn() => handle_project_create_page($db),
        ],
        'POST' => [
            '/login' => fn() => handle_login($db),
            '/register' => fn() => handle_register($db),
            '/projects/create' => fn() => handle_project_create($db),
        ],
    ];

    // check static routes first
    if (isset($routes[$method][$path])) {
        $routes[$method][$path]();
        return;
    }

    // dynamic routes
    if (preg_match('#^/projects/(\d+)$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'GET') {
            handle_project_show($db, $id);
            return;
        }
    }

    if (preg_match('#^/projects/(\d+)/edit$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'GET') {
            handle_project_edit_page($db, $id);
            return;
        }
        if ($method === 'POST') {
            handle_project_update($db, $id);
            return;
        }
    }

    if (preg_match('#^/projects/(\d+)/delete$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'POST') {
            handle_project_delete($db, $id);
            return;
        }
    }

    if (preg_match('#^/projects/(\d+)/tasks$#', $path, $m)) {
        $project_id = (int) $m[1];
        if ($method === 'POST') {
            handle_task_create($db, $project_id);
            return;
        }
    }

    if (preg_match('#^/tasks/(\d+)/toggle$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'POST') {
            handle_task_toggle($db, $id);
            return;
        }
    }

    if (preg_match('#^/tasks/(\d+)/delete$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'POST') {
            handle_task_delete($db, $id);
            return;
        }
    }

    http_response_code(404);
    echo "404 Not Found";
}
