<?php
// register.php - processes user registration using PDO
require_once __DIR__ . '/config/pdo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: account.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['parola'] ?? '';
$confirm = $_POST['confirma_parola'] ?? '';
$bio = trim($_POST['bio'] ?? '');
$tip_cont = $_POST['tip_cont'] ?? 'cumparator';
$nume_magazin = trim($_POST['nume_magazin'] ?? '');
$terms = isset($_POST['termeni']);

$errors = [];
if (strlen($username) < 3) $errors[] = 'Username-ul trebuie să aibă minim 3 caractere.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalid.';
if (strlen($password) < 8) $errors[] = 'Parola trebuie să aibă minim 8 caractere.';
if ($password !== $confirm) $errors[] = 'Parolele nu coincid.';
if (!in_array($tip_cont, ['cumparator', 'vanzator', 'ambele'], true)) $errors[] = 'Tipul de cont este invalid.';
if (($tip_cont === 'vanzator' || $tip_cont === 'ambele') && strlen($nume_magazin) < 2) $errors[] = 'Completeaza numele magazinului.';
if (!$terms) $errors[] = 'Trebuie să accepți termenii și condițiile.';

$avatar_path = null;
// Cerinta: upload de fisiere - primim avatarul din formular, il validam si il salvam pe server.
if (!empty($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES['avatar'];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Eroare la încărcarea fișierului.';
    } else {
        /*
         * VULNERABLE VERSION - Unrestricted File Upload demo only.
         * Do not enable this code on the public server.
         *
         * This variant accepts any uploaded file, keeps the original filename
         * and stores it in a public directory. A PHP file uploaded as avatar
         * could then be accessed directly from /uploads/avatars/.
         */
//          $destDir = __DIR__ . '/uploads/avatars';
//          if (!is_dir($destDir)) mkdir($destDir, 0755, true);
//          $fname = basename($f['name']);
//          $dest = $destDir . '/' . $fname;
//          if (!move_uploaded_file($f['tmp_name'], $dest)) {
//              $errors[] = 'Nu s-a putut salva fisierul.';
//          } else {
//              $avatar_path = 'uploads/avatars/' . $fname;
//          }


        // SECURE VERSION - active code. Allows only real image MIME types and generates a safe filename.
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            $errors[] = 'Imaginea trebuie sa fie jpg/png/gif.';
        } else {
            $ext = $allowed[$mime];
            $fname = bin2hex(random_bytes(16)) . '.' . $ext;
            $destDir = __DIR__ . '/uploads/avatars';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $dest = $destDir . '/' . $fname;
            if (!move_uploaded_file($f['tmp_name'], $dest)) {
                $errors[] = 'Nu s-a putut salva imaginea.';
            } else {
                $avatar_path = 'uploads/avatars/' . $fname;
            }
        }
    }
}

if (!empty($errors)) {
    $msg = urlencode(implode(' ', $errors));
    $params = '&username=' . urlencode($username) . '&email=' . urlencode($email) . '&bio=' . urlencode($bio);
    header('Location: account.php?register_error=' . $msg . $params);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $pdo = get_pdo();
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password_hash, role, avatar, bio) VALUES (:u, :e, :p, :r, :a, :b)');
    $stmt->execute([
        ':u' => $username,
        ':e' => $email,
        ':p' => $hash,
        ':r' => $tip_cont,
        ':a' => $avatar_path,
        ':b' => $bio,
    ]);
    header('Location: account.php?registered=1');
    exit;
} catch (PDOException $ex) {
    $err = $ex->getCode();
    $msg = 'Eroare la înregistrare.';
    if (strpos($ex->getMessage(), 'Duplicate') !== false) {
        $msg = 'Username sau email deja folosit.';
    }
    header('Location: account.php?register_error=' . urlencode($msg) . '&username=' . urlencode($username) . '&email=' . urlencode($email));
    exit;
}

