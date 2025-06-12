<?php
session_start();
include('db.php');

if ($_SESSION['logged_id'] <= 0) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if (!isset($_POST['order_id'])) {
    die(json_encode(['success' => false, 'message' => 'Order ID is required']));
}

$order_id = $_POST['order_id'];
$advance_amount = floatval($_POST['advance_amount']);

try {
    // Get order details first
    $stmt = $pdo->prepare("SELECT order_total_amount, discount_amount FROM pixel_media_order WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die(json_encode(['success' => false, 'message' => 'Order not found']));
    }

    $order_total = $order['order_total_amount'];
    $discount = $order['discount_amount'] ?? 0;
    $final_amount = $order_total - $discount;

    if ($advance_amount > $final_amount) {
        die(json_encode(['success' => false, 'message' => 'Advance cannot be more than final amount']));
    }

    // Update advance payment
    $stmt = $pdo->prepare("UPDATE pixel_media_order SET payment_amount = ? WHERE order_id = ?");
    $stmt->execute([$advance_amount, $order_id]);

    // Calculate new due amount
    $new_due = $final_amount - $advance_amount;

    echo json_encode([
        'success' => true,
        'message' => 'Advance payment updated successfully',
        'new_advance' => $advance_amount,
        'new_due' => $new_due,
        'order_total' => $order_total,
        'discount' => $discount,
        'final_amount' => $final_amount
    ]);

} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
}