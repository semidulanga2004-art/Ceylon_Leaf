<?php
session_start();
include '../database/db_connect.php';

// Protect page (login required)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
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

// quntity eka update karana eka handle karanwa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_qty = intval($_POST['quantity']);
    if ($new_qty > 0) {
        $up_stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id");
        $up_stmt->execute([
            ':quantity' => $new_qty,
            ':cart_id' => $cart_id,
            ':user_id' => $user_id,
        ]);
    }
}

// delete karana dewal handle karanwa
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $del_stmt = $pdo->prepare("DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id");
    if ($del_stmt->execute([':cart_id' => $cart_id, ':user_id' => $user_id])) {
        $message = "Item removed from cart.";
        $message_type = "success";
    }
}

//product details fetch karanawa 
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image_url 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :user_id ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shopping Cart - CeylonLeaf</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php include '../components/navbar.php'; ?>

    <div class="cart-container">
        <h1 class="cart-title">Your Shopping Cart</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($cart_items)): ?>
            <div class="cart-layout">
                <!-- Cart Items Table -->
                <div class="cart-items-card">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all" checked></th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                            ?>
                                <tr class="cart-item-row">
                                    <td>
                                        <input type="checkbox" class="item-checkbox" checked 
                                               data-price="<?php echo $item['price']; ?>" 
                                               data-cart-id="<?php echo $item['cart_id']; ?>">
                                    </td>
                                    <td class="product-info-cell">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Tea Image">
                                        <span><strong><?php echo htmlspecialchars($item['name']); ?></strong></span>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form action="cart.php" method="POST" class="cart-qty-form">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <input type="number" name="quantity" class="cart-qty-input" 
                                                   value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                                            <input type="hidden" name="update_cart" value="1">
                                        </form>
                                    </td>
                                    <td class="item-subtotal">Rs.<span class="subtotal-val"><?php echo number_format($subtotal, 2); ?></span></td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('Remove item from cart?');">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                //creating the summury box
                <div class="cart-summary-card">
                    <h2>Order Summary</h2>
                    <div class="summary-line">
                        <span>Selected Items:</span>
                        <span id="selected-count">0</span>
                    </div>
                    <hr>
                    <div class="summary-line total-line">
                        <span>Total to Pay:</span>
                        <span id="total-amount">Rs.0.00</span>
                    </div>
                    
                    <form action="checkout.php" method="POST" id="checkout-form">
                        <div id="hidden-inputs-container"></div>
                        <button type="submit" id="checkout-btn" class="auth-btn">Proceed to Buy</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is currently empty.</p>
                <a href="../index.php#category" class="auth-btn" style="display:inline-block; width:auto; padding:10px 20px; text-decoration:none;">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="../script.js"></script>

</body>                
</html>


                