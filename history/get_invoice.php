<?php
session_start();

require_once __DIR__ . '/ceylon_leaf/database/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_GET['order_id'])) {
    exit('Unauthorized access.');
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

try {
    // Fetch the order only if it belongs to the logged-in customer
    $o_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $o_stmt->execute([$order_id, $user_id]);
    $order = $o_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        exit('Invoice not found.');
    }

    // Fetch customer details
    $u_stmt = $pdo->prepare("SELECT full_name, email, telephone, address FROM users WHERE id = ?");
    $u_stmt->execute([$user_id]);
    $user = $u_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch ordered items
    $i_stmt = $pdo->prepare("SELECT product_name, quantity, price, subtotal FROM order_items WHERE order_id = ?");
    $i_stmt->execute([$order_id]);
    $items = $i_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('Unable to load invoice details.');
}

$bill_display = !empty($order['bill_number']) ? $order['bill_number'] : '#INV-' . $order['id'];
$payment_display = !empty($order['payment_method']) ? $order['payment_method'] : 'Card / Online';
$status_display = !empty($order['status']) ? $order['status'] : 'Not Packed';
?>

<div style="font-size:14px; line-height:1.6; color:#333;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
        <div>
            <h2 style="margin:0 0 6px 0; color:#2c5e1a;">CeylonLeaf Official Invoice</h2>
            <p style="margin:0;">
                <strong>Bill No:</strong> <?php echo htmlspecialchars($bill_display); ?><br>
                <strong>Date &amp; Time:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
            </p>
        </div>
        <div style="text-align:right;">
            <p style="margin:0;">
                <strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_display); ?><br>
                <strong>Order Status:</strong> <?php echo htmlspecialchars($status_display); ?>
            </p>
        </div>
    </div>

    <div style="background:#f4f6f8; padding:12px 14px; border-radius:8px; margin:14px 0; border:1px solid #e1e4e8;">
        <strong>Customer Details:</strong><br>
        Name: <?php echo htmlspecialchars($user['full_name'] ?? 'Customer'); ?><br>
        Email: <?php echo htmlspecialchars($user['email'] ?? ''); ?><br>
        Phone: <?php echo htmlspecialchars($user['telephone'] ?? ''); ?><br>
        Address: <?php echo htmlspecialchars($user['address'] ?? ''); ?>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-top:10px;">
        <thead>
            <tr style="background:#2c5e1a; color:#fff; text-align:left;">
                <th style="padding:10px; border:1px solid #2c5e1a;">Item Name</th>
                <th style="padding:10px; border:1px solid #2c5e1a;">Qty</th>
                <th style="padding:10px; border:1px solid #2c5e1a;">Price</th>
                <th style="padding:10px; border:1px solid #2c5e1a;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item):
                $sub = (!empty($item['subtotal']) && (float)$item['subtotal'] > 0)
                    ? (float)$item['subtotal']
                    : ((float)$item['price'] * (int)$item['quantity']);
            ?>
                <tr>
                    <td style="padding:9px 10px; border:1px solid #ddd;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td style="padding:9px 10px; border:1px solid #ddd;"><?php echo (int)$item['quantity']; ?></td>
                    <td style="padding:9px 10px; border:1px solid #ddd;">$<?php echo number_format((float)$item['price'], 2); ?></td>
                    <td style="padding:9px 10px; border:1px solid #ddd;">$<?php echo number_format($sub, 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align:right; margin-top:16px; font-size:16px;">
        <strong>Total Amount: <span style="color: #2c5e1a;">$<?php echo number_format((float)$order['total_amount'], 2); ?></span></strong>
    </div>
</div>
