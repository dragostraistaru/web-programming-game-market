<?php
session_start();
require_once __DIR__ . '/config/mysqli.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?login_error=' . urlencode('Trebuie sa fii autentificat.'));
    exit;
}

$email = trim($_POST['email'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$role = $_POST['role'] ?? 'cumparator';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: account.php?profile_error=' . urlencode('Email invalid.'));
    exit;
}

if (!in_array($role, ['cumparator', 'vanzator', 'ambele'], true)) {
    header('Location: account.php?profile_error=' . urlencode('Rol invalid.'));
    exit;
}

try {
    $mysqli = get_mysqli();
    $stmt = $mysqli->prepare('UPDATE users SET email = ?, bio = ?, role = ? WHERE id = ?');
    $stmt->bind_param('sssi', $email, $bio, $role, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();

    $_SESSION['role'] = $role;

    header('Location: account.php?profile_updated=1');
    exit;
} catch (Throwable $e) {
    header('Location: account.php?profile_error=' . urlencode('Nu s-a putut actualiza profilul.'));
    exit;
}
