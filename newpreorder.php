<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['username']);
}

if (!isLoggedIn()) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
}

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decode cart data from JSON
    $cartData = json_decode($_POST['cart_data'], true);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $table_id = mysqli_real_escape_string($conn, $_POST['table_id']);
    $parking_id = mysqli_real_escape_string($conn, $_POST['parking_id']);
    $guests = mysqli_real_escape_string($conn, $_POST['guests']);

    $errors = [];
    if (empty($date)) {
        $errors[] = "Please select a date.";
    }
    if (empty($time)) {
        $errors[] = "Please select a time.";
    }
    if (empty($table_id)) {
        $errors[] = "Please select a table.";
    }
    if (empty($parking_id)) {
        $errors[] = "Please select a parking slot.";
    }
    if (empty($guests)) {
        $errors[] = "Please enter the number of guests.";
    }

    if (empty($errors)) {
       // Insert into preorder table to get order_id
$preorder_query = "INSERT INTO preorder (user_id, table_id, parking_id, date, time, guests, order_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
$stmt = mysqli_prepare($conn, $preorder_query);
mysqli_stmt_bind_param($stmt, "iiissi", $user_id, $table_id, $parking_id, $date, $time, $guests);

if (mysqli_stmt_execute($stmt)) {
    $order_id = mysqli_insert_id($conn); // Get the inserted order_id

    // Insert into preorder_items table
    $insert_query = "INSERT INTO preorder_items (order_id, id, quantity) VALUES (?, ?, ?)";
    $stmt_items = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt_items, "iii", $order_id, $itemId, $quantity);
     
    
    }

    if (empty($errors)) {
        echo "<script>alert('Your pre-order has been considered. Please wait for confirmation from our staff!'); window.location.href='#';</script>";
    } else {
        echo "<script>alert('Error: Could not make the pre-order. Please try again later.'); window.location.href='preorder.php';</script>";
    }
} else {
    echo "<script>alert('Error: Could not make the pre-order. Please try again later.');</script>";
    echo "Error: " . mysqli_error($conn);
}
    }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .remove-food-item {
            margin-top: 32px; /* Align with the form control height */
        }
        .food-item-group {
            position: relative;
        }
        .remove-food-item {
            position: absolute;
            top: 32px; /* Adjust this value to match the height of your input fields */
            right: 0;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?> <!-- Include navigation -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="mb-4">Pre-order</h2>
                <?php
                if (!empty($errors)) {
                    echo '<div class="alert alert-danger">';
                    foreach ($errors as $error) {
                        echo '<p>' . $error . '</p>';
                    }
                    echo '</div>';
                }
                ?>
<?php
echo '<h2>Selected Items for Preorder:</h2>';
    echo '<ul>';
    
    
    // Prepare a query to get the ID based on the name
    $getItemIdQuery = "SELECT id FROM all_items WHERE name = ?";
    $stmt = mysqli_prepare($conn, $getItemIdQuery);

    foreach ($cartData as $item) {
        echo '<li>' . $item['name'] . ' - Quantity: ' . $item['quantity'] . '</li>';

        // Bind parameters and execute the query
        mysqli_stmt_bind_param($stmt, "s", $item['name']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $itemId = $row['id'];
            $quantity=$item['quantity'];
            // Now you have $itemId for further processing
            
        } else {
            echo 'Item not found in database: ' . $item['name'];
        }
    }
    
       

    echo '</ul>';
?>
                <form id="preorderForm" method="post" action="" >
                    
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
                            <!-- Populate options dynamically -->
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="guests">Number of Guests:</label>
                        <input type="number" name="guests" id="guests" class="form-control"  min="2" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

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
                        $('#parking_id').html('<option value="">Select Parking Slot</option>');

                        if (response.available) {
                            $.each(response.tables, function (index, table) {
                                $('#table_id').append('<option value="' + table.table_id + '">' + table.table_name + '</option>');
                            });
                            $.each(response.parking, function (index, slot) {
                                $('#parking_id').append('<option value="' + slot.parking_id + '">' + slot.park_name + '</option>');
                            });

                            if (selectedTable) {
                                $('#table_id').val(selectedTable);
                            }
                            if (selectedParking) {
                                $('#parking_id').val(selectedParking);
                            }
                        } else {
                            $('#table_id').html('<option value="">No tables available for this date and time</option>');
                            $('#parking_id').html('<option value="">No parking slots available for this date and time</option>');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error checking availability:', error);
                    }
                });
            }
        }

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