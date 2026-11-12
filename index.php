
<?php

session_start();

// Check whether user is logged in
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
    <title>Home - CeylonLeaf</title>
</head>

<body>

    <h1>Welcome to CeylonLeaf!</h1>

    <h2>
        Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
    </h2>

    <p>You have successfully logged in.</p>

</body>
</html>
