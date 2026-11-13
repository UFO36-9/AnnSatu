<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    verify_csrf(isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : null);
    $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    try {
        $stmt = db()->prepare('SELECT id, full_name, password_hash FROM employees WHERE email = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$email]);
        $employee = $stmt->fetch();
        if ($employee && password_verify($password, $employee['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['employee_id'] = (int) $employee['id'];
            $_SESSION['employee_name'] = $employee['full_name'];
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            header('Location: admin.php');
            exit;
        }
        $loginError = 'That email and password don’t match. Please try again.';
    } catch (PDOException $exception) {
        error_log('AnnSatu login error: ' . $exception->getMessage());
        $loginError = 'We couldn’t connect to the database. Check your setup and try again.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    verify_csrf(isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : null);
    $_SESSION = [];
    session_regenerate_id(true);
    header('Location: admin.php');
    exit;
}

$employeeSignedIn = !empty($_SESSION['employee_id']);
$orders = $reservations = $feedback = [];
$dashboardError = '';
if ($employeeSignedIn) {
    try {
        $pdo = db();
        $orders = $pdo->query('SELECT o.id, o.customer_name, o.phone, o.fulfillment, o.delivery_address, o.notes, o.total, o.status, o.created_at, GROUP_CONCAT(CONCAT(oi.quantity, " × ", oi.item_name) ORDER BY oi.id SEPARATOR ", ") AS items FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id GROUP BY o.id ORDER BY o.created_at DESC LIMIT 50')->fetchAll();
        $reservations = $pdo->query('SELECT id, customer_name, phone, reservation_date, reservation_time, guests, status, created_at FROM reservations ORDER BY reservation_date DESC, reservation_time DESC LIMIT 50')->fetchAll();
        $feedback = $pdo->query('SELECT customer_name, email, message, created_at FROM feedback ORDER BY created_at DESC LIMIT 20')->fetchAll();
    } catch (PDOException $exception) {
        error_log('AnnSatu dashboard error: ' . $exception->getMessage());
        $dashboardError = 'The dashboard could not load. Check that the database has been set up.';
    }
}
$csrf = csrf_token();
function status_options(string $type, string $current): string
{
    $statuses = $type === 'order'
        ? ['received' => 'Received', 'preparing' => 'Preparing', 'ready' => 'Ready', 'completed' => 'Completed', 'cancelled' => 'Cancelled']
        : ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'seated' => 'Seated', 'cancelled' => 'Cancelled'];
    $html = '';
    foreach ($statuses as $value => $label) {
        $selected = $current === $value ? ' selected' : '';
        $html .= '<option value="' . e($value) . '"' . $selected . '>' . e($label) . '</option>';
    }
    return $html;
}
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#f8f6f1"><title><?= $employeeSignedIn ? 'Staff dashboard' : 'Staff sign in' ?> — AnnSatu</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="styles.css"></head>
<body class="admin-page">
<header class="site-header admin-header"><a class="brand" href="index.php"><span class="brand-mark">a.</span><span>annsatu<span class="brand-period">.</span></span></a><a class="text-link" href="index.php">← Back to the restaurant</a></header>
<?php if (!$employeeSignedIn): ?>
<main class="login-wrap"><div class="login-card"><p class="eyebrow"><span class="eyebrow-line"></span> THE CREW BEHIND THE TABLE</p><h1>Good to have<br><em>you back.</em></h1><p>Sign in to keep things running smoothly.</p><?php if ($loginError !== ''): ?><p class="login-error" role="alert"><?= e($loginError) ?></p><?php endif; ?><form method="post"><input type="hidden" name="action" value="login"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><label>Work email<input type="email" name="email" autocomplete="username" required maxlength="190"></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button class="button button-dark button-full" type="submit">Sign in <span aria-hidden="true">→</span></button></form><a class="setup-link" href="setup.php">Setting up for the first time?</a></div></main>
<?php else: ?>
<main class="dashboard-wrap"><div class="dashboard-heading"><div><p class="eyebrow"><span class="eyebrow-line"></span> THE ANN SATU BACK OFFICE</p><h1>Good day, <em><?= e((string) $_SESSION['employee_name']) ?>.</em></h1><p>Here’s what’s happening around the table.</p></div><form method="post"><input type="hidden" name="action" value="logout"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><button class="button button-outline" type="submit">Sign out</button></form></div>
<?php if ($dashboardError !== ''): ?><p class="login-error" role="alert"><?= e($dashboardError) ?></p><?php endif; ?>
<section class="dashboard-panel"><div class="panel-heading"><div><p class="eyebrow">FRESH FROM THE KITCHEN</p><h2>Recent orders <span><?= count($orders) ?></span></h2></div></div><?php if (!$orders): ?><p class="empty-state">No orders just yet. Your next one could be right around the corner.</p><?php else: ?><div class="table-scroll"><table><thead><tr><th>Order</th><th>Guest</th><th>What they ordered</th><th>Type</th><th>Total</th><th>Status</th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr><td>#<?= (int) $order['id'] ?><small><?= e(date('M j, g:i a', strtotime($order['created_at']))) ?></small></td><td><?= e($order['customer_name']) ?><small><?= e($order['phone']) ?></small></td><td><?= e($order['items'] ?? '') ?><?php if ($order['delivery_address']): ?><small><?= e($order['delivery_address']) ?></small><?php endif; ?><?php if ($order['notes']): ?><small>Note: <?= e($order['notes']) ?></small><?php endif; ?></td><td><?= e(ucfirst($order['fulfillment'])) ?></td><td>$<?= number_format((float) $order['total'], 2) ?></td><td><select class="status-select" data-status-type="order_status" data-record-id="<?= (int) $order['id'] ?>" data-csrf="<?= e($csrf) ?>"><?= status_options('order', $order['status']) ?></select></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<section class="dashboard-panel"><div class="panel-heading"><div><p class="eyebrow">PULL UP A CHAIR</p><h2>Table requests <span><?= count($reservations) ?></span></h2></div></div><?php if (!$reservations): ?><p class="empty-state">No reservation requests at the moment.</p><?php else: ?><div class="table-scroll"><table><thead><tr><th>Guest</th><th>Date &amp; time</th><th>Party</th><th>Phone</th><th>Status</th></tr></thead><tbody><?php foreach ($reservations as $reservation): ?><tr><td><?= e($reservation['customer_name']) ?><small>Requested <?= e(date('M j', strtotime($reservation['created_at']))) ?></small></td><td><?= e(date('M j, Y', strtotime($reservation['reservation_date']))) ?><small><?= e($reservation['reservation_time']) ?></small></td><td><?= (int) $reservation['guests'] ?> <?= (int) $reservation['guests'] === 1 ? 'guest' : 'guests' ?></td><td><?= e($reservation['phone']) ?></td><td><select class="status-select" data-status-type="reservation_status" data-record-id="<?= (int) $reservation['id'] ?>" data-csrf="<?= e($csrf) ?>"><?= status_options('reservation', $reservation['status']) ?></select></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<section class="dashboard-panel"><div class="panel-heading"><div><p class="eyebrow">KIND WORDS &amp; GOOD IDEAS</p><h2>Guest notes <span><?= count($feedback) ?></span></h2></div></div><?php if (!$feedback): ?><p class="empty-state">Guest notes will show up here.</p><?php else: ?><div class="feedback-list"><?php foreach ($feedback as $note): ?><article class="feedback-note"><p><?= nl2br(e($note['message'])) ?></p><span><?= e($note['customer_name']) ?><?= $note['email'] ? ' · ' . e($note['email']) : '' ?> · <?= e(date('M j, Y', strtotime($note['created_at']))) ?></span></article><?php endforeach; ?></div><?php endif; ?></section>
</main><div class="toast" data-toast role="status" aria-live="polite"></div>
<script>window.ANNSATU_CSRF = <?= json_encode($csrf, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script><script src="admin.js" defer></script>
<?php endif; ?>
<footer class="site-footer"><a class="brand brand-footer" href="index.php"><span class="brand-mark">a.</span><span>annsatu<span class="brand-period">.</span></span></a><span>GOOD FOOD. BETTER TOGETHER.</span><span>© <?= date('Y') ?> AnnSatu</span></footer>
</body></html>
