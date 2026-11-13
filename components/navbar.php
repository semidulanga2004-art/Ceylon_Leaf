<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// For testing purposes, you can simulate a logged-in user by uncommenting the line below:
// $_SESSION['logged_in'] = true; 
?>
<nav class="navbar">
    <div class="logo">
        <a href="/ceylon_leaf/index.php">CeylonLeaf</a>
    </div>
    <div class="nav-links">
        <a href="#category">Category</a>
        <a href="#aboutus">About Us</a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <!-- Check whether the user is logged in -->
            <a href="/ceylon_leaf/cart/cart.php" class="cart-icon">🛒 Cart</a>
            <a href="/ceylon_leaf/history/history.php">Buying History</a>
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle Theme">🌙</button>
            <a href="/ceylon_leaf/components/logout.php" class="btn-login">Logout</a>

        <?php else: ?> <!If the user has not logged in it shows the below navigation links

            <!-- Day / Night Mode Toggle Button -->
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle Theme">🌙</button>
            <a href="/ceylon_leaf/login/login.php" class="btn-login">Login</a>
        <?php endif; ?>



    </div>
</nav>