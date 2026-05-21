<?php
session_start();
require_once __DIR__ . '/config/pdo.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?login_error=' . urlencode('Trebuie sa fii autentificat.'));
    exit;
}

/*
 * VULNERABLE VERSION - CSRF demo only.
 * Do not enable this variant on the public server.
 *
 * The old endpoint accepted any POST request from a logged-in browser without
 * checking whether the request came from the real profile form.
 */

// SECURE VERSION - active code. Require the per-session CSRF token from account.php.
$csrfToken = $_POST['csrf_token'] ?? '';
if (
    empty($_SESSION['csrf_token']) ||
    !is_string($csrfToken) ||
    !hash_equals($_SESSION['csrf_token'], $csrfToken)
) {
    header('Location: account.php?profile_error=' . urlencode('Cerere invalida CSRF.'));
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
    $pdo = get_pdo();
    $stmt = $pdo->prepare('UPDATE users SET email = :email, bio = :bio, role = :role WHERE id = :id');
    $stmt->execute([
        ':email' => $email,
        ':bio' => $bio,
        ':role' => $role,
        ':id' => $_SESSION['user_id'],
    ]);

    $_SESSION['role'] = $role;

    header('Location: account.php?profile_updated=1');
    exit;
} catch (Throwable $e) {
    header('Location: account.php?profile_error=' . urlencode('Nu s-a putut actualiza profilul.'));
    exit;
}
