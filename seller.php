<?php
session_start();
require_once __DIR__ . '/config/mysqli.php';

function e($v) {
    return htmlspecialchars($v !== null ? $v : '', ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?login_error=' . urlencode('Trebuie sa fii autentificat pentru zona de vanzator.'));
    exit;
}

if (!in_array($_SESSION['role'] ?? '', ['vanzator', 'ambele'], true)) {
    http_response_code(403);
    $access_denied = true;
} else {
    $access_denied = false;
}

$message = '';
$errors = [];
$listings = [];

if (!$access_denied) {
    try {
        $mysqli = get_mysqli();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $game_title = trim($_POST['game_title'] ?? '');
            $platform = trim($_POST['platform'] ?? '');
            $key_type = trim($_POST['key_type'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $stock = trim($_POST['stock'] ?? '');

            if ($game_title === '') $errors[] = 'Completeaza numele jocului.';
            if ($platform === '') $errors[] = 'Completeaza platforma.';
            if ($key_type === '') $errors[] = 'Completeaza tipul cheii.';
            if (!is_numeric($price) || (float)$price <= 0) $errors[] = 'Pretul trebuie sa fie mai mare decat 0.';
            if (!ctype_digit($stock) || (int)$stock < 1) $errors[] = 'Stocul trebuie sa fie minim 1.';

            if (empty($errors)) {
                $stmt = $mysqli->prepare(
                    'INSERT INTO seller_listings (seller_id, game_title, platform, key_type, price, stock)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                $price_value = (float)$price;
                $stock_value = (int)$stock;
                $stmt->bind_param('isssdi', $_SESSION['user_id'], $game_title, $platform, $key_type, $price_value, $stock_value);
                $stmt->execute();
                $stmt->close();
                $message = 'Oferta a fost adaugata.';
            }
        }

        $stmt = $mysqli->prepare(
            'SELECT id, game_title, platform, key_type, price, stock, status, created_at
             FROM seller_listings
             WHERE seller_id = ?
             ORDER BY created_at DESC'
        );
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $listings[] = $row;
        }
        $stmt->close();
    } catch (Throwable $e) {
        $errors[] = 'Eroare la baza de date.';
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMarket - Seller</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>

<h1>GameMarket</h1>
<h2>Zona vanzator</h2>

<div class="main-menu">
    <a href="listing.php">Games &amp; Deals</a>
    <a href="forum.php">Forum</a>
    <a href="account.php">Cont</a>
    <a href="logout.php">Logout</a>
</div>

<?php if ($access_denied): ?>
    <p>Acces interzis. Aceasta pagina este disponibila doar pentru vanzatori.</p>
<?php else: ?>
    <p>Bun venit, <?php echo e($_SESSION['username'] ?? 'vanzator'); ?>. Aici poti adauga oferte pentru jocuri.</p>

    <?php if ($message): ?>
        <p><?php echo e($message); ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="seller.php">
        <fieldset>
            <legend>Adauga oferta</legend>

            <p>
                <label for="game_title">Nume joc</label><br>
                <input type="text" id="game_title" name="game_title" maxlength="100" required>
            </p>

            <p>
                <label for="platform">Platforma</label><br>
                <input type="text" id="platform" name="platform" maxlength="50" required>
            </p>

            <p>
                <label for="key_type">Tip cheie</label><br>
                <input type="text" id="key_type" name="key_type" maxlength="50" required>
            </p>

            <p>
                <label for="price">Pret</label><br>
                <input type="number" id="price" name="price" min="0.01" step="0.01" required>
            </p>

            <p>
                <label for="stock">Stoc</label><br>
                <input type="number" id="stock" name="stock" min="1" value="1" required>
            </p>

            <p>
                <button type="submit">Publica oferta</button>
            </p>
        </fieldset>
    </form>

    <h3>Ofertele mele</h3>

    <?php if (empty($listings)): ?>
        <p>Nu ai adaugat inca nicio oferta.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Joc</th>
                <th>Platforma</th>
                <th>Tip cheie</th>
                <th>Pret</th>
                <th>Stoc</th>
                <th>Status</th>
            </tr>
            <?php foreach ($listings as $listing): ?>
                <tr>
                    <td><?php echo e($listing['game_title']); ?></td>
                    <td><?php echo e($listing['platform']); ?></td>
                    <td><?php echo e($listing['key_type']); ?></td>
                    <td><?php echo e($listing['price']); ?></td>
                    <td><?php echo e($listing['stock']); ?></td>
                    <td><?php echo e($listing['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
