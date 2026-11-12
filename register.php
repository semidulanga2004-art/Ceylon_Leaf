
<?php

session_start();
include 'database/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get registration form data
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check whether all fields are filled
    if (
        empty($full_name) ||
        empty($email) ||
        empty($telephone) ||
        empty($address) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all required fields!";

    }

    // Validate email address
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address!";

    }

    // Validate password length
    elseif (strlen($password) < 8) {

        $error = "Password must contain at least 8 characters!";

    }

    // Check password confirmation
    
elseif ($password !== $confirm_password) {

    $error = "Passwords do not match!";

} else {

    try {

        // Check whether email already exists
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $error = "Email address is already registered!";

        } else {

            // Securely hash the password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert user details into database
            $insert_stmt = $pdo->prepare(
                "INSERT INTO users
                (full_name, email, telephone, address, password)
                VALUES (?, ?, ?, ?, ?)"
            );

            $insert_stmt->execute([
                $full_name,
                $email,
                $telephone,
                $address,
                $hashed_password
            ]);

            $success = "Account created successfully!";

        }

    } catch (PDOException $e) {

        error_log($e->getMessage());
        $error = "Something went wrong. Please try again!";

    }

}


}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - CeylonLeaf</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
      <?php include 'navbar.php'; ?>

    <div class="auth-container">

        <div class="auth-card">

            
<h2>Create Account</h2>

<p class="auth-subtitle">Join CeylonLeaf today</p>

<!-- Display error message -->
<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Display success message -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
        <a href="login.php">Click here to login</a>
    </div>
<?php endif; ?>

<!-- Registration form -->
            <form action="register.php" method="POST">

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name"
                        required placeholder="John Doe">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                        required placeholder="example@mail.com">
                </div>

                <div class="form-group">
                    <label for="telephone">Telephone Number</label>
                    <input type="tel" id="telephone" name="telephone"
                        required placeholder="+94 7X XXX XXXX">
                </div>

                <div class="form-group">
                    <label for="address">Home Address</label>
                    <textarea id="address" name="address" rows="3"
                        required placeholder="Street address, City, District"></textarea>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                        required placeholder="Create a password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password"
                        name="confirm_password" required
                        placeholder="Confirm your password">
                </div>

                <button type="submit" class="auth-btn">
                    Register
                </button>

            </form>

            <div class="auth-footer">
                <p>
                    Already have an account?
                    <a href="login.php">Login here</a>
                </p>
            </div>

        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="script.js"></script>

</body>
</html> 