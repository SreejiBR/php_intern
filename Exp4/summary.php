<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_order'])) {
        $_SESSION['orders'] = array_filter($_SESSION['orders'], function ($order) {
            return $order['product'] !== $_POST['product_name'];
        });
    } elseif (isset($_POST['clear_orders'])) {
        // Clear all orders
        $_SESSION['orders'] = [];
    }
    header("Location: summary.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Summary</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; padding: 20px; }
        .container { width: 100%; max-width: 800px; padding: 20px; background-color: #f8f8f8; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        .quantity-input { width: 60px; text-align: center; }
        .btn { padding: 8px 10px; border-radius: 5px; font-weight: bold; cursor: pointer; }
        .btn-delete { background-color: #f44336; color: white; border: none; }
        .btn-clear { width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; margin-top: 20px; font-weight: bold; }
        .btn-clear:hover { background-color: #45a049; }
        .total-label { font-weight: bold; font-size: 1.1em; color: #333; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #4CAF50; text-decoration: none; font-weight: bold; }
    </style>
    <script>
        function updateTotal(row, price) {
            const quantity = row.querySelector('.quantity-input').value;
            const totalField = row.querySelector('.total-price');
            totalField.textContent = (quantity * price).toFixed(2);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.total-price').forEach(function (element) {
                grandTotal += parseFloat(element.textContent);
            });
            document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Order Summary</h2>
        <?php if (!empty($_SESSION['orders'])): ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price (INR)</th>
                    <th>Total (INR)</th>
                    <th>Actions</th>
                </tr>
                <?php
                $grandTotal = 0;
                foreach ($_SESSION['orders'] as $order):
                    $total = $order['quantity'] * $order['price'];
                    $grandTotal += $total;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['product']); ?></td>
                    <td>
                        <input type="number" class="quantity-input" min="1" value="<?php echo $order['quantity']; ?>"
                               onchange="updateTotal(this.parentNode.parentNode, <?php echo $order['price']; ?>)">
                    </td>
                    <td><?php echo number_format($order['price'], 2); ?></td>
                    <td class="total-price"><?php echo number_format($total, 2); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($order['product']); ?>">
                            <button type="submit" name="delete_order" class="btn btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="total-label">Grand Total (INR)</td>
                    <td id="grandTotal"><?php echo number_format($grandTotal, 2); ?></td>
                    <td></td>
                </tr>
            </table>
            <form method="post">
                <button type="submit" name="clear_orders" class="btn btn-clear">Clear All Orders</button>
            </form>
        <?php else: ?>
            <p>No products added to the order yet.</p>
        <?php endif; ?>
        <a href="index.php" class="back-link">Back to Add Products</a>
    </div>
</body>
</html>
