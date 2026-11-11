
<?php
session_start();
include 'db_connect.php';

// Check whether the customer is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$selected_items = $_POST['selected_items'] ?? [];

// Check whether any items are selected
if (empty($selected_items)) {
    header("Location: cart.php?payment=error&msg=No+items+selected");
    exit();
}

$selected_ids = array_map('intval', $selected_items);
$placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

// 1. Retrieve selected cart items using PDO
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name AS product_name, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? AND c.id IN ($placeholders)";

$stmt = $conn->prepare($sql);
$params = array_merge([$user_id], $selected_ids);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate subtotal for selected products
$items = [];
$subtotal = 0;

foreach ($result as $row) {
    $items[] = $row;
    $subtotal += $row['price'] * $row['quantity'];
}

if (empty($items)) {
    header("Location: cart.php?payment=error&msg=Items+not+found");
    exit();
}

// Calculate VAT and final payment amount
$vat = $subtotal * 0.10;
$grand_total = $subtotal + $vat;

// 2. Retrieve customer information
$user_stmt = $conn->prepare("SELECT full_name, email, telephone, address FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

// 3. Generate a unique bill number
$bill_number = "INV-" . time() . "-" . mt_rand(100, 999);

// 4. Insert new order into the orders table
$order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, bill_number) VALUES (?, ?, ?)");

if ($order_stmt->execute([$user_id, $grand_total, $bill_number])) {

    $order_id = $conn->lastInsertId();

    $email_items_list = "";

    // 5. Save purchased items and remove them from the cart
    foreach ($items as $item) {

        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
        $item_stmt->execute([$order_id, $item['product_name'], $item['quantity'], $item['price']]);

        // Remove purchased item from customer's cart
        $del_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $del_stmt->execute([$item['cart_id'], $user_id]);

        $item_subtotal = number_format($item['price'] * $item['quantity'], 2);

        // Prepare purchased items for the order email
        $email_items_list .= "<tr>
            <td style='padding:8px; border:1px solid #ddd;'>{$item['product_name']}</td>
            <td style='padding:8px; border:1px solid #ddd;'>{$item['quantity']}</td>
            <td style='padding:8px; border:1px solid #ddd;'>\${$item['price']}</td>
            <td style='padding:8px; border:1px solid #ddd;'>\${$item_subtotal}</td>
        </tr>";
    }

    // 6. Send order notification email to the owner
    $owner_email = "your_email@gmail.com"; // Replace with owner's email
    $subject = "New Order Received - #" . $bill_number;

    // Create HTML email with customer and purchase details
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
      <h2>New Order Placed! ({$bill_number})</h2>
      <h3>Customer Details:</h3>
      <p>
        <strong>Name:</strong> {$user_data['full_name']}<br>
        <strong>Email:</strong> {$user_data['email']}<br>
        <strong>Phone:</strong> {$user_data['telephone']}<br>
        <strong>Address:</strong> {$user_data['address']}
      </p>
      <h3>Purchased Items:</h3>
      <table style='width:100%; border-collapse:collapse;' border='1'>
        <tr style='background:#f4f4f4;'>
            <th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th>
        </tr>
        {$email_items_list}
      </table>
      <p><strong>Total Amount Paid (incl. 10% VAT): \$" . number_format($grand_total, 2) . "</strong></p>
    </body>
    </html>
    ";

    // Set email headers for an HTML message
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: orders@ceylonleaf.com\r\n";

    @mail($owner_email, $subject, $message, $headers);

    // 7. Redirect customer after successful order processing
    header("Location: cart.php?payment=success&order_id=" . $order_id);
    exit();

} else {

    header("Location: cart.php?payment=error&msg=Database+Error");
    exit();
}
