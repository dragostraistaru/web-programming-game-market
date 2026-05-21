<?php
// Backwards-compatible wrapper for pages that used the old SQLite helper.
require_once __DIR__ . '/pdo.php';

function get_sqlite_pdo() {
    return get_pdo();
}
