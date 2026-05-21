<?php
// avatar_file.php - controlled file read endpoint for Path Traversal lab demo

$file = $_GET['file'] ?? '';

if ($file === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Lipseste parametrul file.';
    exit;
}

/*
 * VULNERABLE VERSION - Path Traversal demo only.
 * Do not enable this code on the public server.
 *
 * Exploit payload:
 * avatar_file.php?file=../../db_init.sql
 *
 * This variant concatenates user input directly into a filesystem path. The
 * ../ sequences can escape uploads/avatars and read files from the project.
 */
//  $path = __DIR__ . '/uploads/avatars/' . $file;
//  if (!is_file($path)) {
//      http_response_code(404);
//      header('Content-Type: text/plain; charset=UTF-8');
//      echo 'Fisier negasit.';
//      exit;
//  }
//  header('Content-Type: text/plain; charset=UTF-8');
//  readfile($path);
//  exit;


// SECURE VERSION - active code. Resolve the path and require it to stay inside uploads/avatars.
$baseDir = realpath(__DIR__ . '/uploads/avatars');
$requestedPath = realpath($baseDir . DIRECTORY_SEPARATOR . $file);

if (
    !$baseDir ||
    !$requestedPath ||
    strpos($requestedPath, $baseDir . DIRECTORY_SEPARATOR) !== 0 ||
    !is_file($requestedPath)
) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Fisier negasit.';
    exit;
}

$mime = mime_content_type($requestedPath);
header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
readfile($requestedPath);
