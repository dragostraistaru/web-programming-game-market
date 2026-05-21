<?php
// logout.php
session_start();
require_once __DIR__ . '/config/pdo.php';

// clear session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();

// Cerinta: Remember me - la logout stergem cookie-ul persistent si tokenul din baza de date.
if (!empty($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    try {
        $pdo = get_pdo();
        $stmt = $pdo->prepare('UPDATE users SET remember_token = NULL WHERE remember_token = :token');
        $stmt->execute([':token' => $token]);
    } catch (Throwable $e) {
        // ignore
    }
    setcookie('rememberme', '', time() - 3600, '/', '', false, true);
}

header('Location: account.php?logged_out=1');
exit;

