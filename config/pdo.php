<?php
// config/pdo.php
function get_pdo() {
    $cfg = require __DIR__ . '/db_config.php';

    $host = $cfg['mysql_host'];
    $port = $cfg['mysql_port'];
    $user = $cfg['mysql_user'];
    $pass = $cfg['mysql_pass'];
    $db   = $cfg['mysql_db'];

    $dsn = "mysql:host=$host;port=$port;dbname={$db};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        throw new RuntimeException('Eroare DB PDO: ' . $e->getMessage());
    }
}

