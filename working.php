<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// Include database connection and other necessary files
include("db.php");

// Ensure the user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get the cart data from the POST request
$cartData = isset($_POST['cart_data']) ? json_decode($_POST['cart_data'], true) : [];

// Calculate the total amount
$totalAmount = 0;
foreach ($cartData as $item) {
    $totalAmount += $item['quantity'] * $item['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preorder - The Gallery Café</title>
    <!-- Include necessary CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include("nav.php"); ?> <!-- Include navigation -->

<div class="container mt-5">
    <h3>Your Preorder</h3>
    <?php if (empty($cartData)): ?>
        <p>No items in your preorder. <a href="menu.php">Go back to menu</a></p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartData as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo '$' . number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo '$' . number_format($item['quantity'] * $item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                    <td><strong><?php echo '$' . number_format($totalAmount, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <div class="text-right">
            <a href="confirm_preorder.php" class="btn btn-success">Confirm Preorder</a>
        </div>
    <?php endif; ?>
</div>

<footer class="text-center mt-5">
    <div class="container">
        <p>&copy; 2024 The Gallery Café. All Rights Reserved.</p>
    </div>
</footer>

<!-- Include necessary JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
</body>
</html>
