<?php
session_start();
// Auto-login using rememberme cookie if session not set
require_once __DIR__ . '/config/mysqli.php';
if (empty($_SESSION['user_id']) && !empty($_COOKIE['rememberme'])) {
    try {
        $token = $_COOKIE['rememberme'];
        $mysqli = get_mysqli();
        $stmt = $mysqli->prepare('SELECT id, username, role FROM users WHERE remember_token = ? LIMIT 1');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $res = $stmt->get_result();
        $u = $res->fetch_assoc();
        $stmt->close();
        if ($u) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['role'] = $u['role'];
        }
    } catch (Exception $e) {
        // ignore auto-login errors
    }
}

$login_error = isset($_GET['login_error']) ? $_GET['login_error'] : null;
$register_error = isset($_GET['register_error']) ? $_GET['register_error'] : null;
$registered = isset($_GET['registered']);
$logout = isset($_GET['logged_out']);

function e($v) { return htmlspecialchars($v !== null ? $v : '', ENT_QUOTES, 'UTF-8'); }

$current_user = null;
if (!empty($_SESSION['user_id'])) {
    try {
        $mysqli = get_mysqli();
        $stmt = $mysqli->prepare('SELECT id, username, email, role, avatar, bio, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $current_user = $res->fetch_assoc();
        $stmt->close();
    } catch (Exception $e) {
        $current_user = null;
    }
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMarket - Cont</title>
    <link rel="stylesheet" href="style3.css">
    <style>
        .auth-container {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 24px;
            max-width: 1000px;
            margin: 24px auto;
        }

        .auth-box {
            flex: 1;
            min-width: 300px;
            background-color: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }

        .auth-box h3 {
            margin-bottom: 20px;
        }

        .camp {
            margin-bottom: 14px;
        }

        .label {
            display: block;
            font-size: 0.88em;
            font-weight: bold;
            margin-bottom: 4px;
            color: var(--text);
        }

        .auth-box input[type="text"],
        .auth-box input[type="email"],
        .auth-box input[type="password"],
        .auth-box input[type="date"],
        .auth-box input[type="file"],
        .auth-box input[type="number"],
        .auth-box select,
        .auth-box textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background-color: white;
            color: var(--text);
            font-family: var(--font);
            font-size: 0.95em;
            transition: border-color 0.2s;
        }

        .auth-box input:focus,
        .auth-box select:focus,
        .auth-box textarea:focus {
            outline: none;
            border-color: var(--accent);
        }

        .invalid {
            border-color: #dc2626 !important;
            background-color: #fff5f5 !important;
        }

        .eroare {
            color: #dc2626;
            font-size: 0.78em;
            margin-top: 3px;
            display: none;
        }

        .eroare.vizibil {
            display: block;
        }

        .succes-msg {
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            padding: 10px 14px;
            border-radius: var(--radius);
            margin-bottom: 14px;
            display: none;
            font-size: 0.9em;
        }

        .succes-msg.vizibil {
            display: block;
        }

        .separator {
            border: none;
            border-top: 1px solid var(--border);
            margin: 16px 0;
        }

        .auth-box input[type="submit"] {
            width: 100%;
            padding: 11px;
            background-color: var(--menu);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 6px;
        }

        .auth-box input[type="submit"]:hover {
            background-color: var(--menu-hover);
        }

        .putere-wrap {
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
        }

        .putere-bar {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: width 0.3s, background-color 0.3s;
        }

        .putere-label {
            font-size: 0.72em;
            color: var(--muted);
            margin-top: 2px;
        }

        .profile-box {
            max-width: 720px;
            margin: 24px auto;
            background-color: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }

        .profile-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--border);
            background-color: #f3f4f6;
        }

        .profile-actions {
            margin-top: 18px;
        }

        .profile-actions a {
            display: inline-block;
            padding: 10px 16px;
            background-color: var(--menu);
            color: white;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: bold;
        }

        .profile-actions a:hover {
            background-color: var(--menu-hover);
        }
    </style>
</head>
<body>

<h1>GameMarket</h1>
<h2>Contul meu</h2>

<p>
    <b>Bun venit!</b>
    <?php if ($current_user): ?>
        Ești autentificat ca <?php echo e($current_user['username']); ?>.
    <?php else: ?>
        Autentifică-te sau creează un cont nou.
    <?php endif; ?>
    <br>
    <span title="Info">Contul îți permite să cumperi chei și să postezi pe forum.</span>
</p>

<div class="main-menu">
    <a href="forum.php" id="homeLink" target="_self" title="Home">Home</a>
    &nbsp;|&nbsp;
    <a href="listing.php" id="listingLink" target="_blank" title="Browse games">Games &amp; Deals</a>
    &nbsp;|&nbsp;
    <a href="forum.php#newpost" id="forumLink" target="_self" title="Forum">Forum</a>
    &nbsp;|&nbsp;
    <a href="widgets.php" id="dashboardLink" target="_self" title="Dashboard">Dashboard</a>
    <?php if ($current_user): ?>
        &nbsp;|&nbsp;
        <a href="logout.php" title="Logout">Logout</a>
    <?php endif; ?>
