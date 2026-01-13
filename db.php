<?php

function db_connect(string $path): PDO {
    $pdo = new PDO("sqlite:$path", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function db_run_migrations(PDO $db, string $migrations_dir): void {
    $db->exec("CREATE TABLE IF NOT EXISTS migrations (name TEXT PRIMARY KEY)");

    $files = glob("$migrations_dir/*.sql");
    sort($files);

    foreach ($files as $file) {
        $name = basename($file);
        $stmt = $db->prepare("SELECT 1 FROM migrations WHERE name = :name");
        $stmt->execute(['name' => $name]);

        if ($stmt->fetch()) {
            continue;
        }

        $sql = file_get_contents($file);
        $db->exec($sql);
        $db->prepare("INSERT INTO migrations (name) VALUES (:name)")->execute(['name' => $name]);
    }
}
