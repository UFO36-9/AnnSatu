<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['message' => 'Method not allowed.'], 405);
}

$data = request_data();
verify_csrf(isset($data['csrf']) && is_string($data['csrf']) ? $data['csrf'] : null);
$action = isset($data['action']) && is_string($data['action']) ? $data['action'] : '';

function required_text(array $data, string $key, int $maxLength): string
{
    $value = isset($data[$key]) && is_string($data[$key]) ? trim($data[$key]) : '';
    if ($value === '' || mb_strlen($value) > $maxLength) {
        json_response(['message' => 'Please check the information you entered.'], 422);
    }
    return $value;
}

function optional_email(array $data): ?string
{
    $email = isset($data['email']) && is_string($data['email']) ? trim($data['email']) : '';
    if ($email === '') {
        return null;
    }
    if (strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['message' => 'Please enter a valid email address.'], 422);
    }
    return $email;
}

try {
    $pdo = db();
    if ($action === 'reservation') {
        $name = required_text($data, 'name', 100);
        $phone = required_text($data, 'phone', 30);
        $date = isset($data['date']) && is_string($data['date']) ? $data['date'] : '';
        $time = isset($data['time']) && is_string($data['time']) ? $data['time'] : '';
        $guests = filter_var($data['guests'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 8]]);
        $validTimes = ['11:00 AM', '12:00 PM', '1:00 PM', '5:00 PM', '6:00 PM', '7:00 PM', '8:00 PM', '9:00 PM'];
        $parsedDate = DateTime::createFromFormat('Y-m-d', $date);
        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date || $date < date('Y-m-d') || !in_array($time, $validTimes, true) || $guests === false) {
            json_response(['message' => 'Please choose a valid date, time, and party size.'], 422);
        }
        $stmt = $pdo->prepare('INSERT INTO reservations (customer_name, phone, reservation_date, reservation_time, guests) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $date, $time, $guests]);
        json_response(['message' => 'Your request is in! We’ll be in touch to confirm your table.', 'reservation_id' => (int) $pdo->lastInsertId()]);
    }

    if ($action === 'cancel_reservation') {
        $id = filter_var($data['reservation_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $phone = required_text($data, 'phone', 30);
        if ($id === false) {
            json_response(['message' => 'Please check your reservation number and try again.'], 422);
        }
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND phone = ? AND status IN ('pending', 'confirmed') AND reservation_date >= CURRENT_DATE()");
        $stmt->execute([$id, $phone]);
        if ($stmt->rowCount() === 0) {
            json_response(['message' => 'We couldn’t find an active future reservation with those details. Please call us if you need a hand.'], 404);
        }
        json_response(['message' => 'Your reservation has been cancelled. We hope to see you another time.']);
    }

    if ($action === 'feedback') {
        $name = required_text($data, 'name', 100);
        $message = required_text($data, 'message', 1500);
        $email = optional_email($data);
        $stmt = $pdo->prepare('INSERT INTO feedback (customer_name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        json_response(['message' => 'Thanks for taking the time to write. We’re listening.']);
    }

    if ($action === 'order') {
        $name = required_text($data, 'name', 100);
        $phone = required_text($data, 'phone', 30);
        $email = optional_email($data);
        $fulfillment = isset($data['fulfillment']) && is_string($data['fulfillment']) ? $data['fulfillment'] : '';
        $address = isset($data['address']) && is_string($data['address']) ? trim($data['address']) : '';
        $notes = isset($data['notes']) && is_string($data['notes']) ? trim($data['notes']) : '';
        $items = $data['items'] ?? null;
        if (!in_array($fulfillment, ['pickup', 'delivery'], true) || ($fulfillment === 'delivery' && ($address === '' || mb_strlen($address) > 255)) || mb_strlen($notes) > 500 || !is_array($items) || count($items) < 1 || count($items) > 20) {
            json_response(['message' => 'Please check your order details and try again.'], 422);
        }
        $quantities = [];
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['id'], $item['quantity'])) {
                json_response(['message' => 'Your order contains an invalid item.'], 422);
            }
            $id = filter_var($item['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $quantity = filter_var($item['quantity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 20]]);
            if ($id === false || $quantity === false) {
                json_response(['message' => 'Please check item quantities and try again.'], 422);
            }
            $quantities[$id] = min(20, ($quantities[$id] ?? 0) + $quantity);
        }
        $placeholders = implode(',', array_fill(0, count($quantities), '?'));
        $stmt = $pdo->prepare("SELECT id, name, price FROM menu_items WHERE is_available = 1 AND id IN ({$placeholders})");
        $stmt->execute(array_keys($quantities));
        $menuItems = $stmt->fetchAll();
        if (count($menuItems) !== count($quantities)) {
            json_response(['message' => 'One of those menu items is no longer available. Please refresh and try again.'], 422);
        }
        $total = 0.0;
        foreach ($menuItems as $item) {
            $total += (float) $item['price'] * $quantities[(int) $item['id']];
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO orders (customer_name, phone, email, fulfillment, delivery_address, notes, total) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $email, $fulfillment, $fulfillment === 'delivery' ? $address : null, $notes ?: null, $total]);
        $orderId = (int) $pdo->lastInsertId();
        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
        foreach ($menuItems as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['name'], $quantities[(int) $item['id']], $item['price']]);
        }
        $pdo->commit();
        json_response(['message' => 'Order received! We can’t wait to make it for you.', 'order_id' => $orderId]);
    }

    if (in_array($action, ['order_status', 'reservation_status'], true)) {
        if (empty($_SESSION['employee_id'])) {
            json_response(['message' => 'Please sign in again to continue.'], 401);
        }
        $id = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : '';
        $statuses = $action === 'order_status' ? ['received', 'preparing', 'ready', 'completed', 'cancelled'] : ['pending', 'confirmed', 'seated', 'cancelled'];
        if ($id === false || !in_array($status, $statuses, true)) {
            json_response(['message' => 'Invalid status update.'], 422);
        }
        $table = $action === 'order_status' ? 'orders' : 'reservations';
        $stmt = $pdo->prepare("UPDATE {$table} SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        if ($stmt->rowCount() === 0) {
            $check = $pdo->prepare("SELECT id FROM {$table} WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                json_response(['message' => 'That record could not be found.'], 404);
            }
        }
        json_response(['message' => 'Status updated.']);
    }

    json_response(['message' => 'Unknown action.'], 400);
} catch (PDOException $exception) {
    error_log('AnnSatu database error: ' . $exception->getMessage());
    json_response(['message' => 'We couldn’t save that just now. Please try again in a moment.'], 503);
}
