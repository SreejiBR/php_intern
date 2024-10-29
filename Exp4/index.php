<?php
session_start();

// Initialize the orders session if not set
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    $product = htmlspecialchars($_POST['product']);
    $quantity = max(1, (int)$_POST['quantity']);
    $price = max(0.01, (float)$_POST['price']);
    
    $found = false;
    foreach ($_SESSION['orders'] as &$order) {
        if ($order['product'] === $product) {
            $order['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    
    // Add new product if it doesn't exist
    if (!$found) {
        $_SESSION['orders'][] = [
            'product' => $product,
            'quantity' => $quantity,
            'price' => $price
        ];
    }
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; padding: 20px; }
        .container { width: 100%; max-width: 600px; padding: 20px; background-color: #f8f8f8; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; }
        form label { display: block; font-weight: bold; margin-top: 10px; }
        form input[type="text"], form input[type="number"] { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ddd; }
        form button { width: 100%; padding: 10px; margin-top: 15px; background-color: #4CAF50; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        form button:hover { background-color: #45a049; }
        .summary-link { display: block; text-align: center; margin-top: 20px; color: #4CAF50; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Product</h2>
        <form method="post" autocomplete="off">
            <label for="product">Product Name:</label>
            <input type="text" id="product" name="product" required>
            
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
            
            <label for="price">Price (INR):</label>
            <input type="number" id="price" name="price" min="0.01" step="0.01" required>
            
            <button type="submit" name="add_order">Add Product</button>
        </form>
        <a href="summary.php" class="summary-link">View Order Summary</a>
    </div>
</body>
</html>
