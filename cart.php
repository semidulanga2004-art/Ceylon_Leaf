<?php
session_start();
include 'db_connect.php';

// Protect page (login required)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
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
        $up_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $up_stmt->bind_param("iii", $new_qty, $cart_id, $user_id);
        $up_stmt->execute();
        $up_stmt->close();
    }
}

// delete karana dewal handle karanwa
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $del_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $del_stmt->bind_param("ii", $cart_id, $user_id);
    if ($del_stmt->execute()) {
        $message = "Item removed from cart.";
        $message_type = "success";
    }
    $del_stmt->close();
}

//product details fetch karanawa 
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image_url 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? ORDER BY c.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();        


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shopping Cart - CeylonLeaf</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="cart-container">
        <h1 class="cart-title">Your Shopping Cart</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($cart_items->num_rows > 0): ?>
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
                            <?php while ($item = $cart_items->fetch_assoc()): 
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
                                    <td class="item-subtotal">$<span class="subtotal-val"><?php echo number_format($subtotal, 2); ?></span></td>
                                    <td>
                                        <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" 
                                           class="btn-delete" 
                                           onclick="return confirm('Remove item from cart?');">Remove</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

</body>                
</html>


                