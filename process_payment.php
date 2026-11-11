
<?php

session_start();

include 'db_connect.php';

// Check whether the customer is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$selected_items = $_POST['selected_items'] ?? [];

// Check whether any items are selected
if (empty($selected_items)) {
    header("Location: cart.php?payment=error&msg=No+items+selected");
    exit();
}

$selected_ids = array_map('intval', $selected_items);
$placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

// 1. Retrieve selected cart items using PDO
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name AS product_name, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? AND c.id IN ($placeholders)";

$stmt = $conn->prepare($sql);
$params = array_merge([$user_id], $selected_ids);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate subtotal for selected products
$items = [];
$subtotal = 0;

foreach ($result as $row) {
    $items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}

if (empty($items)) {
    header("Location: cart.php?payment=error&msg=Items+not+found");
    exit();
}

// Calculate VAT and final payment amount
$vat = $subtotal * 0.10;
$grand_total = $subtotal + $vat;



// 2. Retrieve customer information
$user_stmt = $conn->prepare("SELECT full_name, email, telephone, address FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

