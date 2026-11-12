<?php

// Prevent any PHP notices/warnings from breaking the JSON response
error_reporting(0);


// Hide PHP errors from the browser.
ini_set('display_errors', 0);

session_start();

require_once __DIR__ . '../database/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to add items to your cart.']);
    exit();
}

// Get selected product details
$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate product ID and quantity
if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product or quantity.']);
    exit();
}


// Check if item already exists in user's cart
$stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {

    // Update quantity if product already exists
    $new_qty = $row['quantity'] + $quantity;

    $update_stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");

    if ($update_stmt->execute([$new_qty, $row['id']])) {
        echo json_encode(['status' => 'success', 'message' => 'Cart updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update cart in database.']);
    }

} else {

    // Insert a new product into the cart
    $insert_stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");

    if ($insert_stmt->execute([$user_id, $product_id, $quantity])) {
        echo json_encode(['status' => 'success', 'message' => 'Item added to cart!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to insert item into cart.']);
    }
}