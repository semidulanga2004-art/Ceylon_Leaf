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

// Fetch items
$items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->execute([$order_id]);
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

<br>
<table class="bill-table">
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($it = $items_stmt->fetch(PDO::FETCH_ASSOC)): 
            $subtotal = $it['subtotal'] ?? ($it['price'] * $it['quantity']);
        ?>
            <tr>
                <td><?php echo htmlspecialchars($it['product_name']); ?></td>
                <td><?php echo $it['quantity']; ?></td>
                <td>$<?php echo number_format($it['price'], 2); ?></td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<div style="text-align: right; margin-top: 15px;">
    <h3>Grand Total: $<?php echo number_format($order['total_amount'], 2); ?></h3>
</div>
