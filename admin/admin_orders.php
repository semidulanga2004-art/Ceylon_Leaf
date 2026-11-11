<?php
session_start();
include 'db_connect.php';

// Check if owner/admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders View - CeylonLeaf</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="admin-container">
        <h1 class="admin-title">📦 Customer Orders Management (Owner Panel)</h1>
        
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>