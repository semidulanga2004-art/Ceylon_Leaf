<?php

session_start();
require_once __DIR__ . '/../database/db_connect.php';

// login eke status eka check karanwa
if (!isset($_SESSION['logged_in']) ||$_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// item select wela nathnam redirect karnawa
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['selected_items'])) {
    header("Location: cart.php");
    exit();
}

$user_id =$_SESSION['user_id'];
$user_name =$_SESSION['user_name'] ?? 'Valued Customer';
$selected_cart_ids =$_POST['selected_items'];

// select karapu item tika fetch karala total eka calculate karanwa
$placeholders = implode(',', array_fill(0, count($selected_cart_ids), '?'));$sql = "SELECT c.id AS cart_id, c.quantity, p.name, p.price, p.id AS product_id 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? AND c.id IN ($placeholders)";

$stmt = $pdo->prepare($sql);
$params = array_merge([$user_id], $selected_cart_ids);
$stmt->execute($params);

$subtotal = 0;
$item_count = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {$subtotal += $row['price'] *$row['quantity'];
    $item_count +=$row['quantity'];
}
$stmt->closeCursor();

$vat =$subtotal * 0.10;
$grand_total = $subtotal +$vat;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - CeylonLeaf</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../style1.css">
</head>
<body class="checkout-body">

    <div class="checkout-modal-container">
        <!-- Header Bar -->
        <div class="checkout-header">
            <div class="checkout-brand">
                <span class="bank-badge">COMMERCIAL BANK SECURE GATEWAY</span>
                <span class="gateway-lock" style="margin-left: 10px;">🔒 256-bit SSL Encrypted</span>
            </div>
            <div class="checkout-timer" id="timer">10 : 00</div>
            <a href="cart.php" class="close-modal">&times;</a>
        </div>

        <div class="checkout-grid">
            <!-- Left Column: Payment Form -->
            <div class="payment-form-section">
                <div class="section-heading">
                    <h3>Card Details</h3>
                    <a href="cart.php" class="edit-link">Edit Order</a>
                </div>
                <p class="form-hint">Enter your payment information to authorize the order</p>

</body>
</html>                



