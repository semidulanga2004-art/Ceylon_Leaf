<?php
session_start();

require_once __DIR__ . '/../database/db_connect.php';

// Check whether the customer is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /ceylon_leaf/login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];
$error_message = '';

try {
    $stmt = $pdo->prepare(
        "SELECT id, bill_number, total_amount, payment_method, created_at, status
         FROM orders
         WHERE user_id = ?
         ORDER BY created_at DESC"
    );
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Unable to load purchase history at the moment.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - CeylonLeaf</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        

        .history-container {
            max-width: 1050px;
            margin: 40px auto;
            padding: 0 20px;
            min-height: 60vh;
        }

        .history-header {
            margin-bottom: 24px;
        }

        .history-header h1 {
            color: #2c5e1a;
            margin: 0 0 6px 0;
            font-size: 28px;
        }

        .history-header p {
            color: #666;
            margin: 0;
            font-size: 15px;
        }

        .history-card {
            background: #ffffff;
            border: 1px solid #e1e4e8;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .history-table th {
            background-color: #2c5e1a;
            color: #ffffff;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: bold;
        }

        .history-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
            vertical-align: middle;
        }

        .history-table tr:last-child td {
            border-bottom: none;
        }

        .history-table tbody tr:hover {
            background-color: #f9fbf8;
        }

        .bill-no {
            font-weight: bold;
            color: #2c5e1a;
        }

        .amount-cell {
            font-weight: bold;
            color: #222;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-not-packed {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-packed {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background-color: #d4edda;
            color: #155724;
        }

        .btn-invoice {
            background-color: #2c5e1a;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-invoice:hover {
            background-color: #1e3f11;
        }

        .empty-state {
            background: #ffffff;
            border: 1px solid #e1e4e8;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .empty-state h2 {
            color: #2c5e1a;
            margin: 0 0 10px 0;
        }

        .empty-state p {
            color: #666;
            margin: 0 0 22px 0;
            font-size: 15px;
        }

        .btn-shop-now {
            background-color: #2c5e1a;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            display: inline-block;
            transition: background 0.2s;
        }

        .btn-shop-now:hover {
            background-color: #1e3f11;
        }

        .error-banner {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Invoice Modal Overlay */
        .invoice-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .invoice-modal-overlay.active {
            display: flex;
        }

        .invoice-modal-box {
            background: #ffffff;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .invoice-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid #eee;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>

<body>

    <?php include  '../components/navbar.php'; ?>
    <main class="history-container">
        <div class="history-header">
            <h1>My Purchase History</h1>
            <p>Track your CeylonLeaf tea orders and view your official invoices.</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-banner">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif (empty($orders)): ?>
            <div class="empty-state">
                <h2>No Purchases Yet</h2>
                <p>You haven't placed any tea orders yet. Explore our fresh Ceylon tea collection to get started!</p>
                <a href="../index.php" class="btn-shop-now">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="history-card">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Bill Number</th>
                            <th>Order Date &amp; Time</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Order Status</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $bill_display = !empty($order['bill_number'])
                                ? $order['bill_number']
                                : '#INV-' . $order['id'];

                            $payment_display = !empty($order['payment_method'])
                                ? $order['payment_method']
                                : 'Card / Online';

                            $status_raw = $order['status'] ?? 'Not Packed';
                            $status_class = 'status-not-packed';
                            if ($status_raw === 'Packed') {
                                $status_class = 'status-packed';
                            } elseif ($status_raw === 'Delivered') {
                                $status_class = 'status-delivered';
                            }
                        ?>
                            <tr>
                                <td class="bill-no"><?php echo htmlspecialchars($bill_display); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                                <td class="amount-cell">Rs.<?php echo number_format((float)$order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($payment_display); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($status_raw); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn-invoice view-invoice-btn"
                                            data-order-id="<?php echo (int)$order['id']; ?>">
                                        View Invoice
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="invoice-modal-overlay" role="dialog" aria-modal="true">
        <div class="invoice-modal-box">
            <div id="invoiceModalContent">Loading invoice...</div>
            <div class="invoice-modal-actions">
                <button type="button" class="btn-invoice" onclick="window.print()">Print Invoice</button>
                <button type="button" id="closeInvoiceModal" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>

    
    <script src="../script.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('invoiceModal');
            const modalContent = document.getElementById('invoiceModalContent');
            const closeBtn = document.getElementById('closeInvoiceModal');
            const invoiceButtons = document.querySelectorAll('.view-invoice-btn');

            invoiceButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const orderId = this.getAttribute('data-order-id');
                    modalContent.innerHTML = '<p>Loading invoice details...</p>';
                    modal.classList.add('active');

                    fetch('get_invoice.php?order_id=' + encodeURIComponent(orderId))
                        .then(function (response) {
                            return response.text();
                        })
                        .then(function (html) {
                            modalContent.innerHTML = html;
                        })
                        .catch(function () {
                            modalContent.innerHTML = '<p style="color: #721c24;">Failed to load invoice. Please try again.</p>';
                        });
                });
            });

            closeBtn.addEventListener('click', function () {
                modal.classList.remove('active');
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
<?php include '../components/footer.php'; ?>
</body>
</html>