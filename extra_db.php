<?php
session_start();
require_once __DIR__ . '/config/sqlite.php';

function e($v) {
    return htmlspecialchars($v !== null ? $v : '', ENT_QUOTES, 'UTF-8');
}

$events = [];
$error = null;

try {
    $pdo = get_sqlite_pdo();
    $username = $_SESSION['username'] ?? 'guest';

    $stmt = $pdo->prepare('INSERT INTO app_events (username, event_name, created_at) VALUES (:username, :event_name, :created_at)');
    $stmt->execute([
        ':username' => $username,
        ':event_name' => 'Acces pagina baza de date aditionala',
        ':created_at' => date('Y-m-d H:i:s'),
    ]);

    $stmt = $pdo->query('SELECT username, event_name, created_at FROM app_events ORDER BY id DESC LIMIT 10');
    $events = $stmt->fetchAll();
} catch (Throwable $e) {
    $error = 'Nu s-a putut conecta la baza de date SQLite.';
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMarket - SQLite</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>

<h1>GameMarket</h1>
<h2>Baza de date aditionala SQLite</h2>

<div class="main-menu">
    <a href="listing.php">Games &amp; Deals</a>
    <a href="account.php">Cont</a>
    <a href="seller.php">Seller</a>
</div>

<?php if ($error): ?>
    <p><?php echo e($error); ?></p>
<?php else: ?>
    <p>Aceasta pagina foloseste o baza de date SQLite separata de MySQL/MariaDB.</p>

    <table>
        <tr>
            <th>Utilizator</th>
            <th>Eveniment</th>
            <th>Data</th>
        </tr>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?php echo e($event['username']); ?></td>
                <td><?php echo e($event['event_name']); ?></td>
                <td><?php echo e($event['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
