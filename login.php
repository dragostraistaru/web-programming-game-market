<?php
// login.php - processes login using MySQLi
session_start();
require_once __DIR__ . '/config/mysqli.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: account.php');
    exit;
}

$email = isset($_POST['login_email']) ? trim($_POST['login_email']) : '';
$password = isset($_POST['login_parola']) ? $_POST['login_parola'] : '';
$captcha = isset($_POST['login_captcha']) ? trim($_POST['login_captcha']) : '';
// Cerinta: Remember me - verificam daca utilizatorul a bifat optiunea din formularul de login.
$tine_minte = isset($_POST['tine_minte']) && $_POST['tine_minte'] === 'yes';

if ($email === '' || $password === '') {
    $msg = 'Completează email și parolă.';
    header('Location: account.php?login_error=' . urlencode($msg) . '&login_email=' . urlencode($email));
    exit;
}

if ($captcha === '' || !isset($_SESSION['login_captcha_answer']) || !hash_equals($_SESSION['login_captcha_answer'], $captcha)) {
    unset($_SESSION['login_captcha_question'], $_SESSION['login_captcha_answer']);
    $msg = 'Raspuns CAPTCHA invalid.';
    header('Location: account.php?login_error=' . urlencode($msg) . '&login_email=' . urlencode($email));
    exit;
}

try {
    $mysqli = get_mysqli();
    $stmt = $mysqli->prepare('SELECT id, username, password_hash, role FROM users WHERE email = ? LIMIT 1');
    if (!$stmt) throw new RuntimeException('Eroare DB prepare');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        unset($_SESSION['login_captcha_question'], $_SESSION['login_captcha_answer']);
        $msg = 'Email sau parolă incorectă.';
        header('Location: account.php?login_error=' . urlencode($msg) . '&login_email=' . urlencode($email));
        exit;
    }

    // login OK
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    unset($_SESSION['login_captcha_question'], $_SESSION['login_captcha_answer']);

    if ($tine_minte) {
        // Cerinta: Remember me - generam token il salvam in baza de date si setam cookie persistent 30 de zile.
        if (function_exists('random_bytes')) {
            $token = bin2hex(random_bytes(32));
        } else {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }
        $exp = time() + (30 * 24 * 60 * 60);
        $stmt = $mysqli->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
        $stmt->bind_param('si', $token, $user['id']);
        $stmt->execute();
        $stmt->close();
        // set cookie (HttpOnly)
        setcookie('rememberme', $token, $exp, '/', '', false, true);
    }

    header('Location: account.php');
    exit;

} catch (Exception $e) {
    // in dev show message; in production, log this instead
    $msg = 'Eroare internă. Încearcă din nou.';
    header('Location: account.php?login_error=' . urlencode($msg));
    exit;
}