</div>

<img
        src="images/logo.png"
        width="200"
        height="80"
        alt="GameMarket Logo"
        title="GameMarket - official logo">

<?php if ($logout): ?>
    <div class="profile-box">
        Ai fost delogat cu succes.
    </div>
<?php endif; ?>

<?php if ($current_user): ?>
    <div class="profile-box">
        <h3>Profilul meu</h3>

        <div class="profile-header">
            <?php if (!empty($current_user['avatar'])): ?>
                <img class="profile-avatar" src="<?php echo e($current_user['avatar']); ?>" alt="Avatar profil">
            <?php else: ?>
                <img class="profile-avatar" src="images/logo.png" alt="Avatar implicit">
            <?php endif; ?>

            <div>
                <p><strong>Username:</strong> <?php echo e($current_user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo e($current_user['email']); ?></p>
                <p><strong>Rol:</strong> <?php echo e($current_user['role']); ?></p>
                <p><strong>Creat la:</strong> <?php echo e($current_user['created_at']); ?></p>
            </div>
        </div>

        <p><strong>Bio:</strong> <?php echo e($current_user['bio'] ?: 'Nu ai completat inca un bio.'); ?></p>

        <div class="profile-actions">
            <a href="logout.php">Logout</a>
        </div>
    </div>
<?php else: ?>
<div class="auth-container">
    <div class="auth-box">
        <h3>Autentificare</h3>

        <div id="succes-login" class="succes-msg <?php echo ($registered ? 'vizibil' : ''); ?>">
            <?php if ($registered): ?>Cont creat cu succes! Te poți autentifica acum.<?php endif; ?>
        </div>

        <?php if ($login_error): ?>
            <div class="eroare vizibil"><?php echo e($login_error); ?></div>
        <?php endif; ?>

        <form id="formLogin" action="login.php" method="post" name="loginForm" novalidate>
            <fieldset>
                <legend>Login</legend>

                <div class="camp">
                    <label class="label" for="login-email">Email</label>
                    <input
                            type="email"
                            id="login-email"
                            name="login_email"
                            value="<?php echo e(isset($_GET['login_email']) ? $_GET['login_email'] : ''); ?>"
                            maxlength="100"
                            title="Adresa ta de email">
                    <span class="eroare" id="err-login-email">Introdu un email valid.</span>
                </div>

                <div class="camp">
                    <label class="label" for="login-parola">Parolă</label>
                    <input
                            type="password"
                            id="login-parola"
                            name="login_parola"
                            value=""
                            maxlength="50"
                            title="Parola contului tău">
                    <span class="eroare" id="err-login-parola">Parola nu poate fi goală.</span>
                </div>

                <div class="camp">
                    <input type="checkbox" name="tine_minte" id="tine-minte" value="yes" title="Ține-mă minte">
                    <label for="tine-minte"> Ține-mă minte</label>
                </div>

                <div class="camp">
                    <input type="submit" name="login_submit" value="Autentifică-te" title="Submit login">
                </div>
            </fieldset>
        </form>
    </div>

    <div class="auth-box">
        <h3>Creează cont nou</h3>

        <div id="succes-register" class="succes-msg <?php echo ($registered ? 'vizibil' : ''); ?>">
            <?php if ($registered): ?>Cont creat cu succes! Verifică emailul pentru confirmare.<?php endif; ?>
        </div>

        <?php if ($register_error): ?>
            <div class="eroare vizibil"><?php echo e($register_error); ?></div>
        <?php endif; ?>

        <form id="formRegister" action="register.php" method="post" name="registerForm" enctype="multipart/form-data" novalidate>
            <fieldset>
                <legend>Înregistrare</legend>

                <div class="camp">
                    <label class="label" for="username">Username</label>
                    <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo e(isset($_GET['username']) ? $_GET['username'] : ''); ?>"
                            maxlength="30"
                            title="Numele tău de utilizator (minim 3 caractere)">
                    <span class="eroare" id="err-username">Username-ul trebuie să aibă minim 3 caractere.</span>
                </div>

                <div class="camp">
                    <label class="label" for="email">Email</label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo e(isset($_GET['email']) ? $_GET['email'] : ''); ?>"
                            maxlength="100"
                            title="Adresa ta de email">
                    <span class="eroare" id="err-email">Introdu un email valid (ex: user@email.com).</span>
                </div>

                <div class="camp">
                    <label class="label" for="parola">Parolă</label>
                    <input
                            type="password"
                            id="parola"
                            name="parola"
                            value=""
                            maxlength="50"
                            title="Minim 8 caractere">
                    <div class="putere-wrap">
                        <div class="putere-bar" id="putere-bar"></div>
                    </div>
                    <span class="putere-label" id="putere-label"></span>
                    <span class="eroare" id="err-parola">Parola trebuie să aibă minim 8 caractere.</span>
                </div>

                <div class="camp">
                    <label class="label" for="confirma-parola">Confirmă parola</label>
                    <input
                            type="password"
                            id="confirma-parola"
                            name="confirma_parola"
                            value=""
                            maxlength="50"
                            title="Repetă parola">
                    <span class="eroare" id="err-confirma">Parolele nu coincid.</span>
                </div>

                <hr class="separator">

                <div class="camp">
                    <label class="label" for="data-nasterii">Data nașterii</label>
                    <input
                            type="date"
                            id="data-nasterii"
                            name="data_nasterii"
                            title="Data ta de naștere">
                    <span class="eroare" id="err-data">Data nașterii este obligatorie și nu poate fi în viitor.</span>
                </div>

                <div class="camp">
                    <label class="label">Gen</label>
                    <input type="radio" name="gen" value="m" id="gen-m" checked title="Masculin">
                    <label for="gen-m">Masculin</label>
                    &nbsp;
                    <input type="radio" name="gen" value="f" id="gen-f" title="Feminin">
                    <label for="gen-f">Feminin</label>
                    &nbsp;
                    <input type="radio" name="gen" value="altul" id="gen-altul" title="Altul">
                    <label for="gen-altul">Altul</label>
                </div>

                <div class="camp">
                    <label class="label" for="tara">Țară</label>
                    <select id="tara" name="tara" title="Selectează țara ta">
                        <option value="" selected>-- Selectează țara --</option>
                        <option value="RO">România</option>
                        <option value="MD">Moldova</option>
                        <option value="DE">Germania</option>
                        <option value="FR">Franța</option>
                        <option value="UK">Marea Britanie</option>
                        <option value="US">SUA</option>
                        <option value="OTHER">Alta</option>
                    </select>
                    <span class="eroare" id="err-tara">Selectează o țară.</span>
                </div>

                <div class="camp">
                    <label class="label" for="oras">Oraș / Regiune</label>
                    <select id="oras" name="oras" title="Selectează orașul sau regiunea">
                        <option value="">-- Selectează mai întâi țara --</option>
                    </select>
                    <span class="eroare" id="err-oras">Selectează un oraș sau o regiune.</span>
                </div>

                <div class="camp">
                    <label class="label" for="tip-cont">Tip cont</label>
                    <select id="tip-cont" name="tip_cont" title="Alege tipul de cont" size="1">
                        <option value="cumparator" selected>Cumpărător</option>
                        <option value="vanzator">Vânzător</option>
                        <option value="ambele">Ambele</option>
                    </select>
                </div>

                <div class="camp" id="camp-magazin" style="display: none;">
                    <label class="label" for="nume-magazin">Nume magazin</label>
                    <input
                            type="text"
                            id="nume-magazin"
                            name="nume_magazin"
                            value=""
                            maxlength="50"
                            title="Numele magazinului tău">
                    <span class="eroare" id="err-magazin">Completează numele magazinului.</span>
                </div>

                <div class="camp">
                    <label class="label" for="avatar">Avatar (imagine profil)</label>
                    <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            accept="image/*"
                            title="Încarcă o imagine de profil">
                    <span class="eroare" id="err-avatar">Fișierul trebuie să fie o imagine (jpg, png, gif).</span>
                </div>

                <div class="camp">
                    <label class="label" for="bio">Bio (opțional)</label>
                    <textarea
                            id="bio"
                            name="bio"
                            rows="3"
                            maxlength="200"
                            title="Scrie câteva cuvinte despre tine"><?php echo e(isset($_GET['bio']) ? $_GET['bio'] : 'Pasionat de jocuri, caut chei la prețuri corecte.'); ?></textarea>
                </div>

                <hr class="separator">

                <div class="camp">
                    <input type="checkbox" name="termeni" id="termeni" value="yes" title="Accept termenii">
                    <label for="termeni"> Am citit și accept <strong>termenii și condițiile</strong>.</label>
                    <span class="eroare" id="err-termeni">Trebuie să accepți termenii și condițiile.</span>
                </div>

                <div class="camp">
                    <input type="checkbox" name="newsletter" id="newsletter" value="yes" title="Newsletter">
                    <label for="newsletter"> Vreau să primesc oferte și noutăți pe email.</label>
                </div>

                <div class="camp">
                    <input type="submit" name="register_submit" value="Creează cont" title="Submit register">
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?php endif; ?>

<br>
<h3 title="Footer">GameMarket 2026 &mdash; Contul meu</h3>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="account-data.js"></script>
<script src="Account.js"></script>

</body>
</html>



