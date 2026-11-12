<?php

session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = intval($_GET['id']);
$message = "";
$error = "";