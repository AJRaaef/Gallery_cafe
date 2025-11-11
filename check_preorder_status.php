<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch user_id from the users table
$username = $_SESSION['username'];
$user_query = "SELECT user_id FROM users WHERE username='$username'";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result) {
    die('User query failed: ' . mysqli_error($conn));
}

$user_row = mysqli_fetch_assoc($user_result);

if (!$user_row) {
    die('User not found for username: ' . $username);
}

$user_id = $user_row['user_id'];

// Fetch latest preorder details for the logged-in user
$preorder_query = "SELECT * FROM preorder WHERE user_id='$user_id' ORDER BY order_id DESC LIMIT 1";
$preorder_result = mysqli_query($conn, $preorder_query);

if (!$preorder_result) {
    die('Preorder query failed: ' . mysqli_error($conn));
}

$preorder_row = mysqli_fetch_assoc($preorder_result);

// Initialize message variable
$message = "";

if (!$preorder_row) {
    $message = "No preorders found.";
} else {
    $order_id = $preorder_row['order_id'];
    $status = $preorder_row['order_status'];

    // Fetch parking_name from parking table
    $parking_id = $preorder_row['parking_id'];
    $parking_query = "SELECT park_name FROM parking WHERE parking_id='$parking_id'";
    $parking_result = mysqli_query($conn, $parking_query);
    $parking_row = mysqli_fetch_assoc($parking_result);
    $parking_name = $parking_row['park_name'];

    // Fetch table_name from tables table
    $table_id = $preorder_row['table_id'];
    $table_query = "SELECT table_name FROM tables WHERE table_id='$table_id'";
    $table_result = mysqli_query($conn, $table_query);
    $table_row = mysqli_fetch_assoc($table_result);
    $table_name = $table_row['table_name'];

    // Fetch food_id, food_name, quantity, price from preorder_items and all_items tables
    $order_details_query = "SELECT pi.id AS food_id, ai.name AS food_name, pi.quantity, ai.price
                            FROM preorder_items pi
                            INNER JOIN all_items ai ON pi.id = ai.id
                            WHERE pi.order_id='$order_id'";
    $order_details_result = mysqli_query($conn, $order_details_query);

    if (!$order_details_result) {
        die('Order details query failed: ' . mysqli_error($conn));
    }

    // Prepare details for display
    $details_message = "<h5>Preorder Details</h5>";
    $details_message .= "<p><strong>Order ID:</strong> $order_id</p>";
    $details_message .= "<p><strong>User Name:</strong> $username</p>";
    $details_message .= "<p><strong>Parking Name:</strong> $parking_name</p>";
    $details_message .= "<p><strong>Table Name:</strong> $table_name</p>";
    $details_message .= "<p><strong>Items:</strong></p><table class='table table-striped'><thead><tr><th>Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>";

    $total_amount = 0;
    while ($item_row = mysqli_fetch_assoc($order_details_result)) {
        $food_name = $item_row['food_name'];
        $quantity = $item_row['quantity'];
        $price = $item_row['price'];
        $item_total = $quantity * $price;
        $total_amount += $item_total;
        $details_message .= "<tr><td>$food_name</td><td>$quantity</td><td>$$price</td><td>$$item_total</td></tr>";
    }
    $details_message .= "</tbody></table>";
    $details_message .= "<p><strong>Total Amount:</strong> $$total_amount</p>";

    // Prepare status message
    if (strtolower($status) == 'pending') {
        $message = "Your preorder is pending. Kindly wait for the confirmation message from our staff.";
    } elseif (strtolower($status) == 'confirmed') {
        $message = "Your preorder has been confirmed.";
    } elseif (strtolower($status) == 'cancelled') {
        $message = "Your preorder has been cancelled.";
    }

    // Combine message and details for output
    $message .= "<br><br>" . $details_message;
}

// Handle AJAX request for dynamic status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'check_status') {
    echo $message; // Output the message for AJAX request
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Preorder Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?> <!-- Include navigation -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Check Preorder Status</h4>
                    </div>
                    <div class="card-body">
                        <p id="status-message">Loading your preorder status...</p>
                        <div id="preorder-details"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function checkPreorderStatus() {
                $.ajax({
                    url: 'check_preorder_status.php',
                    method: 'POST',
                    data: { action: 'check_status' },
                    success: function(response) {
                        $('#status-message').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }

            // Initial check
            checkPreorderStatus();

            // Check every 5 seconds
            setInterval(checkPreorderStatus, 5000);

            // Hide the status message after 1 minute
            setTimeout(function() {
                $('#status-message').fadeOut();
            }, 60000);
        });
    </script>
</body>
</html>
