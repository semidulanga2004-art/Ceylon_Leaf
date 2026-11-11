<?php
session_start();
include '../database/db_connect.php';

// Check if owner/admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders View - CeylonLeaf</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <h1 class="admin-title">📦 Customer Orders Management (Owner Panel)</h1>

        <?php
        // Fetch all orders with customer details and status
        $sql = "SELECT o.id AS order_id, o.total_amount, o.created_at, o.status, 
                       u.full_name, u.email, u.telephone, u.address 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";

        $result = $pdo->query($sql);

        if ($result && $result->rowCount() > 0):
            while ($order = $result->fetch(PDO::FETCH_ASSOC)):
        ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <strong>Bill No: #INV-<?php echo $order['order_id']; ?></strong>
                            <span style="color: #666; margin-left: 15px; font-size: 13px;">📅 <?php echo $order['created_at']; ?></span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px;">
                            <strong style="color: #2c5e1a; font-size: 16px;">Total: $<?php echo number_format($order['total_amount'], 2); ?></strong>
                            <span>Status: <?php echo htmlspecialchars($order['status']); ?></span>
                        </div>
                    </div>

                    <div class="order-body">
                        <!-- Customer Details -->
                        <div class="customer-info-box">
                            <strong>Customer Details:</strong><br>
                            👤 Name: <?php echo htmlspecialchars($order['full_name']); ?> |
                            📧 Email: <?php echo htmlspecialchars($order['email']); ?> |
                            📞 Phone: <?php echo htmlspecialchars($order['telephone']); ?><br>
                            📍 Address: <?php echo htmlspecialchars($order['address']); ?>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
        else:
            ?>
            <div class="empty-cart">
                <p>No orders placed yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>