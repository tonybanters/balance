<?php

session_start();

require __DIR__ . '/../db.php';
require __DIR__ . '/../helpers.php';
require __DIR__ . '/../models/user.php';
require __DIR__ . '/../models/project.php';
require __DIR__ . '/../models/task.php';
require __DIR__ . '/../models/comment.php';
require __DIR__ . '/../models/organization.php';
require __DIR__ . '/../controllers/auth.php';
require __DIR__ . '/../controllers/dashboard.php';
require __DIR__ . '/../controllers/projects.php';
require __DIR__ . '/../controllers/tasks.php';
require __DIR__ . '/../controllers/search.php';
require __DIR__ . '/../controllers/organizations.php';
require __DIR__ . '/../router.php';

$db = db_connect(__DIR__ . '/../balance.db');
db_run_migrations($db, __DIR__ . '/../migrations');

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

route($method, $path, $db);
