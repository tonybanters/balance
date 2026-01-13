<?php

function db_connect(string $path): PDO {
    $pdo = new PDO("sqlite:$path", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function db_run_migrations(PDO $db, string $migrations_dir): void {
    $files = glob("$migrations_dir/*.sql");
    sort($files);

    foreach ($files as $file) {
        $sql = file_get_contents($file);
        $db->exec($sql);
    }
}
