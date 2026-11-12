<?php
session_start();
include '../database/db_connect.php';

if (!isset($_SESSION['logged_in']) || !isset($_GET['order_id'])) {
    exit('Access denied.');
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Fetch order
$stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email, u.telephone, u.address 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    exit('Invoice not found.');
}
?>
<div class="receipt-header">
    <h2>CeylonLeaf Official Receipt</h2>
    <p><strong>Bill No:</strong> <?php echo htmlspecialchars($order['bill_number'] ?? '#INV-' . $order['id']); ?></p>
    <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
</div>
<hr style="margin: 15px 0; border: 0; border-top: 1px solid var(--border-color);">
<div class="receipt-customer">
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['telephone']); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'Cash on Delivery'); ?></p>
</div>
