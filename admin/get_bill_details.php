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