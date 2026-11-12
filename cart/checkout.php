<?php

session_start();
include 'db_connect.php';

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

$stmt =$conn->prepare($sql);$types = 'i' . str_repeat('i', count($selected_cart_ids));$params = array_merge([$user_id],$selected_cart_ids);
$stmt->bind_param($types, ...$params);$stmt->execute();
$result =$stmt->get_result();

$subtotal = 0;
$item_count = 0;
while ($row = $result->fetch_assoc()) {$subtotal += $row['price'] *$row['quantity'];
    $item_count +=$row['quantity'];
}
$stmt->close();

$vat =$subtotal * 0.10;
$grand_total = $subtotal +$vat;





?>