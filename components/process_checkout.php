<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// For testing purposes, you can simulate a logged-in user by uncommenting the line below:
// $_SESSION['logged_in'] = true; 
?>
<nav class="navbar">
    <div class="logo">
        <a href="../index.php">CeylonLeaf</a>
    </div>
    <div class="nav-links">
        <a href="../index.php/#category">Category</a>
        <a href="../index.php/#aboutus">About Us</a>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
            <a href="../cart/cart.php" class="cart-icon">🛒 Cart</a>
            <a href="../history/history.php">Buying History</a>
            <a href="../components/logout.php" class="btn-login">Logout</a>
        <?php else: ?>
            <!-- Day / Night Mode Toggle Button -->
            <button id="theme-toggle" class="theme-toggle" type="button" aria-label="Toggle Theme">🌙</button>
            <a href="../login/login.php" class="btn-login">Login</a>
        <?php endif; ?>



    </div>
</nav>