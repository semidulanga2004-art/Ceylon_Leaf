<?php
session_start();
include 'db_connect.php';

// Protect page (login required)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// paymet eka sucess da kiyala balala e anuwa user t msg ekak  display karanwa
if (isset($_GET['payment'])) {
    if ($_GET['payment'] === 'success') {
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        $message = "Payment successful! Your order (#" . $order_id . ") has been placed. <a href='history.php'><strong>View Invoice in Buying History</strong></a>";
        $message_type = "success";
    } elseif ($_GET['payment'] === 'error') {
        $error_detail = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Payment processing failed';
        $message = "Payment failed: " . $error_detail;
        $message_type = "error";
    }
}

// quntity eka update karana eka handle karanwa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_qty = intval($_POST['quantity']);
    if ($new_qty > 0) {
        $up_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $up_stmt->bind_param("iii", $new_qty, $cart_id, $user_id);
        $up_stmt->execute();
        $up_stmt->close();
    }
}

// delete karana dewal handle karanwa
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $del_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $cart_id, $user_id);
    if ($del_stmt->execute()) {
        $message = "Item removed from cart.";
        $message_type = "success";
    }
    $del_stmt->close();
}


?>