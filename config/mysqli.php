<?php
// config/mysqli.php
function get_mysqli() {
    $cfg = require __DIR__ . '/db_config.php';

    $host = $cfg['mysql_host'];
    $port = $cfg['mysql_port'];
    $user = $cfg['mysql_user'];
    $pass = $cfg['mysql_pass'];
    $db   = $cfg['mysql_db'];

    $mysqli = new mysqli($host, $user, $pass, $db, $port);
    if ($mysqli->connect_errno) {
        // In production, log errors instead of displaying
        throw new RuntimeException('Eroare DB MySQLi: ' . $mysqli->connect_error);
    }
    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}

