<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

$message = '';
$complete = false;
try {
    $complete = (int) db()->query('SELECT COUNT(*) FROM employees')->fetchColumn() > 0;
    if (!$complete && $_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf(isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : null);
        $name = isset($_POST['name']) && is_string($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
        if ($name === '' || mb_strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190 || strlen($password) < 12) {
            $message = 'Please enter a name, valid email, and password with at least 12 characters.';
        } else {
            $stmt = db()->prepare('INSERT INTO employees (full_name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $_SESSION['employee_id'] = (int) db()->lastInsertId();
            $_SESSION['employee_name'] = $name;
            session_regenerate_id(true);
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            header('Location: admin.php');
            exit;
        }
    }
} catch (PDOException $exception) {
    error_log('AnnSatu setup error: ' . $exception->getMessage());
    $message = 'We couldn’t connect to the database. Import schema.sql and check your database settings.';
}
$csrf = csrf_token();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>First-time setup — AnnSatu</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="styles.css"></head><body class="admin-page"><header class="site-header admin-header"><a class="brand" href="index.php"><span class="brand-mark">a.</span><span>annsatu<span class="brand-period">.</span></span></a><a class="text-link" href="admin.php">Staff sign in →</a></header><main class="login-wrap"><div class="login-card"><p class="eyebrow"><span class="eyebrow-line"></span> FIRST-TIME SETUP</p><h1>Set the table<br><em>for your team.</em></h1><?php if ($complete): ?><p>Your first staff account has already been created. Head to the sign-in page.</p><a class="button button-dark button-full" href="admin.php">Go to staff sign in <span>→</span></a><?php else: ?><p>Create the first staff account to access orders, bookings, and guest notes.</p><?php if ($message !== ''): ?><p class="login-error" role="alert"><?= e($message) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><label>Your name<input name="name" autocomplete="name" required maxlength="100"></label><label>Work email<input type="email" name="email" autocomplete="email" required maxlength="190"></label><label>Password <span>(at least 12 characters)</span><input type="password" name="password" autocomplete="new-password" minlength="12" required></label><button class="button button-dark button-full" type="submit">Create staff account <span>→</span></button></form><p class="setup-warning">This page closes automatically after the first account is created.</p><?php endif; ?></div></main><footer class="site-footer"><a class="brand brand-footer" href="index.php"><span class="brand-mark">a.</span><span>annsatu<span class="brand-period">.</span></span></a><span>GOOD FOOD. BETTER TOGETHER.</span><span>© <?= date('Y') ?> AnnSatu</span></footer></body></html>
