<?php

function handle_login_page(): void {
    $error = get_flash('error');
    require __DIR__ . '/../views/login.php';
}

function handle_login(PDO $db): void {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = user_authenticate($db, $email, $password);

    if (!$user) {
        set_flash('error', 'invalid');
        redirect('/login');
    }

    $_SESSION['user_id'] = $user['id'];
    redirect('/projects');
}

function handle_register_page(): void {
    $error = get_flash('error');
    require __DIR__ . '/../views/register.php';
}

function handle_register(PDO $db): void {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';

    $user = user_create($db, $email, $password, $name);

    if (!$user) {
        set_flash('error', 'exists');
        redirect('/register');
    }

    redirect('/login');
}

function handle_logout(): void {
    session_destroy();
    redirect('/login');
}
