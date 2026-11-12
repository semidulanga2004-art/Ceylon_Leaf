
<?php
session_start();

require_once __DIR__ . '/database/db_connect.php';

// Check whether the customer is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - CeylonLeaf</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>

    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="history-container">

        <h1>My Purchase History</h1>

        <p>View your previous purchases and invoices here.</p>

    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
