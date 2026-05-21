<?php
// login.php - processes login using PDO/SQLite
session_start();
require_once __DIR__ . '/config/pdo.php';

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
    $msg = 'Completeaza email si parola.';
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
    $pdo = get_pdo();

    /*
     * VULNERABLE VERSION - SQL Injection demo only.
     * Do not enable this code on the public server.
     *
     * Exploit payload for login_email:
     * x@x.com'OR'1'='1'--
     *
     * With that payload, the query becomes true for the first user found and
     * the password check is bypassed because this vulnerable variant trusts the
     * returned row directly.
     */
//      $sql = "SELECT id, username, password_hash, role FROM users
//              WHERE email = '$email' AND password_hash = '$password'
//              LIMIT 1";
//      $user = $pdo->query($sql)->fetch();
//
//      if (!$user) {
//          unset($_SESSION['login_captcha_question'], $_SESSION['login_captcha_answer']);
//          $msg = 'Email sau parola incorecta.';
//          header('Location: account.php?login_error=' . urlencode($msg) . '&login_email=' . urlencode($email));
//          exit;
//      }


    // SECURE VERSION - active code. Uses prepared statements and verifies the password hash.
    $stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        unset($_SESSION['login_captcha_question'], $_SESSION['login_captcha_answer']);
        $msg = 'Email sau parola incorecta.';
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
        $stmt = $pdo->prepare('UPDATE users SET remember_token = :token WHERE id = :id');
        $stmt->execute([
            ':token' => $token,
            ':id' => $user['id'],
        ]);
        // set cookie (HttpOnly)
        setcookie('rememberme', $token, $exp, '/', '', false, true);
    }

    header('Location: account.php');
    exit;

} catch (Exception $e) {
    // in dev show message; in production, log this instead
    $msg = 'Eroare interna. Incearca din nou.';
    header('Location: account.php?login_error=' . urlencode($msg));
    exit;
}
