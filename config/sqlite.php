<?php
// SQLite database used as an additional database besides MySQL/MariaDB.
function get_sqlite_pdo() {
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    $pdo = new PDO('sqlite:' . $dataDir . '/app_extra.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS app_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT,
            event_name TEXT NOT NULL,
            created_at TEXT NOT NULL
        )'
    );

    return $pdo;
}
