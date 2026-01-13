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
            '/search' => fn() => handle_search($db),
            '/organizations' => fn() => handle_organizations_list($db),
            '/organizations/create' => fn() => handle_organization_create_page($db),
        ],
        'POST' => [
            '/login' => fn() => handle_login($db),
            '/register' => fn() => handle_register($db),
            '/projects/create' => fn() => handle_project_create($db),
            '/organizations/create' => fn() => handle_organization_create($db),
        ],
    ];

    // check static routes first
    if (isset($routes[$method][$path])) {
        $routes[$method][$path]();
        return;
    }

    // dynamic routes

    // Organization routes
    if (preg_match('#^/o/([a-z0-9-]+)$#', $path, $m)) {
        $slug = $m[1];
        if ($method === 'GET') {
            handle_organization_show($db, $slug);
            return;
        }
    }

    if (preg_match('#^/o/([a-z0-9-]+)/settings$#', $path, $m)) {
        $slug = $m[1];
        if ($method === 'GET') {
            handle_organization_settings($db, $slug);
            return;
        }
    }

    if (preg_match('#^/o/([a-z0-9-]+)/invite$#', $path, $m)) {
        $slug = $m[1];
        if ($method === 'POST') {
            handle_organization_invite($db, $slug);
            return;
        }
    }

    if (preg_match('#^/o/([a-z0-9-]+)/members/(\d+)/remove$#', $path, $m)) {
        $slug = $m[1];
        $member_id = (int) $m[2];
        if ($method === 'POST') {
            handle_organization_remove_member($db, $slug, $member_id);
            return;
        }
    }

    if (preg_match('#^/o/([a-z0-9-]+)/projects/create$#', $path, $m)) {
        $slug = $m[1];
        if ($method === 'GET') {
            handle_organization_project_create_page($db, $slug);
            return;
        }
        if ($method === 'POST') {
            handle_organization_project_create($db, $slug);
            return;
        }
    }

    if (preg_match('#^/invite/([a-f0-9]+)$#', $path, $m)) {
        $token = $m[1];
        if ($method === 'GET') {
            handle_invite_accept_page($db, $token);
            return;
        }
        if ($method === 'POST') {
            handle_invite_accept($db, $token);
            return;
        }
    }

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

    if (preg_match('#^/projects/(\d+)/tasks/reorder$#', $path, $m)) {
        $project_id = (int) $m[1];
        if ($method === 'POST') {
            handle_task_reorder($db, $project_id);
            return;
        }
    }

    if (preg_match('#^/tasks/(\d+)$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'GET') {
            handle_task_show($db, $id);
            return;
        }
    }

    if (preg_match('#^/tasks/(\d+)/edit$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'GET') {
            handle_task_edit_page($db, $id);
            return;
        }
        if ($method === 'POST') {
            handle_task_edit($db, $id);
            return;
        }
    }

    if (preg_match('#^/tasks/(\d+)/comments$#', $path, $m)) {
        $task_id = (int) $m[1];
        if ($method === 'POST') {
            handle_comment_create($db, $task_id);
            return;
        }
    }

    if (preg_match('#^/comments/(\d+)/delete$#', $path, $m)) {
        $id = (int) $m[1];
        if ($method === 'POST') {
            handle_comment_delete($db, $id);
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
