<?php
session_start();
require_once __DIR__ . '/config/mysqli.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?login_error=' . urlencode('Trebuie sa fii autentificat.'));
    exit;
}

try {
    $mysqli = get_mysqli();

    // Cerinta: stergere fisiere incarcate - cautam avatarul utilizatorului autentificat.
    $stmt = $mysqli->prepare('SELECT avatar FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!empty($user['avatar'])) {
        // Cerinta: stergere fisiere incarcate - stergem fisierul avatar de pe server.
        $avatarPath = str_replace('\\', '/', $user['avatar']);
        $allowedPrefix = 'uploads/avatars/';

        if (strpos($avatarPath, $allowedPrefix) === 0) {
            $fullPath = __DIR__ . '/' . $avatarPath;
            $realUploadsDir = realpath(__DIR__ . '/uploads/avatars');
            $realFile = realpath($fullPath);

            if ($realUploadsDir && $realFile && strpos($realFile, $realUploadsDir . DIRECTORY_SEPARATOR) === 0 && is_file($realFile)) {
                unlink($realFile);
            }
        }

        // Cerinta: stergere fisiere incarcate - eliminam calea avatarului din baza de date.
        $stmt = $mysqli->prepare('UPDATE users SET avatar = NULL WHERE id = ?');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: account.php?avatar_deleted=1');
    exit;
} catch (Throwable $e) {
    header('Location: account.php?avatar_error=1');
    exit;
}
