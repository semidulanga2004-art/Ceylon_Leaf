<?php

session_start();
include 'db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get email and password
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input fields
    if (!empty($email) && !empty($password)) {

        try {

            // Retrieve user details from database
            $stmt = $pdo->prepare(
                "SELECT id, full_name, password
                 FROM users
                 WHERE email = ?"
            );

            $stmt->execute([$email]);

            $user = $stmt->fetch();

            // Check whether account exists
            if ($user) {

                // Verify hashed password
                if (password_verify($password, $user['password'])) {

                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];

                    header("Location: index.php");
                    exit();

                } else {

                    $error = "Invalid password!";

                }

            } else {

                $error = "No account found with that email address!";

            }

        } catch (PDOException $e) {

            error_log($e->getMessage());
            $error = "Login failed. Please try again.";

        }

    } else {

        $error = "Please fill in all fields!";

    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - CeylonLeaf</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="auth-container">

        <div class="auth-card">

            <h2>Welcome Back</h2>

            <p class="auth-subtitle">
                Log in to your CeylonLeaf account
            </p>

            <form action="login.php" method="POST">

                <div class="form-group">

                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        placeholder="Enter your email"
                    >

                </div>

                <div class="form-group">

                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="Enter your password"
                    >

                </div>

                <button type="submit" class="auth-btn">
                    Login
                </button>

            </form>

            <div class="auth-footer">
                <p>
                    Don't have an account?
                    <a href="register.php">Register here</a>
                </p>
            </div>

        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>

</body>
</html>
