<?php

session_start();
require_once __DIR__ . '/../database/db_connect.php';

// Login status check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

// Item selection check
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['selected_items'])) {
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Valued Customer';
$selected_cart_ids = array_map('intval', $_POST['selected_items']);

// Fetch selected cart items and calculate totals
$placeholders = implode(',', array_fill(0, count($selected_cart_ids), '?'));
$sql = "SELECT c.id AS cart_id, c.quantity, p.name, p.price, p.id AS product_id 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? AND c.id IN ($placeholders)";

$stmt = $pdo->prepare($sql);
$params = array_merge([$user_id], $selected_cart_ids);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
$item_count = 0;
foreach ($result as $row) {
    $subtotal += $row['price'] * $row['quantity'];
    $item_count += $row['quantity'];
}

$vat = $subtotal * 0.10;
$grand_total = $subtotal + $vat;

?>