<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// For testing purposes, you can simulate a logged-in user by uncommenting the line below:
// $_SESSION['logged_in'] = true; 
?>
<nav class="navbar">
    <div class="logo">
        <a href="index.php">CeylonLeaf</a>
    </div>
    <div class="nav-links">
        <a href="#category">Category</a>
        <a href="#aboutus">About Us</a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            //checks whether the logged-in session variable exists and its value is true, and if it is true it display the following navigation links
            <a href="cart.php" class="cart-icon">🛒 Cart</a>
            <a href="history.php">Buying History</a>
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle Theme">🌙</button>
            <a href="logout.php" class="btn-login">Logout</a>

        <?php else: ?> //If the user has not logged in it shows the below navigation links

            <!-- Day / Night Mode Toggle Button -->
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle Theme">🌙</button>
            <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>



    </div>
</nav>