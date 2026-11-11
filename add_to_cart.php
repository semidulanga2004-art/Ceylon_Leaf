<?php

// Prevent any PHP notices/warnings from breaking the JSON response
error_reporting(0);


// Hide PHP errors from the browser.
ini_set('display_errors', 0);

session_start();

include 'db_connect.php';

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


