<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['username']);
}
function isDateInPast($date) {
    $currentDate = date('Y-m-d');
    return $date < $currentDate;
}

// Include database connection and other necessary files
include("db.php");

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Check if cart data is received
$cartData = [];
if (isset($_POST['cart_data'])) {
    $cartData = json_decode($_POST['cart_data'], true);
    $_SESSION['cart_data'] = $cartData; // Store cart data in session to persist across form submissions
} elseif (isset($_SESSION['cart_data'])) {
    $cartData = $_SESSION['cart_data']; // Retrieve cart data from session if it exists
} else {
    header("Location: menu.php"); // Redirect to menu if no cart data is available
    exit;
}

// Get the user ID based on the session username
$username = $_SESSION['username'];
$user_query = "SELECT user_id FROM users WHERE username=?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);

if (!$user_result) {
    die('User query failed: ' . mysqli_error($conn));
}

$user_row = mysqli_fetch_assoc($user_result);

if (!$user_row) {
    die('User not found for username: ' . $username);
}

$user_id = $user_row['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['table_id']) && isset($_POST['parking_id']) && isset($_POST['guests'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $table_id = mysqli_real_escape_string($conn, $_POST['table_id']);
    $parking_id = mysqli_real_escape_string($conn, $_POST['parking_id']);
    $guests = mysqli_real_escape_string($conn, $_POST['guests']);

    // Handle "None" selection
    if ($parking_id === '') {
        $parking_id = '3'; // Set to 3 if "None" is selected
    }

    // Insert into preorder table to get order_id
    $preorder_query = "INSERT INTO preorder (user_id, table_id, parking_id, date, time, guests, order_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
    $stmt = mysqli_prepare($conn, $preorder_query);
    mysqli_stmt_bind_param($stmt, "iiissi", $user_id, $table_id, $parking_id, $date, $time, $guests);

    if (mysqli_stmt_execute($stmt)) {
        $order_id = mysqli_insert_id($conn); // Get the inserted order_id

        // Prepare to insert into preorder_items table
        $insert_query = "INSERT INTO preorder_items (order_id, id, quantity) VALUES (?, ?, ?)";
        $stmt_items = mysqli_prepare($conn, $insert_query);

        // Fetch item IDs and insert each item into preorder_items
        $getItemIdQuery = "SELECT id FROM all_items WHERE name = ?";
        $stmt_get_item_id = mysqli_prepare($conn, $getItemIdQuery);

        foreach ($cartData as $item) {
            mysqli_stmt_bind_param($stmt_get_item_id, "s", $item['name']);
            mysqli_stmt_execute($stmt_get_item_id);
            $result = mysqli_stmt_get_result($stmt_get_item_id);

            if ($row = mysqli_fetch_assoc($result)) {
                $itemId = $row['id'];
                $quantity = $item['quantity'];
                mysqli_stmt_bind_param($stmt_items, "iii", $order_id, $itemId, $quantity);
                mysqli_stmt_execute($stmt_items);
            } else {
                echo 'Item not found in database: ' . $item['name'];
            }
        }

        echo "<script>alert('Your pre-order has been considered. Please wait for confirmation from our staff!'); window.location.href='check_preorder_status.php';</script>";
    } else {
        echo "<script>alert('Error: Could not make the pre-order. Please try again later.'); window.location.href='menu.php';</script>";
        echo "Error: " . mysqli_error($conn);
    }
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
    <style>
        /* Additional CSS styles as needed */
        body {
            background: #f5f5f5;
        }
        .section-bg {
            background: url('booktable1.jpg') no-repeat center center;
            background-size: cover;
            padding: 20px 0;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            border-radius: 5px;
            
        }
        .cart-item-info p {
            margin: 0;
        }
        .cart-item-total {
            font-weight: bold;
            text-align: right;
        }
        .cart-total {
            margin-top: 10px;
            text-align: right;
        }
        .cart-total h5 {
            margin: 0;
            font-weight: bold;
        }
        .header-bg {
            background-color: #007bff; /* Change this color as needed */
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header-bg {
            background: url('simple.webp') no-repeat center center/cover;
    color: #ffffff;
    padding: 10px 15px;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
}
    </style>
</head>
<body>

<?php include("nav.php"); ?> <!-- Include navigation -->

<div class="section-bg">
    <div class="container mt-5">
        <h3 class="header-bg">Review Your Preorder</h3>
        <form id="preorderForm" action="preorder.php" method="POST">
            <div id="cartItemsList">
                <?php
                $totalAmount = 0;
                foreach ($cartData as $item) {
                    $itemTotal = $item['quantity'] * $item['price'];
                    $totalAmount += $itemTotal;
                    echo '<div class="cart-item">';
                    echo '<div class="cart-item-info">';
                    echo '<p>' . $item['name'] . ' - Quantity: ' . $item['quantity'] . '</p>';
                    echo '</div>';
                    echo '<p class="cart-item-total">$' . number_format($itemTotal, 2) . '</p>';
                    echo '<input type="hidden" name="item_names[]" value="' . $item['name'] . '">';
                    echo '<input type="hidden" name="item_quantities[]" value="' . $item['quantity'] . '">';
                    echo '<input type="hidden" name="item_prices[]" value="' . $item['price'] . '">';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="cart-total">
                <h5>Total Amount: $<span id="totalAmount"><?php echo number_format($totalAmount, 2); ?></span></h5>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="time">Time:</label>
                <input type="time" name="time" id="time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="table_id">Table ID:</label>
                <select name="table_id" id="table_id" class="form-control" required>
                    <option value="">Select Table</option>
                    <!-- Populate options dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="parking_id">Parking Slot:</label>
                <select name="parking_id" id="parking_id" class="form-control" required>
                    <option value="">Select Parking Slot</option>
                    <option value="3">None</option> <!-- "None" option with value 3 -->
                    <!-- Populate options dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="guests">Number of Guests:</label>
                <input type="number" name="guests" id="guests" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Preorder</button>
        </form>
    </div>
</div>




<!-- Include necessary JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkJRE1d2onfW7CZQ2yJr1Vka7E+kpaAO9+77D0o4zcpXfMZ0FjK8" crossorigin="anonymous"></script>
<script src="scripts.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        
        $(document).ready(function() {
      

           
        });

        function loadAvailableOptions() {
            var date = $('#date').val();
            var time = $('#time').val();
            if (date && time) {
                $.ajax({
                    type: 'POST',
                    url: 'check_availability.php',
                    data: { date: date, time: time },
                    dataType: 'json',
                    success: function (response) {
                        var selectedTable = $('#table_id').val();
                        var selectedParking = $('#parking_id').val();

                        $('#table_id').html('<option value="">Select Table</option>');
                        $('#parking_id').html('<option value="">Select Parking Slot</option></option><option value="3">None</option>');

                        if (response.tables_available) {
                        $.each(response.tables, function (index, table) {
                            $('#table_id').append('<option value="' + table.table_id + '">' + table.table_name + '</option>');
                        });
                    } else {
                        $('#table_id').html('<option value="">No tables available for this date and time</option>');
                    }

                    if (response.parking_available) {
                        $.each(response.parking, function (index, slot) {
                            $('#parking_id').append('<option value="' + slot.parking_id + '">' + slot.park_name + '</option>');
                        });
                    } else {
                        $('#parking_id').html('<option value="">No parking slots available for this date and time</option><option value="3">None</option>');
                    }
                },
                    error: function (xhr, status, error) {
                        console.error('Error checking availability:', error);
                    }
                });
            }
        }

        $('#date').change(function () {
            var selectedDate = new Date($(this).val());
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate < today) {
                alert('The selected date is in the past. Please choose a future date.');
                $(this).val('');
            } else {
                loadAvailableOptions();
            }
        });

        $('#time').change(function () {
            loadAvailableOptions();
        });

    $('#date, #time').change(function () {
        loadAvailableOptions();
    });

        $('#date, #time').change(function () {
            loadAvailableOptions();
        });

        $('#table_id, #parking_id').focus(function () {
            if (!$('#date').val() || !$('#time').val()) {
                alert('Please select date and time first.');
                $(this).blur(); // Remove focus from the current field
            }
        });

        $('#preorderForm').submit(function (e) {
            if (!$('#date').val() || !$('#time').val()) {
                alert('Please select date and time first.');
                e.preventDefault();
            }
        });
    
    </script>
</body>
</html>

