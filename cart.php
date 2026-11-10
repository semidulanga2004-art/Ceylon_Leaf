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